<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // if (Schema::hasColumn('items', 'supplier_id')) {
            //     $table->dropForeign(['supplier_id']);
            //     $table->dropColumn('supplier_id');
            // }
        });

        Schema::table('request_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // $table->unsignedBigInteger('supplier_id')->nullable();
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });

        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 