<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Peran';

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
                    ->label('Role Name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('permissions')
                    ->label('Permissions')
                    ->multiple()
                    ->options(Permission::all()->pluck('name', 'id'))
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role Name'),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->listWithLineBreaks()
                    ->bulleted(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'), // Pastikan baris ini diakhiri dengan tanda koma
        ]; // Tambahkan tanda kurung kurawal untuk menutup array
    }
}