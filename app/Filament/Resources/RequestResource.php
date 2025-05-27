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
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required()
                    ->disabled(fn () => !auth()->user()->hasAnyRole(['admin', 'pimpinan'])),
            ]);
    }

    // Kategori 3: Tabel untuk Daftar Permintaan
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->visible(fn () => auth()->user()->hasRole('admin')),
                Tables\Columns\TextColumn::make('item.name')->label('Item'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                // Aksi Approve
                Action::make('approve')
                    ->label('Setujui')
                    ->action(function ($record) {
                        try {
                            // Mulai transaksi database
                            DB::beginTransaction();

                            // Ambil item terkait
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

                            // Periksa apakah stok cukup
                            if ($item->stock >= $record->quantity) {
                                // Kurangi stok
                                $item->decrement('stock', $record->quantity);
                                // Perbarui status permintaan
                                $record->update(['status' => 'approved']);
                                // Commit transaksi
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
                    ->visible(function ($record) {
                        return $record->status === 'pending';
                    })
                    ->authorize(function () {
                        return auth()->user()->hasAnyRole(['admin', 'pimpinan']);
                    })
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
                    ->visible(function ($record) {
                        return $record->status === 'pending';
                    })
                    ->authorize(function () {
                        return auth()->user()->hasAnyRole(['admin', 'pimpinan']);
                    })
                    ->color('danger'),
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
            ]);
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