<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PurchaseOrder extends Model {
    protected $fillable = ['order_no','supplier_id','order_date','expected_date','grand_total','status'];
    protected $casts = ['order_date'=>'date','expected_date'=>'date','grand_total'=>'decimal:2'];
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
}
