<?php
/**
 * Eloquent model for itemized bills and financial status.
 *
 * @package HMS
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\BillStatusEnum;
class Bill extends Model {
    /**
     * Recalculate bill status from total payments.
     *
     * Sums payments and compares against grand_total with 2-decimal precision
     * (uses bccomp when available; otherwise a rounded numeric compare).
     *
     * @return void
     */
public function recalcStatusFromPayments(): void {
        $paid = (float)($this->payments()->sum('amount'));
        $total = (float)$this->grand_total;
        $cmp = function_exists('bccomp') ? bccomp((string)$paid, (string)$total, 2) : (round($paid,2) <=> round($total,2));
        $status = $paid <= 0 ? \App\Enums\BillStatusEnum::ISSUED : ($cmp >= 0 ? \App\Enums\BillStatusEnum::PAID : \App\Enums\BillStatusEnum::PARTIAL);
        $this->update(['status'=>$status]);
    }

    use SoftDeletes;
    protected $fillable=['bill_no','patient_id','visit_id','bill_date','subtotal','tax','discount','grand_total','status'];
    protected $casts=['bill_date'=>'datetime','subtotal'=>'decimal:2','tax'=>'decimal:2','discount'=>'decimal:2','grand_total'=>'decimal:2','status'=>BillStatusEnum::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
    public function items(): HasMany { return $this->hasMany(BillItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}