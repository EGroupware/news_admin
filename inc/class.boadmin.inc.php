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
		var $public_functions = array(
					'add'    => True,
					'edit'   => True,
					'delete' => True
				);

		function boadmin()
		{
		
		
		}

		function delete()
		{
			global $news_id;

			$so = createobject('news_admin.soadmin');
			$ui = createobject('news_admin.uiadmin');

			$so->delete($news_id);
			$ui->news_list(lang('Item has been deleted'));
		}

		function add()
		{
			global $news;

			$so = createobject('news_admin.soadmin');
			$ui = createobject('news_admin.uiadmin');

			if (! $news['subject'])
			{
				$errors[] = lang('The subject is missing');
			}

			if (! $news['content'])
			{
				$errors[] = lang('The news content is missing');
			}

			if (is_array($errors))
			{
				$ui->add($errors);
			}
			else
			{
				$so->add($news);
				$ui->news_list(lang('New item has been added'));
			}
		}

		function format_fields($fields)
		{
			global $phpgw;
			$cat    = createobject('phpgwapi.categories','news_admin');

			$item = array(
				'id'          => $fields['id'],
				'date'        => $phpgw->common->show_date($fields['date']),
				'subject'     => $phpgw->strip_html($fields['subject']),
				'submittedby' => $fields['submittedby'],
				'content'     => $fields['content'],
				'status'      => lang($fields['status']),
				'cat'         => $cat->id2name($fields['cat'])
			);
			return $item;
		}

		function view($news_id)
		{
			$so     = createobject('news_admin.soadmin');
			$item   = $so->view($news_id);

			$_item = $this->format_fields($item);

			return $_item;
		}

		function getlist($order,$sort,$cat_id)
		{
			global $phpgw;

			$so    = createobject('news_admin.soadmin');

			$items = $so->getlist($order,$sort,$cat_id);

			while (is_array($items) && $item = each($items))
			{
				$_items[] = $this->format_fields($item[1]);
			}
			return $_items;
		}

	}