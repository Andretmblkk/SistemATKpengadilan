<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('delivery_status', ['not_delivered', 'delivered'])->default('not_delivered')->after('status');
            $table->index('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['delivery_status']);
            $table->dropColumn('delivery_status');
        });
    }
};