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

                // Pengurangan stok dan trigger otomatisasi pengajuan pembelian
                foreach ($data['data']['items'] as $itemData) {
                    $item = \App\Models\Item::find($itemData['item_id']);
                    if ($item) {
                        $item->stock = max(0, $item->stock - $itemData['quantity']);
                        $item->save();
                        \Log::info('Stok barang dikurangi', ['item_id' => $item->id, 'stok_akhir' => $item->stock, 'batas_minimal' => $item->reorder_point]);
                        // Cek reorder point dan buat draft pengajuan jika perlu
                        if ($item->stock <= $item->reorder_point) {
                            $existing = \App\Models\PurchaseRequest::where('item_id', $item->id)
                                ->whereIn('status', ['draft', 'waiting_approval'])
                                ->first();
                            if (!$existing) {
                                \App\Models\PurchaseRequest::create([
                                    'item_id' => $item->id,
                                    'current_stock' => $item->stock,
                                    'reorder_point' => $item->reorder_point,
                                    'status' => 'draft',
                                    'created_by' => auth()->id(),
                                ]);
                                \Log::info('Pengajuan pembelian otomatis dibuat', ['item_id' => $item->id, 'stok' => $item->stock]);
                            } else {
                                \Log::info('Pengajuan pembelian sudah ada, tidak dibuat ulang', ['item_id' => $item->id]);
                            }
                        }
                    }
                }
            }

            return $atkRequest;
        } catch (\Exception $e) {
            Log::error('Error creating AtkRequest: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}