<?php
/**
 * Eloquent model for clinical prescriptions associated with a visit.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\PrescriptionTypeEnum;
class Prescription extends Model {
    protected $fillable=['patient_id','visit_id','type','text'];
    protected $casts=['type'=>PrescriptionTypeEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
}