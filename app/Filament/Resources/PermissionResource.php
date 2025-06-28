<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Izin';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    // Batasi akses hanya untuk role 'admin'
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
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
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    // Pembatasan manual untuk navigasi
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Sembunyikan untuk semua role
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Permission Name')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Permission Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('admin')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}