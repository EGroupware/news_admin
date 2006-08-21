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

	class uinews
	{
		var $start = 0;
		var $query = '';
		var $sort  = '';
		var $order = ''; 
		var $cat_id;
		var $template;
		var $bo;
		var $news_data;
		var $news_id;
		var $sbox;
		var $public_functions = array(
			'write_news'  => True,
			'add'         => True,
			'edit'        => True,
			'delete'      => True,
			'delete_item' => True,
			'read_news'      => True,
			'show_news_home' => True
		);

		function uinews()
		{
			$GLOBALS['egw']->html =& CreateObject('phpgwapi.html');
			$this->nextmatchs =& CreateObject('phpgwapi.nextmatchs');
			$this->template = $GLOBALS['egw']->template;
			$this->bo   =& CreateObject('news_admin.bonews',True);
			$this->sbox =& CreateObject('news_admin.sbox');
			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->order = $this->bo->order;
			$this->sort = $this->bo->sort;
			$this->cat_id = $this->bo->cat_id;
		}

		//with $default, we are called from the news form
		function selectlist($type,$default=false)
		{
			$link_data['menuaction'] = ($type == 'read') ? 'news_admin.uinews.read_news' : 'news_admin.uinews.write_news';
			$link_data['start'] = 0;
			$right = ($type == 'read') ? EGW_ACL_READ : EGW_ACL_ADD;
			$selectlist = ($default === false) ? ('<option>' . lang($type . ' news') . '</option>') : '<option value="none">' . lang('PLEASE SELECT') . '...</option>';  // wbshang 2005-4-4
			foreach($this->bo->cats as $cat)
			{
				if($this->bo->acl->is_permitted($cat['id'],$right))
				{
					$cat_id = (int)$cat['id'];
					$link_data['cat_id'] = $cat_id;
					$selectlist .= '<option value="';
					$selectlist .= $default !== False ? $cat_id : $GLOBALS['egw']->link('/index.php',$link_data);
					$selectlist .= '"';
					$selectlist .= ($default === $cat_id) ? ' selected="selected"' : ''; 
					$selectlist .= '>' . $cat['name'] . '</option>' . "\n";
				}
			}

			if (!$default)
			{
				if($type=='read' || $this->bo->acl->is_permitted('all',$right))
				{
					$link_data['cat_id'] = 'all';
					$selectlist .= '<option style="font-weight:bold" value="' . $GLOBALS['egw']->link('/index.php',$link_data)  
						. '">' . lang('All news') . '</option>'  . "\n";
				}
			}
			return $selectlist;
		}

		function read_news()
		{
			$limit = ($GLOBALS['egw_info']['common']['maxmatchs'] ? $GLOBALS['egw_info']['common']['maxmatchs'] : 5);

			$news_id = get_var('news_id',Array('GET'));

			$news = $news_id ? array($news_id => $this->bo->get_news($news_id)) :  
				$this->bo->get_newslist($this->cat_id,$this->start,'','',$limit,True);

			$this->template->set_file(array(
				'main' => 'read.tpl'
			));
			$this->template->set_block('main','news_form');
			$this->template->set_block('main','row');
			$this->template->set_block('main','row_empty');

			$GLOBALS['egw']->common->egw_header();
			echo parse_navbar();
			$this->template->set_block('main','category');
			$var['lang_read'] = lang('Read');
			$var['lang_write'] = lang('Write');
			$var['readable'] = $this->selectlist('read');
			$var['maintainlink'] = (($this->cat_id != 'all') && $this->bo->acl->is_permitted($this->cat_id,EGW_ACL_ADD)) ? 
				('<a href="' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.write_news&start=0&cat_id='.$this->cat_id)
				. '">' . lang('Maintain') . '</a>') : '';
			$var['cat_name'] = ($this->cat_id != 'all') ? $this->bo->catbo->id2name($this->cat_id) : lang('All news');
			$this->template->set_var($var);
			$this->template->parse('_category','category');

			$this->template->set_var('icon',$GLOBALS['egw']->common->image('news_admin','news-corner.gif'));

			foreach($news as $newsitem)
			{
				$var = Array(
					'subject' => $newsitem['subject'],
					'submitedby' => 'Submitted by ' . $GLOBALS['egw']->common->grab_owner_name($newsitem['submittedby']) . ' on ' . $GLOBALS['egw']->common->show_date($newsitem['date']),
					'content' => $newsitem['content'],
				);

				$this->template->set_var($var);
				$this->template->parse('rows','row',True);
			}
			if ($this->start)
			{
				$link_data['menuaction'] = 'news_admin.uinews.read_news';
				$link_data['start'] = $this->start - $limit;
				$link_data['cat_id'] = $this->cat_id;
				$this->template->set_var('lesslink',
					'<a href="' . $GLOBALS['egw']->link('/index.php',$link_data) . '">&lt;&lt;&lt;</a>'
				);
			}
			if ($this->bo->total > $this->start + $limit)
			{
				$link_data['menuaction'] = 'news_admin.uinews.read_news';
				$link_data['start'] = $this->start + $limit;
				$link_data['cat_id'] = $this->cat_id;
				$this->template->set_var('morelink',
					'<a href="' . $GLOBALS['egw']->link('/index.php',$link_data) . '">' . lang('More news') . '</a>'
				);
			}
			if (! $this->bo->total)
			{
				$this->template->set_var('row_message',lang('No entries found'));
				$this->template->parse('rows','row_empty',True);
			}

			$this->template->pfp('_out','news_form');
		}

		//this is currently broken
		function show_news_home()
		{
			$title = '<font color="#FFFFFF">'.lang('News Admin').'</font>';
			$portalbox =& CreateObject('phpgwapi.listbox',array(
				'title'     => $title,
				'primary'   => $GLOBALS['egw_info']['theme']['navbar_bg'],
				'secondary' => $GLOBALS['egw_info']['theme']['navbar_bg'],
				'tertiary'  => $GLOBALS['egw_info']['theme']['navbar_bg'],
				'width'     => '100%',
				'outerborderwidth' => '0',
				'header_background_image' => $GLOBALS['egw']->common->image('phpgwapi/templates/default','bg_filler')
			));

			$app_id = $GLOBALS['egw']->applications->name2id('news_admin');
			$GLOBALS['portal_order'][] = $app_id;

			$var = Array(
				'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
				'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
				'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
				'question' => Array('url' => '/set_box.php', 'app' => $app_id),
				'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
			);

			while(list($key,$value) = each($var))
			{
				$portalbox->set_controls($key,$value);
			}

			$newslist = $this->bo->get_newslist($cat_id);

			$image_path = $GLOBALS['egw']->common->get_image_path('news_admin');

			if(is_array($newslist))
			{
			foreach($newslist as $newsitem)
			{
				$portalbox->data[] = array(
					'text' => $newsitem['subject'] . ' - ' . lang('Submitted by') . ' ' . $GLOBALS['egw']->common->grab_owner_name($newsitem['submittedby']) . ' ' . lang('on') . ' ' . $GLOBALS['egw']->common->show_date($newsitem['date']),
					'link' => $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.show_news&news_id=' . $newsitem['id'])
				);
			}
			}
			else
			{
				$portalbox->data[] = array('text' => lang('no news'));
			}

			$tmp = "\r\n"
				. '<!-- start News Admin -->' . "\r\n"
				. $portalbox->draw()
				. '<!-- end News Admin -->'. "\r\n";
			$this->template->set_var('phpgw_body',$tmp,True);
		}

		//the following function is unmaintained
		function show_news_website($section='mid')
		{
			$cat_id = $_GET['cat_id'];
			$start = $_GET['start'];
			$oldnews = $_GET['oldnews'];
			$news_id = $_GET['news_id'];

			if (! $cat_id)
			{
				$cat_id = 0;
			}

			$this->template->set_file(array(
				'_news' => 'news_' . $section . '.tpl'
			));
			$this->template->set_block('_news','news_form');
			$this->template->set_block('_news','row');
			$this->template->set_block('_news','category');


			if($news_id)
			{
				$news = array($news_id => $this->bo->get_news($news_id));
			}
			else
			{
				$news = $this->bo->get_NewsList($cat_id,$oldnews,$start,$total);
			}

			$var = Array();

			$this->template->set_var('icon',$GLOBALS['egw']->common->image('news_admin','news-corner.gif'));

			foreach($news as $newsitem)
			{
				$var = Array(
					'subject'=> $newsitem['subject'],
					'submitedby' => 'Submitted by ' . $GLOBALS['egw']->common->grab_owner_name($newsitem['submittedby']) . ' on ' . $GLOBALS['egw']->common->show_date($newsitem['date']),
					'content'    => nl2br($newsitem['content'])
				);

				$this->template->set_var($var);
				$this->template->parse('rows','row',True);
			}

			$out = $this->template->fp('out','news_form');

			if ($this->bo->total > 5 && ! $oldnews)
			{
				$link_values = array(
					'menuaction'    => 'news_admin.uinews.show_news',
					'oldnews'       => 'True',
					'cat_id'        => $cat_id,
					'category_list' => 'True'
				);

				$out .= '<center><a href="' . $GLOBALS['egw']->link('/index.php',$link_values) . '">View news archives</a></center>';
			}
			return $out;
		}

		function add()
		{
			if($_POST['cancel'])
			{
			// Changed by dawnlinux @ realss.com 
			// When a user has fininshed adding a news, it will jump to read news page.
			//Header('Location: ' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.write_news'));
			Header('Location: ' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.read_news'));
				return;
			}
			if($_POST['submitit'])
			{
				$this->news_data = $_POST['news'];
				if(!$this->news_data['subject'])
				{
					$errors[] = lang('The subject is missing');
				}
				if(!$this->news_data['content'])
				{
					$errors[] = lang('The news content is missing');
				}
				if($this->news_data['category'] == "none")
				{
					$errors[] = lang('Please select a category');
				}
				if(!is_array($errors))
				{
					$this->news_data['date'] = time();
					$this->bo->set_dates($_POST['from'],$_POST['until'],$this->news_data);
					$this->news_id = $this->bo->add($this->news_data);
					if(!$this->news_id)
					{
						$this->message = lang('failed to add message');
					}
					else
					{
						$this->message = lang('Message has been added');

						//then send mail to receiver, wbshang 2005-5-12
						if($this->news_data['mailto'])
						{
							$errors = $this->bo->send_mail($this->news_data);
							if(is_array($errors))
							{
								$this->message = $errors;
							}
						}

						//after having added, we must switch to edit mode instead of stay in add
						$this->modify('edit');
						return;
					}
				}
				else
				{
					$this->message = $errors;
				}
			}
			else
			{
				$this->news_data['category'] = $this->cat_id;
			}
			$this->modify('add');
		}

		function delete()
		{
			$news_id = $_POST['news_id'] ? $_POST['news_id'] : $_GET['news_id'];

			$GLOBALS['egw']->common->egw_header();
			echo parse_navbar();
			$this->template->set_file(array(
				'form' => 'admin_delete.tpl'
			));
			$this->template->set_var('lang_message',lang('Are you sure you want to delete this entry ?'));
			$this->template->set_var('lang_yes',lang('Yes'));
			$this->template->set_var('lang_no',lang('No'));

			$this->template->set_var('link_yes',$GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.delete_item&news_id=' . $news_id));
			$this->template->set_var('link_no',$GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.write_news'));

			$this->template->pfp('_out','form');
		}

		function delete_item()
		{
			$item = (int)get_var('news_id',array('GET','POST'));
			if($item)
			{
				$this->bo->delete($item);
				$msg = lang('Item has been deleted');
			}
			else
			{
				$msg = lang('Item not found');
			}
			$this->write_news($msg);
		}

		function edit()
		{
			$this->news_data = $_POST['news'];
			$this->news_id   = (isset($_GET['news_id']) ? $_GET['news_id'] : $_POST['news']['id']);

			if($_POST['cancel'])
			{
				if(isset($this->news_data['category']))
				{
						Header('Location: ' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.write_news&cat_id='.$this->news_data['category']));
						return;
				}
				else
				{
						Header('Location: ' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.write_news'));
						return;
				}
			}

			if(is_array($this->news_data))
			{
				$this->news_data['subject'] = stripslashes($this->news_data['subject']);
				$this->news_data['teaser'] = stripslashes($this->news_data['teaser']);
				$this->news_data['content'] = stripslashes($this->news_data['content']);
				
				if(!$this->news_data['subject'])
				{
					$errors[] = lang('The subject is missing');
				}
				if(!$this->news_data['content'])
				{
					$errors[] = lang('The news content is missing');
				}
				if($this->news_data['category'] == "none")
				{
					$errors[] = lang('Please select a category');
				}

				if(!is_array($errors))
				{
					$this->bo->set_dates($_POST['from'],$_POST['until'],$this->news_data);
					$this->bo->edit($this->news_data);
					$this->message = lang('News item has been updated');

					// send mail to receiver,wbshang 2005-5-12
				/*	// the following code is used to detect whether there are new receivers,if do,send them mail.
					// but I think it is necessary to send new mail to all receivers if the news is modified,so I comment it. wbshang 2005-5-12
					$cat_id = $this->bo->get_receiver_cats($this->news_id);
					foreach($this->news_data['mailto'] as $entry)
					{
						if(!in_array($entry,$cat_id))
						{
							$mailto[] = $entry;
						}
					}
					if(!empty($mailto))
					{
						$this->bo->send_mail($news,$mailto);
					} */
					if($this->news_data['mailto'])
					{
						$errors = $this->bo->send_mail($this->news_data);
						if(is_array($errors))
						{
							$this->message = $errors;
						}
					}

				}
				else
				{
					$this->message = $errors;
				}
			}
			else
			{
				$this->news_data = $this->bo->get_news($this->news_id,True);
				$this->news_data['date_d'] = date('j',$this->news_data['begin']);
				$this->news_data['date_m'] = date('n',$this->news_data['begin']);
				$this->news_data['date_y'] = date('Y',$this->news_data['begin']);
			}
			$this->modify();
		}

		function modify($type = 'edit')
		{
			$options = $this->bo->get_options($this->news_data);
			
			// init htmlarea with wysiwyg editor
			$style='width:600px; min-width:500px; height:400px;';
			$plugins = 'TableOperations,ContextMenu,ColorChooser,FontChooser,FileManager,SearchReplace,InsertDateTime';
			$GLOBALS['egw']->session->appsession('UploadImage','phpgwapi',array(
				'app' => 'news_admin',
				'upload_dir'   => $this->bo->config['upload_dir'],
				'upload_url'   => $this->bo->config['upload_url'],
				'admin_method' => $GLOBALS['egw']->link('/index.php','menuaction=admin.uiconfig.index&appname=news_admin'),
			));
			$content = $GLOBALS['egw']->html->tinymce('news[content]',$this->news_data['content'],$style,$plugins);
			
			$GLOBALS['egw']->common->egw_header();
			echo parse_navbar();
			
			$this->template->set_file(array(
				'form' => 'admin_form.tpl'
			));

			if(is_array($this->message))
			{
				$this->template->set_var('errors',$GLOBALS['egw']->common->error_list($this->message));
			}
			elseif($this->message)
			{
				$this->template->set_var('errors',$this->message);
			}

			$category_list = $this->selectlist('write', (int)$this->news_data['category']);

			$this->template->set_var('lang_header',lang($type . ' news item'));
			$this->template->set_var('form_action',$GLOBALS['egw']->link('/index.php',
				array('menuaction'	=> 'news_admin.uinews.'.$type,
				 		'news_id'	=> $this->news_id
					)
				)
			);
			$this->template->set_var(array(
				'form_button' => '<input type="submit" name="submitit" value="' . lang('save') . '">',
				'value_id' => $this->news_id,
				'done_button' => '<input type="submit" name="cancel" value="' . lang('Done') . '">',
				'label_subject' => lang('subject') . ':',
				'value_subject' => '<input name="news[subject]" size="60" value="' . @htmlspecialchars($this->news_data['subject'],ENT_COMPAT,$GLOBALS['egw']->translation->charset()) . '">',
				'label_teaser' => lang('teaser') . ':',
				'value_teaser' => '<input name="news[teaser]" size="60" value="' . @htmlspecialchars($this->news_data['teaser'],ENT_COMPAT,$GLOBALS['egw']->translation->charset()) . '" maxLength="100">',
				'label_content' => lang('Content') . ':',
				'value_content' => $content,
				'label_category' => lang('Category') . ':',
				'value_category' => '<select name="news[category]">' . $this->selectlist('write', (int)$this->news_data['category']) . '</select>',
				'label_visible' => lang('Visible') . ':',
				'value_begin_d' =>  $this->sbox->getDays('news[begin_d]',date('j',$this->news_data['begin'])),
				'value_begin_m' =>  $this->sbox->getMonthText('news[begin_m]',date('n',$this->news_data['begin'])),
				'value_begin_y' =>  $this->sbox->getYears('news[begin_y]',date('Y',$this->news_data['begin']),date('Y')),
				'select_from' => $options['from'],
				'select_until' => $options['until'],
				'value_end_d' =>  $this->sbox->getDays('news[end_d]',date('j',$this->news_data['end'])) ,
				'value_end_m' =>  $this->sbox->getMonthText('news[end_m]',date('n',$this->news_data['end'])),
				'value_end_y' =>  $this->sbox->getYears('news[end_y]',date('Y',$this->news_data['end']),date('Y')),
				'label_is_html' => lang('Contains HTML'),
				'value_is_html' => '<input type="checkbox" value="1" name="news[is_html]"' . ($this->news_data['is_html'] ? ' checked="1"' : '') .'>',
			));

			//the following label is added by wbshang,2005-5-12
			if($GLOBALS['egw_info']['user']['preferences']['news_admin']['SendMail'])
			{
				$contact_options = $this->cat_options($this->news_data['mailto']);   // added by wbshang,used to send mail
				$this->template->set_var(array(
					'label_send_mail' => lang('Send Mail to Others') . '?',
					'value_send_mail' => '<select multiple="multiple" size="5" name="news[mailto][]">' . $contact_options . '</select>',
				));
			}
			
			$this->template->pfp('out','form');
		}
		
		function write_news($message = '')
		{
			$this->template->set_file(array(
				'main' => 'write.tpl'
			));
			$this->template->set_block('main','list');
			$this->template->set_block('main','row');
			$this->template->set_block('main','row_empty');

			$GLOBALS['egw']->common->egw_header();
			echo parse_navbar();
			$this->template->set_block('main','category');
			$var['lang_read'] = lang('Read');
			$var['lang_write'] = lang('Write');
			$var['readable'] = $this->selectlist('read');
			$var['cat_name'] = $this->cat_id ? $this->bo->catbo->id2name($this->cat_id) : lang('Global news');

			$this->template->set_var($var);
			$this->template->parse('_category','category');

			if ($message)
			{
				$this->template->set_var('message',$message);
			}

			$this->template->set_var('header_date',$this->nextmatchs->show_sort_order($this->sort,'news_date',$this->order,'/index.php',lang('Date'),'&menuaction=news_admin.uinews.write_news'));
			$this->template->set_var('header_subject',$this->nextmatchs->show_sort_order($this->sort,'news_subject',$this->order,'/index.php',lang('Subject'),'&menuaction=news_admin.uinews.write_news'));
			$this->template->set_var('header_status',lang('Visible'));
			$this->template->set_var('header_edit',lang('Edit'));
			$this->template->set_var('header_delete',lang('Delete'));
			$this->template->set_var('header_view',lang('View'));

			$items      = $this->bo->get_newslist($this->cat_id,$this->start,$this->order,$this->sort);

			$left  = $this->nextmatchs->left('/index.php',$this->start,$this->bo->total,'menuaction=news_admin.uinews.write_news');
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->bo->total,'menuaction=news_admin.uinews.write_news');
			
			$this->template->set_var(array(
				'left' => $left,
				'right' => $right,
				'lang_showing' => $this->nextmatchs->show_hits($this->bo->total,$this->start),
			));

			foreach($items as $item)
			{
				$this->nextmatchs->template_alternate_row_color($this->template);
				$this->template->set_var('row_date',$GLOBALS['egw']->common->show_date($item['date']));
				if(strlen($item['news_subject']) > 40)
				{
					$subject = $GLOBALS['egw']->strip_html(substr($item['subject'],40,strlen($item['subject'])));
				}
				else
				{
					$subject = $GLOBALS['egw']->strip_html($item['subject']);
				}
				$this->template->set_var('row_subject',$subject);
				$this->template->set_var('row_status',$this->bo->get_visibility($item));

				$this->template->set_var('row_view','<a href="' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.read_news&news_id=' . $item['id']) . '">' . lang('View') . '</a>');
				$this->template->set_var('row_edit','<a href="' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.edit&news_id=' . $item['id']) . '">' . lang('Edit') . '</a>');
				$this->template->set_var('row_delete','<a href="' . $GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.delete&news_id=' . $item['id']) . '">' . lang('Delete') . '</a>');

				$this->template->parse('rows','row',True);
			}

			if(!$this->bo->total)
			{
				$this->nextmatchs->template_alternate_row_color($this->template);
				$this->template->set_var('row_message',lang('No entries found'));
				$this->template->parse('rows','row_empty',True);
			}

			$this->template->set_var('link_add',$GLOBALS['egw']->link('/index.php','menuaction=news_admin.uinews.add'));
			$this->template->set_var('lang_add',lang('Add new news'));

			$this->template->pfp('out','list');
		}

		// the following functions are added by wbshang @ realss, 2005-5-12
		// Get the categories list of addressbook,used to send mail
		function cat_options($selected)
		{
			$cat =& CreateObject('phpgwapi.categories','','addressbook');
			$cat_id = is_array($selected) ? $selected : $this->bo->get_receiver_cats($this->news_id);
			return $cat->formated_list('select','all',$cat_id,True);
		}
	}
?>
