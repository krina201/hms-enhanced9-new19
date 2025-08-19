<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ApprovalWorkflow extends Model {
    protected $fillable=['name','applies_to','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function steps(): HasMany { return $this->hasMany(ApprovalStep::class); }
}
