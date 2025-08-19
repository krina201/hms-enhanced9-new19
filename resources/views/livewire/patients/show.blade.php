<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-xl font-semibold">Patient {{ $patient->mrn }}</h1>
        <a class="px-3 py-1 bg-blue-600 text-white rounded" href="{{ route('patients.edit',$patient->id) }}">Edit</a>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div class="p-3 border rounded">
            <div><strong>Name:</strong> {{ $patient->first_name }} {{ $patient->last_name }}</div>
            <div><strong>DOB:</strong> {{ optional($patient->dob)->format('Y-m-d') }}</div>
            <div><strong>Gender:</strong> {{ $patient->gender?->value }}</div>
        </div>
        <div class="p-3 border rounded">
            <div><strong>Phone:</strong> {{ $patient->phone }}</div>
            <div><strong>Email:</strong> {{ $patient->email }}</div>
        </div>
    </div>
    <div class="mt-4">
        @livewire('attachments.manager', ['attachableType'=>get_class($patient), 'attachableId'=>$patient->id], key('pat-att-'.$patient->id))
    </div>
</div>
