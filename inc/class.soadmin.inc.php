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
			$this->db = $GLOBALS['phpgw']->db;
		}

		function add($news)
		{
			$this->db->query("INSERT INTO phpgw_news (news_date,news_submittedby,news_content,news_subject,"
				. "news_status,news_cat) VALUES ('" . time() . "','" . $GLOBALS['phpgw_info']['user']['account_id'] . "','"
				. addslashes($news['content']) . "','" . addslashes($news['subject']) . "','"
				. $news['status'] . "','" . $news['category'] . "')",__LINE__,__FILE__);
		}

		function edit($news)
		{
			$this->db->query("UPDATE phpgw_news SET "
				. "news_date='" . time() . "',"
				. "news_content='" . addslashes($news['content']) . "',"
				. "news_subject='" . addslashes($news['subject']) . "' "
				. "WHERE news_id=" . intval($news['id']),__LINE__,__FILE__);
		}

		function delete($news_id)
		{
			$this->db->query("DELETE FROM phpgw_news WHERE news_id='$news_id'",__LINE__,__FILE__);
		}

		function total($cat_id)
		{
			$this->db->query("SELECT COUNT(*) FROM phpgw_news WHERE news_cat='$cat_id'",__LINE__,__FILE__);
			$this->db->next_record();

			return $this->db->f(0);
		}

		function view($news_id)
		{
			$this->db->query("SELECT * FROM phpgw_news WHERE news_id='$news_id'",__LINE__,__FILE__);
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
				$ordermethod = "ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = 'ORDER BY news_date DESC';
			}

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->db->query("SELECT * FROM phpgw_news WHERE news_cat='$cat_id' $ordermethod",__LINE__,__FILE__);
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
