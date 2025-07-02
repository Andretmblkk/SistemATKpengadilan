@php /** @var \App\Models\PurchaseRequest $record */ @endphp
<div>
    <h2 class="text-lg font-bold mb-2">Detail Pengajuan Pembelian</h2>
    <div class="mb-2">
        <strong>Barang:</strong> {{ $record->item->name ?? '-' }}
    </div>
    <div class="mb-2">
        <strong>Stok Saat Ini:</strong> {{ $record->current_stock }}
    </div>
    <div class="mb-2">
        <strong>Batas Stok Minimal:</strong> {{ $record->reorder_point }}
    </div>
    <div class="mb-2">
        <strong>Jumlah Pengajuan:</strong> {{ $record->requested_quantity }}
    </div>
    <div class="mb-2">
        <strong>Status:</strong> {{ __(ucfirst($record->status)) }}
    </div>
    @if($record->status === 'rejected')
        <div class="mb-2">
            <strong>Alasan Penolakan:</strong> {{ $record->rejection_reason }}
        </div>
    @endif
    <div class="mb-2">
        <strong>Dibuat oleh:</strong> {{ $record->creator->name ?? '-' }}
    </div>
    <div class="mb-2">
        <strong>Disetujui oleh:</strong> {{ $record->approver->name ?? '-' }}
    </div>
    <div class="mb-2">
        <strong>Dibuat pada:</strong> {{ $record->created_at->format('d-m-Y H:i') }}
    </div>
</div> 