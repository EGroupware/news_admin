<!-- BEGIN form -->

 <center>{errors}</center>

 <form method="POST" action="{form_action}">
 <input type="hidden" name="news[id]" value="{value_id}">
  <table width="75%" align="center" cellspacing="0" style="{ border: 1px solid #000000; }">
   <tr class="th">
    <td colspan="2"><b>{lang_header}<b></td>
   </tr>

   <tr class="row_on">
    <td>{label_subject}&nbsp;</td>
    <td>{value_subject}&nbsp;</td>
   </tr>

   <tr class="row_off">
    <td>{label_teaser}&nbsp;</td>
    <td>{value_teaser}&nbsp;</td>
   </tr>

   <tr class="row_on">
    <td>{label_content}&nbsp;</td>
    <td>{value_content}&nbsp;</td>
   </tr>

   <tr class="row_off">
    <td>{label_category}&nbsp;</td>
    <td>{value_category}&nbsp;</td>
   </tr>

   <tr class="row_on">
    <td>{label_status}&nbsp;</td>
    <td>{value_status}&nbsp;</td>
   </tr>

   <tr class="row_off">
    <td>{label_date}&nbsp;</td>
    <td>
       {value_date_d}&nbsp;
       {value_date_m}&nbsp;
       {value_date_y}
    </td>
   </tr>

   <tr class="th">
    <td colspan="2" align="right">
     {form_button}
     {done_button}
    </td>
   </tr>
  </table>
 </form>
 <br>
<!-- END form -->
