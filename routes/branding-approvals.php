<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PoPrintController;

Route::middleware(['web','auth','tenant'])->group(function() {
    Route::get('/purchase-orders/{id}/print', PoPrintController::class)->middleware('can:purchase_orders.print')->name('po.print');
});
