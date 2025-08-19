<div class="p-6 max-w-6xl space-y-6">
  @if (session('warning')) <div class="mb-4 text-yellow-800">{{ session('warning') }}</div> @endif
  @if (session('success')) <div class="mb-4 text-green-700">{{ session('success') }}</div> @endif

  <form wire:submit.prevent="save" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm mb-1">Supplier</label>
        <input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="data.supplier_id">
        @error('data.supplier_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
      </div>
      <div>
        <label class="block text-sm mb-1">Order No</label>
        <input type="text" class="border rounded w-full px-3 py-2" wire:model.defer="data.order_no">
      </div>
      <div>
        <label class="block text-sm mb-1">Order Date</label>
        <input type="date" class="border rounded w-full px-3 py-2" wire:model.defer="data.order_date">
      </div>
      <div>
        <label class="block text-sm mb-1">Expected Date</label>
        <input type="date" class="border rounded w-full px-3 py-2" wire:model.defer="data.expected_date">
      </div>
      <div>
        <label class="block text-sm mb-1">Status</label>
        <select class="border rounded w-full px-3 py-2" wire:model.defer="data.status">
          @foreach(\App\Enums\PurchaseOrderStatusEnum::cases() as $s)
            <option value="{{ $s->value }}">{{ $s->value }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm mb-1">Location (optional)</label>
        <input type="number" class="border rounded w-full px-3 py-2" wire:model.defer="data.location_id">
      </div>
    </div>

    <div class="border rounded p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Items</h3>
        <button class="border rounded px-3 py-2" wire:click.prevent="addItem">+ Add Item</button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full border text-sm">
          <thead><tr class="bg-gray-50">
            <th class="px-3 py-2">Item ID</th>
            <th class="px-3 py-2">Qty</th>
            <th class="px-3 py-2">Unit Price</th>
            <th class="px-3 py-2">Tax %</th>
            <th class="px-3 py-2">Discount</th>
            <th class="px-3 py-2">Actions</th>
          </tr></thead>
          <tbody>
            @foreach($items as $i => $it)
            <tr class="border-t">
              <td class="px-3 py-2"><input type="number" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.inventory_item_id"></td>
              <td class="px-3 py-2"><input type="number" step="0.001" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.qty"></td>
              <td class="px-3 py-2"><input type="number" step="0.01" class="border rounded px-2 py-1 w-28" wire:model.defer="items.{{ $i }}.unit_price"></td>
              <td class="px-3 py-2"><input type="number" step="0.01" class="border rounded px-2 py-1 w-20" wire:model.defer="items.{{ $i }}.tax_rate"></td>
              <td class="px-3 py-2"><input type="number" step="0.01" class="border rounded px-2 py-1 w-24" wire:model.defer="items.{{ $i }}.discount"></td>
              <td class="px-3 py-2">
                <button class="text-red-700" wire:click.prevent="removeItem({{ $i }})">Remove</button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div><b>Subtotal:</b> {{ number_format($data['subtotal'],2) }}</div>
      <div><b>Tax:</b> {{ number_format($data['tax_total'],2) }}</div>
      <div><b>Discount:</b> {{ number_format($data['discount_total'],2) }}</div>
      <div><b>Grand:</b> {{ number_format($data['grand_total'],2) }}</div>
    </div>

    <div class="pt-2">
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
      <a href="{{ route('purchaseorder.index') }}" class="px-4 py-2">Cancel</a>
    </div>
  </form>
</div>
