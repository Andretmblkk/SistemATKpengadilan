<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\Request;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RequestResource extends Resource
{
    // Kategori 1: Konfigurasi Dasar Resource
    protected static ?string $model = Request::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Permintaan Barang';
    protected static ?string $pluralLabel = 'Permintaan Barang';
    protected static ?string $navigationGroup = 'Inventory';

    // Kategori 2: Formulir untuk Create/Edit
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Staff')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->dehydrated(true),
                Forms\Components\Select::make('item_id')
                    ->label('Item')
                    ->relationship('item', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn () => !auth()->user()->hasAnyRole(['admin', 'pimpinan'])),
                Forms\Components\Select::make('delivery_status')
                    ->label('Status Pengambilan')
                    ->options([
                        'not_delivered' => 'Belum Diambil',
                        'delivered' => 'Sudah Diambil',
                    ])
                    ->default('not_delivered')
                    ->required()
                    // Hanya muncul saat edit dan status adalah 'approved'
                    ->visible(fn ($get, $operation, $record) => $operation === 'edit' && $get('status') === 'approved')
                    ->disabled(fn ($get) => $get('status') !== 'approved')
                    ->helperText('Hanya dapat diubah jika status permintaan adalah Approved.')
                    ->dehydrated(true),
            ]);
    }

    // Kategori 3: Tabel untuk Daftar Permintaan
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        default => 'Tidak Diketahui',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_status')
                    ->label('Status Pengambilan')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'not_delivered' => 'Belum Diambil',
                        'delivered' => 'Sudah Diambil',
                        default => 'Tidak Diketahui',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple()
                    ->preload(),
                SelectFilter::make('delivery_status')
                    ->label('Status Pengambilan')
                    ->options([
                        'not_delivered' => 'Belum Diambil',
                        'delivered' => 'Sudah Diambil',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                // Aksi Approve
                Action::make('approve')
                    ->label('Setujui')
                    ->action(function ($record) {
                        try {
                            DB::beginTransaction();
                            $item = Item::find($record->item_id);
                            if (!$item) {
                                Log::error('Item tidak ditemukan untuk request ID: ' . $record->id . ', item_id: ' . $record->item_id);
                                Notification::make()
                                    ->title('Gagal')
                                    ->body('Item tidak ditemukan.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            if ($item->stock >= $record->quantity) {
                                $item->decrement('stock', $record->quantity);
                                $record->update(['status' => 'approved']);
                                DB::commit();
                                Log::info('Permintaan disetujui, stok dikurangi. Request ID: ' . $record->id . ', Item ID: ' . $item->id . ', Jumlah: ' . $record->quantity . ', Stok tersisa: ' . $item->stock);
                                Notification::make()
                                    ->title('Permintaan disetujui')
                                    ->body('Stok item ' . $item->name . ' telah dikurangi sebanyak ' . $record->quantity . '.')
                                    ->success()
                                    ->send();
                            } else {
                                Log::warning('Stok tidak cukup untuk request ID: ' . $record->id . ', Item ID: ' . $item->id . ', Stok tersedia: ' . $item->stock . ', Diminta: ' . $record->quantity);
                                Notification::make()
                                    ->title('Stok tidak cukup')
                                    ->body('Stok item ' . $item->name . ' hanya ' . $item->stock . ' unit.')
                                    ->danger()
                                    ->send();
                                DB::rollBack();
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Error saat menyetujui permintaan ID: ' . $record->id . ', Pesan: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->authorize(fn () => auth()->user()->hasAnyRole(['admin', 'pimpinan']))
                    ->color('success'),
                // Aksi Reject
                Action::make('reject')
                    ->label('Tolak')
                    ->action(function ($record) {
                        try {
                            $record->update(['status' => 'rejected']);
                            Log::info('Permintaan ditolak. Request ID: ' . $record->id);
                            Notification::make()
                                ->title('Permintaan ditolak')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Error saat menolak permintaan ID: ' . $record->id . ', Pesan: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->authorize(fn () => auth()->user()->hasAnyRole(['admin', 'pimpinan']))
                    ->color('danger'),
                // Aksi Mark as Delivered
                Action::make('mark_delivered')
                    ->label('Tandai Diambil')
                    ->action(function ($record) {
                        try {
                            if ($record->status !== 'approved') {
                                Notification::make()
                                    ->title('Gagal')
                                    ->body('Permintaan harus disetujui sebelum ditandai sebagai diambil.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $record->update(['delivery_status' => 'delivered']);
                            Log::info('Permintaan ditandai sebagai diambil. Request ID: ' . $record->id);
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Permintaan telah ditandai sebagai diambil.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Error saat menandai permintaan sebagai diambil ID: ' . $record->id . ', Pesan: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'approved' && $record->delivery_status === 'not_delivered')
                    ->authorize(fn () => auth()->user()->hasAnyRole(['admin', 'staff']))
                    ->color('info'),
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->defaultPaginationPageOption(10);
    }

    // Kategori 4: Pengelolaan Halaman
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
            'view' => Pages\ViewRequest::route('/{record}'),
        ];
    }

    // Kategori 5: Pengaturan Hak Akses
    public static function canCreate(array $parameters = []): bool
    {
        return auth()->user()->hasRole('staff') || auth()->user()->hasRole('admin');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('staff') || auth()->user()->hasRole('admin');
    }
}