<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\AtkRequest;
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
use App\Models\User;

class RequestResource extends Resource
{
    // Kategori 1: Konfigurasi Dasar Resource
    protected static ?string $model = AtkRequest::class;
    protected static ?array $with = ['user', 'requestItems', 'requestItems.item'];
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Permintaan Barang';
    protected static ?string $pluralLabel = 'Permintaan Barang';
    protected static ?string $navigationGroup = 'Inventaris';

    // Kategori 2: Formulir untuk Create/Edit
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Staff')
                    ->options(\App\Models\User::all()->pluck('name','id'))
                    ->required()
                    ->default(auth()->id())
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->dehydrated(true),
                Forms\Components\Repeater::make('items')
                    ->label('Daftar Barang')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->options(\App\Models\Item::all()->pluck('name','id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $item = \App\Models\Item::find($state);
                                $set('stock', $item ? $item->stock : null);
                            })
                            ->helperText(function ($state) {
                                $item = \App\Models\Item::find($state);
                                return $item ? 'Stok saat ini: ' . $item->stock : 'Pilih barang untuk melihat stok.';
                            }),
                        Forms\Components\Hidden::make('stock'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(fn ($get) => $get('stock') ?? null)
                            ->helperText(fn ($get) => $get('stock') !== null ? 'Maksimal: ' . $get('stock') : null),
                    ])
                    ->minItems(1)
                    ->createItemButtonLabel('Tambah Barang')
                    ->columnSpanFull()
                    ->disableItemCreation(false)
                    ->disableItemDeletion(false)
                    ->rules([
                        function ($state) {
                            $itemIds = collect($state)->pluck('item_id');
                            if ($itemIds->count() !== $itemIds->unique()->count()) {
                                return 'Tidak boleh ada barang yang sama dalam satu permintaan.';
                            }
                            return null;
                        }
                    ]),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
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
            ->modifyQueryUsing(fn ($query) => $query->with(['requestItems.item', 'user']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning', // kuning
                        'approved' => 'success', // hijau
                        'rejected' => 'danger', // merah
                        default => 'secondary', // abu-abu
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('requestItems')
                    ->label('Barang Diajukan')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->requestItems || $record->requestItems->count() === 0) {
                            return 'Tidak ada barang';
                        }
                        return $record->requestItems->map(function($item) {
                            return $item->item ? $item->item->name : '-';
                        })->implode(', ');
                    })
                    ->wrap()
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detail Permintaan Barang')
                    ->modalContent(function ($record) {
                        return view('filament.resources.request-resource.pages.modal-content', [
                            'requestId' => $record->id
                        ]);
                    })
                    ->modalActions([
                        \Filament\Actions\Action::make('close')
                            ->label('Tutup')
                            ->color('gray')
                            ->close()
                    ]),
                Action::make('approveSelected')
                    ->label('Setujui yang Dipilih')
                    ->action('approveSelected')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole('pimpinan')),
                Action::make('rejectSelected')
                    ->label('Tolak yang Dipilih')
                    ->action('rejectSelected')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole('pimpinan')),
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
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function canCreate(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['staff', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('admin') ||
            (auth()->user()->hasRole('staff') && $record->user_id === auth()->id())
        );
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canView(Model $record): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = \App\Models\AtkRequest::whereHas('requestItems', function($q) {
            $q->where('status', \App\Models\RequestItem::STATUS_PENDING);
        })->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $total = \App\Models\AtkRequest::count();
        $pending = \App\Models\AtkRequest::whereHas('requestItems', function($q) {
            $q->where('status', \App\Models\RequestItem::STATUS_PENDING);
        })->count();
        $approved = \App\Models\AtkRequest::whereDoesntHave('requestItems', function($q) {
            $q->where('status', \App\Models\RequestItem::STATUS_PENDING);
        })->whereHas('requestItems', function($q) {
            $q->where('status', \App\Models\RequestItem::STATUS_APPROVED);
        })->count();
        if ($pending > 0) {
            return 'danger'; // merah
        } elseif ($approved > 0 && $approved == $total) {
            return 'success'; // hijau
        } elseif ($approved > 0 && $pending == 0) {
            return 'warning'; // kuning (campuran)
        }
        return 'secondary';
    }
}