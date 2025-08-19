<?php
namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Support\Branding;

class PoPrintController extends Controller
{
    public function __invoke($id)
    {
        $po = PurchaseOrder::with('items.item','supplier')->findOrFail($id);
        $this->authorize('print', $po);
        $branding = Branding::data();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.po', compact('po','branding'))->setPaper('a4','portrait');
            return $pdf->download('PO-'.$po->order_no.'.pdf');
        }

        return response()->view('pdf.po', compact('po','branding'));
    }
}
