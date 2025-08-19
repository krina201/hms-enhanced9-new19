<?php
/**
 * Livewire component for paginated index listing with search/filters.
 *
 * @package HMS
 */
namespace App\Livewire\Visits;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visit;

class Index extends Component
{
    use WithPagination;
    public string $q=''; public string $trash='active'; public string $type=''; public string $sort='visit_date'; public string $dir='desc';

    public function updatingQ(){ $this->resetPage(); }

    public function render()
    {
        abort_unless(auth()->user()->can('visits.view'), 403);
        $rows = Visit::query()
            ->with('patient')
            ->when($this->q, fn($q)=>$q->whereHas('patient', fn($p)=>$p->where('mrn','like','%'.$this->q.'%')->orWhere('first_name','like','%'.$this->q.'%')))
            ->when($this->type, fn($q)=>$q->where('type',$this->type))
            ->when($this->trash==='trashed', fn($q)=>$q->onlyTrashed())
            ->when($this->trash==='all', fn($q)=>$q->withTrashed())
            ->orderBy($this->sort ?? 'visit_date', $this->dir ?? 'desc');
        return view('livewire.visits.index',['rows'=>$rows]);
    }
}