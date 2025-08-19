@php($branding = \App\Support\Branding::data())
<!doctype html><html><head><meta charset="utf-8"><style>
body{font-family: DejaVu Sans, sans-serif; font-size:12px;} table{width:100%; border-collapse: collapse;} th,td{border:1px solid #ddd; padding:6px;}
h1{font-size:18px;margin:0 0 8px;} .muted{color:#666}
</style></head><body>@if(!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class))<div style="padding:8px;background:#fff3cd;color:#664d03;margin-bottom:8px;border:1px solid #ffe69c;">PDF engine not installed — showing HTML preview.</div>@endif
<table style="border:none">
<tr style="border:none">
  <td style="border:none">
    <h1>{{ $branding['name'] }}</h1>
    <div class="muted">{{ $branding['address'] }}</div>
  </td>
  <td style="border:none; text-align:right">
    <div><strong>Bill #:</strong> {{ $bill->bill_no }}</div>
    <div><strong>Date:</strong> {{ optional($bill->bill_date)->format('Y-m-d H:i') }}</div>
  </td>
</tr>
</table>

<h3>Patient</h3>
<table>
<tr><th>MRN</th><th>Name</th><th>Visit</th></tr>
<tr>
  <td>{{ $bill->patient?->mrn }}</td>
  <td>{{ $bill->patient?->first_name }} {{ $bill->patient?->last_name }}</td>
  <td>{{ $bill->visit?->visit_no }}</td>
</tr>
</table>

<h3>Items</h3>
<table>
<thead><tr><th>#</th><th>Description</th><th>Qty</th><th>Unit</th><th>Tax%</th><th>Discount</th><th>Line Total</th></tr></thead>
<tbody>
@php($i=1)
@foreach($bill->items as $it)
@php($line = $it->qty * $it->unit_price)
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ $it->description }}</td>
    <td style="text-align:right">{{ number_format($it->qty,3) }}</td>
    <td style="text-align:right">{{ number_format($it->unit_price,2) }}</td>
    <td style="text-align:right">{{ number_format($it->tax_rate,2) }}</td>
    <td style="text-align:right">{{ number_format($it->discount,2) }}</td>
    <td style="text-align:right">{{ number_format($line,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<h3>Summary</h3>
<table>
<tr><th>Subtotal</th><td style="text-align:right">{{ number_format($bill->subtotal,2) }}</td></tr>
<tr><th>Tax</th><td style="text-align:right">{{ number_format($bill->tax,2) }}</td></tr>
<tr><th>Discount</th><td style="text-align:right">{{ number_format($bill->discount,2) }}</td></tr>
<tr><th>Grand Total</th><td style="text-align:right"><strong>{{ number_format($bill->grand_total,2) }}</strong></td></tr>
</table>

</body></html>
