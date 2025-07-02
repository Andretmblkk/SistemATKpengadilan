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
                            @elseif($request->status === 'sebagian_disetujui')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sebagian Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Tidak Diketahui
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
                    <div class="border rounded-lg p-4 {{ $item->status !== 'menunggu' ? 'bg-gray-50' : '' }}">
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
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak Diketahui
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @if($item->status === 'pending' && auth()->user()->hasRole('admin'))
                        <div class="flex space-x-2 mt-3">
                            <button
                                wire:click="approveSingle({{ $item->id }})"
                                class="px-3 py-1 rounded border border-green-600 text-green-700 dark:text-green-400 font-bold text-xs bg-transparent hover:bg-green-50 dark:hover:bg-green-900"
                            >
                                Setujui
                            </button>
                            <button
                                wire:click="rejectSingle({{ $item->id }})"
                                class="px-3 py-1 rounded border border-red-600 text-red-700 dark:text-red-400 font-bold text-xs bg-transparent hover:bg-red-50 dark:hover:bg-red-900"
                            >
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
                        $menungguCount = $request->requestItems ? $request->requestItems->where('status', 'pending')->count() : 0;
                        $disetujuiCount = $request->requestItems ? $request->requestItems->where('status', 'approved')->count() : 0;
                        $ditolakCount = $request->requestItems ? $request->requestItems->where('status', 'rejected')->count() : 0;
                    @endphp
                    @if($menungguCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $menungguCount }} Menunggu
                        </span>
                    @endif
                    @if($disetujuiCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $disetujuiCount }} Disetujui
                        </span>
                    @endif
                    @if($ditolakCount > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $ditolakCount }} Ditolak
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div> 