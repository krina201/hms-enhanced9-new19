<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Batch Labels</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .card { border: 1px solid #333; padding: 8px; height: 180px; overflow: hidden; }
    .small { font-size: 10px; }
  </style>
</head>
<body>
  <div class="grid">
    @foreach($batches as $b)
      <div class="card">
        <div><b>{{ $b->item->name ?? ('Item #'.$b->inventory_item_id) }}</b></div>
        <div class="small">Batch: {{ $b->batch_no }}</div>
        <div class="small">Expiry: {{ optional($b->expiry_date)->format('Y-m-d') ?? '—' }}</div>
        <div class="small">Qty: {{ number_format($b->qty_on_hand,3) }}</div>
        <div class="small">Code: B{{ $b->id }}</div>
        @php
          $barcodePng = null;
          if (class_exists(\Picqer\Barcode\BarcodeGeneratorPNG::class)) {
              $gen = new \Picqer\Barcode\BarcodeGeneratorPNG();
              $barcodePng = base64_encode($gen->getBarcode('B'.$b->id, $gen::TYPE_CODE_128, 2, 50));
          }
        @endphp
        @if($barcodePng)
          <img src="data:image/png;base64,{{ $barcodePng }}" style="width:100%; max-height:60px;">
        @else
          @if($qrAvailable)
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->generate('BATCH:'.$b->id.'|SKU:'.($b->item->sku ?? '').'|BATCH:'.$b->batch_no) !!}
          @else
            <div class="small">[Install picqer/php-barcode-generator or simple-qrcode]</div>
          @endif
        @endif
      </div>
    @endforeach
  </div>
</body>
</html>
