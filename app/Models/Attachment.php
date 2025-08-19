<?php
/**
 * Eloquent model for secure, polymorphic attachments.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class Attachment extends Model {
    protected $fillable=['attachable_type','attachable_id','path','original_name','mime','size'];
    public function attachable(): MorphTo { return $this->morphTo(); }
}