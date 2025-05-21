<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\AtkRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class RequestResource extends Resource
{
    protected static ?string $model = AtkRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationLabel = 'ATK Requests';

    protected static ?string $navigationGroup = 'Inventory';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff', 'pimpinan']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'staff']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Requested By')
                    ->relationship('user', 'name')
                    ->default(fn() => auth()->id())
                    ->dehydrated(true),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Item')
                            ->options(function () {
                                $items = \App\Models\Item::pluck('name', 'id')->toArray();
                                Log::info('Available items for repeater: ' . json_encode($items));
                                return $items ?: ['0' => 'No items available'];
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->dehydrated(true)
                            ->afterStateUpdated(function ($state) {
                                Log::info('Selected item_id: ' . $state);
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->dehydrated(true),
                    ])
                    ->columns(2)
                    ->dehydrated(true)
                    ->afterStateUpdated(function ($state) {
                        Log::info('Repeater items data: ' . json_encode($state));
                    }),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull()
                    ->dehydrated(true),
            ])
            ->statePath('data')
            ->reactive();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Request ID'),
                Tables\Columns\TextColumn::make('items.name')
                    ->label('Items')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Requested By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested At')
                    ->dateTime(),
            ])
            ->query(function () {
                return AtkRequest::with('items');
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->action(function (AtkRequest $record) {
                        $record->update(['status' => 'approved']);
                        foreach ($record->items as $item) {
                            $quantity = $record->items()->where('item_id', $item->id)->first()->pivot->quantity;
                            $item->stock -= $quantity;
                            $item->save();
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn($record) => auth()->user()->hasAnyRole(['admin', 'pimpinan']) && $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->action(function (AtkRequest $record) {
                        $record->update(['status' => 'rejected']);
                    })
                    ->requiresConfirmation()
                    ->visible(fn($record) => auth()->user()->hasAnyRole(['admin', 'pimpinan']) && $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }
}