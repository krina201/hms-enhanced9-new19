<div class="p-6 max-w-5xl space-y-4">
  @if (session('success')) <div class="mb-4 text-green-700">{{ session('success') }}</div> @endif
  @if (session('warning')) <div class="mb-4 text-yellow-800">{{ session('warning') }}</div> @endif

  <form wire:submit.prevent="save" class="space-y-3">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div><label class="text-sm block">Req No</label><input type="text" class="border rounded w-full px-3 py-2" wire:model.defer="data.req_no"></div>
      <div><label class="text-sm block">Req Date</label><input type="date" class="border rounded w-full px-3 py-2" wire:model.defer="data.req_date"></div>
      <div><label class="text-sm block">Supplier (optional)</label><input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="data.supplier_id"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div><label class="text-sm block">Status</label>
        <select class="border rounded w-full px-3 py-2" wire:model.defer="data.status">
          @foreach(\App\Enums\RequisitionStatusEnum::cases() as $s)
            <option value="{{ $s->value }}">{{ $s->value }}</option>
          @endforeach
        </select>
      </div>
      <div><label class="text-sm block">Requested By</label><input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="data.requested_by"></div>
    </div>

    <div class="border rounded p-3">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">Items</h3>
        <button class="border rounded px-3 py-1" wire:click.prevent="addItem">+ Add</button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full border text-sm">
          <thead><tr class="bg-gray-50">
            <th class="px-3 py-2">Item ID</th>
            <th class="px-3 py-2">Qty</th>
            <th class="px-3 py-2">Notes</th>
            <th class="px-3 py-2">Actions</th>
          </tr></thead>
          <tbody>
            @foreach($items as $i => $it)
              <tr class="border-t">
                <td class="px-3 py-2"><input type="number" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.inventory_item_id"></td>
                <td class="px-3 py-2"><input type="number" step="0.001" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.qty"></td>
                <td class="px-3 py-2"><input type="text" class="border rounded px-2 py-1 w-40" wire:model.defer="items.{{ $i }}.notes"></td>
                <td class="px-3 py-2"><button class="text-red-700" wire:click.prevent="removeItem({{ $i }})">Remove</button></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex items-center space-x-2">
      <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Save</button>
      <button class="border rounded px-3 py-2" type="button" wire:click="submit">Submit</button>
      <button class="border rounded px-3 py-2" type="button" wire:click="approveCurrentStep">Approve Current Step</button>
      <button class="border rounded px-3 py-2" type="button" wire:click="rejectCurrentStep">Reject Current Step</button>
      <button class="border rounded px-3 py-2" type="button" wire:click="convertToPO">Convert to PO</button>
    </div>
  </form>

  @if($id)
    <div class="border rounded p-3">
      <h3 class="font-semibold mb-2">Approvals</h3>
      <table class="min-w-full border text-sm mb-3">
        <thead><tr class="bg-gray-50"><th class="px-3 py-2">Level</th><th class="px-3 py-2">Role</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Approved By</th><th class="px-3 py-2">At</th><th class="px-3 py-2">Note</th></tr></thead>
        <tbody>
          @foreach($this->approvals as $ap)
            <tr class="border-t">
              <td class="px-3 py-2">{{ $ap->step->level }}</td>
              <td class="px-3 py-2">{{ $ap->step->role_name }}</td>
              <td class="px-3 py-2">{{ $ap->status->value }}</td>
              <td class="px-3 py-2">{{ $ap->approved_by ?? '—' }}</td>
              <td class="px-3 py-2">{{ optional($ap->approved_at)->format('Y-m-d H:i') ?? '—' }}</td>
              <td class="px-3 py-2">{{ $ap->note ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <h4 class="font-semibold mb-1">Approval Audit Trail</h4>
      <table class="min-w-full border text-xs">
        <thead><tr class="bg-gray-50"><th class="px-2 py-1">When</th><th class="px-2 py-1">Step</th><th class="px-2 py-1">Action</th><th class="px-2 py-1">Actor</th><th class="px-2 py-1">Note</th></tr></thead>
        <tbody>
          @php
          $events = \App\Models\RequisitionApprovalEvent::whereIn('requisition_approval_id', $this->approvals->pluck('id'))
            ->with('approval.step')->orderBy('created_at')->get();
          @endphp
          @foreach($events as $ev)
            <tr class="border-t">
              <td class="px-2 py-1">{{ $ev->created_at->format('Y-m-d H:i') }}</td>
              <td class="px-2 py-1">Level {{ $ev->approval->step->level }} ({{ $ev->approval->step->role_name }})</td>
              <td class="px-2 py-1">{{ $ev->action }}</td>
              <td class="px-2 py-1">{{ $ev->actor_id ?? 'system' }}</td>
              <td class="px-2 py-1">{{ $ev->note ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
