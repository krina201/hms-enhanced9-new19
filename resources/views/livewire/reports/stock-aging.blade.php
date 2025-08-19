<div class="p-6">
  <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
    <input type="number" class="border rounded px-3 py-2" placeholder="Location" wire:model="location_id">
    <input type="number" class="border rounded px-3 py-2" placeholder="Soon threshold (days)" wire:model="days_threshold">
    <label class="flex items-center space-x-2"><input type="checkbox" wire:model="only_expiring"><span>Only non-expired</span></label>
    <button class="border rounded px-3 py-2" wire:click="exportCsv">Export CSV</button>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="rounded-xl shadow p-3">
      <div class="text-xs text-gray-500">Expiring ≤ {{ $days_threshold ?? 30 }} days</div>
      <div class="text-2xl font-semibold">{{ $soonCount }}</div>
    </div>
    <div class="rounded-xl shadow p-3">
      <div class="text-xs text-gray-500">Expired</div>
      <div class="text-2xl font-semibold">{{ $expiredCount }}</div>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-3 py-2 text-left">Item</th>
          <th class="px-3 py-2">Batch</th>
          <th class="px-3 py-2">Expiry</th>
          <th class="px-3 py-2">Days</th>
          <th class="px-3 py-2">Qty</th>
          <th class="px-3 py-2">Location</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
          @php $dte = $r->expiry_date ? now()->diffInDays($r->expiry_date, false) : null; @endphp
          <tr class="border-t">
            <td class="px-3 py-2">{{ $r->item->name ?? $r->inventory_item_id }}</td>
            <td class="px-3 py-2 text-center">{{ $r->batch_no }}</td>
            <td class="px-3 py-2 text-center">{{ optional($r->expiry_date)->format('Y-m-d') ?? '—' }}</td>
            <td class="px-3 py-2 text-center">{{ $dte ?? '—' }}</td>
            <td class="px-3 py-2 text-center">{{ number_format($r->qty_on_hand,3) }}</td>
            <td class="px-3 py-2 text-center">{{ $r->location_id ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4">{{ $rows->links() }}</div>
  </div>
</div>
