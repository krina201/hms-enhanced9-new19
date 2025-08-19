<?php
namespace App\Livewire\Grn;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GoodsReceipt;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?int $po_id = null;
    public ?int $supplier_id = null;
    public ?string $supplier_name = null;
    public ?int $location_id = null;

    protected $queryString = ['search','date_from','date_to','po_id','supplier_id','supplier_name','location_id'];

    public function query()
    {
        return GoodsReceipt::query()
            ->select('goods_receipts.*')
            ->join('purchase_orders','purchase_orders.id','=','goods_receipts.purchase_order_id')
            ->leftJoin('suppliers','suppliers.id','=','purchase_orders.supplier_id')
            ->when($this->search, fn($q) => $q->where('goods_receipts.grn_no','like','%'.$this->search.'%'))
            ->when($this->date_from, fn($q)=>$q->whereDate('goods_receipts.grn_date','>=',$this->date_from))
            ->when($this->date_to, fn($q)=>$q->whereDate('goods_receipts.grn_date','<=',$this->date_to))
            ->when($this->po_id, fn($q)=>$q->where('goods_receipts.purchase_order_id',$this->po_id))
            ->when($this->supplier_id, fn($q)=>$q->where('purchase_orders.supplier_id',$this->supplier_id))
            ->when($this->supplier_name, fn($q)=>$q->where('suppliers.name','like','%'.$this->supplier_name.'%'))
            ->when($this->location_id, fn($q)=>$q->where('goods_receipts.location_id',$this->location_id))
            ->orderByDesc('goods_receipts.id');
    }

    public function exportCsv()
    {
        abort_unless(auth()->user()?->can('grn.export') ?? false, 403);
        $filename = 'grn_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $query = $this->query()->with('purchaseOrder.supplier');
        return response()->streamDownload(function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['GRN No','Date','PO','Supplier','Location','Posted']);
            $query->chunkById(500, function($chunk) use ($out) {
                foreach ($chunk as $r) {
                    fputcsv($out, [
                        $r->grn_no,
                        optional($r->grn_date)->format('Y-m-d'),
                        $r->purchase_order_id,
                        optional($r->purchaseOrder?->supplier)->name ?? $r->purchaseOrder?->supplier_id,
                        $r->location_id,
                        optional($r->posted_at)?->format('Y-m-d H:i:s'),
                    ]);
                }
            }, 'goods_receipts.id');
            fclose($out);
        }, $filename, $headers);
    }

    public function render()
    {
        return view('livewire.grn.index', [
            'rows' => $this->query()->paginate(15)
        ]);
    }
}
