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
		var $bo;
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
			$this->session_data = $GLOBALS['phpgw']->session->appsession('session_data','news_admin');
			$this->bo   = createobject('news_admin.boadmin');
			$this->cats = createobject('phpgwapi.categories');
		}

		function common_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			//echo parse_navbar();
		}

		function save_session_data()
		{
			$GLOBALS['phpgw']->session->appsession('session_data','news_admin',$this->session_data);
		}

		function view()
		{
			$news_id = get_var('news_id',Array('GET','POST'));

			$this->common_header();

			$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('news_admin'));
			$GLOBALS['phpgw']->template->set_file(array(
				'_news' => 'news.tpl',
			));
			$GLOBALS['phpgw']->template->set_block('_news','news_form');
			$GLOBALS['phpgw']->template->set_block('_news','row');

			$fields = $this->bo->view($news_id);

			$GLOBALS['phpgw']->template->set_var('icon',$GLOBALS['phpgw']->common->get_image_path('news_admin') . '/news-corner.gif');
			$GLOBALS['phpgw']->template->set_var('subject',$fields['subject']);
			$GLOBALS['phpgw']->template->set_var('submitedby','Submitted by ' . $GLOBALS['phpgw']->accounts->id2name($fields['submittedby']) . ' on ' . $fields['date']);
			$GLOBALS['phpgw']->template->set_var('content',nl2br($fields['content']));

			$GLOBALS['phpgw']->template->parse('rows','row',True);

			$GLOBALS['phpgw']->template->pfp('_out','news_form');
		}

		function delete()
		{
			$news_id = get_var('news_id',Array('GET','POST'));
			$cat_id = get_var('cat_id',Array('GET','POST'));

			$this->common_header();
			$GLOBALS['phpgw']->template->set_file(array(
				'form' => 'admin_delete.tpl'
			));
			$GLOBALS['phpgw']->template->set_var('lang_message',lang('Are you sure you want to delete this entry ?'));
			$GLOBALS['phpgw']->template->set_var('lang_yes',lang('Yes'));
			$GLOBALS['phpgw']->template->set_var('lang_no',lang('No'));

			$GLOBALS['phpgw']->template->set_var('link_yes',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.boadmin.delete&news_id=' . $news_id));
			$GLOBALS['phpgw']->template->set_var('link_no',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));

			$GLOBALS['phpgw']->template->pfp('_out','form');
		}

		function add($errors = '')
		{
			$news   = get_var('news',Array('POST'));
			$submit = get_var('submit',Array('POST'));

			if(get_var('cancel',Array('POST')))
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));
			}
			if($submit)
			{
				if (! $news['subject'])
				{
					$errors[] = lang('The subject is missing');
				}
				if (! $news['content'])
				{
					$errors[] = lang('The news content is missing');
				}

				if (!is_array($errors))
				{
					$this->bo->add($news);
					$message = lang('News item has been added');
				}
			}

			$this->common_header();

			$GLOBALS['phpgw']->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if (is_array($errors))
			{
				$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}
			elseif($message)
			{
				$GLOBALS['phpgw']->template->set_var('errors',$message);
			}

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Add news item'));
			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.add'));
			$GLOBALS['phpgw']->template->set_var('form_button','<input type="submit" name="submit" value="' . lang('Add') . '">');
			$GLOBALS['phpgw']->template->set_var('done_button','<input type="submit" name="cancel" value="' . lang('Done') . '">');

			$GLOBALS['phpgw']->template->set_var('label_subject',lang('subject') . ':');
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="news[subject]" size="60" value="' . $news['subject'] . '">');

			$GLOBALS['phpgw']->template->set_var('label_content',lang('Content') . ':');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea cols="60" rows="6" name="news[content]" wrap="virtual">' . stripslashes($news['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('label_category',lang('Category') . ':');
			$GLOBALS['phpgw']->template->set_var('value_category','<select name="news[category]"><option value="0">' . lang('Main') . '</option>' . $this->cats->formated_list('select','mains',$news['category']) . '</select>');

			$GLOBALS['phpgw']->template->set_var('label_status',lang('Status') . ':');
			$GLOBALS['phpgw']->template->set_var('value_status','<select name="news[status]"><option value="Active">'
				. lang('Active') . '</option><option value="Disabled">'
				. lang('Disabled') . '</option></select>');

			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function edit($errors = '')
		{
			$news    = get_var('news',Array('POST'));
			$news_id = get_var('news_id',Array('GET'));

			if(get_var('cancel',Array('POST')))
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));
			}
			if (is_array($news))
			{
				if (! $news['subject'])
				{
					$errors[] = lang('The subject is missing');
				}
				if (! $news['content'])
				{
					$errors[] = lang('The news content is missing');
				}

				if (!is_array($errors))
				{
					$this->bo->edit($news);
					$message = lang('News item has been updated');
				}
			}
			else
			{
				$news = $this->bo->view($news_id,True);
			}

			$this->common_header();

			$GLOBALS['phpgw']->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if(is_array($errors))
			{
				$GLOBALS['phpgw']->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}
			elseif($message)
			{
				$GLOBALS['phpgw']->template->set_var('errors',$message);
			}

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);

			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Edit news item'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.edit'));
			$GLOBALS['phpgw']->template->set_var('form_button','<input type="submit" name="submit" value="' . lang('Edit') . '">');
			$GLOBALS['phpgw']->template->set_var('done_button','<input type="submit" name="cancel" value="' . lang('Done') . '">');
			$GLOBALS['phpgw']->template->set_var('value_id',$news_id);

			$GLOBALS['phpgw']->template->set_var('label_subject',lang('subject') . ':');
			$GLOBALS['phpgw']->template->set_var('value_subject','<input name="news[subject]" size="60" value="' . $news['subject'] . '">');

			$GLOBALS['phpgw']->template->set_var('label_content',lang('Content') . ':');
			$GLOBALS['phpgw']->template->set_var('value_content','<textarea cols="60" rows="6" name="news[content]" wrap="virtual">' . stripslashes($news['content']) . '</textarea>');

			$GLOBALS['phpgw']->template->set_var('label_category',lang('Category') . ':');
			$GLOBALS['phpgw']->template->set_var('value_category','<select name="news[category]"><option value="0">' . lang('Main') . '</option>' . $this->cats->formated_list('select','mains',$news['category']) . '</select>');

			$GLOBALS['phpgw']->template->set_var('label_status',lang('Status') . ':');
			$GLOBALS['phpgw']->template->set_var('value_status','<select name="news[status]"><option value="Active">'
				. lang('Active') . '</option><option value="Disabled">'
				. lang('Disabled') . '</option></select>');

			$GLOBALS['phpgw']->template->pfp('out','form');
		}

		function news_list($message = '')
		{
			$news_id = get_var('news_id',Array('GET','POST'));
			$sort    = get_var('sort',Array('GET','POST'));
			$cat_id  = get_var('cat_id',Array('GET','POST'));

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

			$GLOBALS['phpgw']->template->set_file(array(
				'_list' => 'admin_list.tpl'
			));
			$GLOBALS['phpgw']->template->set_block('_list','list');
			$GLOBALS['phpgw']->template->set_block('_list','row');
			$GLOBALS['phpgw']->template->set_block('_list','row_empty');

			if ($message)
			{
				$GLOBALS['phpgw']->template->set_var('message',$message);
			}

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('bgcolor',$GLOBALS['phpgw_info']['theme']['bgcolor']);
			$GLOBALS['phpgw']->template->set_var('lang_header',lang('Webpage news admin'));
			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main'));

			$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.news_list'));
			$GLOBALS['phpgw']->template->set_var('input_category',$this->cats->formated_list('select','mains',$cat_id));

			$GLOBALS['phpgw']->template->set_var('header_date',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_date',$order,'/index.php',lang('Date'),'&menuaction=news_admin.uiadmin.news_list'));
			$GLOBALS['phpgw']->template->set_var('header_subject',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_subject',$order,'/index.php',lang('Subject'),'&menuaction=news_admin.uiadmin.news_list'));
			$GLOBALS['phpgw']->template->set_var('header_status',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'news_status',$order,'/index.php',lang('Status'),'&menuaction=news_admin.uiadmin.news_list'));
			$GLOBALS['phpgw']->template->set_var('header_edit','edit');
			$GLOBALS['phpgw']->template->set_var('header_delete','delete');
			$GLOBALS['phpgw']->template->set_var('header_view','view');

			$nextmatchs = createobject('phpgwapi.nextmatchs');
			$total      = $this->bo->total($cat_id);
			$items      = $this->bo->getlist($order,$sort,$cat_id);

			while (is_array($items) && $_item = each($items))
			{
				$item = $_item[1];

				$nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);
				$GLOBALS['phpgw']->template->set_var('row_date',$item['date']);
				if (strlen($item['news_subject']) > 40)
				{
					$subject = $GLOBALS['phpgw']->strip_html(substr($item['subject'],40,strlen($item['subject'])));
				}
				else
				{
					$subject = $GLOBALS['phpgw']->strip_html($item['subject']);
				}
				$GLOBALS['phpgw']->template->set_var('row_subject',$subject);
				$GLOBALS['phpgw']->template->set_var('row_status',$item['status']);

				$GLOBALS['phpgw']->template->set_var('row_view','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.view&news_id=' . $item['id']) . '">' . lang('view') . '</a>');
				$GLOBALS['phpgw']->template->set_var('row_edit','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.edit&news_id=' . $item['id']) . '">' . lang('edit') . '</a>');
				$GLOBALS['phpgw']->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.delete&news_id=' . $item['id']) . '">' . lang('Delete') . '</a>');

				$GLOBALS['phpgw']->template->parse('rows','row',True);
			}

			if (! $total)
			{
				$nextmatchs->template_alternate_row_color(&$GLOBALS['phpgw']->template);
				$GLOBALS['phpgw']->template->set_var('row_message',lang('No entries found'));
				$GLOBALS['phpgw']->template->parse('rows','row_empty',True);
			}

			if ($total)
			{
				$GLOBALS['phpgw']->template->set_var('link_view_cat','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uinews.show_news&cat_id=' . $cat_id) . '">' . lang('View this category') . '</a>');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('link_view_cat',lang('View this category'));
			}

			$GLOBALS['phpgw']->template->set_var('link_add',$GLOBALS['phpgw']->link('/index.php','menuaction=news_admin.uiadmin.add&news%5Bcategory%5D=' . $cat_id));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('add'));

			$GLOBALS['phpgw']->template->pfp('out','list');
		}
	}
