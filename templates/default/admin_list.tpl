<!-- BEGIN list -->
<b>{lang_header}</b><hr><p>

<form action="{form_action}" method="POST">
 {lang_category}: <select name="cat_id" onChange="this.form.submit();"><option value="0">{lang_main}</option>{input_category}</select> &nbsp; &nbsp; <a href="{link_add}">{lang_add}</a> &nbsp; &nbsp; {link_view_cat}
</form>

 <center>{message}</center><p>

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

<!-- BEGIN row_empty -->
  <tr bgcolor="{tr_color}">
   <td colspan="6" align="center">{row_message}</td>
  </tr>
<!-- END row_empty -->
