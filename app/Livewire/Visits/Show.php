<?php
/**
 * Livewire component for read-only detail view and related widgets.
 *
 * @package HMS
 */
namespace App\Livewire\Visits;
use Livewire\Component;
use App\Models\Visit;
class Show extends Component {
    public Visit $visit;
    public function mount(int $id){ $this->visit=Visit::with('patient')->findOrFail($id); abort_unless(auth()->user()->can('visits.view'),403); }
    public function render(){ return view('livewire.visits.show'); }
}