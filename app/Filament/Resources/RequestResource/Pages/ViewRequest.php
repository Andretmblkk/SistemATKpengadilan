<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Models\RequestItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Actions\Action as FormAction;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    public $selectedItems = [];
    public $approveAll = false;

    public function getViewData(): array
    {
        $this->record->load(['requestItems.item', 'user']);
        return parent::getViewData();
    }

    public function toggleSelectAll()
    {
        if ($this->approveAll) {
            $this->selectedItems = $this->record->requestItems->where('status', 'pending')->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function toggleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }
    }

    public function updateDeliveryStatus()
    {
        if (empty($this->selectedItems)) {
            Notification::make()
                ->title('Gagal')
                ->body('Tidak ada item yang dipilih.')
                ->danger()
                ->send();
            return;
        }

        $items = $this->record->requestItems()->whereIn('id', $this->selectedItems)->get();

        if ($items->isEmpty()) {
            Notification::make()
                ->title('Gagal')
                ->body('Item yang dipilih tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        // Check if all selected items are approved
        $allApproved = $items->every(function ($item) {
            return $item->status === 'approved';
        });

        if (!$allApproved) {
            Notification::make()
                ->title('Gagal')
                ->body('Hanya item yang sudah disetujui yang bisa diupdate status pengambilannya.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();
        try {
            // Update delivery status to delivered
            $this->record->delivery_status = 'delivered';
            $this->record->save();
            
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Status pengambilan berhasil diupdate menjadi "Sudah Diambil".')
                ->success()
                ->send();
            $this->selectedItems = [];
            $this->approveAll = false;
            $this->record->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function approveSingle($itemId)
    {
        $item = $this->record->requestItems()->find($itemId);
        
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

            // Kurangi stok item sesuai jumlah yang disetujui
            $itemModel = $item->item;
            if ($itemModel) {
                $itemModel->stock = max(0, $itemModel->stock - $item->quantity);
                $itemModel->save(); // Akan trigger observer otomatis
            }
            
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item disetujui dan stok otomatis dikurangi.')
                ->success()
                ->send();
            $this->record->refresh();
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
        $item = $this->record->requestItems()->find($itemId);
        
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
            $this->record->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function setReadyToPickup($itemId)
    {
        $item = $this->record->requestItems()->find($itemId);
        if (!$item) {
            Notification::make()
                ->title('Gagal')
                ->body('Item tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }
        if ($item->status !== 'approved') {
            Notification::make()
                ->title('Gagal')
                ->body('Hanya item yang sudah disetujui yang bisa diubah menjadi boleh diambil.')
                ->danger()
                ->send();
            return;
        }
        DB::beginTransaction();
        try {
            $item->status = 'ready_to_pickup';
            $item->ready_to_pickup_at = now();
            $item->ready_to_pickup_by = auth()->id();
            $item->save();
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Status item diubah menjadi boleh diambil.')
                ->success()
                ->send();
            $this->record->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => auth()->user() && auth()->user()->hasRole('staff') && $this->record->user_id === auth()->id()),
            Action::make('viewDetails')
                ->label('Lihat Detail & Approve')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->modalHeading('Detail Permintaan & Approve')
                ->modalDescription('Lihat detail barang dan lakukan approve atau update status pengambilan')
                ->modalContent(view('filament.resources.request-resource.pages.modal-content', [
                    'request' => $this->record,
                    'selectedItems' => $this->selectedItems,
                    'approveAll' => $this->approveAll
                ]))
                ->modalActions([
                    Action::make('approveSelected')
                        ->label(fn () => 'Status Diantar/Diambil (' . count($this->selectedItems) . ')')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action('updateDeliveryStatus')
                        ->visible(fn () => count($this->selectedItems) > 0 && $this->record->requestItems->whereIn('id', $this->selectedItems)->every(fn($item) => $item->status === 'approved')),
                    Action::make('close')
                        ->label('Tutup')
                        ->color('gray')
                        ->close()
                ])
                ->visible(fn () => auth()->user() && auth()->user()->hasRole('admin')),
            \Filament\Actions\DeleteAction::make()
                ->label('Hapus Permintaan')
                ->visible(fn () => auth()->user() && auth()->user()->hasRole('admin')),
        ];
    }

    protected function getRelations(): array
    {
        return [
            \App\Filament\Resources\RequestResource\RelationManagers\RequestItemsRelationManager::class
        ];
    }
}