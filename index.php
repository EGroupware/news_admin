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

  $phpgw->template->set_file(array("list" => "list.tpl",
                                   "row"  => "list_row.tpl"));

  if ($order) {
     $ordermethod = "order by $order $sort";
  } else {
     $ordermethod = "order by news_date desc";
  }


  $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
  $phpgw->template->set_var("bgcolor",$phpgw_info["theme"]["bgcolor"]);
  $phpgw->template->set_var("lang_header",lang("Webpage news admin"));

  $phpgw->template->set_var("header_date",$phpgw->nextmatchs->show_sort_order($sort,"news_date",$order,"index.php",lang("Date")));
  $phpgw->template->set_var("header_subject",$phpgw->nextmatchs->show_sort_order($sort,"news_subject",$order,"index.php",lang("Subject")));
  $phpgw->template->set_var("header_status",$phpgw->nextmatchs->show_sort_order($sort,"news_status",$order,"index.php",lang("Status")));
  $phpgw->template->set_var("header_edit","edit");
  $phpgw->template->set_var("header_delete","delete");
  $phpgw->template->set_var("header_view","view");

  $phpgw->db->query("select * from webpage_news $ordermethod",__LINE__,__FILE__);
  while ($phpgw->db->next_record()) {
    $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
    $phpgw->template->set_var("row_date",$phpgw->common->show_date($phpgw->db->f("news_date")));
    if (strlen($phpgw->db->f("news_subject")) > 40) {
       $subject = $phpgw->strip_html(substr($phpgw->db->f("news_subject"),40,strlen($phpgw->db->f("news_subject"))));
    } else {
       $subject = $phpgw->strip_html($phpgw->db->f("news_subject"));
    }
    $phpgw->template->set_var("row_subject",$subject);
    
    if ($phpgw->db->f("news_status") == "Disabled") {
       $phpgw->template->set_var("row_status","<b>" . lang("Disabled") . "</b>");
    } else {
       $phpgw->template->set_var("row_status",lang($phpgw->db->f("news_status")));
    }

    $phpgw->template->set_var("row_edit",'<a href="' . $phpgw->link("/news_admin/edit.php","news_id=" . $phpgw->db->f("news_id")) . '">' . lang("edit") . '</a>');
    $phpgw->template->set_var("row_view",'<a href="' . $phpgw->link("/news_admin/view.php","news_id=" . $phpgw->db->f("news_id")) . '">' . lang("view") . '</a>');

    $phpgw->template->parse("rows","row",True);
  }

  if ($phpgw->db->num_rows() == 0) {
     $phpgw->template->set_var("rows","");
  }

  $phpgw->template->set_var("add_link",$phpgw->link("/news_admin/add.php"));
  $phpgw->template->set_var("lang_add",lang("add"));
  
  $phpgw->template->pparse("out","list");
  $phpgw->common->phpgw_footer();
?>


