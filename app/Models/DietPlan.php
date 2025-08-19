<?php
/**
 * Eloquent model for diet plans for patients/visits.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\DietTypeEnum;
class DietPlan extends Model {
    protected $fillable=['patient_id','visit_id','type','notes'];
    protected $casts=['type'=>DietTypeEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
}