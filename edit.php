<?php
  /**************************************************************************\
  * phpGroupWare - Webpage news admin                                        *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  * --------------------------------------------                             *
  * This program was sponsered by Golden Glair productions                   *
  * http://www.goldenglair.com                                               *
  \**************************************************************************/

  /* $Id$ */

  $phpgw_info["flags"] = array("currentapp" => "news_admin","enable_nextmatchs_class" => True);
  if ($submit) {
     $phpgw_info["flags"]["noheader"] = True;
     $phpgw_info["flags"]["nonavbar"] = True;
  }
  include("../header.inc.php");

  if ($submit) {
     $phpgw->db->query("update webpage_news set news_subject='" . addslashes($subject) . "',"
                     . "news_content='" . addslashes($content) . "',news_status='$status' "
                     . "where news_id='$news_id'",__LINE__,__FILE__);
     Header("Location: " . $phpgw->link("index.php"));
     exit; 
  }

  $phpgw->template->set_file(array("form" => "form.tpl",
                                   "row"  => "form_row.tpl"));

  $phpgw->db->query("select * from webpage_news where news_id='$news_id'",__LINE__,__FILE__);
  $phpgw->db->next_record();

  $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
  $phpgw->template->set_var("bgcolor",$phpgw_info["theme"]["bgcolor"]);

  $phpgw->template->set_var("lang_header",lang("Edit news item"));
  $phpgw->template->set_var("form_action",$phpgw->link("edit.php","news_id=" . $phpgw->db->f("news_id")));
  $phpgw->template->set_var("form_button",'<input type="submit" name="submit" value="' . lang("Edit") . '">');

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("subject") . ":");
  $phpgw->template->set_var("value",'<input name="subject" size="60" value="' . $phpgw->db->f("news_subject") . '">');
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("Content") . ":");
  $phpgw->template->set_var("value",'<textarea cols="60" rows="6" name="content" wrap="virtual">' . stripslashes($phpgw->db->f("news_content")) . '</textarea>');
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("Status") . ":");
  $s[$phpgw->db->f("news_status")] = " selected";
  $phpgw->template->set_var("value",'<select name="status"><option value="Active"' . $s["Active"] . '>'
                                  . lang("active") . '</option><option value="Disabled"' . $s["Disabled"] . '>'
                                  . lang("Disabled") . '</option></select>');
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->pparse("out","form");     
?>