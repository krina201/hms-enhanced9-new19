<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequisitionApprovalEvent extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'requisition_approval_id', 'action', 'actor_id', 'note', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function approval(): BelongsTo { return $this->belongsTo(RequisitionApproval::class, 'requisition_approval_id'); }
}
