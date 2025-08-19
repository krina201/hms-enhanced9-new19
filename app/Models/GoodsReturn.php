<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class GoodsReturn extends Model {
    protected $fillable = ['return_no','return_date','location_id','reason','created_by'];
    protected $casts = ['return_date'=>'date'];
    public function items(): HasMany { return $this->hasMany(GoodsReturnItem::class); }
}
