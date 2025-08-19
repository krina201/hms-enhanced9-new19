<?php
/**
 * Eloquent model for bill line items (qty, price, tax, discount).
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BillItem extends Model {
    protected $fillable=['bill_id','description','qty','unit_price','tax_rate','discount'];
    protected $casts=['qty'=>'decimal:3','unit_price'=>'decimal:2','tax_rate'=>'decimal:2','discount'=>'decimal:2'];
    public function bill(): BelongsTo { return $this->belongsTo(Bill::class); }
}