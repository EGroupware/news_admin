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
  include("../header.inc.php");

  $phpgw->template->set_file(array("form" => "form.tpl",
                                   "row"  => "form_row.tpl"));

  $phpgw->db->query("select * from webpage_news where news_id='$news_id'",__LINE__,__FILE__);
  $phpgw->db->next_record();

  $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
  $phpgw->template->set_var("bgcolor",$phpgw_info["theme"]["bgcolor"]);

  $phpgw->template->set_var("lang_header",lang("View news item"));
  $phpgw->template->set_var("form_action","");
  $phpgw->template->set_var("form_button","");

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("subject") . ":");
  $phpgw->template->set_var("value",$phpgw->strip_html($phpgw->db->f("news_subject")));
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("Content") . ":");
  $phpgw->template->set_var("value",$phpgw->strip_html($phpgw->db->f("news_content")));
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("Status") . ":");
  $phpgw->template->set_var("value",lang($phpgw->db->f("news_status")));
  $phpgw->template->parse("rows","row",True);

  $phpgw->template->pparse("out","form");     
?>