<?php
	/**************************************************************************\
	* phpGroupWare - News                                                      *
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

	class uinews
	{
		var $template;
		var $db;
		var $public_functions = array(
			'show_news'      => True,
			'show_news_home' => True
		);

		function uinews()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->db       = $GLOBALS['phpgw']->db;
			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('news_admin'));
		}

		function show_news($show_category_select = False)
		{
			global $cat_id, $start, $category_list, $oldnews;

			$news_id = $GLOBALS['HTTP_GET_VARS']['news_id'];

			if($news_id)
			{
				$specific = " AND news_id='" . $news_id . "'";
			}

			if (! function_exists('parse_navbar'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->template->set_file(array(
				'_news' => 'news.tpl'
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');
			$this->template->set_block('_news','category');

			$this->db->query("SELECT COUNT(*) FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$total = $this->db->f(0);

			if (! $oldnews)
			{
				$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id' $specific ORDER BY news_date DESC",0,__LINE__,__FILE__,5);
			}
			else
			{
				$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id' ORDER BY news_date DESC ",$start,__LINE__,__FILE__,$total);
			}

			$var = Array();

			$this->template->set_var('icon',$GLOBALS['phpgw']->common->image('news_admin','news-corner.gif'));

			if ($show_category_select || $category_list)
			{
				$var['form_action'] = $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.show_news&category_list=True');
				$var['lang_category'] = lang('Category');

				$var['lang_main'] = lang('Main');
//				$cats = createobject('phpgwapi.categories');
				$var['input_category'] = ExecMethod('phpgwapi.categories.formated_list',
					Array(
						'format'	=>'select',
						'type'	=> 'mains',
						'selected'	=> $cat_id
					)
				);
				$this->template->set_var($var);
				$this->template->parse('_category','category');
			}

			while ($this->db->next_record())
			{
				$var = Array(
					'subject'	=> $this->db->f('news_subject'),
					'submitedby'	=> 'Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($this->db->f('news_submittedby')) . ' on ' . $GLOBALS['phpgw']->common->show_date($this->db->f('news_date')),
					'content'	=> nl2br(stripslashes($this->db->f('news_content')))
				);

				$this->template->set_var($var);
				$this->template->parse('rows','row',True);
			}

			$this->template->pfp('_out','news_form');
			if ($total > 5 && ! $oldnews)
			{
				$link_values = array(
					'menuaction'    => 'news_admin.uinews.show_news',
					'oldnews'       => 'True',
					'cat_id'        => $cat_id,
					'category_list' => 'True'
				);

				echo '<center><a href="' . $GLOBALS['phpgw']->link('/index.php',$link_values) . '">View news archives</a></center>';
			}
		}

		function show_news_home()
		{
			$title = '<font color="#FFFFFF">'.lang('News Admin').'</font>';
			$portalbox = CreateObject('phpgwapi.listbox',array(
				'title'     => $title,
				'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'     => '100%',
				'outerborderwidth' => '0',
				'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi/templates/phpgw_website','bg_filler.gif')
			));

			$app_id = $GLOBALS['phpgw']->applications->name2id('news_admin');
			$GLOBALS['portal_order'][] = $app_id;

			$var = Array(
				'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
				'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
				'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
				'question' => Array('url' => '/set_box.php', 'app' => $app_id),
				'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
			);

			while(list($key,$value) = each($var))
			{
				$portalbox->set_controls($key,$value);
			}

			$this->db->query("SELECT COUNT(*) FROM phpgw_news WHERE news_status='Active' AND news_cat='0'",__LINE__,__FILE__);
			$this->db->next_record();
			$total = $this->db->f(0);

			$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id' ORDER BY news_date DESC",0,__LINE__,__FILE__,5);

			$image_path = $GLOBALS['phpgw']->common->get_image_path('news_admin');

			while ($this->db->next_record())
			{
				$portalbox->data[] = array(
					'text' => $this->db->f('news_subject') . ' - ' . lang('Submitted by') . ' ' . $GLOBALS['phpgw']->accounts->id2name($this->db->f('news_submittedby')) . ' ' . lang('on') . ' ' . $GLOBALS['phpgw']->common->show_date($this->db->f('news_date')),
					'link' => $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.show_news&news_id=' . $this->db->f('news_id'))
				);
			}

			echo "\r\n"
				. '<!-- start News Admin -->' . "\r\n"
				. $portalbox->draw()
				. '<!-- end News Admin -->'  . "\r\n";
		}
	}
