<?php
use Illuminate\Support\Facades\Route;

use App\Livewire\Grn\Index as GrnIndex;
use App\Livewire\Grn\Show as GrnShow;
use App\Http\Controllers\GrnPrintController;
use App\Livewire\PurchaseOrder\Receive as PurchaseOrderReceive;
use App\Livewire\Dashboard\InventoryWidgets;

Route::middleware(['web','auth','tenant'])->group(function() {
    Route::get('/grn', GrnIndex::class)->name('grn.index');
    Route::get('/grn/{id}', GrnShow::class)->name('grn.show');
    Route::get('/grn/{id}/print', GrnPrintController::class)->middleware('can:grn.view')->name('grn.print');

    Route::get('/purchase-orders/{id}/receive', PurchaseOrderReceive::class)->name('purchaseorder.receive');

    Route::get('/dashboard/inventory', InventoryWidgets::class)->name('dashboard.inventory');
});
