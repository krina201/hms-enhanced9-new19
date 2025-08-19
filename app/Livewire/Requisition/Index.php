<?php
namespace App\Livewire\Requisition;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Requisition;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $q = Requisition::query()
            ->when($this->search, fn($q)=>$q->where('req_no','like','%'.$this->search.'%'));
        return view('livewire.requisition.index', [
            'rows' => $q->latest()->paginate(15)
        ]);
    }
}
