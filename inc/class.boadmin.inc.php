<?php

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
				$ui->news_list();
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

		function getlist($order,$sort)
		{
			global $phpgw;

			$so    = createobject('news_admin.soadmin');

			$items = $so->getlist($order,$sort);

			while (is_array($items) && $item = each($items))
			{
				$_items[] = $this->format_fields($item[1]);
			}
			return $_items;
		}

	}