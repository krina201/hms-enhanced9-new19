<?php
namespace App\Services\Inventory;

use App\Models\GoodsReturn;
use App\Models\GoodsReturnItem;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Enums\MovementTypeEnum;
use Illuminate\Support\Facades\DB;

class ReturnsService
{
    public function postGoodsReturn(GoodsReturn $gr): void
    {
        DB::transaction(function () use ($gr) {
            foreach ($gr->items as $item) {
                // Decrement batch qty if present
                if ($item->stock_batch_id) {
                    $batch = StockBatch::findOrFail($item->stock_batch_id);
                    $batch->decrement('qty_on_hand', $item->qty);
                }
                // Movement with negative qty to indicate return/adjustment out
                StockMovement::create([
                    'inventory_item_id' => $item->inventory_item_id,
                    'batch_id' => $item->stock_batch_id,
                    'type' => MovementTypeEnum::ADJUST,
                    'qty' => -abs($item->qty),
                    'reason' => 'Goods Return #' . $gr->return_no,
                    'from_location_id' => $gr->location_id,
                    'to_location_id' => null,
                    'reference_type' => 'goods_returns',
                    'reference_id' => $gr->id,
                    'moved_at' => now(),
                ]);
            }
        });
    }
}
