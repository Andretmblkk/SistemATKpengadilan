<?php

namespace App\Http\Controllers;

use App\Models\AtkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AtkRequestController extends Controller
{
    public function store(Request $request): AtkRequest
    {
        Log::info('Raw request data: ' . json_encode($request->all()));
        try {
            $data = $request->validate([
                'data.user_id' => 'required|exists:users,id',
                'data.notes' => 'nullable|string',
                'data.items' => 'nullable|array',
                'data.items.*.item_id' => 'required|exists:items,id',
                'data.items.*.quantity' => 'required|integer|min:1',
            ]);

            $atkRequest = AtkRequest::create([
                'user_id' => $data['data']['user_id'],
                'notes' => $data['data']['notes'],
                'status' => 'pending',
            ]);

            if (!empty($data['data']['items'])) {
                $syncData = collect($data['data']['items'])->mapWithKeys(function ($item) {
                    return [$item['item_id'] => ['quantity' => $item['quantity']]];
                })->toArray();
                $atkRequest->items()->sync($syncData);
                Log::info('Synced items for AtkRequest: ' . json_encode($syncData));
            }

            return $atkRequest;
        } catch (\Exception $e) {
            Log::error('Error creating AtkRequest: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}