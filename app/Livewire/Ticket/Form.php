<?php
namespace App\Livewire\Ticket;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Enums\TicketStatusEnum;
use App\Enums\PriorityEnum;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class Form extends Component
{
    public ?int $id = null;
    public array $data = [
        'subject' => '',
        'description' => '',
        'status' => '',
        'priority' => '',
        'module' => '',
        'reference_id' => null,
        'opened_by' => null,
        'assigned_to' => null,
        'location_id' => null,
    ];

    public string $newComment = '';
    public array $statuses = [];
    public array $priorities = [];

    protected function rules() {
        return [
            'data.subject' => 'required|string|max:160',
            'data.description' => 'nullable|string|max:1200',
            'data.status' => 'required|string|max:40',
            'data.priority' => 'required|string|max:40',
            'data.module' => 'nullable|string|max:60',
            'data.reference_id' => 'nullable|integer',
            'data.opened_by' => 'required|integer',
            'data.assigned_to' => 'nullable|integer',
            'data.location_id' => 'nullable|integer',
        ];
    }

    public function mount($id = null)
    {
        abort_unless(auth()->user()?->can('tickets.edit') ?? false, 403);

        $this->id = $id;
        $this->statuses = array_map(fn($c) => $c->value, TicketStatusEnum::cases());
        $this->priorities = array_map(fn($c) => $c->value, PriorityEnum::cases());

        $this->data['opened_by'] = Auth::id();

        if ($id) {
            $m = Ticket::findOrFail($id);
            $this->data = array_merge($this->data, $m->toArray());
        } else {
            $this->data['status'] = TicketStatusEnum::OPEN->value;
            $this->data['priority'] = PriorityEnum::MEDIUM->value;
        }
    }

    public function save(ActivityLogger $log)
    {
        abort_unless(auth()->user()?->can('tickets.edit') ?? false, 403);

        $validated = $this->validate()['data'];
        $m = Ticket::updateOrCreate(['id' => $this->id], $validated);
        $this->id = $m->id;

        $log->log('tickets', 'save', $m->id, []);

        session()->flash('success', 'Ticket saved.');
        return redirect()->route('ticket.edit', $this->id);
    }

    public function addComment(ActivityLogger $log)
    {
        abort_unless(auth()->user()?->can('tickets.comment') ?? false, 403);

        $this->validate([ 'newComment' => 'required|string|min:1|max:2000' ]);
        $ticket = Ticket::findOrFail($this->id);
        $c = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $this->newComment,
        ]);
        $log->log('tickets', 'comment', $ticket->id, ['comment_id' => $c->id]);
        $this->newComment = '';
    }

    public function deleteComment($commentId, ActivityLogger $log)
    {
        $comment = TicketComment::findOrFail($commentId);
        abort_unless(auth()->user()?->can('tickets.delete_comment') || $comment->user_id === auth()->id(), 403);
        $comment->delete();
        $log->log('tickets', 'comment_delete', $this->id, ['comment_id' => $commentId]);
    }

    public function getCommentsProperty()
    {
        if (!$this->id) return collect();
        return TicketComment::where('ticket_id', $this->id)->latest()->get();
    }

    public function render()
    {
        return view('livewire.ticket.form');
    }
}
