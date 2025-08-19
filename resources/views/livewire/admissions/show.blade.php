<div class="max-w-4xl">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-xl font-semibold">Admission #{{ $admission->id }}</h1>
        <div class="flex gap-2">
            <a class="px-3 py-1 bg-blue-600 text-white rounded" href="{{ route('admissions.edit',$admission->id) }}">Edit</a>
            <a class="px-3 py-1 bg-emerald-600 text-white rounded" href="{{ route('admissions.discharge',$admission->id) }}" target="_blank">Discharge Summary PDF</a>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div class="p-3 border rounded">
            <div class="font-semibold mb-1">Patient</div>
            <div>MRN: {{ $admission->patient?->mrn }}</div>
            <div>Name: {{ $admission->patient?->first_name }} {{ $admission->patient?->last_name }}</div>
            <div>Visit: {{ $admission->visit?->visit_no }}</div>
        </div>
        <div class="p-3 border rounded">
            <div class="font-semibold mb-1">Admission</div>
            <div>Admit: {{ optional($admission->admit_date)->format('Y-m-d H:i') }}</div>
            <div>Discharge: {{ optional($admission->discharge_date)->format('Y-m-d H:i') }}</div>
            <div>Status: {{ $admission->status?->value }}</div>
            <div>Ward/Bed: {{ $admission->ward }} / {{ $admission->bed }}</div>
        </div>
    </div>
    <div class="mt-4">
        @livewire('attachments.manager', ['attachableType'=>get_class($admission), 'attachableId'=>$admission->id], key('adm-att-'.$admission->id))
    </div>
</div>
