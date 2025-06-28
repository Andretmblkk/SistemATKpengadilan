<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Informasi Permintaan -->
        <x-filament::section>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                @if($this->record->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($this->record->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @elseif($this->record->status === 'rejected')
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
                        @if($this->record->delivery_status)
                        <div>
                            <span class="font-medium text-gray-700">Status Pengambilan:</span>
                            <span class="ml-2">
                                @if($this->record->delivery_status === 'not_delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Belum Diambil
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Sudah Diambil
                                    </span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Barang</h3>
                    <div class="space-y-2">
                        <div>
                            <span class="font-medium text-gray-700">Total Item:</span>
                            <span class="ml-2">{{ $this->record->requestItems->count() }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Total Jumlah:</span>
                            <span class="ml-2">{{ $this->record->requestItems->sum('quantity') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span class="ml-2">
                                @php
                                    $pendingCount = $this->record->requestItems->where('status', 'pending')->count();
                                    $approvedCount = $this->record->requestItems->where('status', 'approved')->count();
                                    $rejectedCount = $this->record->requestItems->where('status', 'rejected')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $pendingCount }} Menunggu
                                    </span>
                                @endif
                                @if($approvedCount > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $approvedCount }} Disetujui
                                    </span>
                                @endif
                                @if($rejectedCount > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $rejectedCount }} Ditolak
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Daftar Barang -->
        <x-filament::section>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Daftar Barang</h3>
                @if(auth()->user()->hasRole('pimpinan') && $this->record->requestItems->where('status', 'pending')->count() > 0)
                    <x-filament::button
                        wire:click="$dispatch('openModal', { requestId: {{ $this->record->id }} })"
                        color="primary"
                        icon="heroicon-o-eye"
                    >
                        Lihat Detail & Approve
                    </x-filament::button>
                @endif
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Barang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->record->requestItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->item->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $item->item->description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @elseif($item->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    <!-- Livewire Component untuk Modal -->
    @livewire('request-detail-modal')
</x-filament-panels::page> 