<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ApprovalStatusEnum;
class RequisitionApproval extends Model {
    protected $fillable=['requisition_id','approval_step_id','status','approved_by','approved_at','note'];
    protected $casts=['approved_at'=>'datetime','status'=>ApprovalStatusEnum::class];
    public function step(): BelongsTo { return $this->belongsTo(ApprovalStep::class,'approval_step_id'); }
    public function requisition(): BelongsTo { return $this->belongsTo(Requisition::class); }
}
