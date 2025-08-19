<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TicketComment extends Model {
    protected $fillable=['ticket_id','user_id','body'];
    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
}
