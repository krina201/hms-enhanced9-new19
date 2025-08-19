<div>
    <h1 class="text-xl font-semibold mb-3">Visits</h1>
    <div class="flex gap-2 mb-2">
        <input type="text" wire:model.debounce.300ms="q" placeholder="Search patient MRN/Name" class="border px-2 py-1 rounded">
        <select wire:model="type" class="border px-2 py-1 rounded">
            <option value="">All</option>
            @foreach(\App\Enums\VisitTypeEnum::cases() as $t)
                <option value="{{ $t->value }}">{{ $t->value }}</option>
            @endforeach
        </select>
        <a href="{{ route('visits.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">New</a>
    </div>
    <table class="w-full text-sm border">
        <thead><tr class="bg-gray-100">
            <th class="p-2">Visit#</th><th class="p-2">Date</th><th class="p-2">Type</th><th class="p-2">Patient</th><th class="p-2">Dept</th><th></th>
        </tr></thead>
        <tbody>
            @foreach($rows as $r)
            <tr class="border-b">
                <td class="p-2">{{ $r->visit_no }}</td>
                <td class="p-2">{{ optional($r->visit_date)->format('Y-m-d H:i') }}</td>
                <td class="p-2">{{ $r->type?->value }}</td>
                <td class="p-2">{{ $r->patient?->mrn }} - {{ $r->patient?->first_name }}</td>
                <td class="p-2">{{ $r->department }}</td>
                <td class="p-2"><a href="{{ route('visits.edit',$r->id) }}" class="text-blue-600">Edit</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-2">{{ $rows->links() }}</div>
</div>
