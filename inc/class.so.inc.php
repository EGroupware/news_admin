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

	class so
	{
		var $db;

		function so()
		{
			$this->db       = $GLOBALS['phpgw']->db;
		}

			
		function get_newslist($cat_id, $start, $order,$sort,$limit=0,$activeonly)
		{
			if ($order)
			{
				$ordermethod = ' ORDER BY ' . $this->db->db_addslashes($order) . ' ' . $this->db->db_addslashes($sort);
			}
			else
			{
				$ordermethod = ' ORDER BY news_date DESC';
			}

			$sql = 'SELECT * FROM phpgw_news WHERE news_cat=' . intval($cat_id);
			$sql .= $activeonly ? " AND news_status='Active'" : '';


			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__,$limit);

			$news = array();

			while ($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array(
					'subject'	=> htmlentities(stripslashes($this->db->f('news_subject'))),
					'submittedby'	=> $this->db->f('news_submittedby'),
					'date' => $this->db->f('news_date'),
					'id' => $this->db->f('news_id'),
					'status' => $this->db->f('news_status'),
					'teaser' => htmlentities(stripslashes($this->db->f('news_teaser'))),
					'content'	=> nl2br(htmlentities(stripslashes($this->db->f('news_content'))))
				);
			}
			return $news;
		}

		function get_all_public_news($limit=5)
		{
			$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' ORDER BY news_date DESC",0,__LINE__,__FILE__,$limit);

			$news = array();

			while ($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array(
					'subject'	=> $this->db->f('news_subject'),
					'submittedby'	=> $this->db->f('news_submittedby'),
					'date' => $this->db->f('news_date'),
					'id' => $this->db->f('news_id'),
					'teaser' => $this->db->f('news_teaser'),
					'content'	=> nl2br(stripslashes($this->db->f('news_content')))
				);
			}
			return $news;
		}

		function add($news)
		{
			$sql  = 'INSERT INTO phpgw_news (news_date,news_submittedby,news_content,news_subject,news_status,news_teaser,news_cat) ';
			$sql .= 'VALUES (' . mktime(0,0,0, intval($news['date_m']), intval($news['date_d']), intval($news['date_y'])) . ',';
			$sql .=  $GLOBALS['phpgw_info']['user']['account_id'] . ",'" . $this->db->db_addslashes($news['content']) ."','";
			$sql .=  $this->db->db_addslashes($news['subject']) ."','" . $this->db->db_addslashes($news['status']) . "','";
			$sql .=  $this->db->db_addslashes($news['teaser']) . "'," . intval($news['category']) . ')';
			$this->db->query($sql);

			return $this->db->get_last_insert_id('phpgw_news', 'news_id');
		}

		function edit($news)
		{
			$this->db->query("UPDATE phpgw_news SET "
				. "news_date='" . mktime(0,0,0,intval($news['date_m']), intval($news['date_d']), intval($news['date_y'])) . "',"
				. "news_content='" . $this->db->db_addslashes($news['content']) . "',"
				. "news_subject='" . $this->db->db_addslashes($news['subject']) . "', "
				. "news_teaser='" . $this->db->db_addslashes($news['teaser']) . "', "
				. "news_status='" . $this->db->db_addslashes($news['status']) . "', "
				. "news_cat='" . $this->db->db_addslashes($news['category']) . "' "
				. "WHERE news_id=" . intval($news['id']),__LINE__,__FILE__);
		}

		function delete($news_id)
		{
			$this->db->query('DELETE FROM phpgw_news WHERE news_id=' . intval($news_id) ,__LINE__,__FILE__);
		}


		function total($cat_id,$activeonly)
		{
			$sql = 'SELECT COUNT(*) FROM phpgw_news WHERE news_cat=' . intval($cat_id);
			$sql .= $aciveonly ? " AND news_status='Active'" : '';
			$this->db->query($sql,__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return (int) $this->db->f(0);
			}
			else
			{
				return 0;
			}
		}

		function get_news($news_id)
		{
			$this->db->query('SELECT * FROM phpgw_news WHERE news_id=' . intval($news_id),__LINE__,__FILE__);
			$this->db->next_record();

			$items = array(
				'id'          => $this->db->f('news_id'),
				'date'        => $this->db->f('news_date'),
				'subject'     => $this->db->f('news_subject'),
				'submittedby' => $this->db->f('news_submittedby'),
				'teaser'			=> $this->db->f('news_teaser'),
				'content'     => $this->db->f('news_content'),
				'status'      => $this->db->f('news_status'),
				'category'    => $this->db->f('news_cat')
			);
			return $items;
		}

// 		function getlist($order,$sort,$cat_id)
// 		{
// 			if ($order)
// 			{
// 				$ordermethod = ' ORDER BY ' . $this->db->db_addslashes($order) . ' ' . $this->db->db_addslashes($sort);
// 			}
// 			else
// 			{
// 				$ordermethod = ' ORDER BY news_date DESC';
// 			}

// 			$this->db->query('SELECT * FROM phpgw_news WHERE news_cat=' . intval($cat_id) . $ordermethod,__LINE__,__FILE__);
// 			while ($this->db->next_record())
// 			{
// 				$items[] = array(
// 					'id'          => $this->db->f('news_id'),
// 					'date'        => $this->db->f('news_date'),
// 					'subject'     => $this->db->f('news_subject'),
// 					'submittedby' => $this->db->f('news_submittedby'),
// 					'content'     => $this->db->f('news_content'),
// 					'status'      => $this->db->f('news_status'),
// 					'cat'         => $this->db->f('news_cat')
// 				);
// 			}
// 			return $items;
// 		}

	}
