<?php
/**
 * Livewire component for read-only detail view and related widgets.
 *
 * @package HMS
 */
namespace App\Livewire\Bills;
use Livewire\Component;
use App\Models\Bill;
class Show extends Component {
    public Bill $bill;
    public function mount(int $id){ $this->bill=Bill::with('patient','items')->findOrFail($id); abort_unless(auth()->user()->can('billing.view'),403); }
    public function render(){ return view('livewire.bills.show'); }
}