<div class="p-6">
  <div class="grid grid-cols-1 md:grid-cols-7 gap-3 mb-4">
    <input type="text" class="border rounded px-3 py-2" placeholder="Search GRN No" wire:model.debounce.400ms="search">
    <input type="date" class="border rounded px-3 py-2" wire:model="date_from">
    <input type="date" class="border rounded px-3 py-2" wire:model="date_to">
    <input type="number" class="border rounded px-3 py-2" placeholder="PO #" wire:model="po_id">
    <input type="number" class="border rounded px-3 py-2" placeholder="Supplier ID" wire:model="supplier_id">
    <input type="text" class="border rounded px-3 py-2" placeholder="Supplier Name" wire:model.debounce.400ms="supplier_name">
    <input type="number" class="border rounded px-3 py-2" placeholder="Location ID" wire:model="location_id">
  </div>
  <div class="mb-3">
    <button class="border rounded px-3 py-2" wire:click="exportCsv">Export CSV</button>
  </div>
  <table class="min-w-full border">
    <thead>
      <tr class="bg-gray-50">
        <th class="px-3 py-2 text-left">GRN No</th>
        <th class="px-3 py-2">Date</th>
        <th class="px-3 py-2">PO</th>
        <th class="px-3 py-2">Supplier</th>
        <th class="px-3 py-2">Location</th>
        <th class="px-3 py-2">Posted</th>
        <th class="px-3 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $r->grn_no }}</td>
          <td class="px-3 py-2 text-center">{{ optional($r->grn_date)->format('Y-m-d') }}</td>
          <td class="px-3 py-2 text-center">#{{ $r->purchase_order_id }}</td>
          <td class="px-3 py-2 text-center">{{ optional($r->purchaseOrder?->supplier)->name ?? $r->purchaseOrder?->supplier_id }}</td>
          <td class="px-3 py-2 text-center">{{ $r->location_id ?? '—' }}</td>
          <td class="px-3 py-2 text-center">{{ optional($r->posted_at)->diffForHumans() ?? '—' }}</td>
          <td class="px-3 py-2">
            <a class="text-blue-600" href="{{ route('grn.show', $r->id) }}">View</a>
            <span class="mx-1">·</span>
            <a class="text-gray-700" href="{{ route('grn.print', $r->id) }}" target="_blank">Print PDF</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $rows->links() }}</div>
</div>
