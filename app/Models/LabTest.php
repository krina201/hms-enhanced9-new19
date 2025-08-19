<?php
/**
 * Eloquent model for lab tests and their status lifecycle.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\LabTestStatusEnum;
class LabTest extends Model {
    protected $fillable=['patient_id','visit_id','test_name','status','ordered_at','completed_at','result_path'];
    protected $casts=['ordered_at'=>'datetime','completed_at'=>'datetime','status'=>LabTestStatusEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
}