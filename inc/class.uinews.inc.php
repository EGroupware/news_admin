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
					'show_news' => True
				);

		function uinews()
		{
			global $phpgw;

			$this->template = $phpgw->template;
			$this->db       = $phpgw->db;
			$this->template->set_root($phpgw->common->get_tpl_dir('news_admin'));
		}

		function show_news($show_category_select = False)
		{
			global $cat_id, $start, $phpgw, $category_list;

			if (! function_exists('parse_navbar'))
			{
				$phpgw->common->phpgw_header();
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

			$this->db->query("select count(*) from phpgw_news where news_status='Active' and news_cat='$cat_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$total = $this->db->f(0);

			if (! $oldnews)
			{
				$this->db->limit_query("select * from phpgw_news where news_status='Active' and news_cat='$cat_id' order by news_date desc",1,__LINE__,__FILE__,5);
			}
			else
			{
				$this->db->limit_query("select * from phpgw_news where news_status='Active' and news_cat='$cat_id' order by news_date desc ",$start,__LINE__,__FILE__,$total);
			}

			$image_path = $phpgw->common->get_image_path('news_admin');

			while ($this->db->next_record())
			{
				$this->template->set_var('icon_dir',$image_path);
				$this->template->set_var('subject',$this->db->f('news_subject'));
				$this->template->set_var('submitedby','Submitted by ' . $phpgw->accounts->id2name($this->db->f('news_submittedby')) . ' on ' . $phpgw->common->show_date($this->db->f('news_date')));
				$this->template->set_var('content',nl2br($this->db->f('news_content')));
		
				$this->template->parse('rows','row',True);
			}

			if ($show_category_select || $category_list)
			{
				$this->template->set_var('form_action',$phpgw->link('/news_admin/main.php','menuaction=news_admin.uinews.show_news&category_list=True'));
				$this->template->set_var('lang_category',lang('Category'));

				$cats = createobject('phpgwapi.categories');
				$this->template->set_var('lang_main',lang('Main'));
				$this->template->set_var('input_category',$cats->formated_list('select','mains',$cat_id));
				$this->template->parse('_category','category');
			}

			$this->template->pfp('_out','news_form');
			if ($total > 5 && ! $oldnews)
			{
				echo '<center><a href="index.php?oldnews=True">View news archives</a></center>';
			}
		}
	}
