<div>
    <h1 class="text-xl font-semibold mb-3">Patients</h1>
    <div class="flex gap-2 mb-2">
        <input type="text" wire:model.debounce.300ms="q" placeholder="Search MRN/Name/Phone" class="border px-2 py-1 rounded">
        <a href="{{ route('patients.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">New</a>
        <button wire:click="exportCsv" class="px-3 py-1 bg-emerald-600 text-white rounded">Export CSV</button>
    </div>
    <table class="w-full text-sm border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">MRN</th><th class="p-2">Name</th><th class="p-2">DOB</th><th class="p-2">Gender</th><th class="p-2">Phone</th><th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr class="border-b">
                <td class="p-2">{{ $r->mrn }}</td>
                <td class="p-2">{{ $r->first_name }} {{ $r->last_name }}</td>
                <td class="p-2">{{ optional($r->dob)->format('Y-m-d') }}</td>
                <td class="p-2">{{ $r->gender?->value }}</td>
                <td class="p-2">{{ $r->phone }}</td>
                <td class="p-2">
                    @can('update', $r)
                    <a href="{{ route('patients.edit',$r->id) }}" class="text-blue-600">Edit</a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-2">{{ $rows->links() }}</div>
</div>
