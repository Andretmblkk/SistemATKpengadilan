<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        $actions = parent::getHeaderActions();
        if (auth()->user()->hasRole('pimpinan') && $this->record->status === 'dikirim') {
            $actions[] = Actions\Action::make('accept')
                ->label('Laporan Diterima')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->status = 'diterima';
                    $this->record->save();
                    \Filament\Notifications\Notification::make()
                        ->title('Laporan telah diterima')
                        ->success()
                        ->send();
                });
        }
        return $actions;
    }
}