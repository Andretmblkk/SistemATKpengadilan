<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

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
                    ->required(),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Item')
                            ->options(\App\Models\Item::whereNotNull('name')->pluck('name', 'id')->toArray())
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->required()
                    ->minItems(1) // Memastikan minimal 1 item ditambahkan
                    ->validationMessages([
                        'required' => 'Please add at least one item to the request.',
                        'min' => 'Please add at least one item to the request.',
                    ]),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
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
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->action(function (Request $record) {
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
                    ->action(function (Request $record) {
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