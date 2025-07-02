<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Informasi Permintaan -->
        <x-filament::section>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Permintaan</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-700">Staff:</span>
                            <span class="ml-2">{{ $this->record->user->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="ml-2">
                                @php
                                    $menungguCount = $this->record->requestItems->where('status', 'menunggu')->count();
                                    $disetujuiCount = $this->record->requestItems->where('status', 'disetujui')->count();
                                    $ditolakCount = $this->record->requestItems->where('status', 'ditolak')->count();
                                    $total = $this->record->requestItems->count();
                                @endphp
                                @if($disetujuiCount > 0 && $menungguCount === 0 && $ditolakCount === 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                    <div class="mt-2 text-green-700 text-sm font-semibold">Telah disetujui, barang siap diambil.</div>
                                @elseif($disetujuiCount > 0 && ($menungguCount > 0 || $ditolakCount > 0))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Sebagian Disetujui
                                    </span>
                                    <div class="mt-2 text-blue-700 text-sm font-semibold">Sebagian barang telah disetujui, silakan cek detail.</div>
                                @elseif($menungguCount === $total)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($ditolakCount === $total)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Tanggal Permintaan:</span>
                            <span class="ml-2">{{ $this->record->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Total Item:</span>
                            <span class="ml-2">{{ $this->record->requestItems->count() }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Total Jumlah:</span>
                            <span class="ml-2">{{ $this->record->requestItems->sum('quantity') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Daftar Barang -->
        <x-filament::section>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Daftar Barang</h3>
                @if(auth()->user()->hasRole('admin') && $this->record->requestItems->where('status', 'pending')->count() > 0)
                    <x-filament::button
                        wire:click="$dispatch('openModal', { requestId: {{ $this->record->id }} })"
                        color="primary"
                        icon="heroicon-o-eye"
                        class="border-2 border-primary-700 dark:border-primary-400 shadow-sm"
                    >
                        Lihat Detail & Approve
                    </x-filament::button>
                @endif
            </div>
            <div class="text-gray-500 italic text-center py-8">
                Silakan klik "Lihat Detail & Approve" untuk memproses persetujuan setiap item permintaan barang.
            </div>
        </x-filament::section>
    </div>

    <!-- Livewire Component untuk Modal -->
    @livewire('request-detail-modal')

    @push('scripts')
    <script>
        document.addEventListener('livewire:update', function () {
            window.location.reload();
        });
    </script>
    @endpush
</x-filament-panels::page> 