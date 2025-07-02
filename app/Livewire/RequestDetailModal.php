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
            $requestItem->update(['status' => 'disetujui']);
            // Update status utama permintaan
            $parentRequest = $requestItem->request;
            $menunggu = $parentRequest->requestItems()->where('status', 'menunggu')->count();
            $disetujui = $parentRequest->requestItems()->where('status', 'disetujui')->count();
            $ditolak = $parentRequest->requestItems()->where('status', 'ditolak')->count();
            $total = $parentRequest->requestItems()->count();
            if ($disetujui > 0 && $menunggu === 0 && $ditolak === 0) {
                $parentRequest->status = 'disetujui';
            } elseif ($disetujui > 0 && ($menunggu > 0 || $ditolak > 0)) {
                $parentRequest->status = 'sebagian_disetujui';
            } elseif ($ditolak === $total) {
                $parentRequest->status = 'ditolak';
            } else {
                $parentRequest->status = 'menunggu';
            }
            $parentRequest->save();
            $this->loadRequest(); // Reload data
            session()->flash('message', 'Barang berhasil disetujui');
        }
    }

    public function rejectSingle($itemId)
    {
        $requestItem = \App\Models\RequestItem::find($itemId);
        if ($requestItem) {
            $requestItem->update(['status' => 'ditolak']);
            // Update status utama permintaan
            $parentRequest = $requestItem->request;
            $menunggu = $parentRequest->requestItems()->where('status', 'menunggu')->count();
            $disetujui = $parentRequest->requestItems()->where('status', 'disetujui')->count();
            $ditolak = $parentRequest->requestItems()->where('status', 'ditolak')->count();
            $total = $parentRequest->requestItems()->count();
            if ($disetujui > 0 && $menunggu === 0 && $ditolak === 0) {
                $parentRequest->status = 'disetujui';
            } elseif ($disetujui > 0 && ($menunggu > 0 || $ditolak > 0)) {
                $parentRequest->status = 'sebagian_disetujui';
            } elseif ($ditolak === $total) {
                $parentRequest->status = 'ditolak';
            } else {
                $parentRequest->status = 'menunggu';
            }
            $parentRequest->save();
            $this->loadRequest(); // Reload data
            session()->flash('message', 'Barang berhasil ditolak');
        }
    }

    public function render()
    {
        return view('livewire.request-detail-modal');
    }
} 