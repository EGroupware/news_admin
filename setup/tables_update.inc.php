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

	$test[] = '0.0.1';
	function news_admin_upgrade0_0_1()
	{
		global $setup_info;

		$setup_info['news_admin']['currentver'] = '0.8.1';
		return $setup_info['news_admin']['currentver'];
	}

	$test[] = '0.8.1';
	function news_admin_upgrade0_8_1()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->RenameTable('webpage_news','phpgw_news');

		$setup_info['news_admin']['currentver'] = '0.8.1.001';
		return $setup_info['news_admin']['currentver'];
	}

	$test[] = '0.8.1.001';
	function news_admin_upgrade0_8_1_001()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->AddColumn('phpgw_news','news_cat',array('type' => 'int','precision' => 4,'nullable' => True));
		$phpgw_setup->oProc->query("update phpgw_news set news_cat='0'",__LINE__,__FILE__);

		$setup_info['news_admin']['currentver'] = '0.8.1.002';
		return $setup_info['news_admin']['currentver'];
	}


?>
