<div class="max-w-2xl">
    <h1 class="text-xl font-semibold mb-3">Payments for Bill #{{ $bill->bill_no }}</h1>
    <div class="p-3 border rounded mb-3">
        <div>Grand Total: <strong>{{ number_format($bill->grand_total,2) }}</strong></div>
        <div>Paid: <strong>{{ number_format($paid,2) }}</strong></div>
        <div>Balance: <strong>{{ number_format($balance,2) }}</strong></div>
        <div>Status: <strong>{{ $bill->status?->value }}</strong></div>
    </div>
    <form wire:submit.prevent="add" class="grid grid-cols-4 gap-2 p-3 border rounded mb-3">
        <div><label class="block text-xs">Date</label><input type="datetime-local" wire:model="data.payment_date" class="border px-2 py-1 rounded w-full">@error('data.payment_date')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror</div>
        <div><label class="block text-xs">Amount</label><input type="number" step="0.01" wire:model="data.amount" class="border px-2 py-1 rounded w-full">@error('data.amount')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror</div>
        <div><label class="block text-xs">Method</label>
            <select wire:model="data.method" class="border px-2 py-1 rounded w-full">
                @foreach($methods as $m)<option value="{{ $m->value }}">{{ $m->value }}</option>@endforeach
            </select>
        </div>
        <div><label class="block text-xs">Reference</label><input type="text" wire:model="data.reference" class="border px-2 py-1 rounded w-full"></div>
        <div class="col-span-4"><button class="px-3 py-1 bg-emerald-600 text-white rounded">Add Payment</button></div>
    </form>
    <table class="w-full text-sm border">
        <thead><tr class="bg-gray-100"><th class="p-2">Date</th><th class="p-2">Method</th><th class="p-2">Amount</th><th class="p-2">Ref</th><th></th></tr></thead>
        <tbody>
            @foreach($bill->payments as $p)
            <tr class="border-b">
                <td class="p-2">{{ optional($p->payment_date)->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ $p->method?->value ?? $p->method }}</td>
                <td class="p-2">{{ number_format($p->amount,2) }}</td>
                <td class="p-2">{{ $p->reference }}</td>
                <td class="p-2"><button wire:click="delete({{ $p->id }})" class="text-red-600">Delete</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
