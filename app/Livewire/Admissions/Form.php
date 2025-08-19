<?php
/**
 * Livewire component for create/edit forms with validation.
 *
 * @package HMS
 */
namespace App\Livewire\Admissions;
use Livewire\Component;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\Visit;
use App\Enums\AdmissionStatusEnum;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public ?Admission $admission=null;
    public $data=['patient_id'=>null,'visit_id'=>null,'admit_date'=>null,'discharge_date'=>null,'status'=>'ADMITTED','ward'=>null,'bed'=>null,'location_id'=>null,'notes'=>null];

    public function mount(?int $id=null)
    {
        if ($id) {
            $this->admission = Admission::with('patient','visit')->findOrFail($id);
            abort_unless(auth()->user()->can('admissions.edit'), 403);
            $this->data = $this->admission->only(array_keys($this->data));
            $this->data['status'] = $this->admission->status?->value ?? 'ADMITTED';
        } else {
            abort_unless(auth()->user()->can('admissions.create'), 403);
            $this->data['admit_date'] = now()->format('Y-m-d H:i');
        }
    }

    protected function rules(): array
    {
        return [
            'data.patient_id' => ['required','exists:patients,id'],
            'data.visit_id' => ['nullable','exists:visits,id'],
            'data.admit_date' => ['required','date'],
            'data.discharge_date' => ['nullable','date','after_or_equal:data.admit_date'],
            'data.status' => ['required', Rule::in(array_column(AdmissionStatusEnum::cases(),'value'))],
            'data.ward' => ['nullable','string','max:50'],
            'data.bed' => ['nullable','string','max:50'],
            'data.location_id' => ['nullable','integer'],
            'data.notes' => ['nullable','string','max:2000'],
            'data.diagnosis' => ['nullable','string','max:5000'],
            'data.procedures' => ['nullable','string','max:5000'],
            'data.instructions' => ['nullable','string','max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'data.discharge_date.after_or_equal' => 'Discharge date must be the same as or after the admit date.'
        ];
    }

    public function save()
    {
        $this->validate();
        try {
            $payload = $this->data; $payload['status']=AdmissionStatusEnum::from($payload['status']);
            if ($this->admission) { $this->admission->update($payload); session()->flash('success','Admission updated.'); }
            else { $this->admission = Admission::create($payload); session()->flash('success','Admission created.'); }
            return redirect()->route('admissions.show',$this->admission->id);
        } catch (\Throwable $e) { report($e); $this->addError('form',$e->getMessage()); }
    }

    public function delete()
    {
        abort_unless(auth()->user()->can('admissions.edit'), 403);
        if ($this->admission) { $this->admission->delete(); session()->flash('success','Admission deleted.'); return redirect()->route('admissions.index'); }
    }

    public function restore()
    {
        abort_unless(auth()->user()->can('admissions.edit'), 403);
        if ($this->admission && method_exists($this->admission,'restore')) { $this->admission->restore(); session()->flash('success','Admission restored.'); }
    }

    public function forceDelete()
    {
        abort_unless(auth()->user()->can('admissions.edit'), 403);
        if ($this->admission) { $this->admission->forceDelete(); session()->flash('success','Admission permanently deleted.'); return redirect()->route('admissions.index'); }
    }

    public function render()
    {
        return view('livewire.admissions.form', ['patients'=>Patient::orderBy('mrn')->limit(100)->get(),'visits'=>Visit::orderByDesc('visit_date')->limit(100)->get()]);
    }
}