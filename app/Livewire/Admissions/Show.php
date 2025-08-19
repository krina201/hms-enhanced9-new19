<?php
/**
 * Livewire component for read-only detail view and related widgets.
 *
 * @package HMS
 */
namespace App\Livewire\Admissions;
use Livewire\Component;
use App\Models\Admission;
class Show extends Component
{
    public Admission $admission;
    public function mount(int $id) { $this->admission = Admission::with('patient','visit')->findOrFail($id); abort_unless(auth()->user()->can('admissions.view'), 403); }
    public function render() { return view('livewire.admissions.show'); }
}