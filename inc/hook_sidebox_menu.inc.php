<?php
	/**************************************************************************\
	* eGroupWare - Webpage News Admin                                          *
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
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = $GLOBALS['egw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$file = Array(
		array(
			'text' => '<a class="textSidebox" href="'.$GLOBALS['egw']->link('/index.php',array('menuaction' => 'news_admin.uinews.edit')).
				'" onclick="window.open(this.href,\'_blank\',\'dependent=yes,width=700,height=580,scrollbars=yes,status=yes\'); 
				return false;">'.lang('Add').'</a>',
			'no_lang' => true,
			'link' => false
		),
		'Read news' => $GLOBALS['egw']->link('/index.php',array('menuaction' => 'news_admin.uinews.index')),
	);
	display_sidebox($appname,$menu_title,$file);

	$title = lang('Preferences');
	$file = array(
		'Preferences' => $GLOBALS['egw']->link('/index.php','menuaction=preferences.uisettings.index&appname=' . $appname),
		'Categories' => $GLOBALS['egw']->link('/index.php','menuaction=news_admin.news_admin_ui.cats'),
	);
	display_sidebox($appname,$title,$file);

	if($GLOBALS['egw_info']['user']['apps']['admin'])
	{
		$title = lang('Administration');
		$file = Array(
			'Site Configuration' => $GLOBALS['egw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
//			'Global categories' => $GLOBALS['egw']->link('/index.php','menuaction=admin.uicategories.index&appname=' . $appname),
//			'Configure access permissions' => $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uiacl.acllist'),
			'Configure RSS exports' => $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uiexport.exportlist')
		);

		display_sidebox($appname,$title,$file);
	}
	unset($title);
	unset($file);
}
?>
