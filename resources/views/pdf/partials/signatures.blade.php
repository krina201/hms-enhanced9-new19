<table width="100%" style="margin-top:12px;">
  <tr>
    <td style="width:50%; text-align:center;">
      <div style="height:60px;">
        @if(!empty($signatures['prepared']))
          <img src="{{ $signatures['prepared'] }}" style="max-height:60px;">
        @endif
      </div>
      <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:200px;">Prepared By</div>
    </td>
    <td style="width:50%; text-align:center;">
      <div style="height:60px;">
        @if(!empty($signatures['approved']))
          <img src="{{ $signatures['approved'] }}" style="max-height:60px;">
        @endif
      </div>
      <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:200px;">Approved By</div>
    </td>
  </tr>
</table>
