<?php
namespace App\Livewire\Requisition;

use Livewire\Component;
use App\Models\Requisition;
use App\Models\RequisitionApproval;
use App\Enums\RequisitionStatusEnum;
use App\Enums\ApprovalStatusEnum;
use App\Services\ProcurementService;
use App\Services\ApprovalService;

class Form extends Component
{
    public ?int $id = null;
    public array $data = [
        'req_no' => '',
        'req_date' => '',
        'requested_by' => null,
        'status' => '',
        'notes' => '',
        'supplier_id' => null,
    ];
    public array $items = [];
    public array $statuses = [];

    public function rules() { return [
        'data.req_no' => 'required|string|max:40',
        'data.req_date' => 'required|date',
        'data.requested_by' => 'required|integer',
        'data.status' => 'required|string',
        'data.supplier_id' => 'nullable|integer',
        'items' => 'array|min:1',
        'items.*.inventory_item_id' => 'required|integer',
        'items.*.qty' => 'required|numeric|min:0.001',
    ]; }

    public function mount($id = null)
    {
        abort_unless(auth()->user()?->can('requisitions.edit') ?? false, 403);
        $this->id = $id;
        $this->statuses = array_map(fn($c)=>$c->value, RequisitionStatusEnum::cases());

        if ($id) {
            $m = Requisition::with('items')->findOrFail($id);
            $this->data = array_merge($this->data, $m->toArray());
            $this->items = $m->items->map(fn($it)=>['id'=>$it->id,'inventory_item_id'=>$it->inventory_item_id,'qty'=>$it->qty,'notes'=>$it->notes])->toArray();
        } else {
            $this->data['req_no'] = 'REQ-' . now()->format('YmdHis');
            $this->data['req_date'] = now()->toDateString();
            $this->data['requested_by'] = auth()->id();
            $this->data['status'] = RequisitionStatusEnum::DRAFT->value;
            $this->items = [['inventory_item_id'=>null,'qty'=>1,'notes'=>'']];
        }
    }

    public function addItem() { $this->items[] = ['inventory_item_id'=>null,'qty'=>1,'notes'=>'']; }
    public function removeItem($i) { array_splice($this->items, $i, 1); }

    public function save()
    {
        $validated = $this->validate();
        $data = $validated['data']; $items = $validated['items'];
        $m = Requisition::updateOrCreate(['id'=>$this->id], $data);
        $this->id = $m->id;
        $m->items()->delete();
        foreach ($items as $it) { $m->items()->create($it); }
        session()->flash('success','Requisition saved.');
        return redirect()->route('requisition.edit', $m->id);
    }

    public function submit(ApprovalService $svc)
    {
        $this->data['status'] = RequisitionStatusEnum::SUBMITTED->value;
        $this->save();
        $m = Requisition::findOrFail($this->id);
        $svc->startRequisitionWorkflow($m);
        session()->flash('success','Requisition submitted for approval.');
        return redirect()->route('requisition.edit', $m->id);
    }

    public function approveCurrentStep(ApprovalService $svc)
    {
        $m = Requisition::findOrFail($this->id);
        $ok = $svc->approveCurrentStep($m, auth()->id(), ''); // role checked via Spatie inside service
        session()->flash($ok ? 'success' : 'warning', $ok ? 'Approved current step.' : 'Not authorized for this step.');
    }

    public function rejectCurrentStep(ApprovalService $svc)
    {
        $m = Requisition::findOrFail($this->id);
        $ok = $svc->rejectCurrentStep($m, auth()->id(), 'Rejected from UI');
        session()->flash($ok ? 'success' : 'warning', $ok ? 'Rejected current step.' : 'No pending step.');
    }

    public function getApprovalsProperty()
    {
        if (!$this->id) return collect();
        return RequisitionApproval::where('requisition_id',$this->id)->with('step')->orderBy('id')->get();
    }

    public function convertToPO(ProcurementService $service)
    {
        abort_unless(auth()->user()?->can('requisitions.convert') ?? false, 403);
        $m = Requisition::with('items.item')->findOrFail($this->id);
        $pos = $service->convertRequisitionToPO($m);
        session()->flash('success', 'Converted to ' . count($pos) . ' PO(s).');
        return redirect()->route('purchaseorder.index');
    }

    public function render()
    {
        return view('livewire.requisition.form');
    }
}
