<div class="max-w-3xl">
    <div class="flex justify-between items-center mb-3">
        <h1 class="text-xl font-semibold">Visit {{ $visit->visit_no }}</h1>
        <a class="px-3 py-1 bg-blue-600 text-white rounded" href="{{ route('visits.edit',$visit->id) }}">Edit</a>
    </div>
    <div class="grid grid-cols-2 gap-2">
        <div class="p-3 border rounded">
            <div><strong>Patient:</strong> {{ $visit->patient?->mrn }} - {{ $visit->patient?->first_name }}</div>
            <div><strong>Date:</strong> {{ optional($visit->visit_date)->format('Y-m-d H:i') }}</div>
        </div>
        <div class="p-3 border rounded">
            <div><strong>Type:</strong> {{ $visit->type?->value }}</div>
            <div><strong>Dept:</strong> {{ $visit->department }}</div>
        </div>
    </div>
    <div class="mt-4">
        @livewire('attachments.manager', ['attachableType'=>get_class($visit), 'attachableId'=>$visit->id], key('vis-att-'.$visit->id))
    </div>
</div>
