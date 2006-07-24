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
	
	/**
	 * Check if we allow anon access and with which creditials
	 * 
	 * @param array &$anon_account anon account_info with keys 'login', 'passwd' and optional 'passwd_type'
	 * @return boolean true if we allow anon access, false otherwise
	 */
	function registration_check_anon_access(&$anon_account)
	{
		//quick hack for std installations...
		$anon_account = array(
			'login'  => 'anonymous',
			'passwd' => 'anonymous',
			'passwd_type' => 'text',
		);
		return true;
	}
	
	$GLOBALS['egw_info']['flags'] = array(
		'noheader'  => True,
		'nonavbar' => True,
		'currentapp' => 'sitemgr-link',
		'autocreate_session_callback' => 'registration_check_anon_access',
	);
	include('../../header.inc.php');
	
	$news_obj =& CreateObject('news_admin.sonews');
	$export_obj =& CreateObject('news_admin.soexport');
	$tpl =& $GLOBALS['egw']->template;
	
	$cat_id = (int)$_GET['cat_id'];
//	$format = (isset($_GET['format']) ? strtolower(trim($_GET['format'])) : 'rss');
	$limit	= (isset($_GET['limit']) ? trim($_GET['limit']) : 5);
//	$all	= (isset($_GET['all']) ? True : False);

	$site = $export_obj->readconfig($cat_id);

	//TODO allow override of configured value by a configurable flag
	//validate format

// 	$available_formats = array('rss'	=> True, //RSS 0.91
// 				'rdf-chan'	=> True, //RDF 1.0
// 				'rdf2'		=> True, //RDF/RSS 2.0
// 				);

// 	if(!$available_formats[$format])
// 	{
// 		$format = 'rss';
// 	}

	if(!$site['type'])
	{
		echo "THIS CATEGORY IS NOT PUBLICLY ACCESSIBLE";
		die();
	}

	header('Content-type: text/xml; charset='.$GLOBALS['egw']->translation->charset());

	$formats = array(1 => 'rss091', 2 => 'rss1', 3 => 'rss2');
	$itemsyntaxs = array(
		0 => '?item=',
		1 => '&item=',
		2 => '?news%5Bitem%5D=',
		3 => '&news%5Bitem%5D='
	);
	$format = $formats[$site['type']];
	$itemsyntax = $itemsyntaxs[$site['itemsyntax']];
	
	$tpl->root = EGW_SERVER_ROOT. '/news_admin/website/templates/';
	$tpl->set_file(array('news' => $format . '.tpl'));
	$tpl->set_block('news', 'item', 'items');
	if($format == 'rss1')
	{
		$tpl->set_block('news', 'seq', 'seqs');
	}

	$tpl->set_var('encoding', $GLOBALS['egw']->translation->charset());
	$tpl->set_var($site);

// 	if($all)
// 	{
// 		$news = $news_obj->get_all_public_news($limit);
// 	}
// 	else
// 	{
		$news = $news_obj->get_newslist($cat_id, 0,'','',$limit,True);
// 	}
	if(is_array($news))
	{
		foreach($news as $news_id => $news_data) 
		{
			$tpl->set_var($news_data);

			$tpl->set_var('item_link', $site['link'] . $itemsyntax . $news_id);
			$tpl->set_var('pub_date', date("r",$news_data['date']));
			if($format == 'rss1')
			{
				$tpl->parse('seqs','seq',True);
			}
		
			$tpl->parse('items','item',True);
		}
	}
	else
	{
		$tpl->set_var('items', '');
	}
	$tpl->pparse('out','news');
?>
