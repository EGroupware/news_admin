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

	class bo
	{
		var $sonews;
		var $acl;
		var $start = 0;
		var $query = '';
		var $sort  = '';
		var $cat_id;
		var $total = 0;
		var $debug;
		var $use_session = False;

		function bo($session=False)
		{
			$this->acl = CreateObject('news_admin.boacl');
			$this->sonews = CreateObject('news_admin.so');
			$this->accounts = $GLOBALS['phpgw']->accounts->get_list();
			$this->debug = False;
			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
				foreach(array('start','query','sort','order','cat_id') as $var)
				{
					if (isset($_POST[$var]))
					{
						$this->$var = $_POST[$var];
					}
					elseif (isset($_GET[$var]))
					{
						$this->$var = $_GET[$var];
					}
				}
				$this->save_sessiondata();
			}
			$this->catbo = createobject('phpgwapi.categories');
			$this->cats = $this->catbo->return_array('all',0,False,'','','cat_name',True);
		}

		function save_sessiondata()
		{
			$data = array(
				'start' => $this->start,
				'query' => $this->query,
				'sort'  => $this->sort,
				'order' => $this->order,
				'cat_id' => $this->cat_id,
			);
			if($this->debug) { echo '<br>Save:'; _debug_array($data); }
			$GLOBALS['phpgw']->session->appsession('session_data','news_admin',$data);
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','news_admin');
			if($this->debug) { echo '<br>Read:'; _debug_array($data); }

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
			$this->cat_id = $data['cat_id'];
		}

		function get_newslist($cat_id, $start=0, $order='',$sort='',$limit=0,$activeonly=False)
		{
			return $this->acl->is_permitted($cat_id,PHPGW_ACL_READ) ?
				$this->sonews->get_newslist($cat_id, $start,$order,$sort,$limit,$activeonly) :
				array();
		}

		function get_all_public_news($limit = 5)
		{
			return $this->sonews->get_all_public_news($limit);
		}

		function delete($news_id)
		{
			$this->sonews->delete($news_id);
		}

		function add($news)
		{
			return $this->acl->is_permitted($news['category'],PHPGW_ACL_ADD) ?
				$this->sonews->add($news) :
				false;
		}

		function edit($news)
		{
			$oldnews = $this->so->get_news($news['id']);
			return ($this->acl->is_permitted($oldnews['category'],PHPGW_ACL_ADD) && 
					$this->acl->is_permitted($news['category'],PHPGW_ACL_ADD)) ?
				$this->sonews->edit($news) :
				False;
		}

// 		function format_fields($fields)
// 		{
// 			$cat = createobject('phpgwapi.categories','news_admin');

// 			$item = array(
// 				'id'          => $fields['id'],
// 				'date'        => $GLOBALS['phpgw']->common->show_date($fields['date']),
// 				'subject'     => $GLOBALS['phpgw']->strip_html($fields['subject']),
// 				'submittedby' => $fields['submittedby'],
// 				'content'     => $fields['content'],
// 				'status'      => lang($fields['status']),
// 				'cat'         => $cat->id2name($fields['cat'])
// 			);
// 			return $item;
// 		}

		function get_news($news_id)
		{
			$news = $this->sonews->get_news($news_id);
			return $this->acl->is_permitted($news['category'],PHPGW_ACL_READ) ? $news : False;
		}

		function total($cat_id,$activeonly=False)
		{
			return $this->acl->is_permitted($cat_id,PHPGW_ACL_READ) ?
				$this->sonews->total($cat_id,$activeonly) :
				0;
		}
	}
?>
