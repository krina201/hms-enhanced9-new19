<div class="p-6 max-w-5xl space-y-4">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">GRN {{ $grn->grn_no }}</h2>
    <a class="border rounded px-3 py-2" href="{{ route('grn.print', $grn->id) }}" target="_blank">Print PDF</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
    <div><b>Date:</b> {{ optional($grn->grn_date)->format('Y-m-d') }}</div>
    <div><b>PO #:</b> {{ $grn->purchase_order_id }}</div>
    <div><b>Location:</b> {{ $grn->location_id ?? '—' }}</div>
    <div><b>Received By:</b> {{ $grn->received_by }}</div>
    <div class="md:col-span-2"><b>Notes:</b> {{ $grn->notes ?: '—' }}</div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-3 py-2 text-left">Item</th>
          <th class="px-3 py-2">Batch</th>
          <th class="px-3 py-2">Expiry</th>
          <th class="px-3 py-2">Qty</th>
          <th class="px-3 py-2">Unit Price</th>
        </tr>
      </thead>
      <tbody>
        @foreach($grn->items as $i)
          <tr class="border-t">
            <td class="px-3 py-2">#{{ $i->inventory_item_id }} {{ $i->item->name ?? '' }}</td>
            <td class="px-3 py-2 text-center">{{ $i->batch_no ?? '—' }}</td>
            <td class="px-3 py-2 text-center">{{ optional($i->expiry_date)->format('Y-m-d') ?? '—' }}</td>
            <td class="px-3 py-2 text-center">{{ number_format($i->received_qty,3) }}</td>
            <td class="px-3 py-2 text-center">{{ number_format($i->unit_price ?? 0,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
