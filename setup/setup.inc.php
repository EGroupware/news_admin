<?php
	/**************************************************************************\
	* phpGroupWare - Addressbook                                               *
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
	$setup_info['news_admin']['title']     = 'News Admin';
	$setup_info['news_admin']['version']   = '0.0.1';
	$setup_info['news_admin']['app_order'] = 4;
	$setup_info['news_admin']['enable']    = 1;

	/* The tables this app creates */
	$setup_info['news_admin']['tables']    = array(
		'webpage_news'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['news_admin']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['news_admin']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.10', '0.9.11' , '0.9.12', '0.9.13')
	);
?>
