<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class ItemObserver
{
    public function updated(Item $item)
    {
        // Cek jika stok menyentuh atau di bawah reorder_point
        if (
            $item->stock <= $item->reorder_point &&
            $item->reorder_point > 0 &&
            !PurchaseRequest::where('item_id', $item->id)
                ->whereIn('status', ['draft', 'waiting_approval', 'approved'])
                ->exists()
        ) {
            PurchaseRequest::create([
                'item_id' => $item->id,
                'current_stock' => $item->stock,
                'reorder_point' => $item->reorder_point,
                'requested_quantity' => $item->reorder_point * 2, // Atau logika sesuai kebutuhan
                'status' => 'draft',
                'created_by' => Auth::id() ?? 1, // fallback ke user id 1 jika tidak ada auth
            ]);
        }
    }
} 