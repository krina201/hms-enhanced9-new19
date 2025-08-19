<?php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\GoodsReceiptPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        GoodsReceipt::class => GoodsReceiptPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
        // Optional: Super Admin gate shortcut (assumes Spatie):
        Gate::before(function ($user, $ability) {
            return method_exists($user,'hasRole') && $user->hasRole('Super Admin') ? true : null;
        });
    }
}
