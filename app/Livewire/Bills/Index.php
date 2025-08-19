<?php
/**
 * Livewire component for paginated index listing with search/filters.
 *
 * @package HMS
 */
namespace App\Livewire\Bills;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bill;

class Index extends Component
{
    use WithPagination;
    public string $q=''; public string $trash='active'; public string $status='';

    public function render()
    {
        $rows = Bill::query()
            ->with('patient')
            ->when($this->q, fn($q)=>$q->where('bill_no','like','%'.$this->q.'%')->orWhereHas('patient', fn($p)=>$p->where('mrn','like','%'.$this->q.'%')))
            ->when($this->status, fn($q)=>$q->where('status',$this->status))
            ->when($this->trash==='trashed', fn($q)=>$q->onlyTrashed())
            ->when($this->trash==='all', fn($q)=>$q->withTrashed())
            ->orderBy($this->sort ?? 'id', $this->dir ?? 'desc');
        return view('livewire.bills.index',['rows'=>$rows]);
    }
}