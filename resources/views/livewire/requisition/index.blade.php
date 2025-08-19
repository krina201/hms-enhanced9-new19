<div class="p-6">
  <div class="flex items-center justify-between mb-3">
    <input type="text" class="border rounded px-3 py-2" placeholder="Search Req No" wire:model.debounce.400ms="search">
    <a class="border rounded px-3 py-2" href="{{ route('requisition.create') }}">+ New Requisition</a>
  </div>
  <table class="min-w-full border">
    <thead><tr class="bg-gray-50">
      <th class="px-3 py-2 text-left">Req No</th>
      <th class="px-3 py-2">Date</th>
      <th class="px-3 py-2">Status</th>
      <th class="px-3 py-2">Supplier</th>
      <th class="px-3 py-2">Actions</th>
    </tr></thead>
    <tbody>
      @foreach($rows as $r)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $r->req_no }}</td>
          <td class="px-3 py-2 text-center">{{ optional($r->req_date)->format('Y-m-d') }}</td>
          <td class="px-3 py-2 text-center">{{ $r->status->value }}</td>
          <td class="px-3 py-2 text-center">{{ $r->supplier_id ?? '—' }}</td>
          <td class="px-3 py-2">
            <a class="text-blue-600" href="{{ route('requisition.edit', $r->id) }}">Open</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $rows->links() }}</div>
</div>
