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

  $phpgw->template->set_file(array("form" => "form.tpl",
                                   "row"  => "form_row.tpl"));
  
  if ($submit) {
     $phpgw->db->query("insert into webpage_news (news_date,news_submittedby,news_content,news_subject,"
                     . "news_status) values ('" . time() . "','" . $phpgw_info["user"]["account_id"] . "','"
                     . addslashes($content) . "','" . addslashes($subject) . "','$status')",__LINE__,__FILE__);
     Header("Location: " . $phpgw->link("index.php"));
     exit;  
  } else {

     $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
     $phpgw->template->set_var("bgcolor",$phpgw_info["theme"]["bgcolor"]);

     $phpgw->template->set_var("lang_header",lang("Add news item"));
     $phpgw->template->set_var("form_action",$phpgw->link("add.php"));
     $phpgw->template->set_var("form_button",'<input type="submit" name="submit" value="' . lang("Add") . '">');

     $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
     $phpgw->template->set_var("label",lang("subject") . ":");
     $phpgw->template->set_var("value",'<input name="subject" size="60">');
     $phpgw->template->parse("rows","row",True);

     $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
     $phpgw->template->set_var("label",lang("Content") . ":");
     $phpgw->template->set_var("value",'<textarea cols="60" rows="6" name="content" wrap="virtual"></textarea>');
     $phpgw->template->parse("rows","row",True);

     $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
     $phpgw->template->set_var("label",lang("Status") . ":");
     $phpgw->template->set_var("value",'<select name="status"><option value="Active">'
                                     . lang("Active") . '</option><option value="Disabled">'
                                     . lang("Disabled") . '</option></select>');
     $phpgw->template->parse("rows","row",True);

     $phpgw->template->pparse("out","form");
  }
?>