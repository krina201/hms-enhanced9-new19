<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>PO {{ $po->order_no }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; }
    h2 { margin: 6px 0 10px 0; }
    .no-border td, .no-border th { border: none; }
  </style>
</head>
<body>
  @include('pdf.partials.branding', ['branding' => $branding])

  <h2>Purchase Order</h2>
  <table class="no-border">
    <tr>
      <td>PO No: <b>{{ $po->order_no }}</b></td>
      <td>Date: <b>{{ optional($po->order_date)->format('Y-m-d') }}</b></td>
    </tr>
    <tr>
      <td>Supplier: <b>{{ $po->supplier->name ?? ('#'.$po->supplier_id) }}</b></td>
      <td>Expected: <b>{{ optional($po->expected_date)->format('Y-m-d') ?? '—' }}</b></td>
    </tr>
  </table>
  <br>
  <table>
    <thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Tax %</th><th>Discount</th><th>Total</th></tr></thead>
    <tbody>
      @foreach($po->items as $i)
      <tr>
        <td>#{{ $i->inventory_item_id }} {{ $i->item->name ?? '' }}</td>
        <td style="text-align:right">{{ number_format($i->qty,3) }}</td>
        <td style="text-align:right">{{ number_format($i->unit_price,2) }}</td>
        <td style="text-align:right">{{ number_format($i->tax_rate,2) }}</td>
        <td style="text-align:right">{{ number_format($i->discount ?? 0,2) }}</td>
        <td style="text-align:right">{{ number_format(($i->qty * $i->unit_price) + (($i->qty*$i->unit_price)*($i->tax_rate/100)) - ($i->discount ?? 0), 2) }}</td>
      </tr>
      @endforeach
      <tr>
        <td colspan="4"></td>
        <td><b>Grand</b></td>
        <td style="text-align:right"><b>{{ number_format($po->grand_total,2) }}</b></td>
      </tr>
    </tbody>
  </table>

  @php($terms = \App\Support\Docs::terms('po'))
  @include('pdf.partials.terms', ['terms'=>$terms])

  @php($signatures = \App\Support\Docs::signatures())
  @include('pdf.partials.signatures', ['signatures'=>$signatures])
</body>
</html>
