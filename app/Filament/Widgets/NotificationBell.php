<?php

namespace App\Filament\Widgets;

use App\Models\AtkRequest;
use App\Models\RequestItem;
use Filament\Widgets\Widget;

class NotificationBell extends Widget
{
    protected static string $view = 'filament.widgets.notification-bell';
    protected static ?int $sort = -1;

    public $pendingCount = 0;
    public $unreadCount = 0;
    public $showDot = false;

    public function mount(): void
    {
        // Tampilkan titik jika ada permintaan barang atau pengajuan pembelian yang statusnya approved atau waiting_approval
        $this->showDot = 
            \App\Models\AtkRequest::where('status', 'approved')->exists() ||
            \App\Models\PurchaseRequest::where('status', 'approved')->exists() ||
            \App\Models\PurchaseRequest::where('status', 'waiting_approval')->exists();
    }

    public function markAsRead()
    {
        // Tidak perlu logika apapun, hanya reset titik
        $this->showDot = false;
    }
} 