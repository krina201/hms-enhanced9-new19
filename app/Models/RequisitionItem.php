<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequisitionItem extends TenantModel
{
    use HasFactory;

    protected $fillable = ['requisition_id','inventory_item_id','qty','notes'];
    protected $casts = ['qty'=>'decimal:3'];

    public function requisition(): BelongsTo { return $this->belongsTo(Requisition::class); }
    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
}
