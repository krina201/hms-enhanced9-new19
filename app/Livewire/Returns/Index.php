<?php
namespace App\Livewire\Returns;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GoodsReturn;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.returns.index', ['rows' => GoodsReturn::latest()->paginate(15)]);
    }
}
