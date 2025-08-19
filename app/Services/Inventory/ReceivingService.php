<?php
namespace App\Services\Inventory;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Enums\MovementTypeEnum;
use App\Enums\PurchaseOrderStatusEnum;
use Illuminate\Support\Facades\DB;

class ReceivingService
{
    /**
     * Post a GRN: create/update batches and stock movements.
     * Also update PO status to PARTIALLY_RECEIVED or RECEIVED.
     */
    public function postGoodsReceipt(GoodsReceipt $grn): void
    {
        DB::transaction(function () use ($grn) {
            foreach ($grn->items as $line) {
                // Find or create batch by item + batch_no + location
                $batch = null;
                if ($line->batch_no) {
                    $batch = StockBatch::where('inventory_item_id', $line->inventory_item_id)
                        ->where('batch_no', $line->batch_no)
                        ->when($grn->location_id, fn($q) => $q->where('location_id', $grn->location_id))
                        ->first();
                }
                if (!$batch) {
                    $batch = StockBatch::create([
                        'inventory_item_id' => $line->inventory_item_id,
                        'batch_no' => $line->batch_no ?: 'GRN-' . $grn->grn_no . '-' . $line->inventory_item_id,
                        'expiry_date' => $line->expiry_date,
                        'qty_on_hand' => 0,
                        'location_id' => $grn->location_id,
                    ]);
                } else {
                    // Optional: keep earliest expiry
                    if ($line->expiry_date && (!$batch->expiry_date || $line->expiry_date < $batch->expiry_date)) {
                        $batch->expiry_date = $line->expiry_date;
                        $batch->save();
                    }
                }

                // Movement record
                StockMovement::create([
                    'inventory_item_id' => $line->inventory_item_id,
                    'batch_id' => $batch->id,
                    'type' => MovementTypeEnum::RESTOCK,
                    'qty' => $line->received_qty,
                    'reason' => 'GRN #' . $grn->grn_no,
                    'from_location_id' => null,
                    'to_location_id' => $grn->location_id,
                    'reference_type' => 'goods_receipts',
                    'reference_id' => $grn->id,
                    'moved_at' => now(),
                ]);

                // Increment batch qty
                $batch->increment('qty_on_hand', $line->received_qty);

                // Link back
                $line->stock_batch_id = $batch->id;
                $line->save();
            }

            // Update PO status based on received sums
            $po = $grn->purchaseOrder()->with('items')->first();
            $all = true;
            foreach ($po->items as $poi) {
                $received = GoodsReceiptItem::whereHas('grn', fn($q) => $q->where('purchase_order_id', $po->id))
                    ->where('purchase_order_item_id', $poi->id)
                    ->sum('received_qty');
                if ($received + 1e-6 < $poi->qty) { // tolerance
                    $all = false;
                }
            }
            $po->status = $all ? PurchaseOrderStatusEnum::RECEIVED : PurchaseOrderStatusEnum::PARTIALLY_RECEIVED;
            $po->save();
        });
    }
}
