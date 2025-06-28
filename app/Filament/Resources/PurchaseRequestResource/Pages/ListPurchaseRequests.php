<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Resources\PurchaseRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseRequests extends ListRecords
{
    protected static string $resource = PurchaseRequestResource::class;

    public function getTitle(): string
    {
        return 'Daftar Pengajuan Pembelian';
    }

    public function getCreateButtonLabel(): string
    {
        return 'Tambah Pengajuan Pembelian';
    }
} 