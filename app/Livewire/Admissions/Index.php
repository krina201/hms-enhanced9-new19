<?php
/**
 * Livewire component for paginated index listing with search/filters.
 *
 * @package HMS
 */
namespace App\Livewire\Admissions;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admission;
use App\Enums\AdmissionStatusEnum;

class Index extends Component
{
    use WithPagination;
    public string $q=''; public string $trash='active'; public string $status='';

    public function render()
    {
        abort_unless(auth()->user()->can('admissions.view'), 403);
        $rows = Admission::query()->with('patient','visit')
            ->when($this->q, fn($q)=>$q->whereHas('patient', fn($p)=>$p->where('mrn','like','%'.$this->q.'%')->orWhere('first_name','like','%'.$this->q.'%')))
            ->when($this->status, fn($q)=>$q->where('status',$this->status))
            ->when($this->trash==='trashed', fn($q)=>$q->onlyTrashed())
            ->when($this->trash==='all', fn($q)=>$q->withTrashed())
            ->orderBy($this->sort ?? 'id', $this->dir ?? 'desc');
        return view('livewire.admissions.index', ['rows'=>$rows, 'statuses'=>AdmissionStatusEnum::cases()]);
    }
}