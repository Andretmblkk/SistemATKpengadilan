<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Request;
use Filament\Notifications\Notification;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;

    protected function handleRecordCreation(array $data): Request
    {
        // Pastikan data 'items' ada
        if (!isset($data['items']) || empty($data['items'])) {
            Notification::make()
                ->title('Error')
                ->body('Please add at least one item to the request.')
                ->danger()
                ->send();
            throw new \Exception('No items selected for the request.');
        }

        $request = static::getModel()::create([
            'user_id' => $data['user_id'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        $request->items()->sync(
            collect($data['items'])->mapWithKeys(function ($item) {
                return [$item['item_id'] => ['quantity' => $item['quantity']]];
            })->toArray()
        );

        return $request;
    }
}