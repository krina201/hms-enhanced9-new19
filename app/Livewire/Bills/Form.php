<?php
/**
 * Livewire component for create/edit forms with validation.
 *
 * @package HMS
 */
namespace App\Livewire\Bills;
use Livewire\Component;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Patient;
use App\Models\Visit;
use App\Enums\BillStatusEnum;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public ?Bill $bill=null;
    public $data=['bill_no'=>'','patient_id'=>null,'visit_id'=>null,'bill_date'=>null,'status'=>'DRAFT'];
    public $items=[['description'=>'Consultation','qty'=>1,'unit_price'=>0,'tax_rate'=>0,'discount'=>0]];

    public function mount(?int $id=null)
    {
        if ($id) {
            $this->bill = Bill::with('items')->findOrFail($id);
            $this->data = $this->bill->only(array_keys($this->data));
            $this->data['status'] = $this->bill->status?->value ?? 'DRAFT';
            $this->items = $this->bill->items->map->only(['description','qty','unit_price','tax_rate','discount'])->all();
        } else {
            $this->data['bill_date'] = now()->format('Y-m-d H:i');
        }
    }

    protected function rules(): array
    {
        return [
            'data.bill_no' => ['required','string','max:50', Rule::unique('bills','bill_no')->ignore($this->bill?->id) ],
            'data.patient_id' => ['required','exists:patients,id'],
            'data.visit_id' => ['nullable','exists:visits,id'],
            'data.bill_date' => ['required','date'],
            'data.status' => ['required', Rule::in(array_column(BillStatusEnum::cases(),'value'))],
            'items' => ['required','array','min:1'],
            'items.*.description' => ['required','string','max:255'],
            'items.*.qty' => ['required','numeric','min:0.001'],
            'items.*.unit_price' => ['required','numeric','min:0'],
            'items.*.tax_rate' => ['required','numeric','min:0'],
            'items.*.discount' => ['required','numeric','min:0'],
        ];
    }

    public function addItem(){ $this->items[]=['description'=>'','qty'=>1,'unit_price'=>0,'tax_rate'=>0,'discount'=>0]; }
    public function removeItem($i){ unset($this->items[$i]); $this->items=array_values($this->items); }

    private function totals(array $items): array
    {
        $sub=0; $tax=0; $disc=0;
        foreach ($items as $it) {
            $line = (float)$it['qty'] * (float)$it['unit_price'];
            $lineTax = $line * ((float)$it['tax_rate']/100);
            $sub += $line; $tax += $lineTax; $disc += (float)$it['discount'];
        }
        $grand = max(0, $sub + $tax - $disc);
        return ['subtotal'=>round($sub,2),'tax'=>round($tax,2),'discount'=>round($disc,2),'grand_total'=>round($grand,2)];
    }

    public function save()
    {
        $this->validate();
        try {
            $payload = $this->data;
            $payload['status'] = BillStatusEnum::from($payload['status']);
            $tot = $this->totals($this->items);
            $payload = array_merge($payload, $tot);

            if ($this->bill) { $this->bill->update($payload); $this->bill->items()->delete(); }
            else { $this->bill = Bill::create($payload); }

            foreach ($this->items as $it) { $this->bill->items()->create($it); }
            session()->flash('success','Bill saved.');
            return redirect()->route('bills.index');
        } catch (\Throwable $e) { report($e); $this->addError('form',$e->getMessage()); }
    }

    
    public function delete() { abort_unless(auth()->user()->can('billing.edit'), 403); if ($this->bill) { $this->bill->delete(); session()->flash('success','Deleted.'); } }
    public function restore() { abort_unless(auth()->user()->can('billing.edit'), 403); if ($this->bill && method_exists($this->bill,'restore')) { $this->bill->restore(); session()->flash('success','Restored.'); } }
    public function forceDelete() { abort_unless(auth()->user()->can('billing.edit'), 403); if ($this->bill) { $this->bill->forceDelete(); session()->flash('success','Permanently deleted.'); } }
    
    public function render()
    {
        return view('livewire.bills.form', [
            'patients'=>Patient::orderBy('mrn')->limit(100)->get(),
            'visits'=>Visit::orderByDesc('visit_date')->limit(100)->get(),
        ]);
    }
}