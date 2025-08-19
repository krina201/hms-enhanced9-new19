<?php
/**
 * Livewire component for create/edit forms with validation.
 *
 * @package HMS
 */
namespace App\Livewire\Patients;
use Livewire\Component;
use App\Models\Patient;
use Illuminate\Validation\Rule;
use App\Enums\PatientGenderEnum;

class Form extends Component
{
    public ?Patient $patient=null;
    public $data=[
        'mrn'=>'','first_name'=>'','last_name'=>'','dob'=>null,
        'gender'=>null,'phone'=>null,'email'=>null,'address'=>null
    ];

    public function mount(?int $id=null)
    {
        if ($id) {
            $this->patient = Patient::findOrFail($id);
            abort_unless(auth()->user()->can('patients.edit'), 403);
            $this->data = $this->patient->only(array_keys($this->data));
            $this->data['gender'] = $this->patient->gender?->value;
        } else {
            abort_unless(auth()->user()->can('patients.create'), 403);
        }
    }

    protected function rules(): array
    {
        return [
            'data.mrn' => ['required','string','max:50', Rule::unique('patients','mrn')->ignore($this->patient?->id)],
            'data.first_name' => ['required','string','max:120'],
            'data.last_name' => ['nullable','string','max:120'],
            'data.dob' => ['nullable','date'],
            'data.gender' => ['nullable', Rule::in(array_column(PatientGenderEnum::cases(),'value'))],
            'data.phone' => ['nullable','string','max:30'],
            'data.email' => ['nullable','email','max:150'],
            'data.address' => ['nullable','string','max:1000'],
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            $payload = $this->data;
            if ($payload['gender']) $payload['gender'] = PatientGenderEnum::from($payload['gender']);
            if ($this->patient) {
                $this->patient->update($payload);
                session()->flash('success','Patient updated.');
            } else {
                $this->patient = Patient::create($payload);
                session()->flash('success','Patient created.');
            }
            return redirect()->route('patients.index');
        } catch (\Throwable $e) {
            report($e); $this->addError('form',$e->getMessage());
        }
    }

    
    public function delete() { abort_unless(auth()->user()->can('patients.edit'), 403); if ($this->patient) { $this->patient->delete(); session()->flash('success','Deleted.'); } }
    public function restore() { abort_unless(auth()->user()->can('patients.edit'), 403); if ($this->patient && method_exists($this->patient,'restore')) { $this->patient->restore(); session()->flash('success','Restored.'); } }
    public function forceDelete() { abort_unless(auth()->user()->can('patients.edit'), 403); if ($this->patient) { $this->patient->forceDelete(); session()->flash('success','Permanently deleted.'); } }
    
    public function render() { return view('livewire.patients.form'); }
}