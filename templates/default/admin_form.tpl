<!-- BEGIN form -->
<b>{lang_header}</b><hr><p>

 <center>{errors}</center>

 <form method="POST" action="{form_action}">
 <input type="hidden" name="news[id]" value="{value_id}">
  <table border="0" width="75%" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{label_subject}&nbsp;</td>
    <td>{value_subject}&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{label_content}&nbsp;</td>
    <td>{value_content}&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{label_category}&nbsp;</td>
    <td>{value_category}&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{label_status}&nbsp;</td>
    <td>{value_status}&nbsp;</td>
   </tr>

   <tr bgcolor="{background}">
    <td colspan="2" align="right">
     {form_button}
     {done_button}
    </td>
   </tr>
  </table>
 </form>
<!-- END form -->
