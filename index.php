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
		'currentapp' => 'news_admin'
	);
	include('../header.inc.php');

	$news = createobject('news_admin.uinews');
	$news->show_news();

	$phpgw->common->phpgw_footer();
?>