<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsReceipt;

class GrnPrintController extends Controller
{
    public function __invoke($id)
    {
        $grn = GoodsReceipt::with('items.item','purchaseOrder.supplier')->findOrFail($id);
        $this->authorize('view', $grn);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.grn', ['grn' => $grn]);
            return $pdf->download('GRN-'.$grn->grn_no.'.pdf');
        }

        // Fallback: return printable HTML if dompdf isn't installed
        return response()->view('pdf.grn', ['grn' => $grn]);
    }
}
