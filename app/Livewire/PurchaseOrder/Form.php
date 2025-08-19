<?php
namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Enums\PurchaseOrderStatusEnum;
use App\Services\ActivityLogger;

class Form extends Component
{
    public ?int $id = null;
    public array $data = [
        'supplier_id' => null,
        'order_no' => '',
        'order_date' => '',
        'expected_date' => '',
        'status' => '',
        'subtotal' => 0,
        'tax_total' => 0,
        'discount_total' => 0,
        'grand_total' => 0,
        'notes' => '',
        'location_id' => null,
    ];

    public array $items = []; // dynamic PO items
    public array $statuses = [];
    protected bool $strictReceiveEnforcement = true;

    protected function rules() {
        return [
            'data.supplier_id' => 'required|integer',
            'data.order_no' => 'required|string|max:40',
            'data.order_date' => 'required|date',
            'data.expected_date' => 'nullable|date',
            'data.status' => 'required|string',
            'data.notes' => 'nullable|string|max:500',
            'data.location_id' => 'nullable|integer',
            'items' => 'array|min:1',
            'items.*.inventory_item_id' => 'required|integer',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ];
    }

    public function mount($id = null)
    {
        abort_unless(auth()->user()?->can('purchase_orders.edit') ?? false, 403);

        $this->id = $id;
        $this->statuses = array_map(fn($c) => $c->value, PurchaseOrderStatusEnum::cases());
        if ($id) {
            $m = PurchaseOrder::with('items')->findOrFail($id);
            $this->data = array_merge($this->data, $m->toArray());
            $this->items = $m->items->map(fn($it)=>[
                'id'=>$it->id,
                'inventory_item_id'=>$it->inventory_item_id,
                'qty'=>$it->qty,
                'unit_price'=>$it->unit_price,
                'tax_rate'=>$it->tax_rate,
                'discount'=>$it->discount,
            ])->toArray();
            $this->recalculateTotals();
        } else {
            $this->data['status'] = PurchaseOrderStatusEnum::DRAFT->value;
            $this->items = [['inventory_item_id'=>null,'qty'=>1,'unit_price'=>0,'tax_rate'=>0,'discount'=>0]];
        }
    }

    public function addItem()
    {
        $this->items[] = ['inventory_item_id'=>null,'qty'=>1,'unit_price'=>0,'tax_rate'=>0,'discount'=>0];
        $this->recalculateTotals();
    }
    public function removeItem($i)
    {
        array_splice($this->items, $i, 1);
        $this->recalculateTotals();
    }

    private function recalculateTotals(): void
    {
        $subtotal=0; $tax=0; $discount=0;
        foreach ($this->items as $line) {
            $lt = (float)$line['qty'] * (float)$line['unit_price'];
            $ltax = $lt * ((float)($line['tax_rate'] ?? 0) / 100);
            $ldisc = (float)($line['discount'] ?? 0);
            $subtotal += $lt;
            $tax += $ltax;
            $discount += $ldisc;
        }
        $this->data['subtotal'] = round($subtotal,2);
        $this->data['tax_total'] = round($tax,2);
        $this->data['discount_total'] = round($discount,2);
        $this->data['grand_total'] = round($subtotal + $tax - $discount,2);
    }

    private function ensureFullyReceivedIfReceived(PurchaseOrder $po): void
    {
        if ($po->status->value !== PurchaseOrderStatusEnum::RECEIVED->value) return;

        // compute if fully received by GRNs
        $all = true;
        foreach ($po->items as $poi) {
            $received = \App\Models\GoodsReceiptItem::whereHas('grn', fn($q)=>$q->where('purchase_order_id',$po->id))
                ->where('purchase_order_item_id', $poi->id)
                ->sum('received_qty');
            if ($received + 1e-6 < $poi->qty) { $all = false; break; }
        }
        if (!$all) {
            if ($this->strictReceiveEnforcement) {
                // force downgrade to PARTIALLY_RECEIVED
                $po->status = PurchaseOrderStatusEnum::PARTIALLY_RECEIVED;
                $po->save();
                session()->flash('warning', 'Status set to PARTIALLY_RECEIVED because not fully received via GRNs.');
            }
        }
    }

    public function updatedItems()
    {
        $this->recalculateTotals();
    }

    public function save(ActivityLogger $log)
    {
        $validated = $this->validate();
        $data = $validated['data'];
        $items = $validated['items'];

        $original = $this->id ? PurchaseOrder::with('items')->find($this->id) : null;
        $m = PurchaseOrder::updateOrCreate(['id' => $this->id], $data);
        $this->id = $m->id;

        // Upsert items: simple approach - delete missing, update/create present
        $existing = $m->items()->pluck('id')->toArray();
        $keep = [];
        foreach ($items as $line) {
            if (!empty($line['id'])) {
                $m->items()->where('id',$line['id'])->update([
                    'inventory_item_id'=>$line['inventory_item_id'],
                    'qty'=>$line['qty'],
                    'unit_price'=>$line['unit_price'],
                    'tax_rate'=>$line['tax_rate'] ?? 0,
                    'discount'=>$line['discount'] ?? 0,
                ]);
                $keep[] = $line['id'];
            } else {
                $created = $m->items()->create([
                    'inventory_item_id'=>$line['inventory_item_id'],
                    'qty'=>$line['qty'],
                    'unit_price'=>$line['unit_price'],
                    'tax_rate'=>$line['tax_rate'] ?? 0,
                    'discount'=>$line['discount'] ?? 0,
                ]);
                $keep[] = $created->id;
            }
        }
        $toDelete = array_diff($existing, $keep);
        if (!empty($toDelete)) { $m->items()->whereIn('id',$toDelete)->delete(); }

        // Recalc totals and persist
        $m->load('items');
        $this->recalculateTotals();
        $m->update([
            'subtotal'=>$this->data['subtotal'],
            'tax_total'=>$this->data['tax_total'],
            'discount_total'=>$this->data['discount_total'],
            'grand_total'=>$this->data['grand_total'],
        ]);

        $this->ensureFullyReceivedIfReceived($m);

        $log->log('purchase_orders','save',$m->id, []);

        session()->flash('success', 'Purchase Order saved.');
        return redirect()->route('purchaseorder.edit', $m->id);
    }

    public function render() { return view('livewire.purchaseorder.form'); }
}
