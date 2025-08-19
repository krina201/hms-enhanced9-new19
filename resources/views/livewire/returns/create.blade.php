<div class="p-6 max-w-5xl space-y-4">
  @if (session('success')) <div class="mb-4 text-green-700">{{ session('success') }}</div> @endif
  <h2 class="text-xl font-semibold">Goods Return</h2>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <div><label class="text-sm block">Return No</label><input type="text" class="border rounded w-full px-3 py-2" wire:model.defer="return_no"></div>
    <div><label class="text-sm block">Return Date</label><input type="date" class="border rounded w-full px-3 py-2" wire:model.defer="return_date"></div>
    <div><label class="text-sm block">PO #</label><input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="purchase_order_id"></div>
    <div><label class="text-sm block">GRN #</label><input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="grn_id"></div>
    <div><label class="text-sm block">Location</label><input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="location_id"></div>
    <div class="md:col-span-4"><label class="text-sm block">Reason</label><input type="text" class="border rounded w-full px-3 py-2" wire:model.defer="reason"></div>
  </div>

  <div class="border rounded p-3">
    <div class="flex items-center justify-between mb-2">
      <h3 class="font-semibold">Items</h3>
      <button class="border rounded px-3 py-1" wire:click.prevent="addItem">+ Add</button>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full border text-sm">
        <thead><tr class="bg-gray-50">
          <th class="px-3 py-2">Item ID</th>
          <th class="px-3 py-2">Batch</th>
          <th class="px-3 py-2">Qty</th>
          <th class="px-3 py-2">Unit Price</th>
          <th class="px-3 py-2">Notes</th>
          <th class="px-3 py-2">Actions</th>
        </tr></thead>
        <tbody>
          @foreach($items as $i => $it)
            <tr class="border-t">
              <td class="px-3 py-2"><input type="number" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.inventory_item_id"></td>
              <td class="px-3 py-2">
                <input type="number" class="border rounded px-2 py-1 w-28" placeholder="batch id" wire:model.defer="items.{{ $i }}.stock_batch_id">
                <div class="text-xs text-gray-600 mt-1">
                  
                </div>
              </td>
              <td class="px-3 py-2"><input type="number" step="0.001" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.qty"></td>
              <td class="px-3 py-2"><input type="number" step="0.01" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.unit_price"></td>
              <td class="px-3 py-2"><input type="text" class="border rounded px-2 py-1 w-40" wire:model.defer="items.{{ $i }}.notes"></td>
              <td class="px-3 py-2"><button class="text-red-700" wire:click.prevent="removeItem({{ $i }})">Remove</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div><button class="bg-blue-600 text-white px-4 py-2 rounded" wire:click="save">Post Return</button></div>
</div>
