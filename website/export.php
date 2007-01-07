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
		global $site_url, $sitemgr_info;

		$path0 = preg_replace('/\/[^\/]*$/','',$_SERVER['PHP_SELF']) .'/';
		// Force a single trailing slash. Source: http://www.php.net/manual/en/function.preg-replace.php#70833
		$site_urls[] = $path = preg_replace('!/*$!', '/', $path0, 1);
		$site_urls[] = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_ADDR'] . $path ;
		$site_urls[] = $site_url  = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $path;

		//echo "<p>sitemgr_get_site('$site_url')</p>\n";
		$GLOBALS['egw']->db->select('egw_sitemgr_sites','*',
			array('site_url' => $site_urls),__LINE__,__FILE__,false,'','sitemgr');

		if ($GLOBALS['egw']->db->next_record())
		{
			$GLOBALS['site_id'] = $GLOBALS['egw']->db->f('site_id');
			if ($GLOBALS['sitemgr_info']['webserver_url'])
			{
				$GLOBALS['egw_info']['server']['webserver_url'] = $GLOBALS['sitemgr_info']['webserver_url'] = $GLOBALS['egw']->db->f('site_url');
			}
			$anon_account = array(
				'login'  => $GLOBALS['egw']->db->f('anonymous_user'),
				'passwd' => $GLOBALS['egw']->db->f('anonymous_passwd'),
				'passwd_type' => 'text',
			);

			$sitemgr_info['anonymous_user'] = $anon_account['login'];
			
			if($GLOBALS['egw_info']['server']['allow_cookie_auth'])
			{
				$eGW_remember = explode('::::',stripslashes($_COOKIE['eGW_remember']));

				if (count($eGW_remember) == 3 && $GLOBALS['egw']->accounts->name2id($eGW_remember[0],'account_lid','u'))
				{
					$anon_account = array(
						'login' => $eGW_remember[0],
						'passwd' => $eGW_remember[1],
						'passwd_type' => $eGW_remember[2],
					);
				}
			}
			if (!$anon_account['login'])
			{
				die(lang('NO ANONYMOUS USER ACCOUNTS INSTALLED.  NOTIFY THE ADMINISTRATOR.'));
			}
			//echo "<p>sitemgr_get_site('$site_url') site_id=$site_id, anon_account=".print_r($anon_account,true)."</p>\n";
			return true;
		}
		//quick hack for std installations...
		// Policy: admins who don't (want to) install site manager, must leave the default anonymous user and password
		// in order to read feeds via export.php directly
		$anon_account = array(
			'login'  => 'anonymous',
			'passwd' => 'anonymous',
			'passwd_type' => 'text',
		);
		return true;
	}
	
	
	if (!$GLOBALS['sitemgr_info']['egw_path'])
	{
          $GLOBALS['sitemgr_info'] = array(
                'egw_path'         => getcwd().'/../../',
                'htaccess_rewrite' => False,
          );
	}

	$GLOBALS['egw_info']['flags'] = array(
		'noheader'  => True,
		'nonavbar' => True,
		'currentapp' => 'sitemgr-link',
		'autocreate_session_callback' => 'registration_check_anon_access',
	);

	require_once($GLOBALS['sitemgr_info']['egw_path'].'header.inc.php');
	
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
