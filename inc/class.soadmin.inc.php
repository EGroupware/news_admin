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

	class soadmin
	{
		var $db;

		function soadmin()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->db       = $GLOBALS['phpgw']->db;
		}

		function add($news)
		{
			$this->db->query("insert into phpgw_news (news_date,news_submittedby,news_content,news_subject,"
				. "news_status,news_cat) values ('" . time() . "','" . $GLOBALS['phpgw_info']['user']['account_id'] . "','"
				. addslashes($news['content']) . "','" . addslashes($news['subject']) . "','"
				. $news['status'] . "','" . $news['category'] . "')",__LINE__,__FILE__);
		}

		function edit($news_id)
		{
		}

		function delete($news_id)
		{
			$this->db->query("delete from phpgw_news where news_id='$news_id'",__LINE__,__FILE__);
		}

		function total($cat_id)
		{
			$this->db->query("select count(*) from phpgw_news where news_cat='$cat_id'",__LINE__,__FILE__);
			$this->db->next_record();

			return $this->db->f(0);
		}

		function view($news_id)
		{
			$this->db->query("select * from phpgw_news where news_id='$news_id'",__LINE__,__FILE__);
			$this->db->next_record();

			$items = array(
				'id'          => $this->db->f('news_id'),
				'date'        => $this->db->f('news_date'),
				'subject'     => $this->db->f('news_subject'),
				'submittedby' => $this->db->f('news_submittedby'),
				'content'     => $this->db->f('news_content'),
				'status'      => $this->db->f('news_status'),
				'cat'         => $this->db->f('news_cat')
			);
			return $items;
		}

		function getlist($order,$sort,$cat_id)
		{
			if ($order)
			{
				$ordermethod = "order by $order $sort";
			}
			else
			{
				$ordermethod = 'order by news_date desc';
			}

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->db->query("select * from phpgw_news where news_cat='$cat_id' $ordermethod",__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$items[] = array(
					'id'          => $this->db->f('news_id'),
					'date'        => $this->db->f('news_date'),
					'subject'     => $this->db->f('news_subject'),
					'submittedby' => $this->db->f('news_submittedby'),
					'content'     => $this->db->f('news_content'),
					'status'      => $this->db->f('news_status'),
					'cat'         => $this->db->f('news_cat')
				);
			}
			return $items;
		}
	}
?>
