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
		var $bonews;
		var $public_functions = array(
			'show_news'      => True,
			'show_news_home' => True
		);

		function uinews()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->bonews   = CreateObject('news_admin.bonews');
			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('news_admin'));
		}

		function show_news($show_category_select = False)
		{
			global $cat_id, $start, $category_list, $oldnews;

			if (! function_exists('parse_navbar'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
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

			if (function_exists('get_var'))
			{
				$news_id = get_var('news_id',Array('GET'));
			}
			else
			{	
				$news_id = $GLOBALS['HTTP_GET_VARS']['news_id'];
			}

			if($news_id)
			{
				$news = $this->bonews->get_news($news_id);
			}
			else
			{
				$news = $this->bonews->get_NewsList($cat_id, $oldnews, $start, $total);
			}

			$total = $this->bonews->get_NumNewsInCat($cat_id);

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

			foreach($news as $newsitem)
			{
				$var = Array(
					'subject'	=> $newsitem['subject'],
					'submitedby'	=> 'Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']) . ' on ' . $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
					'content'	=> nl2br($newsitem['content'])
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
			$title = lang('News Admin');
			$portalbox = CreateObject('phpgwapi.listbox',array
			(
				'title'     => $title,
				'width'     => '100%',
				'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi/templates/default','bg_filler')
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

			$total = $this->bonews->get_NumNewsInCat(0);

			$newslist = $this->bonews->get_newslist($cat_id);

			$image_path = $GLOBALS['phpgw']->common->get_image_path('news_admin');

			foreach($newslist as $newsitem)
			{
				$portalbox->data[] = array
				(
					'text'					=> $newsitem['subject'] . ' - ' . lang('Submitted by') . ' ' . $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']) . ' ' . lang('on') . ' ' . $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
					'link'					=> $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.show_news&news_id=' . $newsitem['id']),
					'lang_link_statustext'	=> lang('show the news item')
				);
			}

			$GLOBALS['phpgw']->template->set_var('phpgw_body',$portalbox->draw(),True);
		}

		function show_news_website($section='mid')
		{
			global $cat_id, $start, $oldnews;

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->template->set_file(array(
				'_news' => 'news_' . $section . '.tpl'
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');
			$this->template->set_block('_news','category');

			if (function_exists('get_var'))
			{
				$news_id = get_var('news_id',Array('GET'));
			}
			else
			{   
				$news_id = $GLOBALS['HTTP_GET_VARS']['news_id'];
			}

			if($news_id)
			{
				$news = $this->bonews->get_news($news_id);
			}
			else
			{
				$news = $this->bonews->get_NewsList($cat_id,$oldnews,$start,$total);
			}


			$total = $this->bonews->get_NumNewsInCat($cat_id);

			$var = Array();

			$this->template->set_var('icon',$GLOBALS['phpgw']->common->image('news_admin','news-corner.gif'));

			foreach($news as $newsitem)
			{
				$var = Array(
					'subject'    => $newsitem['subject'],
					'submitedby' => 'Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($newsitem['submittedby']) . ' on ' . $GLOBALS['phpgw']->common->show_date($newsitem['submissiondate']),
					'content'    => nl2br($newsitem['content'])
				);

				$this->template->set_var($var);
				$this->template->parse('rows','row',True);
			}

			$out = $this->template->fp('out','news_form');

			if ($total > 5 && ! $oldnews)
			{
				$link_values = array(
					'menuaction'    => 'news_admin.uinews.show_news',
					'oldnews'       => 'True',
					'cat_id'        => $cat_id,
					'category_list' => 'True'
				);

				$out .= '<center><a href="' . $GLOBALS['phpgw']->link('/index.php',$link_values) . '">View news archives</a></center>';
			}
			return $out;
		}
	}
