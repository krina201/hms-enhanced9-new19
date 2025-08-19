<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('goods_returns', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->string('return_no')->unique();
            $table->date('return_date');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('posted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_returns');
    }
};
