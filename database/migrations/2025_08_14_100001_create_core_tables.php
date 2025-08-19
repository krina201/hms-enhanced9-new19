<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->timestamps();
        });

        Schema::create('locations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('unit')->nullable();
            $t->unsignedInteger('reorder_level')->default(0);
            $t->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_no')->unique();
            $t->unsignedBigInteger('supplier_id')->nullable();
            $t->date('order_date')->nullable();
            $t->date('expected_date')->nullable();
            $t->decimal('grand_total', 12, 2)->default(0);
            $t->string('status')->default('DRAFT');
            $t->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('purchase_order_id');
            $t->unsignedBigInteger('inventory_item_id');
            $t->decimal('qty', 12, 3);
            $t->decimal('unit_price', 12, 2)->default(0);
            $t->decimal('tax_rate', 6, 2)->default(0);
            $t->decimal('discount', 12, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $t) {
            $t->id();
            $t->string('grn_no')->unique();
            $t->unsignedBigInteger('purchase_order_id')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->date('grn_date')->nullable();
            $t->string('received_by')->nullable();
            $t->timestamp('posted_at')->nullable();
            $t->timestamps();
        });

        Schema::create('goods_receipt_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('goods_receipt_id');
            $t->unsignedBigInteger('inventory_item_id');
            $t->string('batch_no')->nullable();
            $t->date('expiry_date')->nullable();
            $t->decimal('received_qty', 12, 3)->default(0);
            $t->decimal('unit_price', 12, 2)->nullable();
            $t->timestamps();
        });

        Schema::create('stock_batches', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('inventory_item_id');
            $t->unsignedBigInteger('location_id');
            $t->string('batch_no');
            $t->date('expiry_date')->nullable();
            $t->decimal('qty_on_hand', 12, 3)->default(0);
            $t->timestamps();
            $t->index(['inventory_item_id', 'location_id', 'batch_no']);
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('inventory_item_id');
            $t->unsignedBigInteger('stock_batch_id')->nullable();
            $t->unsignedBigInteger('location_id');
            $t->string('type');
            $t->decimal('qty', 12, 3);
            $t->string('ref_type')->nullable();
            $t->unsignedBigInteger('ref_id')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
        });

        Schema::create('goods_returns', function (Blueprint $t) {
            $t->id();
            $t->string('return_no')->unique();
            $t->date('return_date')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->string('reason')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });

        Schema::create('goods_return_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('goods_return_id');
            $t->unsignedBigInteger('stock_batch_id');
            $t->unsignedBigInteger('inventory_item_id');
            $t->decimal('qty', 12, 3);
            $t->timestamps();
        });

        Schema::create('approval_workflows', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('applies_to');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('approval_workflow_id');
            $t->unsignedInteger('level');
            $t->string('role_name');
            $t->unsignedInteger('sla_hours')->default(24);
            $t->timestamps();
        });

        Schema::create('requisitions', function (Blueprint $t) {
            $t->id();
            $t->string('req_no')->nullable();
            $t->date('req_date')->nullable();
            $t->unsignedBigInteger('supplier_id')->nullable();
            $t->string('status')->default('DRAFT');
            $t->unsignedBigInteger('workflow_id')->nullable();
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->timestamps();
        });

        Schema::create('requisition_approvals', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('requisition_id');
            $t->unsignedBigInteger('approval_step_id');
            $t->string('status')->default('PENDING');
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->text('note')->nullable();
            $t->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('action');
            $t->string('subject_type')->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->json('changes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (
            [
                'activity_logs',
                'requisition_approvals',
                'requisitions',
                'approval_steps',
                'approval_workflows',
                'goods_return_items',
                'goods_returns',
                'stock_movements',
                'stock_batches',
                'goods_receipt_items',
                'goods_receipts',
                'purchase_order_items',
                'purchase_orders',
                'inventory_items',
                'locations',
                'suppliers'
            ] as $t
        ) {
            Schema::dropIfExists($t);
        }
    }
};
