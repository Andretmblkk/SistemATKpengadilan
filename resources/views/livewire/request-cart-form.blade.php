<div>
    <h2 class="text-lg font-bold mb-2">Formulir Permintaan Barang</h2>
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 p-2 rounded mb-2">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="addToCart" class="flex gap-2 mb-4">
        <select wire:model="item_id" class="border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500">
            <option value="">Pilih Barang</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ $item->stock }})</option>
            @endforeach
        </select>
        <input type="number" wire:model="quantity" min="1" class="border border-gray-300 dark:border-gray-600 rounded p-2 w-24 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" placeholder="Jumlah">
        <button type="submit" class="px-4 py-2 rounded font-bold border-2 border-black shadow-sm bg-white text-black hover:bg-gray-200 transition dark:bg-black dark:text-white dark:border-white dark:hover:bg-gray-900">Tambah Barang</button>
    </form>
    @error('item_id') <div class="text-red-600 mb-2">{{ $message }}</div> @enderror
    @error('quantity') <div class="text-red-600 mb-2">{{ $message }}</div> @enderror
    @error('cart') <div class="text-red-600 mb-2">{{ $message }}</div> @enderror

    <div class="mb-4">
        <h3 class="font-semibold mb-2">Daftar Barang yang Diajukan</h3>
        @if(count($cart) > 0)
            <div class="grid gap-2">
                @foreach($cart as $index => $c)
                    <div class="border border-gray-300 dark:border-gray-600 rounded p-2 flex items-center justify-between bg-white dark:bg-gray-800 shadow">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $c['item_name'] }}</span> |
                            Jumlah: <span class="text-blue-700 dark:text-blue-300">{{ $c['quantity'] }}</span> |
                            Stok Tersedia: <span class="text-gray-600 dark:text-gray-300">{{ $c['stock'] }}</span>
                        </div>
                        <button wire:click="removeFromCart({{ $index }})" class="px-2 py-1 rounded font-semibold transition-colors bg-red-600 dark:bg-red-500 text-white hover:bg-red-700 dark:hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-400 border-2 border-red-700 dark:border-red-400 shadow-sm">Hapus</button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 dark:text-gray-300">Belum ada barang yang diajukan.</div>
        @endif
    </div>
    <button wire:click="submitCart" class="px-4 py-2 rounded font-bold border-2 border-black shadow-sm bg-white text-black hover:bg-gray-200 transition dark:bg-black dark:text-white dark:border-white dark:hover:bg-gray-900" @if(count($cart) == 0) disabled @endif>Kirim Permintaan</button>
</div> 