<?php

	class uinews
	{
		var $template;
		var $db;

		function uinews()
		{
			global $phpgw;

			$this->template = $phpgw->template;
			$this->db       = $phpgw->db;
			$this->template->set_root($phpgw->common->get_tpl_dir('news_admin'));
		}

		function show_news()
		{
			global $cat_id, $start, $phpgw;

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->template->set_file(array(
				'_news' => 'news.tpl',
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');

			$this->db->query("select count(*) from phpgw_news where news_status='Active' and news_cat='$cat_id'");
			$this->db->next_record();
			$total = $this->db->f(0);

			if (! $oldnews)
			{
				$this->db->query("select * from phpgw_news where news_status='Active' and news_cat='$cat_id' order by news_date desc " . $this->db->limit(0,5));
			}
			else
			{
				$this->db->query("select * from phpgw_news where news_status='Active' and news_cat='$cat_id' order by news_date desc " . $this->db->limit($start));
			}

			$image_path = $phpgw->common->get_image_path('news_admin');

			while ($this->db->next_record())
			{
				$this->template->set_var('icon_dir',$image_path);
				$this->template->set_var('subject',$this->db->f('news_subject'));
				$this->template->set_var('submitedby','Submitted by ' . $phpgw->accounts->id2name($this->db->f('news_submittedby')) . ' on ' . $phpgw->common->show_date($this->db->f('news_date')));
				$this->template->set_var('content',nl2br($this->db->f('news_content')));
		
				$this->template->parse('rows','row',True);
			}

			$this->template->pfp('_out','news_form');
			if ($total > 5 && ! $oldnews)
			{
				echo '<center><a href="index.php?oldnews=True">View news archives</a></center>';
			}
		}
	}