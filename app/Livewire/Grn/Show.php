<?php
namespace App\Livewire\Grn;

use Livewire\Component;
use App\Models\GoodsReceipt;

class Show extends Component
{
    public int $id;
    public GoodsReceipt $grn;

    public function mount($id)
    {
        $this->id = (int) $id;
        $this->grn = GoodsReceipt::with('items.item','purchaseOrder.supplier')->findOrFail($this->id);
    }

    public function render()
    {
        return view('livewire.grn.show', ['grn' => $this->grn]);
    }
}
