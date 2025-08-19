<?php
/**
 * Eloquent model for payments applied to a bill.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\PaymentMethodEnum;
class Payment extends Model {
    /**
     * Model boot hook to keep Bill status in sync when payments change.
     *
     * Triggers bill->recalcStatusFromPayments() on saved/deleted events.
     *
     * @return void
     */
protected static function booted() { static::saved(function($p){ $p->bill?->recalcStatusFromPayments(); }); static::deleted(function($p){ $p->bill?->recalcStatusFromPayments(); }); }

    protected $fillable=['bill_id','payment_date','amount','method','reference'];
    protected $casts=['payment_date'=>'datetime','amount'=>'decimal:2','method'=>PaymentMethodEnum::class];
    public function bill(): BelongsTo { return $this->belongsTo(Bill::class); }
}