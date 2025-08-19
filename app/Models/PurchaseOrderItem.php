<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseOrderItem extends Model {
    protected $fillable = ['purchase_order_id','inventory_item_id','qty','unit_price','tax_rate','discount'];
    protected $casts = ['qty'=>'decimal:3','unit_price'=>'decimal:2','tax_rate'=>'decimal:2','discount'=>'decimal:2'];
    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class,'inventory_item_id'); }
}
