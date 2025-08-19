<?php
/**
 * Livewire component for file attachments (upload/preview/delete).
 *
 * @package HMS
 */
namespace App\Livewire\Attachments;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class Manager extends Component
{
    use WithFileUploads;
    public string $attachableType; public int $attachableId;
    public $files = [];

    protected $rules = ['files.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx']; // 5MB

    public function upload()
    {
        $this->validate();
        abort_unless(auth()->user()->can('attachments.edit') || auth()->user()->can('admissions.edit') || auth()->user()->can('billing.edit'), 403);
        foreach ($this->files as $f) {
            $path = $f->store('attachments', 'local');
            Attachment::create([
                'attachable_type' => $this->attachableType,
                'attachable_id' => $this->attachableId,
                'path' => $path,
                'original_name' => $f->getClientOriginalName(),
                'mime' => $f->getMimeType(),
                'size' => $f->getSize(),
            ]);
        }
        $this->reset('files');
    }

    public function delete(int $id)
    {
        $att = Attachment::findOrFail($id);
        abort_unless($att->attachable_type === $this->attachableType && $att->attachable_id === $this->attachableId, 403);
        abort_unless(auth()->user()->can('attachments.edit') || auth()->user()->can('admissions.edit') || auth()->user()->can('billing.edit'), 403);
        Storage::disk('public')->delete($att->path);
        $att->delete();
    }

    public function getListProperty()
    {
        return Attachment::where('attachable_type',$this->attachableType)->where('attachable_id',$this->attachableId)->latest()->get();
    }

    public function render() { return view('livewire.attachments.manager'); }
}