<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu membuat ulang tabel requests
    }
};
