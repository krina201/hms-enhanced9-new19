<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Ticket extends Model {
    protected $fillable=['subject','description','status','created_by','assigned_to','sla_hours','due_at','closed_at'];
    protected $casts=['due_at'=>'datetime','closed_at'=>'datetime'];
    public function comments(): HasMany { return $this->hasMany(TicketComment::class); }
}
