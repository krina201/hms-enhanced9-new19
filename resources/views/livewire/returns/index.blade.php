<div class="p-6">
  <div class="mb-3">
    <a class="border rounded px-3 py-2" href="{{ route('returns.create') }}">+ New Return</a>
  </div>
  <table class="min-w-full border">
    <thead><tr class="bg-gray-50">
      <th class="px-3 py-2 text-left">Return No</th>
      <th class="px-3 py-2">Date</th>
      <th class="px-3 py-2">PO</th>
      <th class="px-3 py-2">GRN</th>
      <th class="px-3 py-2">Location</th>
    </tr></thead>
    <tbody>
      @foreach($rows as $r)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $r->return_no }}</td>
          <td class="px-3 py-2 text-center">{{ optional($r->return_date)->format('Y-m-d') }}</td>
          <td class="px-3 py-2 text-center">{{ $r->purchase_order_id ?? '—' }}</td>
          <td class="px-3 py-2 text-center">{{ $r->grn_id ?? '—' }}</td>
          <td class="px-3 py-2 text-center">{{ $r->location_id ?? '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $rows->links() }}</div>
</div>
