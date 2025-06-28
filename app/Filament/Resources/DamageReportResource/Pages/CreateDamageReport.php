<?php

namespace App\Filament\Resources\DamageReportResource\Pages;

use App\Filament\Resources\DamageReportResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\User;

class CreateDamageReport extends CreateRecord
{
    protected static string $resource = DamageReportResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Laporan Barang Rusak Baru')
            ->body("Item {$this->record->item->name} dilaporkan rusak oleh {$this->record->user->name}.")
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['reported_at'])) {
            $data['reported_at'] = now('Asia/Jayapura')->toDateString();
        }
        return $data;
    }
}