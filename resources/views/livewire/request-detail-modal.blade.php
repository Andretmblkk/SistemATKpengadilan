<div>
    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if(!$request)
        <div class="text-center py-4 text-red-500">
            Data permintaan tidak ditemukan
        </div>
    @else
        <div class="space-y-6">
            <!-- Informasi Permintaan -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Staff:</span>
                        <span class="ml-2">{{ $request->user ? $request->user->name : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Tanggal:</span>
                        <span class="ml-2">{{ $request->created_at ? $request->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="ml-2">
                            @if($request->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Menunggu
                                </span>
                            @elseif($request->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Disetujui
                                </span>
                            @elseif($request->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Daftar Barang -->
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @if($request->requestItems && $request->requestItems->count() > 0)
                    @foreach($request->requestItems as $item)
                    <div class="border rounded-lg p-4 {{ $item->status !== 'pending' ? 'bg-gray-50' : '' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->item ? $item->item->name : 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">{{ $item->item ? $item->item->description : 'N/A' }}</p>
                                <p class="text-sm text-gray-700 mt-1">
                                    <span class="font-medium">Jumlah:</span> {{ $item->quantity ?? 0 }}
                                </p>
                            </div>
                            <div class="text-right ml-4">
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
                            </div>
                        </div>
                        
                        @if($item->status === 'pending' && auth()->user()->hasRole('pimpinan'))
                        <div class="flex space-x-2 mt-3">
                            <button
                                wire:click="approveSingle({{ $item->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui
                            </button>
                            <button
                                wire:click="rejectSingle({{ $item->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak
                            </button>
                        </div>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-gray-500">
                        Tidak ada barang yang diajukan
                    </div>
                @endif
            </div>

            <!-- Ringkasan Status -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Ringkasan Status:</h4>
                <div class="flex space-x-4 text-sm">
                    @php
                        $pendingCount = $request->requestItems ? $request->requestItems->where('status', 'pending')->count() : 0;
                        $approvedCount = $request->requestItems ? $request->requestItems->where('status', 'approved')->count() : 0;
                        $rejectedCount = $request->requestItems ? $request->requestItems->where('status', 'rejected')->count() : 0;
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
                </div>
            </div>
        </div>
    @endif
</div> 