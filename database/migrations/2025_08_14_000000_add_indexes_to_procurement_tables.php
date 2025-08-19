<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->index(['grn_date']);
            $table->index(['purchase_order_id']);
            $table->index(['location_id']);
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index(['supplier_id']);
            $table->index(['status']);
        });
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->index(['inventory_item_id']);
            $table->index(['batch_no']);
            $table->index(['expiry_date']);
        });
        if (Schema::hasTable('goods_returns')) {
            Schema::table('goods_returns', function (Blueprint $table) {
                if (!Schema::hasColumn('goods_returns', 'return_no')) return;
                $table->unique('return_no');
            });
        }
        if (Schema::hasTable('goods_receipts')) {
            Schema::table('goods_receipts', function (Blueprint $table) {
                if (Schema::hasColumn('goods_receipts', 'grn_no')) {
                    $table->unique('grn_no');
                }
            });
        }
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_orders', 'order_no')) {
                    $table->unique('order_no');
                }
            });
        }
    }
    public function down(): void
    {
        // safe drops omitted for brevity
    }
};
