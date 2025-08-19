<?php
/**
 * Controller to render a printable PDF of a bill (DomPDF or HTML fallback).
 *
 * @package HMS
 */
namespace App\Http\Controllers;
use App\Models\Bill;

class BillPrintController
{
    public function __invoke(int $id)
    {
        $bill = Bill::with('patient','visit','items')->findOrFail($id);
        // authorize if you add a Policy: $this->authorize('print', $bill);
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bill', compact('bill'));
            return $pdf->stream("bill_{$bill->bill_no}.pdf");
        }
        return view('pdf.bill', compact('bill'));
    }
}