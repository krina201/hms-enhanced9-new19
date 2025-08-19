<?php
/**
 * Livewire component for paginated index listing with search/filters.
 *
 * @package HMS
 */
namespace App\Livewire\Patients;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;

class Index extends Component
{
    use WithPagination;
    public string $q=''; public string $trash='active'; public string $sort='mrn'; public string $dir='asc';

    public function updatingQ(){ $this->resetPage(); }

    public function exportCsv()
    {
        abort_unless(auth()->user()->can('patients.view'), 403);
        $filename = 'patients_'.now()->format('Ymd_His').'.csv';
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="'.$filename.'"'];
        $query = Patient::query()
            ->when($this->q, fn($q)=>$q->where(function($qq){
                $qq->where('mrn','like','%'.$this->q.'%')
                   ->orWhere('first_name','like','%'.$this->q.'%')
                   ->orWhere('last_name','like','%'.$this->q.'%')
                   ->orWhere('phone','like','%'.$this->q.'%');
            }))
            ->when($this->trash==='trashed', fn($q)=>$q->onlyTrashed())
            ->when($this->trash==='all', fn($q)=>$q->withTrashed())
            ->orderBy($this->sort ?? 'id', $this->dir ?? 'desc');
        return response()->streamDownload(function() use ($query){
            $out=fopen('php://output','w'); fputcsv($out,['MRN','Name','DOB','Gender','Phone']);
            $query->chunkById(500, function($rows) use ($out){
                foreach ($rows as $p) fputcsv($out, [$p->mrn, trim($p->first_name.' '.$p->last_name), optional($p->dob)->format('Y-m-d'), $p->gender?->value, $p->phone]);
            }, 'patients.id'); fclose($out);
        }, $filename, $headers);
    }

    public function render()
    {
        abort_unless(auth()->user()->can('patients.view'), 403);
        $rows = Patient::query()
            ->when($this->q, fn($q)=>$q->where(function($qq){
                $qq->where('mrn','like','%'.$this->q.'%')
                   ->orWhere('first_name','like','%'.$this->q.'%')
                   ->orWhere('last_name','like','%'.$this->q.'%')
                   ->orWhere('phone','like','%'.$this->q.'%');
            }))
            ->orderBy($this->sort, $this->dir)->paginate(12);
        return view('livewire.patients.index', ['rows'=>$rows]);
    }
}