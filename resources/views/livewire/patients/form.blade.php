<div class="max-w-2xl">
    <h1 class="text-xl font-semibold mb-3">{{ $patient ? 'Edit' : 'Create' }} Patient</h1>
    <form wire:submit.prevent="save" class="space-y-3">
        <div class="grid grid-cols-2 gap-2">
            <div><label class="block text-xs">MRN</label><input type="text" wire:model="data.mrn" class="border px-2 py-1 rounded w-full">@error('data.mrn')<div class="text-red-600 text-xs">{{ $message }}</div>@enderror</div>
            <div><label class="block text-xs">DOB</label><input type="date" wire:model="data.dob" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">First name</label><input type="text" wire:model="data.first_name" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Last name</label><input type="text" wire:model="data.last_name" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Gender</label>
                <select wire:model="data.gender" class="border px-2 py-1 rounded w-full">
                    <option value="">--</option>
                    @foreach(\App\Enums\PatientGenderEnum::cases() as $g)
                        <option value="{{ $g->value }}">{{ $g->value }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs">Phone</label><input type="text" wire:model="data.phone" class="border px-2 py-1 rounded w-full"></div>
            <div class="col-span-2"><label class="block text-xs">Email</label><input type="email" wire:model="data.email" class="border px-2 py-1 rounded w-full"></div>
            <div class="col-span-2"><label class="block text-xs">Address</label><textarea wire:model="data.address" class="border px-2 py-1 rounded w-full"></textarea></div>
        </div>
        @error('form')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        <div class="mt-2"><button class="px-3 py-1 bg-blue-600 text-white rounded">Save</button></div>
    @if($patient ?? false)
    <div class="mt-3 flex gap-2">
        <button type="button" onclick="if(confirm('Delete?')){ Livewire.find('{ $this->id }').call('delete') }" @cannot('patients.delete') disabled @endcannot" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
        <button type="button" onclick="if(confirm('Restore?')){ Livewire.find('{ $this->id }').call('restore') }" @cannot('patients.restore') disabled @endcannot" class="px-3 py-1 bg-amber-600 text-white rounded">Restore</button>
        <button type="button" onclick="if(confirm('Force delete?')){ Livewire.find('{ $this->id }').call('forceDelete') }" @cannot('patients.force_delete') disabled @endcannot" class="px-3 py-1 bg-gray-700 text-white rounded">Force Delete</button>
    </div>
    @endif
</form>
</div>


@if($patient)
<div class="mt-6">
    @livewire('attachments.manager', ['attachableType'=>get_class($patient), 'attachableId'=>$patient->id], key('pat-att-'.$patient->id))
</div>
@endif
