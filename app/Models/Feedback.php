<?php
/**
 * Eloquent model for patient feedback and ratings.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FeedbackRatingEnum;
class Feedback extends Model {
    protected $fillable=['patient_id','visit_id','rating','comments'];
    protected $casts=['rating'=>FeedbackRatingEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
}