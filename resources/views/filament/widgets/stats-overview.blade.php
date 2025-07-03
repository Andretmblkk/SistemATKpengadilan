<h1 class="text-2xl font-bold mb-4">Sistem Informasi ATK PTA Jayapura</h1>
@if(auth()->user()->hasRole('admin'))
    <div class="mb-2">Selamat datang, Admin! Anda dapat mengelola data barang, permintaan, dan pengajuan pembelian.</div>
@elseif(auth()->user()->hasRole('pimpinan'))
    <div class="mb-2">Selamat datang, Pimpinan! Anda dapat memantau dan menyetujui pengajuan pembelian.</div>
@elseif(auth()->user()->hasRole('staff'))
    <div class="mb-2">Selamat datang, Staff! Anda dapat mengajukan permintaan barang dan melihat status permintaan Anda.</div>
@endif 