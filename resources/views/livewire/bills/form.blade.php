<div class="max-w-3xl">
    <h1 class="text-xl font-semibold mb-3">{{ $bill ? 'Edit' : 'Create' }} Bill</h1>
    <form wire:submit.prevent="save" class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div><label class="block text-xs">Bill #</label><input type="text" wire:model="data.bill_no" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Patient</label>
                <select wire:model="data.patient_id" class="border px-2 py-1 rounded w-full">
                    <option value="">--</option>@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->mrn }} - {{ $p->first_name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs">Visit</label>
                <select wire:model="data.visit_id" class="border px-2 py-1 rounded w-full">
                    <option value="">--</option>@foreach($visits as $v)<option value="{{ $v->id }}">{{ $v->visit_no }} ({{ optional($v->visit_date)->format('Y-m-d') }})</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs">Bill Date</label><input type="datetime-local" wire:model="data.bill_date" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Status</label>
                <select wire:model="data.status" class="border px-2 py-1 rounded w-full">
                    @foreach(\App\Enums\BillStatusEnum::cases() as $s)<option value="{{ $s->value }}">{{ $s->value }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="font-semibold">Items</h3>
            <table class="w-full text-sm border">
                <thead><tr class="bg-gray-100"><th class="p-1">Desc</th><th class="p-1">Qty</th><th class="p-1">Unit</th><th class="p-1">Tax%</th><th class="p-1">Discount</th><th></th></tr></thead>
                <tbody>
                    @foreach($items as $i => $it)
                    <tr class="border-b">
                        <td class="p-1"><input type="text" wire:model="items.{{ $i }}.description" class="border px-2 py-1 rounded w-full"></td>
                        <td class="p-1"><input type="number" step="0.001" wire:model="items.{{ $i }}.qty" class="border px-2 py-1 rounded w-full"></td>
                        <td class="p-1"><input type="number" step="0.01" wire:model="items.{{ $i }}.unit_price" class="border px-2 py-1 rounded w-full"></td>
                        <td class="p-1"><input type="number" step="0.01" wire:model="items.{{ $i }}.tax_rate" class="border px-2 py-1 rounded w-full"></td>
                        <td class="p-1"><input type="number" step="0.01" wire:model="items.{{ $i }}.discount" class="border px-2 py-1 rounded w-full"></td>
                        <td class="p-1"><button type="button" wire:click="removeItem({{ $i }})" class="text-red-600">Remove</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" wire:click="addItem" class="mt-2 px-3 py-1 bg-gray-200 rounded">+ Add Item</button>
        </div>
        @error('form')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        <div class="mt-2"><button class="px-3 py-1 bg-blue-600 text-white rounded">Save</button></div>
    @if($patient ?? false)
    <div class="mt-3 flex gap-2">
        <button type="button" onclick="if(confirm('Delete?')){ Livewire.find('{ $this->id }').call('delete') }" @cannot('billing.delete') disabled @endcannot" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
        <button type="button" onclick="if(confirm('Restore?')){ Livewire.find('{ $this->id }').call('restore') }" @cannot('billing.restore') disabled @endcannot" class="px-3 py-1 bg-amber-600 text-white rounded">Restore</button>
        <button type="button" onclick="if(confirm('Force delete?')){ Livewire.find('{ $this->id }').call('forceDelete') }" @cannot('billing.force_delete') disabled @endcannot" class="px-3 py-1 bg-gray-700 text-white rounded">Force Delete</button>
    </div>
    @endif
</form>
</div>


<div class="mt-4">
    <a href="{{ $bill ? route('bills.payments',$bill->id) : '#' }}" class="px-3 py-1 bg-emerald-600 text-white rounded {{ $bill ? '' : 'pointer-events-none opacity-50' }}">Open Payments</a>
</div>


@if($bill)
<div class="mt-6">
    @livewire('attachments.manager', ['attachableType'=>get_class($bill), 'attachableId'=>$bill->id], key('bill-att-'.$bill->id))
</div>
@endif
