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

  include("setup.inc.php");

  $tpl->set_file(array("news" => "news.tpl",
                       "row"  => "news_row.tpl"));

  $db->query("select count(*) from webpage_news where news_status='Active'");
  $db->next_record();
  $total = $db->f(0);

  if (! $oldnews) {
     $db->query("select *,account_lid as submittedby from webpage_news,accounts where news_status='Active' and news_submittedby=accounts.account_id order by news_date desc limit 5");
  } else {  
     $db->query("select *,account_lid as submittedby from webpage_news,accounts where news_status='Active' and news_submittedby=accounts.account_id order by news_date desc limit 5,$total");
  }

  while ($db->next_record()) {
    $tpl->set_var("subject",$db->f("news_subject"));
    $tpl->set_var("submitedby","Submitted by " . $db->f("submittedby") . " on " . date("m/d/Y - h:m:s a",$db->f("news_date")));
    $tpl->set_var("content",$db->f("news_content"));
    
    $tpl->parse("rows","row",True);
  }

  $tpl->pparse("out","news");
  if ($total > 5 && ! $oldnews) {
     echo '<center><a href="news.php?oldnews=True">View news archives</a></center>';
  }
?>
   <p>&nbsp;</p>
   <p>&nbsp;</p>

   <div align="right"><font size="-1">This page uses the news admin from <a href="http://www.phpgroupware.org">phpGroupWare</a></font></div>

