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

	$phpgw_info['flags'] = array(
		'currentapp'              => 'news_admin',
		'enable_nextmatchs_class' => True
	);
	if ($submit)
	{
		$phpgw_info['flags']['noheader'] = True;
		$phpgw_info['flags']['nonavbar'] = True;
	}
	include('../header.inc.php');
	$phpgw->sbox = createobject('phpgwapi.sbox');

  if ($submit) {
     // Its possiable that this could get messed up becuase of there timezone offset
     if ($date_ap == "pm") {
        $date_hour = $date_hour + 12;
     }
     $date = mktime($date_hour,$date_min,$date_sec,$date_month,$date_day,$date_year);
     $phpgw->db->query("update webpage_news set news_subject='" . addslashes($subject) . "',"
                     . "news_content='" . addslashes($content) . "',news_status='$status',news_date='$date' "
                     . "where news_id='$news_id'",__LINE__,__FILE__);
     Header("Location: " . $phpgw->link("/news_admin/index.php"));
     $phpgw->common->phpgw_exit();
 
  }

  $phpgw->template->set_file(array("form" => "form.tpl",
                                   "row"  => "form_row.tpl"));

  $phpgw->db->query("select * from webpage_news where news_id='$news_id'",__LINE__,__FILE__);
  $phpgw->db->next_record();

  $phpgw->template->set_var("th_bg",$phpgw_info["theme"]["th_bg"]);
  $phpgw->template->set_var("bgcolor",$phpgw_info["theme"]["bgcolor"]);

  $phpgw->template->set_var("lang_header",lang("Edit news item"));
  $phpgw->template->set_var("form_action",$phpgw->link("/news_admin/edit.php","news_id=" . $phpgw->db->f("news_id")));
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

  $phpgw->template->set_var("tr_color",$phpgw->nextmatchs->alternate_row_color());
  $phpgw->template->set_var("label",lang("Date") . ":");

  $d_html = $phpgw->common->dateformatorder($phpgw->sbox->getYears("date_year", date("Y",$phpgw->db->f("news_date"))),
                                            $phpgw->sbox->getMonthText("date_month", date("m",$phpgw->db->f("news_date"))),
                                            $phpgw->sbox->getDays("date_day", date("d",$phpgw->db->f("news_date")))
                                           );
  $d_html .= " - ";
  $d_html .= $phpgw->sbox->full_time("date_hour",$phpgw->common->show_date($phpgw->db->f("news_date"),"h"),
                                                "date_min",$phpgw->common->show_date($phpgw->db->f("news_date"),"i"),
                                                "date_sec",$phpgw->common->show_date($phpgw->db->f("news_date"),"s"),
                                                "date_ap",$phpgw->common->show_date($phpgw->db->f("news_date"),"a")
                                               );
  $phpgw->template->set_var("value",$d_html);

  $h = '<select name="status"><option value="Active"' . $s["Active"] . '>'
      . lang("active") . '</option><option value="Disabled"' . $s["Disabled"] . '>'
      . lang("Disabled") . '</option></select>';
  $phpgw->template->parse("rows","row",True);


  $phpgw->template->pparse("out","form");
  $phpgw->common->phpgw_footer();
?>
