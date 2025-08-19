<div class="max-w-3xl">
    <h1 class="text-xl font-semibold mb-3">{{ $admission ? 'Edit' : 'Create' }} Admission</h1>
    <form wire:submit.prevent="save" class="space-y-3">
        <div class="grid grid-cols-2 gap-2">
            <div><label class="block text-xs">Patient</label>
                <select wire:model="data.patient_id" class="border px-2 py-1 rounded w-full">
                    <option value="">--</option>@foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->mrn }} - {{ $p->first_name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs">Visit</label>
                <select wire:model="data.visit_id" class="border px-2 py-1 rounded w-full">
                    <option value="">--</option>@foreach($visits as $v)<option value="{{ $v->id }}">{{ $v->visit_no }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs">Admit Date</label><input type="datetime-local" wire:model="data.admit_date" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Discharge Date</label><input type="datetime-local" wire:model="data.discharge_date" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Status</label>
                <select wire:model="data.status" class="border px-2 py-1 rounded w-full">
                    @foreach(\App\Enums\AdmissionStatusEnum::cases() as $s)<option value="{{ $s->value }}">{{ $s->value }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-xs">Ward</label><input type="text" wire:model="data.ward" class="border px-2 py-1 rounded w-full"></div>
            <div><label class="block text-xs">Bed</label><input type="text" wire:model="data.bed" class="border px-2 py-1 rounded w-full"></div>
            <div class="col-span-2"><label class="block text-xs">Notes</label><textarea wire:model="data.notes" class="border px-2 py-1 rounded w-full"></textarea></div>
        <div class="col-span-2"><label class="block text-xs">Diagnosis</label><textarea wire:model="data.diagnosis" class="border px-2 py-1 rounded w-full"></textarea></div>
            <div class="col-span-2"><label class="block text-xs">Procedures</label><textarea wire:model="data.procedures" class="border px-2 py-1 rounded w-full"></textarea></div>
            <div class="col-span-2"><label class="block text-xs">Discharge Instructions</label><textarea wire:model="data.instructions" class="border px-2 py-1 rounded w-full"></textarea></div>
        </div>
        @error('form')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        <div class="mt-2 flex gap-2">
            <button class="px-3 py-1 bg-blue-600 text-white rounded">Save</button>
            @if($admission)
                @can("admissions.delete")<button type="button" onclick="if(confirm('Delete?')){ Livewire.find('{{ $this->id }}').call('delete') }" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>@endcan
                @can("admissions.restore")<button type="button" onclick="if(confirm('Restore?')){ Livewire.find('{{ $this->id }}').call('restore') }" class="px-3 py-1 bg-amber-600 text-white rounded">Restore</button>@endcan
                @can("admissions.force_delete")<button type="button" onclick="if(confirm('Force delete?')){ Livewire.find('{{ $this->id }}').call('forceDelete') }" class="px-3 py-1 bg-gray-700 text-white rounded">Force Delete</button>@endcan
            @endif
        </div>
    </form>
</div>
