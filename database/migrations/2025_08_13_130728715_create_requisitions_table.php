<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();

            $table->string('req_no')->unique();
            $table->date('req_date');

            $table->unsignedBigInteger('requested_by');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
