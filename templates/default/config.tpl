<!-- BEGIN header -->
<p style="text-align: center; color: {th_err};">{error}</p>
<form name=frm method="POST" action="{action_url}">
{hidden_vars}
<table border="0" align="left">
   <tr class="th">
    <td colspan="2">&nbsp;<b>{title}</b></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
<tr class="row_on">
 <td>{lang_Path_of_the_upload_directory_(has_to_be_writable_by_the_webserver!)}:</td>
 <td><input name="newsettings[upload_dir]" size="40" value="{value_upload_dir}"></td>
</tr>

<tr class="row_off">
 <td>{lang_URL_of_the_upload_directory}:</td>
 <td><input name="newsettings[upload_url]" size="40" value="{value_upload_url}"></td>
</tr>

<!-- END body -->

<!-- BEGIN footer -->
  <tr class="th">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
