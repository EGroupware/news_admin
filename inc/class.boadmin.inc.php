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

	class boadmin
	{
		function boadmin()
		{
			$this->so = createobject('news_admin.soadmin');
		}

		function delete()
		{
			$news_id = $GLOBALS['HTTP_POST_VARS']['news_id'] ? $GLOBALS['HTTP_POST_VARS']['news_id'] : $GLOBALS['HTTP_GET_VARS']['news_id'];

			$this->so->delete($news_id);
			ExecMethod('news_admin.uiadmin.news_list',array('message' => lang('Item has been deleted')));
		}

		function add($news)
		{
			return $this->so->add($news);
		}

		function edit($news)
		{
			return $this->so->edit($news);
		}

		function format_fields($fields)
		{
			$cat = createobject('phpgwapi.categories','news_admin');

			$item = array(
				'id'          => $fields['id'],
				'date'        => $GLOBALS['phpgw']->common->show_date($fields['date']),
				'subject'     => $GLOBALS['phpgw']->strip_html($fields['subject']),
				'submittedby' => $fields['submittedby'],
				'content'     => $fields['content'],
				'status'      => lang($fields['status']),
				'cat'         => $cat->id2name($fields['cat'])
			);
			return $item;
		}

		function view($news_id, $raw_values = False)
		{
			$item = $this->so->view($news_id);

			if (! $raw_values)
			{
				$_item = $this->format_fields($item);
			}
			else
			{
				$_item = $item;
			}

			return $_item;
		}

		function total($cat_id)
		{
			return $this->so->total($cat_id);
		}

		function getlist($order,$sort,$cat_id)
		{
			$items = $this->so->getlist($order,$sort,$cat_id);

			while (is_array($items) && $item = each($items))
			{
				$_items[] = $this->format_fields($item[1]);
			}
			return $_items;
		}
	}
