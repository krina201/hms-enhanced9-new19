<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Requisition extends Model {
    protected $fillable=['req_no','req_date','supplier_id','status','workflow_id','requested_by'];
    protected $casts=['req_date'=>'date'];
    public function approvals(): HasMany { return $this->hasMany(RequisitionApproval::class); }
}
