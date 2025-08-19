<div class="p-3 border rounded">
    <h3 class="font-semibold mb-2">Attachments</h3>
    <div class="flex gap-2 items-center mb-2">
        <input type="file" wire:model="files" multiple class="border px-2 py-1 rounded">
        <button wire:click="upload" class="px-3 py-1 bg-blue-600 text-white rounded">Upload</button>
        <div wire:loading wire:target="files" class="text-xs text-gray-600">Uploading...</div>
    </div>
    <div class="grid grid-cols-3 gap-2">
        @foreach($this->list as $f)
        <div class="border rounded p-2">
            <div class="text-xs break-all">{{ $f->original_name }}</div>
            @if(\Illuminate\Support\Str::contains($f->mime, 'image'))
                <img src="{{ route('attachments.show', $f->id) }}" class="mt-1 max-h-40 object-contain">
            @elseif(\Illuminate\Support\Str::contains($f->mime, 'pdf'))
                <iframe src="{{ route('attachments.show', $f->id) }}" class="mt-1 w-full h-40"></iframe>
            @else
                <a href="{{ route('attachments.show', $f->id) }}" target="_blank" class="text-blue-600">Download</a>
            @endif
            <div class="mt-1">
                <button class="text-red-600 text-xs" wire:click="delete({{ $f->id }})">Delete</button>
            </div>
            @elseif(\Illuminate\Support\Str::contains($f->mime,'text') || \Illuminate\Support\Str::contains($f->mime,'word') || \Illuminate\Support\Str::contains($f->mime,'excel') || \Illuminate\Support\Str::contains($f->mime,'officedocument'))
                <div class="h-40 w-full bg-gray-100 flex items-center justify-center text-xs text-gray-600">Document</div>
                <a href="{{ route('attachments.show', $f->id) }}" target="_blank" class="text-blue-600">Open</a>
            @else
                <div class="h-40 w-full bg-gray-100 flex items-center justify-center text-xs text-gray-600">File</div>
                <a href="{{ route('attachments.show', $f->id) }}" target="_blank" class="text-blue-600">Download</a>
            @endif
            <div class="mt-1">
                <button class="text-red-600 text-xs" wire:click="delete({{ $f->id }})">Delete</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
