<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;
use App\Models\AtkRequest;
use App\Models\RequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestCartForm extends Component
{
    public $item_id;
    public $quantity;
    public $cart = [];
    public $items;

    public function mount()
    {
        $this->items = Item::all();
    }

    public function addToCart()
    {
        $item = Item::find($this->item_id);
        if (!$item) return;
        // Cek duplikat
        foreach ($this->cart as $c) {
            if ($c['item_id'] == $this->item_id) {
                $this->addError('item_id', 'Barang sudah ada di daftar.');
                return;
            }
        }
        if ($this->quantity < 1 || $this->quantity > $item->stock) {
            $this->addError('quantity', 'Jumlah tidak valid.');
            return;
        }
        $this->cart[] = [
            'item_id' => $this->item_id,
            'item_name' => $item->name,
            'quantity' => $this->quantity,
            'stock' => $item->stock,
        ];
        $this->item_id = null;
        $this->quantity = null;
        $this->resetErrorBag();
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function submitCart()
    {
        $this->validate([
            'cart' => 'required|array|min:1',
        ]);
        DB::beginTransaction();
        try {
            $atkRequest = AtkRequest::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
            ]);

            foreach ($this->cart as $item) {
                // Pengecekan stok di backend
                $dbItem = Item::find($item['item_id']);
                if ($dbItem->stock < $item['quantity']) {
                    throw new \Exception('Stok '.$dbItem->name.' tidak mencukupi (tersedia: '.$dbItem->stock.')');
                }

                RequestItem::create([
                    'atk_request_id' => $atkRequest->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            session()->flash('success', 'Permintaan berhasil diajukan!');
            $this->cart = [];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan permintaan ATK: '.$e->getMessage());
            $this->addError('cart', 'Gagal mengajukan permintaan: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.request-cart-form');
    }
}
