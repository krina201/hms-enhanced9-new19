<?php
namespace App\Policies;
use App\Models\User;
use App\Models\PurchaseOrder;
class PurchaseOrderPolicy
{
    public function view(User $user, PurchaseOrder $po): bool { return $user->can('purchase_orders.view'); }
    public function print(User $user, PurchaseOrder $po): bool { return $user->can('purchase_orders.print') || $user->can('purchase_orders.print'); }
}
