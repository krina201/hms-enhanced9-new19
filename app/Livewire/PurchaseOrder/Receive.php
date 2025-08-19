<?php
namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\StockBatch;
use App\Services\Inventory\ReceivingService;
use App\Services\ActivityLogger;
use Illuminate\Support\Collection;

class Receive extends Component
{
    public int $purchaseOrderId;
    public string $grn_no = '';
    public string $grn_date = '';
    public ?int $location_id = null;
    public string $notes = '';

    /** @var array<int,array> */
    public array $lines = []; // each has splits[], each split has received_qty,batch_no,expiry_date,unit_price

    public function mount($id)
    {
        abort_unless(auth()->user()?->can('purchase_orders.receive') ?? false, 403);

        $this->purchaseOrderId = (int) $id;
        $po = PurchaseOrder::with('items')->findOrFail($id);
        $this->grn_no = 'GRN-' . $po->order_no . '-' . now()->format('YmdHis');
        $this->grn_date = now()->toDateString();

        foreach ($po->items as $item) {
            $received = GoodsReceiptItem::whereHas('grn', fn($q) => $q->where('purchase_order_id', $po->id))
                ->where('purchase_order_item_id', $item->id)
                ->sum('received_qty');
            $ordered = (float) $item->qty;
            $open = max(0, $ordered - (float) $received);
            if ($open <= 0) continue;

            $this->lines[] = [
                'purchase_order_item_id' => $item->id,
                'inventory_item_id' => $item->inventory_item_id,
                'ordered_qty' => $ordered,
                'already_received' => (float) $received,
                'open_qty' => $open,
                'splits' => [
                    ['received_qty' => 0, 'batch_no' => '', 'expiry_date' => '', 'unit_price' => $item->unit_price],
                ],
            ];
        }
    }

    public function addSplit($i) { $this->lines[$i]['splits'][] = ['received_qty'=>0,'batch_no'=>'','expiry_date'=>'','unit_price'=>null]; }
    public function removeSplit($i, $j) { array_splice($this->lines[$i]['splits'], $j, 1); }

    public function suggestBatches($i)
    {
        $itemId = $this->lines[$i]['inventory_item_id'];
        $q = StockBatch::where('inventory_item_id', $itemId)
            ->when($this->location_id, fn($q)=>$q->where('location_id',$this->location_id))
            ->where('qty_on_hand','>',0)
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date asc');
        return $q->limit(5)->get(['batch_no','expiry_date','qty_on_hand'])->toArray();
    }

    public function save(ReceivingService $receiver, ActivityLogger $log)
    {
        $this->validate([
            'grn_no' => 'required|string|max:80',
            'grn_date' => 'required|date',
            'location_id' => 'nullable|integer',
            'lines' => 'array|min:1',
            'lines.*.splits' => 'array|min:1',
            'lines.*.splits.*.received_qty' => 'numeric|min:0.001',
            'lines.*.splits.*.batch_no' => 'nullable|string|max:120',
            'lines.*.splits.*.expiry_date' => 'nullable|date',
        ]);

        $useSplits = [];
        foreach ($this->lines as $line) {
            $sum = 0;
            foreach ($line['splits'] as $sp) {
                if (($sp['received_qty'] ?? 0) > 0) {
                    $useSplits[] = [
                        'purchase_order_item_id' => $line['purchase_order_item_id'],
                        'inventory_item_id' => $line['inventory_item_id'],
                        'received_qty' => $sp['received_qty'],
                        'batch_no' => $sp['batch_no'] ?: null,
                        'expiry_date' => $sp['expiry_date'] ?: null,
                        'unit_price' => $sp['unit_price'] ?? null,
                    ];
                    $sum += $sp['received_qty'];
                }
            }
            if ($sum - $line['open_qty'] > 1e-6) {
                $this->addError('lines', "Item #{$line['inventory_item_id']} receiving exceeds open qty.");
                return;
            }
        }

        if (empty($useSplits)) {
            $this->addError('lines', 'At least one split must have received_qty > 0.');
            return;
        }

        $po = PurchaseOrder::findOrFail($this->purchaseOrderId);

        $grn = GoodsReceipt::create([
            'purchase_order_id' => $po->id,
            'grn_no' => $this->grn_no,
            'grn_date' => $this->grn_date,
            'received_by' => auth()->id(),
            'location_id' => $this->location_id,
            'notes' => $this->notes,
            'posted_at' => now(),
        ]);

        foreach ($useSplits as $sp) {
            GoodsReceiptItem::create(array_merge($sp, ['goods_receipt_id' => $grn->id]));
        }

        $grn->load('items', 'purchaseOrder.items');
        $receiver->postGoodsReceipt($grn);

        $log->log('purchase_orders', 'receive', $po->id, ['grn_id' => $grn->id, 'grn_no' => $grn->grn_no]);

        session()->flash('success', 'GRN posted successfully.');
        return redirect()->route('grn.show', $grn->id);
    }

    public function render()
    {
        return view('livewire.purchaseorder.receive');
    }
}
