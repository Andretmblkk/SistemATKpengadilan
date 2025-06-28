<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Barang';

    protected static ?string $navigationGroup = 'Inventaris';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->rules(['required', 'string', 'max:255'])
                    ->dehydrated(true),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->rules(['nullable', 'string'])
                    ->dehydrated(true),
                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->rules(['required', 'integer', 'min:0'])
                    ->dehydrated(true),
                Forms\Components\TextInput::make('reorder_point')
                    ->label('Batas Stok Minimal')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->rules(['required', 'integer', 'min:0'])
                    ->dehydrated(true),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->dehydrated(true),
                Forms\Components\Select::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'alat_tulis' => 'Alat Tulis',
                        'perlengkapan_kantor' => 'Perlengkapan Kantor',
                        'dokumen' => 'Dokumen',
                    ])
                    ->required()
                    ->rules(['required', 'in:alat_tulis,perlengkapan_kantor,dokumen'])
                    ->dehydrated(true),
            ])
            ->statePath('data')
            ->reactive();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric(),
                Tables\Columns\TextColumn::make('reorder_point')
                    ->label('Batas Stok Minimal')
                    ->numeric(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'alat_tulis' => 'Alat Tulis',
                        'perlengkapan_kantor' => 'Perlengkapan Kantor',
                        'dokumen' => 'Dokumen',
                        default => 'Tidak Ada Kategori',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'alat_tulis' => 'Alat Tulis',
                        'perlengkapan_kantor' => 'Perlengkapan Kantor',
                        'dokumen' => 'Dokumen',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
                Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->defaultPaginationPageOption(10);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}