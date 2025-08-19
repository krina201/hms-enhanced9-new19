<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ApprovalStep extends Model {
    protected $fillable=['approval_workflow_id','level','role_name','sla_hours'];
    public function workflow(): BelongsTo { return $this->belongsTo(ApprovalWorkflow::class,'approval_workflow_id'); }
}
