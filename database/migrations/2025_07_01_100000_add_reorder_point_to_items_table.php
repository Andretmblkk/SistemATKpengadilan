<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'reorder_point')) {
                $table->unsignedInteger('reorder_point')->default(0)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'reorder_point')) {
                $table->dropColumn('reorder_point');
            }
        });
    }
}; 