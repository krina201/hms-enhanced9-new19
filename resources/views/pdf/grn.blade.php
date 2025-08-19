<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>GRN {{ $grn->grn_no }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; }
    h2 { margin: 6px 0 10px 0; }
    .no-border td, .no-border th { border: none; }
  </style>
</head>
<body>
  @php($branding = \App\Support\Branding::data())
  @include('pdf.partials.branding', ['branding' => $branding])

  <h2>Goods Receipt Note (GRN)</h2>
  <table class="no-border">
    <tr>
      <td>GRN No: <b>{{ $grn->grn_no }}</b></td>
      <td>Date: <b>{{ optional($grn->grn_date)->format('Y-m-d') }}</b></td>
    </tr>
    <tr>
      <td>PO #: <b>{{ $grn->purchase_order_id }}</b></td>
      <td>Location: <b>{{ $grn->location_id ?? '—' }}</b></td>
    </tr>
    <tr>
      <td colspan="2">Received By: <b>{{ $grn->received_by }}</b></td>
    </tr>
  </table>
  <br>
  <table>
    <thead>
      <tr><th>Item</th><th>Batch</th><th>Expiry</th><th>Qty</th><th>Unit Price</th></tr>
    </thead>
    <tbody>
      @foreach($grn->items as $i)
      <tr>
        <td>#{{ $i->inventory_item_id }} {{ $i->item->name ?? '' }}</td>
        <td>{{ $i->batch_no ?? '—' }}</td>
        <td>{{ optional($i->expiry_date)->format('Y-m-d') ?? '—' }}</td>
        <td style="text-align:right">{{ number_format($i->received_qty,3) }}</td>
        <td style="text-align:right">{{ number_format($i->unit_price ?? 0,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @php($terms = \App\Support\Docs::terms('grn'))
  @include('pdf.partials.terms', ['terms'=>$terms])

  @php($signatures = \App\Support\Docs::signatures())
  @include('pdf.partials.signatures', ['signatures'=>$signatures])
</body>
</html>
