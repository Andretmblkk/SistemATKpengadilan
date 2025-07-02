<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Resources\PurchaseRequestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class ViewPurchaseRequest extends ViewRecord
{
    protected static string $resource = PurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui Pengajuan')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn () => auth()->user()->hasRole('pimpinan') && $this->record->status === 'waiting_approval')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->status = 'approved';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Pengajuan pembelian telah disetujui.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('reject')
                ->label('Tolak Pengajuan')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn () => auth()->user()->hasRole('pimpinan') && $this->record->status === 'waiting_approval')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $this->record->status = 'rejected';
                    $this->record->rejection_reason = $data['rejection_reason'];
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Pengajuan pembelian telah ditolak.')
                        ->success()
                        ->send();
                }),
        ];
    }
} 