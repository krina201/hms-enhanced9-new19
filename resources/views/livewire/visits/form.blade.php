<div class="max-w-2xl">
    <h1 class="text-xl font-semibold mb-3">{{ $visit ? 'Edit' : 'Create' }} Visit</h1>
    <form wire:submit.prevent="save" class="space-y-3">
        <div class="grid grid-cols-2 gap-2">
            <div><label class="block text-xs">Patient</label>
                <select wire:model="data.patient_id" class="border px-2 py-1 rounded w-full">
                    <option value="">--select--</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->mrn }} - {{ $p->first_name }} {{ $p->last_name }}</option>
                    @endforeach
                </select>
                @error('data.patient_id')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror
            </div>
            <div><label class="block text-xs">Visit #</label><input type="text" wire:model="data.visit_no" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Visit Date</label><input type="datetime-local" wire:model="data.visit_date" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Type</label>
                <select wire:model="data.type" class="border px-2 py-1 rounded w-full">
                    @foreach(\App\Enums\VisitTypeEnum::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2"><label class="block text-xs">Chief Complaint</label><textarea wire:model="data.chief_complaint" class="border px-2 py-1 rounded w-full"></textarea></div>
        </div>
        @error('form')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        <div class="mt-2"><button class="px-3 py-1 bg-blue-600 text-white rounded">Save</button></div>
    @if($patient ?? false)
    <div class="mt-3 flex gap-2">
        <button type="button" onclick="if(confirm('Delete?')){ Livewire.find('{ $this->id }').call('delete') }" @cannot('visits.delete') disabled @endcannot" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
        <button type="button" onclick="if(confirm('Restore?')){ Livewire.find('{ $this->id }').call('restore') }" @cannot('visits.restore') disabled @endcannot" class="px-3 py-1 bg-amber-600 text-white rounded">Restore</button>
        <button type="button" onclick="if(confirm('Force delete?')){ Livewire.find('{ $this->id }').call('forceDelete') }" @cannot('visits.force_delete') disabled @endcannot" class="px-3 py-1 bg-gray-700 text-white rounded">Force Delete</button>
    </div>
    @endif
</form>
</div>


@if($visit)
<div class="mt-6">
    @livewire('attachments.manager', ['attachableType'=>get_class($visit), 'attachableId'=>$visit->id], key('vis-att-'.$visit->id))
</div>
@endif
