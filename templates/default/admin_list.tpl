<!-- BEGIN list -->
<form action="{form_action}" method="POST">
 {lang_category}: <select name="cat_id" onChange="this.form.submit();"><option value="0">{lang_main}</option>{input_category}</select>
</form>

<b>{lang_header}</b><hr><p>

 <table border="0" align="center" width="85%">
  <tr bgcolor="{th_bg}">
   <td width="12%">{header_date}</td>
   <td>{header_subject}</td>
   <td width="5%" align="center">{header_status}</td>
   <td width="5%" align="center">{header_view}</td>
   <td width="5%" align="center">{header_edit}</td>
   <td width="5%" align="center">{header_delete}</td>
  </tr>

  {rows}

  <tr bgcolor="{bgcolor}">
   <td colspan="5">&nbsp;</td>
  </tr>
  <tr bgcolor="{bgcolor}">
   <td colspan="5"><a href="{add_link}">{lang_add}</a></td>
  </tr>

 </table>
<!-- END list -->

<!-- BEGIN row -->
  <tr bgcolor="{tr_color}">
   <td>{row_date}</td>
   <td>{row_subject}&nbsp;</td>
   <td align="center">{row_status}</td>
   <td align="center">{row_view}</td>
   <td align="center">{row_edit}</td>
   <td align="center">{row_delete}</td>
  </tr>
<!-- END row -->
