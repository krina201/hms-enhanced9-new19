<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BatchLabelsRequest;
use App\Models\StockBatch;
use Illuminate\Support\Str;

class BatchLabelsController extends Controller
{
    public function __invoke(BatchLabelsRequest $request)
    {
        $ids = collect(explode(',', (string)$request->get('ids')))
            ->filter()->map(fn($x)=>(int)$x)->all();
        $batches = StockBatch::with('item')->whereIn('id',$ids)->get();

        // Try QR if available
        $qrAvailable = class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class);
        $pdfAvailable = class_exists(\Barryvdh\DomPDF\Facade\Pdf::class);

        $view = view('pdf.labels', compact('batches','qrAvailable'))->render();

        if ($pdfAvailable) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($view)->setPaper('a4', 'portrait');
            return $pdf->download('batch-labels.pdf');
        }
        return response($view);
    }
}
