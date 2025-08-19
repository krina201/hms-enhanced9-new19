<?php
/**
 * Livewire component for managing payments of a bill.
 *
 * @package HMS
 */
namespace App\Livewire\Payments;
use Livewire\Component;
use App\Models\Bill;
use App\Models\Payment;
use App\Enums\PaymentMethodEnum;

class Manage extends Component
{
    public Bill $bill;
    public $data=['payment_date'=>null,'amount'=>0,'method'=>'CASH','reference'=>null];

    public function mount(int $billId) { $this->bill = Bill::with('payments')->findOrFail($billId); abort_unless(auth()->user()->can('billing.edit'), 403); $this->data['payment_date']=now()->format('Y-m-d H:i'); }

    protected function rules(): array
    {
        return [
            'data.payment_date' => ['required','date'],
            'data.amount' => ['required','numeric','min:0.01'],
            'data.method' => ['required','in:'.implode(',', array_column(PaymentMethodEnum::cases(), 'value'))],
            'data.reference' => ['nullable','string','max:120'],
        ];
    }

    public function add()
    {
        $this->validate();
        $this->bill->payments()->create($this->data);
        $this->bill->refresh();
        $this->reset('data'); $this->data=['payment_date'=>now()->format('Y-m-d H:i'),'amount'=>0,'method'=>'CASH','reference'=>null];
    }

    public function delete(int $id)
    {
        $p = Payment::where('bill_id',$this->bill->id)->findOrFail($id);
        $p->delete();
        $this->bill->refresh();
    }

    public function render()
    {
        $paid = (float)$this->bill->payments()->sum('amount');
        $balance = max(0, (float)$this->bill->grand_total - $paid);
        return view('livewire.payments.manage', ['paid'=>$paid,'balance'=>$balance, 'methods'=>PaymentMethodEnum::cases()]);
    }
}