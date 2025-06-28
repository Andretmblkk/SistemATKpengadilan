<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AtkRequest;

class RequestDetailModal extends Component
{
    public $requestId;
    public $request;

    public function mount($requestId)
    {
        $this->requestId = $requestId;
        $this->loadRequest();
    }

    public function loadRequest()
    {
        $this->request = AtkRequest::with(['requestItems.item', 'user'])
            ->find($this->requestId);
    }

    public function approveSingle($itemId)
    {
        $requestItem = \App\Models\RequestItem::find($itemId);
        if ($requestItem) {
            $requestItem->update(['status' => 'approved']);
            $this->loadRequest(); // Reload data
            session()->flash('message', 'Barang berhasil disetujui');
        }
    }

    public function rejectSingle($itemId)
    {
        $requestItem = \App\Models\RequestItem::find($itemId);
        if ($requestItem) {
            $requestItem->update(['status' => 'rejected']);
            $this->loadRequest(); // Reload data
            session()->flash('message', 'Barang berhasil ditolak');
        }
    }

    public function render()
    {
        return view('livewire.request-detail-modal');
    }
} 