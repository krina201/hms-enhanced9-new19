<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-xl font-semibold">Bill {{ $bill->bill_no }}</h1>
        <div class="flex gap-2">
            <a class="px-3 py-1 bg-emerald-600 text-white rounded" href="{{ route('bills.print',$bill->id) }}" target="_blank">Print PDF</a>
            <a class="px-3 py-1 bg-blue-600 text-white rounded" href="{{ route('bills.edit',$bill->id) }}">Edit</a>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div class="p-3 border rounded">
            <div><strong>Patient:</strong> {{ $bill->patient?->mrn }} - {{ $bill->patient?->first_name }}</div>
            <div><strong>Date:</strong> {{ optional($bill->bill_date)->format('Y-m-d H:i') }}</div>
            <div><strong>Status:</strong> {{ $bill->status?->value }}</div>
        </div>
        <div class="p-3 border rounded">
            <div><strong>Grand Total:</strong> {{ number_format($bill->grand_total,2) }}</div>
            <div><a class="text-emerald-700" href="{{ route('bills.payments',$bill->id) }}">Open Payments</a></div>
        </div>
    </div>
    <div class="mt-4">
        @livewire('attachments.manager', ['attachableType'=>get_class($bill), 'attachableId'=>$bill->id], key('bill-att-'.$bill->id))
    </div>
</div>
