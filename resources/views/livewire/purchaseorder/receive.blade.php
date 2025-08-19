<div class="p-6 max-w-6xl space-y-6">
  @if (session('success')) <div class="mb-4 text-green-700">{{ session('success') }}</div> @endif
  <h2 class="text-xl font-semibold">Receive Purchase Order (Multi-batch)</h2>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm mb-1">GRN No</label>
      <input type="text" class="border rounded w-full px-3 py-2" wire:model.defer="grn_no">
      @error('grn_no') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
      <label class="block text-sm mb-1">GRN Date</label>
      <input type="date" class="border rounded w-full px-3 py-2" wire:model.defer="grn_date">
      @error('grn_date') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Location</label>
      <input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="location_id">
    </div>
    <div class="md:col-span-4">
      <label class="block text-sm mb-1">Notes</label>
      <textarea rows="2" class="border rounded w-full px-3 py-2" wire:model.defer="notes"></textarea>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full border text-sm">
      <thead>
        <tr class="bg-gray-50">
          <th class="px-3 py-2 text-left">Item</th>
          <th class="px-3 py-2">Ordered</th>
          <th class="px-3 py-2">Received</th>
          <th class="px-3 py-2">Open</th>
          <th class="px-3 py-2">Splits</th>
        </tr>
      </thead>
      <tbody>
        @foreach($lines as $i => $l)
          <tr class="border-t align-top">
            <td class="px-3 py-2">
              #{{ $l['inventory_item_id'] }}
            </td>
            <td class="px-3 py-2 text-center">{{ number_format($l['ordered_qty'],3) }}</td>
            <td class="px-3 py-2 text-center text-gray-600">{{ number_format($l['already_received'],3) }}</td>
            <td class="px-3 py-2 text-center font-semibold">{{ number_format($l['open_qty'],3) }}</td>
            <td class="px-3 py-2">
              <div class="space-y-2">
                @foreach($l['splits'] as $j => $sp)
                  <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
                    <div>
                      <label class="text-xs block">Receive Qty</label>
                      <input type="number" step="0.001" class="border rounded w-full px-2 py-1" wire:model.defer="lines.{{ $i }}.splits.{{ $j }}.received_qty">
                    </div>
                    <div>
                      <label class="text-xs block">Batch No</label>
                      <input type="text" class="border rounded w-full px-2 py-1" wire:model.defer="lines.{{ $i }}.splits.{{ $j }}.batch_no">
                    </div>
                    <div>
                      <label class="text-xs block">Expiry</label>
                      <input type="date" class="border rounded w-full px-2 py-1" wire:model.defer="lines.{{ $i }}.splits.{{ $j }}.expiry_date">
                    </div>
                    <div>
                      <label class="text-xs block">Unit Price</label>
                      <input type="number" step="0.01" class="border rounded w-full px-2 py-1" wire:model.defer="lines.{{ $i }}.splits.{{ $j }}.unit_price">
                    </div>
                    <div class="flex space-x-2">
                      <button class="border rounded px-2 py-1" wire:click.prevent="removeSplit({{ $i }}, {{ $j }})">Remove</button>
                      <button class="border rounded px-2 py-1" wire:click.prevent="addSplit({{ $i }})">+ Split</button>
                    </div>
                  </div>
                @endforeach

                <div class="text-xs text-gray-600 mt-2">
                  <span>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @error('lines') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

  <div class="mt-4">
    <button class="bg-blue-600 text-white px-4 py-2 rounded" wire:click="save">Post GRN</button>
    <a href="{{ route('purchaseorder.index') }}" class="px-4 py-2">Cancel</a>
  </div>
</div>
