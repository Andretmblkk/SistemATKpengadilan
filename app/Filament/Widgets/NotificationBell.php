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

    public function mount(): void
    {
        // Hitung jumlah permintaan yang masih ada item pending
        $this->pendingCount = AtkRequest::whereHas('requestItems', function($q) {
            $q->where('status', RequestItem::STATUS_PENDING);
        })->count();
    }
} 