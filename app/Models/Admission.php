<?php
/**
 * Eloquent model for inpatient admissions with discharge fields.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\AdmissionStatusEnum;
class Admission extends Model {
    use SoftDeletes;
    protected $fillable=['patient_id','visit_id','admit_date','discharge_date','status','ward','bed','location_id','notes','diagnosis','procedures','instructions'];
    protected $casts=['admit_date'=>'datetime','discharge_date'=>'datetime','status'=>AdmissionStatusEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
}