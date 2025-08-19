<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockMovement extends Model {
    protected $fillable=['inventory_item_id','stock_batch_id','location_id','type','qty','ref_type','ref_id','meta'];
    protected $casts=['qty'=>'decimal:3','meta'=>'array'];
}
