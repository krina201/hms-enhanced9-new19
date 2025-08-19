<div>
    <h1 class="text-xl font-semibold mb-3">Bills</h1>
    <div class="flex gap-2 mb-2">
        <input type="text" wire:model.debounce.300ms="q" placeholder="Search Bill#/MRN" class="border px-2 py-1 rounded">
        <select wire:model="status" class="border px-2 py-1 rounded">
            <option value="">All</option>
            @foreach(\App\Enums\BillStatusEnum::cases() as $s)
                <option value="{{ $s->value }}">{{ $s->value }}</option>
            @endforeach
        </select>
        <a href="{{ route('bills.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">New</a>
    </div>
    <table class="w-full text-sm border">
        <thead><tr class="bg-gray-100"><th class="p-2">Bill#</th><th class="p-2">Date</th><th class="p-2">Patient</th><th class="p-2">Total</th><th class="p-2">Status</th><th></th></tr></thead>
        <tbody>
            @foreach($rows as $r)
            <tr class="border-b">
                <td class="p-2">{{ $r->bill_no }}</td>
                <td class="p-2">{{ optional($r->bill_date)->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ $r->patient?->mrn }} - {{ $r->patient?->first_name }}</td>
                <td class="p-2">{{ number_format($r->grand_total,2) }}</td>
                <td class="p-2">{{ $r->status?->value }}</td>
                <td class="p-2"><a href="{{ route('bills.edit',$r->id) }}" class="text-blue-600">Edit</a> · <a href="{{ route('bills.print',$r->id) }}" target="_blank" class="text-emerald-700">Print</a> · <a href="{{ route('bills.payments',$r->id) }}" class="text-emerald-700">Payments</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-2">{{ $rows->links() }}</div>
</div>
