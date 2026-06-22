<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->decimal('price', 10, 2); $table->unsignedInteger('stock')->default(0);
            $table->string('image_path')->nullable(); $table->boolean('active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); $table->foreignId('member_id')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('pending')->index(); $table->decimal('total', 10, 2); $table->timestamp('ordered_at'); $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $table->string('product_name');
            $table->decimal('unit_price', 10, 2); $table->unsignedInteger('quantity'); $table->decimal('subtotal', 10, 2); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); Schema::dropIfExists('products'); }
};
