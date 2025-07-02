<?php
namespace App\Filament\Resources\RequestResource\Pages;
use Filament\Resources\Pages\Page;

class CreateRequest extends Page
{
    protected static string $resource = \App\Filament\Resources\RequestResource::class;
    protected static string $view = 'filament.resources.request-resource.pages.create-request-cart';
    public function getTitle(): string
    {
        return 'Buat Permintaan Barang';
    }
}