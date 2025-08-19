<?php
/**
 * Eloquent model for OPD/IPD visits linked to a patient.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\VisitTypeEnum;
class Visit extends Model {
    use SoftDeletes;
    protected $fillable=['patient_id','visit_no','visit_date','type','doctor_id','department','location_id','chief_complaint'];
    protected $casts=['visit_date'=>'datetime','type'=>VisitTypeEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
}