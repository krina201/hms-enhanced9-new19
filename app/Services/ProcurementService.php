<?php
namespace App\Services;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Enums\RequisitionStatusEnum;
use App\Enums\PurchaseOrderStatusEnum;
use Illuminate\Support\Facades\DB;

class ProcurementService
{
    /**
     * Convert an APPROVED requisition to a Purchase Order.
     * If requisition has supplier_id set, create a single PO; otherwise group by item's default_supplier_id.
     */
    public function convertRequisitionToPO(Requisition $req): array
    {
        if ($req->status !== RequisitionStatusEnum::APPROVED) {
            throw new \RuntimeException('Requisition must be APPROVED to convert.');
        }

        $pos = [];
        DB::transaction(function () use ($req, &$pos) {
            $req->load('items.item');
            $groups = [];

            if ($req->supplier_id) {
                $groups[$req->supplier_id] = $req->items;
            } else {
                foreach ($req->items as $it) {
                    $sid = $it->item->default_supplier_id ?? null;
                    if (!$sid) { throw new \RuntimeException("Item {$it->inventory_item_id} missing default supplier."); }
                    $groups[$sid][] = $it;
                }
            }

            foreach ($groups as $supplierId => $items) {
                $po = PurchaseOrder::create([
                    'supplier_id' => $supplierId,
                    'order_no' => 'PO-' . $req->req_no . '-' . now()->format('YmdHis'),
                    'order_date' => now()->toDateString(),
                    'expected_date' => null,
                    'status' => PurchaseOrderStatusEnum::ORDERED,
                    'subtotal' => 0, 'tax_total'=>0, 'discount_total'=>0, 'grand_total'=>0,
                ]);
                foreach ($items as $it) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'inventory_item_id' => $it->inventory_item_id,
                        'qty' => $it->qty,
                        'unit_price' => 0, // set later
                        'tax_rate' => 0,
                        'discount' => 0,
                        'total' => 0,
                    ]);
                }
                $pos[] = $po;
            }

            $req->status = RequisitionStatusEnum::CONVERTED;
            $req->save();
        });

        return $pos;
    }
}
