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

	class uiadmin
	{
		var $db;
		var $template;
		var $session_data;
		var $public_functions = array(
			'news_list' => True,
			'add'       => True,
			'view'      => True,
			'edit'      => True,
			'delete'    => True
			);

		function uiadmin()
		{
			$GLOBALS['phpgw']->nextmatchs = createobject('phpgwapi.nextmatchs');
			$this->template     = $GLOBALS['phpgw']->template;
			$this->db           = $GLOBALS['phpgw']->db;
			$this->session_data = $GLOBALS['phpgw']->session->appsession('session_data','news_admin');
		}

		function common_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function save_session_data()
		{
			$GLOBALS['phpgw']->session->appsession('session_data','news_admin',$this->session_data);
		}

		function view()
		{
			$news_id = $GLOBALS['HTTP_POST_VARS']['news_id'] ? $GLOBALS['HTTP_POST_VARS']['news_id'] : $GLOBALS['HTTP_GET_VARS']['news_id'];

			$this->common_header();

			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('news_admin'));
			$this->template->set_file(array(
				'_news' => 'news.tpl',
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');

			$bo     = createobject('news_admin.boadmin');
			$fields = $bo->view($news_id);

			$this->template->set_var('icon',$GLOBALS['phpgw']->common->get_image_path('news_admin') . '/news-corner.gif');
			$this->template->set_var('subject',$fields['subject']);
			$this->template->set_var('submitedby','Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($fields['submittedby']) . ' on ' . $fields['date']);
			$this->template->set_var('content',nl2br($fields['content']));

			$this->template->parse('rows','row',True);

			$this->template->pfp('_out','news_form');
		}

		function delete()
		{
			$news_id = $GLOBALS['HTTP_POST_VARS']['news_id'] ? $GLOBALS['HTTP_POST_VARS']['news_id'] : $GLOBALS['HTTP_GET_VARS']['news_id'];
			$cat_id  = $GLOBALS['HTTP_POST_VARS']['cat_id'] ? $GLOBALS['HTTP_POST_VARS']['cat_id'] : $GLOBALS['HTTP_GET_VARS']['cat_id'];

			$this->common_header();
			$this->template->set_file(array(
				'form' => 'admin_delete.tpl'
			));
			$this->template->set_var('lang_message',lang('Are you sure you want to delete this entry ?'));
			$this->template->set_var('lang_yes',lang('Yes'));
			$this->template->set_var('lang_no',lang('No'));

			$this->template->set_var('link_yes',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.boadmin.delete&news_id=' . $news_id));
			$this->template->set_var('link_no',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));

			$this->template->pfp('_out','form');
		}

		function add($errors = '')
		{
			$news = $GLOBALS['HTTP_POST_VARS']['news'];

			$this->common_header();

			$cats = createobject('phpgwapi.categories');

			$GLOBALS['phpgw']->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if (is_array($errors))
			{
				$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Add news item'));
			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.boadmin.add'));
			$GLOBALS['phpgw']->template->set_var('form_button','<input type="submit" name="submit" value="' . lang('Add') . '">');

			$GLOBALS['phpgw']->template->set_var('label_subject',lang('subject') . ':');
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="news[subject]" size="60" value="' . $news['subject'] . '">');

			$GLOBALS['phpgw']->template->set_var('label_content',lang('Content') . ':');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea cols="60" rows="6" name="news[content]" wrap="virtual">' . stripslashes($news['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('label_category',lang('Category') . ':');
			$GLOBALS['phpgw']->template->set_var('value_category','<select name="news[category]"><option value="0">' . lang('Main') . '</option>' . $cats->formated_list('select','mains',$news['category']) . '</select>');

			$GLOBALS['phpgw']->template->set_var('label_status',lang('Status') . ':');
			$GLOBALS['phpgw']->template->set_var('value_status','<select name="news[status]"><option value="Active">'
				. lang('Active') . '</option><option value="Disabled">'
				. lang('Disabled') . '</option></select>');

			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function edit($errors = '')
		{
			$news = $GLOBALS['HTTP_POST_VARS']['news'];

			$this->common_header();
			$cats = createobject('phpgwapi.categories');

			$GLOBALS['phpgw']->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if (is_array($errors))
			{
				$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Edit news item'));

			if (is_array($fields))
			{
				$bo     = createobject('news_admin.boadmin');
				$fields = $bo->view($news_id,True);
			}

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.boadmin.edit'));
			$GLOBALS['phpgw']->template->set_var('form_button','<input type="submit" name="submit" value="' . lang('Edit') . '">');

			$GLOBALS['phpgw']->template->set_var('label_subject',lang('subject') . ':');
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="news[subject]" size="60" value="' . $news['subject'] . '">');

			$GLOBALS['phpgw']->template->set_var('label_content',lang('Content') . ':');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea cols="60" rows="6" name="news[content]" wrap="virtual">' . stripslashes($news['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('label_category',lang('Category') . ':');
			$GLOBALS['phpgw']->template->set_var('value_category','<select name="news[category]"><option value="0">' . lang('Main') . '</option>' . $cats->formated_list('select','mains',$news['category']) . '</select>');

			$GLOBALS['phpgw']->template->set_var('label_status',lang('Status') . ':');
			$GLOBALS['phpgw']->template->set_var('value_status','<select name="news[status]"><option value="Active">'
				. lang('Active') . '</option><option value="Disabled">'
				. lang('Disabled') . '</option></select>');

			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function news_list($message = '')
		{
			$news_id = $GLOBALS['HTTP_POST_VARS']['news_id'] ? $GLOBALS['HTTP_POST_VARS']['news_id'] : $GLOBALS['HTTP_GET_VARS']['news_id'];
			$sort    = $GLOBALS['HTTP_POST_VARS']['sort']    ? $GLOBALS['HTTP_POST_VARS']['sort']    : $GLOBALS['HTTP_GET_VARS']['sort'];
			$cat_id  = $GLOBALS['HTTP_POST_VARS']['cat_id']  ? $GLOBALS['HTTP_POST_VARS']['cat_id']  : $GLOBALS['HTTP_GET_VARS']['cat_id'];

			$this->common_header();

			if(!$cat_id)
			{
				$cat_id = 0;
			}
			elseif (! $cat_id && $cat_id != '0')
			{
				if (! $this->session_data['cat_id'])
				{
					$cat_id = 0;
				}
				else
				{
					$cat_id = $this->session_data['cat_id'];
				}
			}

			if (! $order)
			{
				if ($this->session_data['order'])
				{
					$order = $this->session_data['order'];
				}
			}

			if (! $sort)
			{
				if ($this->session_data['sort'])
				{
					$sort = $this->session_data['sort'];
				}
			}

			$this->session_data['order']  = $order;
			$this->session_data['sort']   = $sort;
			$this->session_data['cat_id'] = $cat_id;
			$this->save_session_data();

			$this->template->set_file(array(
				'_list' => 'admin_list.tpl'
			));
			$this->template->set_block('_list','list');
			$this->template->set_block('_list','row');
			$this->template->set_block('_list','row_empty');

			if ($message)
			{
				$this->template->set_var('message',$message);
			}

			$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);
			$this->template->set_var('lang_header',lang('Webpage news admin'));
			$this->template->set_var('lang_category',lang('Category'));
			$this->template->set_var('lang_main',lang('Main'));

			$cats = createobject('phpgwapi.categories');
			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('input_category',$cats->formated_list('select','mains',$cat_id));

			$this->template->set_var('header_date',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_date',$order,'/index.php',lang('Date'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_subject',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_subject',$order,'/index.php',lang('Subject'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_status',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_status',$order,'/index.php',lang('Status'),'&menuaction=news_admin.uiadmin.news_list'));
			$this->template->set_var('header_edit','edit');
			$this->template->set_var('header_delete','delete');
			$this->template->set_var('header_view','view');

			$nextmatchs = createobject('phpgwapi.nextmatchs');
			$bo         = createobject('news_admin.boadmin');
			$total      = $bo->total($cat_id);
			$items      = $bo->getlist($order,$sort,$cat_id);

			while (is_array($items) && $_item = each($items))
			{
				$item = $_item[1];

				$nextmatchs->template_alternate_row_color(&$this->template);
				$this->template->set_var('row_date',$item['date']);
				if (strlen($item['news_subject']) > 40)
				{
					$subject = $GLOBALS['phpgw']->strip_html(substr($item['subject'],40,strlen($item['subject'])));
				}
				else
				{
					$subject = $GLOBALS['phpgw']->strip_html($item['subject']);
				}
				$this->template->set_var('row_subject',$subject);
				$this->template->set_var('row_status',$item['status']);

				$this->template->set_var('row_view','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.view&news_id=' . $item['id']) . '">' . lang('view') . '</a>');
				$this->template->set_var('row_edit','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.edit&news_id=' . $item['id']) . '">' . lang('edit') . '</a>');
				$this->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.delete&news_id=' . $item['id']) . '">' . lang('Delete') . '</a>');

				$this->template->parse('rows','row',True);
			}

			if (! $total)
			{
				$nextmatchs->template_alternate_row_color(&$this->template);
				$this->template->set_var('row_message',lang('No entries found'));
				$this->template->parse('rows','row_empty',True);
			}

			if ($total)
			{
				$this->template->set_var('link_view_cat','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.show_news&cat_id=' . $cat_id) . '">' . lang('View this category') . '</a>');
			}
			else
			{
				$this->template->set_var('link_view_cat',lang('View this category'));
			}

			$this->template->set_var('link_add',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.add&news%5Bcategory%5D=' . $cat_id));
			$this->template->set_var('lang_add',lang('add'));

			$this->template->pfp('out','list');
		}
	}
