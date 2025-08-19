<table width="100%" style="border-bottom:1px solid #000; margin-bottom:8px;">
  <tr>
    <td style="width:80px;">
      @if(!empty($branding['logo']))
        <img src="{{ $branding['logo'] }}" style="max-width:70px; max-height:70px;">
      @endif
    </td>
    <td>
      <div style="font-size:16px; font-weight:bold;">{{ $branding['name'] }}</div>
      @if(!empty($branding['address']))
      <div style="font-size:11px;">{{ $branding['address'] }}</div>
      @endif
    </td>
    <td style="text-align:right; font-size:11px;">
      <div>Printed: {{ now()->format('Y-m-d H:i') }}</div>
    </td>
  </tr>
</table>
