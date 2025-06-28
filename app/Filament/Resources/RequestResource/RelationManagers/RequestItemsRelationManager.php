<?php

namespace App\Filament\Resources\RequestResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RequestItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'requestItems';
    protected static ?string $title = 'Barang yang Diajukan';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('NAMA BARANG DEBUG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => 'Tidak Diketahui',
                    }),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'Barang yang Diajukan';
    }
} 