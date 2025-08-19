<?php
use Illuminate\Support\Facades\Route;

use App\Livewire\Grn\Index as GrnIndex;
use App\Livewire\Grn\Show as GrnShow;
use App\Http\Controllers\GrnPrintController;
use App\Http\Controllers\BatchLabelsController;
use App\Livewire\Reports\StockAging;
use App\Livewire\Returns\Index as ReturnsIndex;
use App\Livewire\Returns\Create as ReturnsCreate;
use App\Livewire\Requisition\Index as RequisitionIndex;
use App\Livewire\Requisition\Form as RequisitionForm;

Route::middleware(['web','auth','tenant'])->group(function() {
    Route::get('/grn', GrnIndex::class)->name('grn.index');
    Route::get('/grn/{id}', GrnShow::class)->name('grn.show');
    Route::get('/grn/{id}/print', GrnPrintController::class)->middleware('can:grn.view')->name('grn.print');
    Route::get('/batches/labels', BatchLabelsController::class)->middleware('can:labels.print')->name('batches.labels');

    Route::get('/reports/stock-aging', StockAging::class)->name('reports.stock_aging');

    Route::get('/returns', ReturnsIndex::class)->name('returns.index');
    Route::get('/returns/create', ReturnsCreate::class)->name('returns.create');

    Route::get('/requisitions', RequisitionIndex::class)->name('requisition.index');
    Route::get('/requisitions/create', RequisitionForm::class)->name('requisition.create');
    Route::get('/requisitions/{id}/edit', RequisitionForm::class)->name('requisition.edit');
});


use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Patients\Form as PatientsForm;
use App\Livewire\Visits\Index as VisitsIndex;
use App\Livewire\Visits\Form as VisitsForm;
use App\Livewire\Bills\Index as BillsIndex;
use App\Livewire\Bills\Form as BillsForm;
use App\Http\Controllers\BillPrintController;

Route::middleware(['web','auth','tenant'])->group(function () {
    // Patients
    Route::get('/patients', PatientsIndex::class)->middleware('can:patients.view')->name('patients.index');
    Route::get('/patients/create', PatientsForm::class)->middleware('can:patients.create')->name('patients.create');
    Route::get('/patients/{id}/edit', PatientsForm::class)->middleware('can:patients.edit')->name('patients.edit');

    // Visits
    Route::get('/visits', VisitsIndex::class)->middleware('can:visits.view')->name('visits.index');
    Route::get('/visits/create', VisitsForm::class)->middleware('can:visits.create')->name('visits.create');
    Route::get('/visits/{id}/edit', VisitsForm::class)->middleware('can:visits.edit')->name('visits.edit');

    // Bills
    Route::get('/bills', BillsIndex::class)->middleware('can:billing.view')->name('bills.index');
    Route::get('/bills/create', BillsForm::class)->middleware('can:billing.create')->name('bills.create');
    Route::get('/bills/{id}/edit', BillsForm::class)->middleware('can:billing.edit')->name('bills.edit');
    Route::get('/bills/{id}/print', BillPrintController::class)->middleware('can:billing.view')->name('bills.print');
});

use App\Livewire\Dashboard\OpdIpd;
Route::middleware(['web','auth','tenant'])->group(function () {
    Route::get('/dashboard/opd-ipd', OpdIpd::class)->middleware('can:opd.view')->name('dashboard.opd_ipd');
});


use App\Livewire\Patients\Show as PatientsShow;
use App\Livewire\Visits\Show as VisitsShow;
use App\Livewire\Bills\Show as BillsShow;

Route::middleware(['web','auth','tenant'])->group(function () {
    Route::get('/patients/{id}', PatientsShow::class)->middleware('can:patients.view')->name('patients.show');
    Route::get('/visits/{id}', VisitsShow::class)->middleware('can:visits.view')->name('visits.show');
    Route::get('/bills/{id}', BillsShow::class)->middleware('can:billing.view')->name('bills.show');
});


use App\Http\Controllers\AttachmentController;
Route::middleware(['web','auth','tenant'])->group(function () {
    Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::get('/attachments/{id}/download', [AttachmentController::class, 'download'])->name('attachments.download');
});
