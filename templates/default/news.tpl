<!-- BEGIN news_form -->
 {_category}
 <div align="right">
  <table border="0" width="90%" cellspacing="0" cellpadding="0">
  
   {rows}
  
  </table>
 </div>
<!-- END news_form -->

<!-- BEGIN row -->
   <tr bgcolor="#c7c3c7">
    <td width="13" valign="top" valign="top">
     <img src="{icon_dir}/news-corner.gif" align="top">
    </td>
    <td align="left" width="99%">
     <b>{subject}</b>&nbsp;
    </td>
    <td align="left" width="1%" bgcolor="#FFFFFF">
     &nbsp;
    </td>
   </tr>
   <tr>
    <td width="100%" colspan="3">
     {submitedby}
     <p>{content}</p>
     <p>&nbsp;</p>
    </td>
   </tr>
<!-- END row -->

<!-- BEGIN category -->
<form action="{form_action}" method="POST">
 {lang_category}: <select name="cat_id" onChange="this.form.submit();"><option value="0">{lang_main}</option>{input_category}</select>
</form>
<!-- END category -->
