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
                        // Ambil item terkait
                        $item = Item::find($record->item_id);
                        // Periksa apakah stok cukup
                        if ($item && $item->stock >= $record->quantity) {
                            // Kurangi stok
                            $item->decrement('stock', $record->quantity);
                            // Perbarui status permintaan
                            $record->update(['status' => 'approved']);
                            // Notifikasi sukses
                            Notification::make()
                                ->title('Permintaan disetujui')
                                ->success()
                                ->send();
                        } else {
                            // Notifikasi jika stok tidak cukup
                            Notification::make()
                                ->title('Stok tidak cukup')
                                ->body('Stok item ' . ($item ? $item->name : 'tidak ditemukan') . ' tidak mencukupi.')
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
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Permintaan ditolak')
                            ->success()
                            ->send();
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