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

	class sonews
	{
		var $db;

		function sonews()
		{
			$this->db       = $GLOBALS['phpgw']->db;
		}


		function get_news($news_id)
		{
			$news = array();
			if ($news_id)
			{
				$sql = 'SELECT * FROM phpgw_news WHERE news_id="'.$news_id.'"';
				$this->db->query($sql,__LINE__,__FILE__);
				if ($this->db->next_record())
				{
					$news[$this->db->f('news_id')] = array(
						'subject'	=> $this->db->f('news_subject'),
						'submittedby'	=> $this->db->f('news_submittedby'),
						'submissiondate' => $this->db->f('news_date'),
						'id' => $this->db->f('news_id'),
						'content'	=> stripslashes($this->db->f('news_content'))
					);
				}
			}
			return $news;
		}

		function get_numNewsInCat($cat_id = 0)
		{
			$this->db->query("SELECT COUNT(*) FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id'",__LINE__,__FILE__);
			if ($this->db->next_record())
			{
				return (int) $this->db->f(0);
			}
			else
			{
				return 0;
			}
		}
			
		function get_newslist($cat_id=0, $oldnews=false, $start, $total)
		{
			if (! $oldnews)
			{
				$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id' ORDER BY news_date DESC",0,__LINE__,__FILE__,5);
			}
			else
			{
				$this->db->limit_query("SELECT * FROM phpgw_news WHERE news_status='Active' AND news_cat='$cat_id' ORDER BY news_date DESC ",$start,__LINE__,__FILE__,$total);
			}

			$news = array();

			while ($this->db->next_record())
			{
				$news[$this->db->f('news_id')] = array(
					'subject'	=> $this->db->f('news_subject'),
					'submittedby'	=> $this->db->f('news_submittedby'),
					'submissiondate' => $this->db->f('news_date'),
					'id' => $this->db->f('news_id'),
					'content'	=> nl2br(stripslashes($this->db->f('news_content')))
				);
			}
			return $news;
		}
	}
