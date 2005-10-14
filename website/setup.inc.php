<?php
	/**************************************************************************\
	* eGroupWare - Webpage news admin                                          *
	* http://www.egroupware.org                                                *
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

	$path_to_header = '../../';
	$template_path  = $path_to_header . 'news_admin/website/templates/';
	$domain         = 'default';

	/* ********************************************************************\
	* Don't change anything after this line                                *
	\******************************************************************** */

	error_reporting(error_reporting() & ~E_NOTICE);

	$GLOBALS['egw_info']['flags']['noapi'] = True;
	include($path_to_header . 'header.inc.php');
	include(EGW_SERVER_ROOT . '/phpgwapi/inc/class.Template.inc.php');
	$tpl =& new Template($template_path);
	include(EGW_SERVER_ROOT . '/phpgwapi/inc/class.egw_db.inc.php');

	$GLOBALS['egw']->db =& new egw_db();
	$GLOBALS['egw']->db->Host     = $GLOBALS['egw_domain'][$domain]['db_host'];
	$GLOBALS['egw']->db->Type     = $GLOBALS['egw_domain'][$domain]['db_type'];
	$GLOBALS['egw']->db->Database = $GLOBALS['egw_domain'][$domain]['db_name'];
	$GLOBALS['egw']->db->User     = $GLOBALS['egw_domain'][$domain]['db_user'];
	$GLOBALS['egw']->db->Password = $GLOBALS['egw_domain'][$domain]['db_pass'];

	include(EGW_SERVER_ROOT . '/news_admin/inc/class.sonews.inc.php');
	$news_obj =& new sonews();

	include(EGW_SERVER_ROOT . '/news_admin/inc/class.soexport.inc.php');
	$export_obj =& new soexport();
