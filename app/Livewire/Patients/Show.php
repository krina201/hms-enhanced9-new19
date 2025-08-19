<?php
/**
 * Livewire component for read-only detail view and related widgets.
 *
 * @package HMS
 */
namespace App\Livewire\Patients;
use Livewire\Component;
use App\Models\Patient;
class Show extends Component {
    public Patient $patient;
    public function mount(int $id){ $this->patient=Patient::findOrFail($id); abort_unless(auth()->user()->can('patients.view'),403); }
    public function render(){ return view('livewire.patients.show'); }
}