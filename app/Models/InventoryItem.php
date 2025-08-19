<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class InventoryItem extends Model {
    protected $fillable = ['name','unit','reorder_level'];
    public function batches(): HasMany { return $this->hasMany(StockBatch::class); }
}
