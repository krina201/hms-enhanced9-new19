<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GoodsReceipt extends Model {
    protected $fillable = ['grn_no','purchase_order_id','location_id','grn_date','received_by','posted_at'];
    protected $casts = ['grn_date'=>'date','posted_at'=>'datetime'];
    public function items(): HasMany { return $this->hasMany(GoodsReceiptItem::class); }
    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
}
