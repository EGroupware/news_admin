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

  // Note: Some of this is hard coded for right now ....

  include("setup.inc.php");

  if (! $format) {
     $format = "rdf";
  }

  $tpl->set_file(array("news" => $format . ".tpl",
                       "row"  => $format . "_row.tpl"));

  $db->query("select * from phpgw_news,accounts where news_status='Active' order by news_date "
           . "desc limit 5");

  $tpl->set_var("site_title",$site_title);
  $tpl->set_var("site_link",$site_link);
  $tpl->set_var("site_description",$site_site_description);
  $tpl->set_var("img_title",$img_title);
  $tpl->set_var("img_url",$img_url);
  $tpl->set_var("img_link",$img_link);

  while ($db->next_record()) {
    $tpl->set_var("title",$db->f("news_subject"));
    $tpl->set_var("link",$site_link);
    
    $tpl->parse("rows","row",True);
  }
  $tpl->pparse("out","news");

