<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('goods_return_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('goods_return_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('stock_batch_id')->nullable();

            $table->decimal('qty', 12, 3);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_return_items');
    }
};
