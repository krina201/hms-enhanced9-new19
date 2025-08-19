<?php
namespace App\Policies;
use App\Models\User;
use App\Models\GoodsReceipt;
class GoodsReceiptPolicy
{
    public function view(User $user, GoodsReceipt $grn): bool { return $user->can('grn.view'); }
    public function export(User $user): bool { return $user->can('grn.export'); }
}
