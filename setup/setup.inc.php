<?php
	/**************************************************************************\
	* phpGroupWare - News                                                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['news_admin']['name']      = 'news_admin';
	//$setup_info['news_admin']['title']     = 'News Admin';
	$setup_info['news_admin']['version']   = '0.9.14.501';
	$setup_info['news_admin']['app_order'] = 4;
	$setup_info['news_admin']['enable']    = 1;

	/* The tables this app creates */
	$setup_info['news_admin']['tables']    = array('phpgw_news');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['news_admin']['hooks'][] = 'admin';
	$setup_info['news_admin']['hooks'][] = 'home';

	/* Dependencies for this app to work */
	$setup_info['news_admin']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.13', '0.9.14', '0.9.15')
	);
?>
