<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->foreignId('atk_request_id')->constrained()->onDelete('cascade')->after('id');
            $table->foreignId('item_id')->constrained()->onDelete('cascade')->after('atk_request_id');
            $table->integer('quantity')->default(1)->after('item_id');
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropForeign(['atk_request_id']);
            $table->dropForeign(['item_id']);
            $table->dropColumn(['atk_request_id', 'item_id', 'quantity']);
        });
    }
};