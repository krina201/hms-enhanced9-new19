<div>
    <h1 class="text-xl font-semibold mb-3">Admissions</h1>
    <div class="flex gap-2 mb-2">
        <input type="text" wire:model.debounce.300ms="q" placeholder="Search patient MRN/Name" class="border px-2 py-1 rounded">
        <select wire:model="status" class="border px-2 py-1 rounded">
            <option value="">All</option>
            @foreach($statuses as $s)<option value="{{ $s->value }}">{{ $s->value }}</option>@endforeach
        </select>
        <a href="{{ route('admissions.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">New</a>
    </div>
    <table class="w-full text-sm border">
        <thead><tr class="bg-gray-100"><th class="p-2">Patient</th><th class="p-2">Admit</th><th class="p-2">Discharge</th><th class="p-2">Ward/Bed</th><th class="p-2">Status</th><th></th></tr></thead>
        <tbody>@foreach($rows as $r)
            <tr class="border-b">
                <td class="p-2">{{ $r->patient?->mrn }} - {{ $r->patient?->first_name }}</td>
                <td class="p-2">{{ optional($r->admit_date)->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ optional($r->discharge_date)->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ $r->ward }} / {{ $r->bed }}</td>
                <td class="p-2">{{ $r->status?->value }}</td>
                <td class="p-2"><a class="text-blue-600" href="{{ route('admissions.show',$r->id) }}">Open</a></td>
            </tr>
        @endforeach</tbody>
    </table>
    <div class="mt-2">{{ $rows->links() }}</div>
</div>
