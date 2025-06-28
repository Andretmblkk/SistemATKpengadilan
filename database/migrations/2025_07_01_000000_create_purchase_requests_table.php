<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->unsignedInteger('current_stock');
            $table->unsignedInteger('reorder_point');
            $table->unsignedInteger('requested_quantity')->nullable();
            $table->enum('status', ['draft', 'waiting_approval', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
}; 