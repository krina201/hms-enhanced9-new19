<?php
namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\InventoryItem;
use App\Models\StockBatch;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class InventoryWidgets extends Component
{
    public int $lowStock = 0;
    public int $expiredBatches = 0;
    public int $pendingGrns = 0;

    public function mount()
    {
        // Low stock: items where sum(qty_on_hand) < reorder_level
        $this->lowStock = InventoryItem::query()
            ->leftJoin('stock_batches','inventory_items.id','=','stock_batches.inventory_item_id')
            ->select('inventory_items.id', DB::raw('COALESCE(SUM(stock_batches.qty_on_hand),0) as qty'))
            ->groupBy('inventory_items.id','inventory_items.reorder_level')
            ->havingRaw('qty < inventory_items.reorder_level')
            ->count();

        // Expired batches with qty > 0
        $this->expiredBatches = StockBatch::whereNotNull('expiry_date')
            ->where('expiry_date','<', now()->toDateString())
            ->where('qty_on_hand','>',0)->count();

        // Pending GRNs = POs with open qty (status ORDERED or PARTIALLY_RECEIVED)
        $this->pendingGrns = PurchaseOrder::whereIn('status', ['ORDERED','PARTIALLY_RECEIVED'])->count();
    }

    public function render() { return view('livewire.dashboard.inventory-widgets'); }
}
