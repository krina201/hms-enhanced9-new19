<?php
use Illuminate\Support\Facades\Route;
use App\Livewire\PurchaseOrder\Receive as PurchaseOrderReceive;

// Tenant middleware group assumed
Route::middleware(['web','auth','tenant'])->group(function() {
    Route::get('/purchase-orders/{id}/receive', PurchaseOrderReceive::class)->name('purchaseorder.receive');
});
