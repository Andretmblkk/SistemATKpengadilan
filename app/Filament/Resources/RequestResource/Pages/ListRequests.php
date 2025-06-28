<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function approveSingle($itemId)
    {
        $item = \App\Models\RequestItem::find($itemId);
        
        if (!$item) {
            Notification::make()
                ->title('Gagal')
                ->body('Item tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        if ($item->status !== 'pending') {
            Notification::make()
                ->title('Gagal')
                ->body('Item sudah diproses.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();
        try {
            $item->status = 'approved';
            $item->approved_at = now();
            $item->approved_by = auth()->id();
            $item->save();
            
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item disetujui.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function rejectSingle($itemId)
    {
        $item = \App\Models\RequestItem::find($itemId);
        
        if (!$item) {
            Notification::make()
                ->title('Gagal')
                ->body('Item tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        if ($item->status !== 'pending') {
            Notification::make()
                ->title('Gagal')
                ->body('Item sudah diproses.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();
        try {
            $item->status = 'rejected';
            $item->rejected_at = now();
            $item->rejected_by = auth()->id();
            $item->save();
            
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item ditolak.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
