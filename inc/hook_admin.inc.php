<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$title = $appname;
	$file = Array(
		'Maintain news' => $phpgw->link('/news_admin/main.php','menuaction=news_admin.uiadmin.news_list'),
		'Categories'    => $phpgw->link('/preferences/categories.php','cats_app=news_admin')
	);

	display_section($appname,$title,$file);
?>