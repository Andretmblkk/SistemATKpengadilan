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

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $navigationGroup = 'User Management';

    // Batasi akses hanya untuk role 'admin' dengan debug sementara
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            \Log::debug('No authenticated user found in RoleResource.');
            return false;
        }
        $hasAccess = $user->hasRole('admin');
        \Log::debug('User ' . $user->email . ' hasRole(admin): ' . ($hasAccess ? 'true' : 'false'));
        return $hasAccess;
    }

    // Pembatasan manual untuk navigasi
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
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
                    ->relationship('permissions', 'name')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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