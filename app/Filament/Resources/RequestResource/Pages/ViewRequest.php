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

    public function approveSelected()
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

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if ($item->status === 'pending') {
                    $item->status = 'approved';
                    $item->approved_at = now();
                    $item->approved_by = auth()->id();
                    $item->save();
                }
            }
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item terpilih disetujui.')
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

    public function rejectSelected()
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

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if ($item->status === 'pending') {
                    $item->status = 'rejected';
                    $item->rejected_at = now();
                    $item->rejected_by = auth()->id();
                    $item->save();
                }
            }
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item terpilih ditolak.')
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
            
            DB::commit();
            Notification::make()
                ->title('Berhasil')
                ->body('Item disetujui.')
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
                ->modalDescription('Lihat detail barang dan lakukan approve/reject')
                ->modalContent(view('filament.resources.request-resource.pages.modal-content', [
                    'request' => $this->record,
                    'selectedItems' => $this->selectedItems,
                    'approveAll' => $this->approveAll
                ]))
                ->modalActions([
                    Action::make('approveSelected')
                        ->label(fn () => 'Setujui yang Dipilih (' . count($this->selectedItems) . ')')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action('approveSelected')
                        ->visible(fn () => count($this->selectedItems) > 0),
                    Action::make('rejectSelected')
                        ->label(fn () => 'Tolak yang Dipilih (' . count($this->selectedItems) . ')')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action('rejectSelected')
                        ->visible(fn () => count($this->selectedItems) > 0),
                    Action::make('close')
                        ->label('Tutup')
                        ->color('gray')
                        ->close()
                ])
                ->visible(fn () => auth()->user() && auth()->user()->hasRole('pimpinan')),
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