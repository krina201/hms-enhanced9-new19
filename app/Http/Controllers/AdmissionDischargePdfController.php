<?php
/**
 * Controller to render discharge summary PDF or HTML fallback.
 *
 * @package HMS
 */
namespace App\Http\Controllers;
use App\Models\Admission;

class AdmissionDischargePdfController
{
    public function __invoke(int $id)
    {
        $admission = Admission::with('patient','visit')->findOrFail($id);
        abort_unless(auth()->user()->can('admissions.view'), 403);
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.discharge', compact('admission'));
            return $pdf->stream("discharge_{$admission->id}.pdf");
        }
        return view('pdf.discharge', compact('admission'));
    }
}