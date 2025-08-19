@php($branding = \App\Support\Branding::data())
<!doctype html><html><head><meta charset="utf-8"><style>
body{font-family: DejaVu Sans, sans-serif; font-size:12px;} h1{font-size:18px;margin:0 0 8px;}
table{width:100%; border-collapse: collapse;} th,td{border:1px solid #ddd; padding:6px;}
</style></head><body>@if(!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class))<div style="padding:8px;background:#fff3cd;color:#664d03;margin-bottom:8px;border:1px solid #ffe69c;">PDF engine not installed — showing HTML preview.</div>@endif
<h1>{{ $branding['name'] }} — Discharge Summary</h1>
<p class="muted">{{ $branding['address'] }}</p>
<table>
<tr><th>Patient</th><th>MRN</th><th>Visit</th></tr>
<tr><td>{{ $admission->patient?->first_name }} {{ $admission->patient?->last_name }}</td><td>{{ $admission->patient?->mrn }}</td><td>{{ $admission->visit?->visit_no }}</td></tr>
</table>
<table>
<tr><th>Admit</th><th>Discharge</th><th>Ward/Bed</th><th>Status</th></tr>
<tr>
<td>{{ optional($admission->admit_date)->format('Y-m-d H:i') }}</td>
<td>{{ optional($admission->discharge_date)->format('Y-m-d H:i') }}</td>
<td>{{ $admission->ward }} / {{ $admission->bed }}</td>
<td>{{ $admission->status?->value }}</td>
</tr>
</table>
<h3>Notes</h3>
<p>{{ $admission->notes }}</p>
</body></html>


<h3>Diagnosis</h3>
<p>{{ $admission->diagnosis }}</p>

<h3>Procedures</h3>
<p>{{ $admission->procedures }}</p>

<h3>Discharge Instructions</h3>
<p>{{ $admission->instructions }}</p>
