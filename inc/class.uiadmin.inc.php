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

	$phpgw->nextmatchs = createobject('phpgwapi.nextmatchs');

	class uiadmin
	{
		var $public_functions = array(
					'news_list' => True,
					'add'       => True,
					'view'      => True,
					'edit'      => True,
					'delete'    => True
				);

		function uiadmin()
		{
			global $phpgw;

			$this->template = $phpgw->template;
			$this->db       = $phpgw->db;
		}

		function common_header()
		{
			global $phpgw;

			$phpgw->common->phpgw_header();
			echo parse_navbar();		
		}

		function view()
		{
			global $phpgw, $phpgw_info, $news_id;
			$this->common_header();

			$this->template->set_root($phpgw->common->get_tpl_dir('news_admin'));
			$this->template->set_file(array(
				'_news' => 'news.tpl',
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');

			$bo     = createobject('news_admin.boadmin');
			$fields = $bo->view($news_id);

			$this->template->set_var('icon_dir',$phpgw->common->get_image_path('news_admin'));
			$this->template->set_var('subject',$fields['subject']);
			$this->template->set_var('submitedby','Submitted by ' . $phpgw->accounts->id2name($fields['submittedby']) . ' on ' . $fields['date']);
			$this->template->set_var('content',nl2br($fields['content']));

			$this->template->parse('rows','row',True);

			$this->template->pfp('_out','news_form');
		}

		function add($errors = '')
		{
			global $phpgw, $phpgw_info, $news;
			$this->common_header();

			$cats = createobject('phpgwapi.categories');

			$phpgw->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if (is_array($errors))
			{
				$phpgw->template->set_var('errors',$phpgw->common->error_list($errors));
			}

			$phpgw->template->set_var('th_bg',$phpgw_info['theme']['th_bg']);
			$phpgw->template->set_var('row_on',$phpgw_info['theme']['row_on']);
			$phpgw->template->set_var('row_off',$phpgw_info['theme']['row_off']);
			$phpgw->template->set_var('bgcolor',$phpgw_info['theme']['bgcolor']);

			$phpgw->template->set_var('lang_header',lang('Add news item'));
			$phpgw->template->set_var('form_action',$phpgw->link('/news_admin/main.php','menuaction=news_admin.boadmin.add'));
			$phpgw->template->set_var('form_button','<input type="submit" name="submit" value="' . lang('Add') . '">');

			$phpgw->template->set_var('label_subject',lang('subject') . ':');
			$phpgw->template->set_var('value_subject','<input name="news[subject]" size="60" value="' . $news['subject'] . '">');

			$phpgw->template->set_var('label_content',lang('Content') . ':');
			$phpgw->template->set_var('value_content','<textarea cols="60" rows="6" name="news[content]" wrap="virtual">' . stripslashes($news['content']) . '</textarea>');

			$phpgw->template->set_var('label_category',lang('Category') . ':');
			$phpgw->template->set_var('value_category','<select name="news[category]"><option value="0">' . lang('Main') . '</option>' . $cats->formated_list('select','mains',$news['category']) . '</select>');

			$phpgw->template->set_var('label_status',lang('Status') . ':');
			$phpgw->template->set_var('value_status','<select name="news[status]"><option value="Active">'
					. lang('Active') . '</option><option value="Disabled">'
					. lang('Disabled') . '</option></select>');

			$phpgw->template->pfp('out','form');
		}

		function news_list()
		{
			global $phpgw, $phpgw_info, $order, $sort;

			$this->common_header();

			$this->template->set_file(array(
				'_list' => 'admin_list.tpl'
			));
			$this->template->set_block('_list','list');
			$this->template->set_block('_list','row');

			$this->template->set_var('th_bg',$phpgw_info['theme']['th_bg']);
			$this->template->set_var('bgcolor',$phpgw_info['theme']['bgcolor']);
			$this->template->set_var('lang_header',lang('Webpage news admin'));

			$this->template->set_var('header_date',$phpgw->nextmatchs->show_sort_order($sort,'news_date',$order,'/news_admin/main.php',lang('Date'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_subject',$phpgw->nextmatchs->show_sort_order($sort,'news_subject',$order,'/news_admin/main.php',lang('Subject'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_status',$phpgw->nextmatchs->show_sort_order($sort,'news_status',$order,'/news_admin/main.php',lang('Status'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_edit','edit');
			$this->template->set_var('header_delete','delete');
			$this->template->set_var('header_view','view');

			$nextmatchs = createobject('phpgwapi.nextmatchs');
			$bo         = createobject('news_admin.boadmin');
			$items      = $bo->getlist($order,$sort);

			while (is_array($items) && $_item = each($items))
			{
				$item = $_item[1];

				$nextmatchs->template_alternate_row_color(&$this->template);
				$this->template->set_var('row_date',$item['date']);
				if (strlen($item['news_subject']) > 40)
				{
					$subject = $phpgw->strip_html(substr($item['subject'],40,strlen($item['subject'])));
				}
				else
				{
					$subject = $phpgw->strip_html($item['subject']);
				}
				$this->template->set_var('row_subject',$subject);
				$this->template->set_var('row_status',$item['status']);

				$this->template->set_var('row_view','<a href="' . $phpgw->link('/news_admin/main.php','menuaction=news_admin.uiadmin.view&news_id=' . $item['id']) . '">' . lang('view') . '</a>');
				$this->template->set_var('row_edit','<a href="' . $phpgw->link('/news_admin/main.php','menuaction=news_admin.uiadmin.edit&news_id=' . $item['id']) . '">' . lang('edit') . '</a>');
				$this->template->set_var('row_delete','<a href="' . $phpgw->link('/news_admin/main.php','menuaction=news_admin.uiadmin.delete&news_id=' . $item['id']) . '">' . lang('Delete') . '</a>');

				$this->template->parse('rows','row',True);
			}

			$this->template->set_var('add_link',$phpgw->link('/news_admin/main.php','menuaction=news_admin.uiadmin.add'));
			$this->template->set_var('lang_add',lang('add'));

			$this->template->pfp('out','list');
		
		}


	}