<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GoodsReturnItem extends Model {
    protected $fillable=['goods_return_id','stock_batch_id','inventory_item_id','qty'];
    protected $casts = ['qty'=>'decimal:3'];
    public function goodsReturn(): BelongsTo { return $this->belongsTo(GoodsReturn::class); }
    public function batch(): BelongsTo { return $this->belongsTo(StockBatch::class,'stock_batch_id'); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class,'inventory_item_id'); }
}
