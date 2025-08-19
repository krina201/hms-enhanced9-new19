<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('applies_to'); // e.g. 'requisition'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_workflow_id'); // removed FK
            $table->unsignedInteger('level');
            $table->string('role_name');
            $table->unsignedInteger('sla_hours')->nullable();
            $table->timestamps();
        });

        Schema::table('requisitions', function (Blueprint $table) {
            $table->unsignedBigInteger('workflow_id')->nullable(); // removed FK
        });

        Schema::create('requisition_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('approval_step_id');
            $table->string('status');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('workflow_id');
        });
        Schema::dropIfExists('requisition_approvals');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_workflows');
    }
};
