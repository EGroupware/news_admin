<?php

	class soadmin
	{
		var $db;

		function soadmin()
		{
			global $phpgw;

			$this->template = $phpgw->template;
			$this->db       = $phpgw->db;
		}

		function add($news)
		{
			global $phpgw_info;

			$this->db->query("insert into phpgw_news (news_date,news_submittedby,news_content,news_subject,"
					. "news_status,news_cat) values ('" . time() . "','" . $phpgw_info['user']['account_id'] . "','"
					. addslashes($news['content']) . "','" . addslashes($news['subject']) . "','"
					. $news['status'] . "','" . $news['cat'] . "')",__LINE__,__FILE__);
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

		function getlist($order,$sort)
		{
			if ($order)
			{
				$ordermethod = "order by $order $sort";
			}
			else
			{
				$ordermethod = 'order by news_date desc';
			}

			$this->db->query("select * from phpgw_news $ordermethod",__LINE__,__FILE__);
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