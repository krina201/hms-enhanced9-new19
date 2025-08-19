<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $t) {
            $t->id();
            $t->string('subject');
            $t->text('description')->nullable();
            $t->string('status')->default('OPEN');
            $t->unsignedBigInteger('created_by')->nullable(); // removed FK
            $t->unsignedBigInteger('assigned_to')->nullable(); // removed FK
            $t->unsignedInteger('sla_hours')->default(24);
            $t->timestamp('due_at')->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();
        });

        Schema::create('ticket_comments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('ticket_id'); // removed FK
            $t->unsignedBigInteger('user_id')->nullable(); // removed FK
            $t->text('body');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('tickets');
    }
};
