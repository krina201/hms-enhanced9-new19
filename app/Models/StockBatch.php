<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StockBatch extends Model {
    protected $fillable=['inventory_item_id','location_id','batch_no','expiry_date','qty_on_hand'];
    protected $casts=['expiry_date'=>'date','qty_on_hand'=>'decimal:3'];
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class,'inventory_item_id'); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
}
