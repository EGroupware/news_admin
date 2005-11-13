<?php
	/**************************************************************************\
	* eGroupWare - News                                                        *
	* http://www.egroupware.org                                                *
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

	class sonews
	{
		var $db;
		var $table = 'egw_news';

		function sonews()
		{
			$this->db = clone($GLOBALS['egw']->db);
			$this->db->set_app('news_admin');
		}

		function get_newslist($cat_id, $start, $order,$sort,$limit=0,$activeonly,&$total)
		{
			if(!$order || !preg_match('/^[a-z0-9_]+$/i',$order) || !preg_match('/^(asc|desc)?$/i',$sort))
			{
				$ordermethod = ' ORDER BY news_date DESC';
			}
			else
			{
				$ordermethod = ' ORDER BY '.$order . ' ' . $sort;
			}

			$where = array('news_cat' => $cat_id);
			if($activeonly)
			{
				$now = time();
				$where[] = "news_begin <= $now AND news_end >= $now";
			}
			//$this->db->select($this->table,'COUNT(*)',$where,__LINE__,__FILE__);
			//$total = $this->db->next_record() ? $this->db->f(0) : 0;

			$this->db->select($this->table,'*',$where,__LINE__,__FILE__,$start,$ordermethod,false,$limit);

			$news = array();
			while($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array(
					'subject' => @htmlspecialchars($this->db->f('news_subject', True),ENT_COMPAT,$GLOBALS['egw']->translation->charset()),
					'submittedby' => $this->db->f('news_submittedby'),
					'date'    => $this->db->f('news_date'),
					'id'      => $this->db->f('news_id'),
					'category' => $this->db->f('news_cat'),
					'begin'   => $this->db->f('news_begin'),
					'end'     => $this->db->f('news_end'),
					'teaser'  => @htmlspecialchars($this->db->f('news_teaser', True),ENT_COMPAT,$GLOBALS['egw']->translation->charset()),
					'content' => $this->db->f('news_content',True),
					'is_html' => ($this->db->f('is_html') ? True : False),
				);
			}
			return $news;
		}

		function get_all_public_news($limit=5)
		{
			$now = time();
			$this->db->select($this->table,'*',"news_begin <= $now AND news_end >= $now",__LINE__,__FILE__,
				0,'ORDER BY news_date DESC',false,$limit);

			$news = array();
			while ($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array(
					'subject' => $this->db->f('news_subject', True),
					'submittedby' => $this->db->f('news_submittedby'),
					'date'    => $this->db->f('news_date'),
					'id'      => $this->db->f('news_id'),
					'teaser'  => $this->db->f('news_teaser', True),
					'content' => $this->db->f('news_content', True),
					'is_html' => ($this->db->f('is_html') ? True : False),
				);
			}
			return $news;
		}

		function add($news)
		{
			$this->db->insert($this->table,array(
				'news_date'			=> (int)$news['date'],
				'news_submittedby'	=> $GLOBALS['egw_info']['user']['account_id'],
				'news_content'		=> $news['content'],
				'news_subject'		=> $news['subject'],
				'news_begin'		=> (int)$news['begin'],
				'news_end'			=> (int)$news['end'],
				'news_teaser'		=> $news['teaser'],
				'news_cat'			=> (int)$news['category'],
				'is_html'			=> (int)!!$news['is_html'],
				//added by wbshang,2005-5-13
				// removed by RalfBecker 2005-11-13, as mail_receiver is no column
//				'mail_receiver'     => @implode(",",$news['mailto']),
			),false, __LINE__, __FILE__);

			return $this->db->get_last_insert_id($this->table, 'news_id');
		}

		function edit($news)
		{
			$this->db->update($this->table,array(
				'news_content'	=> $news['content'],
				'news_subject'	=> $news['subject'],
				'news_teaser'	=> $news['teaser'],
				'news_begin'	=> $news['begin'],
				'news_end'		=> $news['end'],
				'news_cat'		=> $news['category'],
				'is_html'		=> $news['is_html'] ? 1 : 0,
				//added by wbshang,2005-5-13
				// removed by RalfBecker 2005-11-13, as mail_receiver is no column
//				'mail_receiver'     => @implode(",",$news['mailto']),
			), array('news_id' => (int)$news['id']), __LINE__, __FILE__);
		}

		function delete($news_id)
		{
			$this->db->delete($this->table,array('news_id' => $news_id),__LINE__,__FILE__);
		}

		function get_news($news_id)
		{
			$this->db->select($this->table,'*',array('news_id' => $news_id),__LINE__,__FILE__);
			$this->db->next_record();

			return array(
				'id'       => $this->db->f('news_id'),
				'date'     => $this->db->f('news_date'),
				'subject'  => $this->db->f('news_subject', True),
				'submittedby' => $this->db->f('news_submittedby'),
				'teaser'   => $this->db->f('news_teaser', True),
				'content'  => $this->db->f('news_content', True),
				'begin'    => $this->db->f('news_begin'),
				'end'      => $this->db->f('news_end'),
				'category' => $this->db->f('news_cat'),
				'is_html'  => ($this->db->f('is_html') ? True : False),
			);
		}

		// the following functions are added by wbshang,2005-5-13

		// to get the cat_ids that have received the news by email
		function get_receiver_cats($news_id)
		{
			// removed by RalfBecker 2005-11-13, as mail_receiver is no column
/*
			$sql = "SELECT mail_receiver FROM phpgw_news WHERE news_id=" . (int)$news_id;
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('mail_receiver');
*/
		}
	}
