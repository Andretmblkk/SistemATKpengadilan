<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus tabel yang tidak digunakan
        Schema::dropIfExists('detail_permintaan_atk');
        Schema::dropIfExists('permintaan_atk');
        Schema::dropIfExists('barang');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu membuat ulang tabel yang tidak digunakan
    }
};
