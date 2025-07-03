<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestResource\Pages;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Pengajuan Pembelian';
    protected static ?string $navigationGroup = 'Inventaris';

    // Batasan akses berdasarkan role
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canEdit($record): bool
    {
        // Admin hanya bisa edit jika status draft atau waiting_approval
        return auth()->check() && auth()->user()->hasRole('admin') && in_array($record->status, ['draft', 'waiting_approval']);
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'pimpinan']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('item_id')
                ->label('Barang')
                ->options(\App\Models\Item::all()->pluck('name','id'))
                ->required()
                ->searchable()
                ->preload()
                ->validationMessages([
                    'required' => 'Barang wajib dipilih.'
                ])
                ->disabled(fn($record) => $record),
            Forms\Components\TextInput::make('current_stock')
                ->label('Stok Saat Ini')
                ->disabled(),
            Forms\Components\TextInput::make('reorder_point')
                ->label('Batas Stok Minimal')
                ->disabled(),
            Forms\Components\TextInput::make('requested_quantity')
                ->label('Jumlah Pengajuan')
                ->numeric()
                ->required(fn($record) => $record?->status === 'draft')
                ->minValue(1)
                ->validationMessages([
                    'required' => 'Jumlah pengajuan wajib diisi.',
                    'min' => 'Jumlah pengajuan minimal 1.'
                ])
                ->disabled(fn($record) => $record && $record->status !== 'draft'),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'draft' => 'Draft',
                    'waiting_approval' => 'Menunggu Persetujuan',
                    'approved' => 'Disetujui',
                    'pembelian_diizinkan' => 'Pembelian Diizinkan',
                    'rejected' => 'Ditolak',
                    'completed' => 'Selesai',
                ])
                ->disabled(),
            Forms\Components\Textarea::make('rejection_reason')
                ->label('Alasan Penolakan')
                ->disabled(fn($record) => $record?->status !== 'rejected'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('item.name')->label('Barang'),
            Tables\Columns\TextColumn::make('current_stock')->label('Stok Saat Ini'),
            Tables\Columns\TextColumn::make('reorder_point')->label('Batas Stok Minimal'),
            Tables\Columns\TextColumn::make('requested_quantity')->label('Jumlah Pengajuan'),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state, $record) => $record->is_stock_updated ? 'primary' : match ($state) {
                    'waiting_approval' => 'warning',
                    'approved'         => 'success',
                    'rejected'         => 'danger',
                    default            => 'secondary',
                })
                ->formatStateUsing(fn($state, $record) => $record->is_stock_updated ? 'Stok Diupdate' : match($state) {
                    'draft' => 'Draft',
                    'waiting_approval' => 'Menunggu Persetujuan',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => ucfirst($state),
                }),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\Action::make('submit')
                ->label('Ajukan ke Pimpinan')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn ($record) => $record->status === 'draft' && auth()->user()->hasRole('admin'))
                ->action(function ($record) {
                    $record->status = 'waiting_approval';
                    $record->save();
                })
                ->requiresConfirmation(),
            Tables\Actions\EditAction::make()
                ->visible(fn ($record) => auth()->user()->hasRole('admin') && in_array($record->status, ['draft', 'waiting_approval'])),
            Tables\Actions\Action::make('approve')
                ->label('Setujui Pembelian')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'waiting_approval' && auth()->user()->hasRole('pimpinan'))
                ->action(function ($record) {
                    $record->status = 'approved';
                    $record->approved_by = auth()->id();
                    $record->save();
                })
                ->requiresConfirmation(),
            Tables\Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn ($record) => $record->status === 'waiting_approval' && auth()->user()->hasRole('pimpinan'))
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->status = 'rejected';
                    $record->rejection_reason = $data['rejection_reason'];
                    $record->save();
                })
                ->requiresConfirmation(),
            Tables\Actions\Action::make('update_stock')
                ->label('Update Stok')
                ->icon('heroicon-o-arrow-up')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'approved' && !$record->is_stock_updated && auth()->user()->hasRole('admin'))
                ->action(function ($record) {
                    $item = $record->item;
                    $item->stock += $record->requested_quantity;
                    $item->save();
                    $record->is_stock_updated = true;
                    $record->save();
                })
                ->requiresConfirmation(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn () => auth()->user()->hasRole('admin')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseRequests::route('/'),
            'create' => Pages\CreatePurchaseRequest::route('/create'),
            'edit' => Pages\EditPurchaseRequest::route('/{record}/edit'),
            'view' => Pages\ViewPurchaseRequest::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return null;
    }
} 