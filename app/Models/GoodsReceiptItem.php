<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id','purchase_order_item_id','inventory_item_id',
        'received_qty','batch_no','expiry_date','stock_batch_id','unit_price'
    ];

    protected $casts = ['expiry_date' => 'date', 'received_qty' => 'decimal:3', 'unit_price' => 'decimal:2'];

    public function grn(): BelongsTo { return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id'); }
    public function purchaseOrderItem(): BelongsTo { return $this->belongsTo(PurchaseOrderItem::class); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function stockBatch(): BelongsTo { return $this->belongsTo(StockBatch::class, 'stock_batch_id'); }
}
