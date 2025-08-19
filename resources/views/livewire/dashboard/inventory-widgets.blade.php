<div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="rounded-xl shadow p-4">
    <div class="text-sm text-gray-500">Low Stock Items</div>
    <div class="text-3xl font-semibold">{{ $lowStock }}</div>
  </div>
  <div class="rounded-xl shadow p-4">
    <div class="text-sm text-gray-500">Expired Batches</div>
    <div class="text-3xl font-semibold">{{ $expiredBatches }}</div>
  </div>
  <div class="rounded-xl shadow p-4">
    <div class="text-sm text-gray-500">Pending GRNs</div>
    <div class="text-3xl font-semibold">{{ $pendingGrns }}</div>
  </div>
</div>
