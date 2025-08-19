<?php
namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class StockAging extends Component
{
    use WithPagination;

    public ?int $location_id = null;
    public ?int $days_threshold = 30;
    public bool $only_expiring = false;

    public function query()
    {
        return StockBatch::query()
            ->with('item')
            ->when($this->location_id, fn($q)=>$q->where('location_id',$this->location_id))
            ->select('*', DB::raw("CASE WHEN expiry_date IS NULL THEN NULL ELSE DATEDIFF(expiry_date, CURDATE()) END as days_to_expiry"))
            ->when($this->only_expiring, fn($q)=>$q->whereNotNull('expiry_date')->whereDate('expiry_date','>=', now()->toDateString()))
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date asc');
    }

    public function exportCsv()
    {
        $filename = 'stock_aging_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $query = \App\Models\StockBatch::query()->with('item')->orderBy('expiry_date');
        return response()->streamDownload(function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Item','Batch','Expiry','Days To Expiry','Qty','Location']);
            $query->chunkById(500, function($rows) use ($out) {
                foreach ($rows as $r) {
                    $dte = $r->expiry_date ? now()->diffInDays($r->expiry_date, false) : null;
                    fputcsv($out, [
                        $r->item->name ?? $r->inventory_item_id,
                        $r->batch_no,
                        optional($r->expiry_date)->format('Y-m-d'),
                        $dte,
                        number_format((float)$r->qty_on_hand, 3, '.', ''),
                        $r->location_id,
                    ]);
                }
            }, 'stock_batches.id');
            fclose($out);
        }, $filename, $headers);
    }
            fclose($out);
        }, $filename, $headers);
    }

    public function render()
    {
        $soonCount = StockBatch::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($this->days_threshold ?? 30)->toDateString()])
            ->count();
        $expiredCount = StockBatch::whereNotNull('expiry_date')->where('expiry_date','<', now()->toDateString())->count();

        return view('livewire.reports.stock-aging', [
            'rows' => $this->query()->paginate(20),
            'soonCount' => $soonCount,
            'expiredCount' => $expiredCount,
        ]);
    }
}
