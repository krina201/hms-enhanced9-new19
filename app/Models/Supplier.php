<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Supplier extends Model {
    protected $fillable = ['name'];
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
}
