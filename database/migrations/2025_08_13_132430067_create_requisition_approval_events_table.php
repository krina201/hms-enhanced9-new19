<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisition_approval_events', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('requisition_approval_id'); // removed FK
            $table->string('action'); // pending_created|approved|rejected|escalated|reminded
            $table->unsignedBigInteger('actor_id')->nullable(); // removed FK

            $table->text('note')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisition_approval_events');
    }
};
