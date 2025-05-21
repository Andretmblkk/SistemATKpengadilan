<?php
namespace App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('Auth status: ', [
            'check' => auth()->check(),
            'id' => auth()->id(),
            'user' => auth()->user() ? auth()->user()->toArray() : null,
            'session' => session()->all(),
        ]);
        if (!isset($data['user_id']) && auth()->check()) {
            $data['user_id'] = auth()->id();
        } elseif (!isset($data['user_id'])) {
            $defaultUserId = 1; // Ganti dengan ID user staff/admin yang valid
            $data['user_id'] = $defaultUserId;
            Log::warning('No authenticated user found, using default user_id: ' . $defaultUserId);
        }
        Log::info('Form data before create: ', $data);
        return $data;
    }
}