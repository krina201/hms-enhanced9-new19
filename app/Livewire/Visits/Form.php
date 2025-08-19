<?php
/**
 * Livewire component for create/edit forms with validation.
 *
 * @package HMS
 */
namespace App\Livewire\Visits;
use Livewire\Component;
use App\Models\Visit;
use App\Models\Patient;
use App\Enums\VisitTypeEnum;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public ?Visit $visit=null; public $data=[
        'patient_id'=>null,'visit_no'=>'','visit_date'=>null,'type'=>'OPD','doctor_id'=>null,'department'=>null,'location_id'=>null,'chief_complaint'=>null
    ];

    public function mount(?int $id=null)
    {
        if ($id) {
            $this->visit = Visit::with('patient')->findOrFail($id);
            abort_unless(auth()->user()->can('visits.edit'), 403);
            $this->data = $this->visit->only(array_keys($this->data));
            $this->data['type'] = $this->visit->type?->value ?? 'OPD';
        } else {
            abort_unless(auth()->user()->can('visits.create'), 403);
            $this->data['visit_date'] = now()->format('Y-m-d H:i');
        }
    }

    protected function rules(): array
    {
        return [
            'data.patient_id' => ['required','exists:patients,id'],
            'data.visit_no' => ['required','string','max:50', Rule::unique('visits','visit_no')->ignore($this->visit?->id)],
            'data.visit_date' => ['required','date'],
            'data.type' => ['required', Rule::in(array_column(VisitTypeEnum::cases(),'value'))],
            'data.department' => ['nullable','string','max:120'],
            'data.chief_complaint' => ['nullable','string','max:2000'],
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            $payload = $this->data;
            $payload['type'] = VisitTypeEnum::from($payload['type']);
            if ($this->visit) { $this->visit->update($payload); session()->flash('success','Visit updated.'); }
            else { $this->visit = Visit::create($payload); session()->flash('success','Visit created.'); }
            return redirect()->route('visits.index');
        } catch (\Throwable $e) { report($e); $this->addError('form',$e->getMessage()); }
    }

    
    public function delete() { abort_unless(auth()->user()->can('visits.edit'), 403); if ($this->visit) { $this->visit->delete(); session()->flash('success','Deleted.'); } }
    public function restore() { abort_unless(auth()->user()->can('visits.edit'), 403); if ($this->visit && method_exists($this->visit,'restore')) { $this->visit->restore(); session()->flash('success','Restored.'); } }
    public function forceDelete() { abort_unless(auth()->user()->can('visits.edit'), 403); if ($this->visit) { $this->visit->forceDelete(); session()->flash('success','Permanently deleted.'); } }
    
    public function render() { return view('livewire.visits.form', ['patients'=>Patient::orderBy('mrn')->limit(50)->get()]); }
}