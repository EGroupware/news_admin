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

	if ($menuaction)
	{
		list($app,$class,$method) = explode('.',$menuaction);
		if (! $app || ! $class || ! $method)
		{
			$invaild_data = True;
		}
	}
	else
	{
		$app = 'home';
		$invaild_data = True;
	}

	$phpgw_info['flags'] = array(
		'noheader'   => True,
		'nonavbar'   => True,
		'currentapp' => $app
	);
	include('../header.inc.php');

	$obj = createobject(sprintf('%s.%s',$app,$class));
	if ((is_array($obj->public_functions) && $obj->public_functions[$method]) && ! $invaild_data)
	{
		eval("\$obj->$method();");
	}
	else
	{
		$phpgw->common->phpgw_header();
		echo parse_navbar();

		$_obj = createobject('news_admin.uinews');
		$_obj->show_news();
	}

	$phpgw->common->phpgw_footer();
?>