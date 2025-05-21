<?php
namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function beforeCreate(): void
    {
        Log::info('Before creating AtkRequest: ' . json_encode($this->data));
    }

    protected function afterCreate(): void
    {
        Log::info('After creating AtkRequest: ' . json_encode($this->record->toArray()));
        Log::info('Related items: ' . json_encode($this->record->items->toArray()));
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        Log::info('Handling record creation with data: ' . json_encode($data));
        return parent::handleRecordCreation($data);
    }
}