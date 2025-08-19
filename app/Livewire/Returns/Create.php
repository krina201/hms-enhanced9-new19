<?php
namespace App\Livewire\Returns;

use Livewire\Component;
use App\Models\GoodsReturn;
use App\Models\GoodsReturnItem;
use App\Models\StockBatch;
use App\Services\Inventory\ReturnsService;
use App\Services\ActivityLogger;

class Create extends Component
{
    public array $suggestions = []

{
    public string $return_no = '';
    public string $return_date = '';
    public ?int $purchase_order_id = null;
    public ?int $grn_id = null;
    public ?int $location_id = null;
    public string $reason = '';

    public array $items = []; // each: inventory_item_id, stock_batch_id, qty, unit_price, notes

    public function mount()
    {
        abort_unless(auth()->user()?->can('returns.create') ?? false, 403);
        $this->return_no = 'RET-' . now()->format('YmdHis');
        $this->return_date = now()->toDateString();
        $this->items = [['inventory_item_id'=>null,'stock_batch_id'=>null,'qty'=>0,'unit_price'=>null,'notes'=>'']];
    }

    public function addItem() { $this->items[] = ['inventory_item_id'=>null,'stock_batch_id'=>null,'qty'=>0,'unit_price'=>null,'notes'=>'']; }
    public function removeItem($i) { array_splice($this->items, $i, 1); }

    public function suggestBatches($i)
    {
        $itemId = $this->items[$i]['inventory_item_id'] ?? null;
        if (!$itemId) return [];
        return StockBatch::where('inventory_item_id', $itemId)
            ->when($this->location_id, fn($q)=>$q->where('location_id',$this->location_id))
            ->where('qty_on_hand','>',0)
            ->orderBy('qty_on_hand','desc')->limit(5)->get(['id','batch_no','expiry_date','qty_on_hand'])->toArray();
    }

    public function save(ReturnsService $service, ActivityLogger $log)
    {
        $this->validate([
            'return_no' => 'required|string|max:80',
            'return_date' => 'required|date',
            'location_id' => 'nullable|integer',
            'items' => 'array|min:1',
            'items.*.inventory_item_id' => 'required|integer',
            'items.*.stock_batch_id' => 'nullable|integer',
            'items.*.qty' => 'required|numeric|min:0.001',
        ]);

        $gr = GoodsReturn::create([
            'purchase_order_id' => $this->purchase_order_id,
            'grn_id' => $this->grn_id,
            'return_no' => $this->return_no,
            'return_date' => $this->return_date,
            'location_id' => $this->location_id,
            'reason' => $this->reason,
            'created_by' => auth()->id(),
            'posted_at' => now(),
        ]);

        foreach ($this->items as $it) {
            GoodsReturnItem::create(array_merge($it, ['goods_return_id' => $gr->id]));
        }

        $gr->load('items');
        $service->postGoodsReturn($gr);
        $log->log('returns', 'post', $gr->id, []);

        session()->flash('success','Goods Return posted.');
        return redirect()->route('returns.index');
    }

    protected function rules()
    {
        return [
            'return_no' => 'required|string|max:80|unique:goods_returns,return_no',
            'return_date' => 'required|date',
            'location_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.stock_batch_id' => 'required|integer|min:1',
            'items.*.qty' => 'required|numeric|min:0.001',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function updated($prop)
    {
        if ($prop === 'location_id') {
            $this->loadSuggestions();
        }
    }

    public function loadSuggestions()
    {
        // Load batches for selected location with qty>0 (moved from blade)
        $this->suggestions = \\App\\Models\\StockBatch::query()
            ->where('location_id', $this->location_id)
            ->where('qty_on_hand', '>', 0)
            ->orderBy('expiry_date')->limit(20)->get(['id','inventory_item_id','batch_no','expiry_date','qty_on_hand'])->toArray();
    }

    public function render() { return view('livewire.returns.create'); }
}
