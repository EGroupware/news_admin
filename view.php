<?php
  /**************************************************************************\
  * phpGroupWare - Webpage news admin                                        *
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

	$phpgw_flags = Array(
		'currentapp' => 'news_admin',
		'enable_nextmatchs_class' => True
	);

  $phpgw_info['flags'] = $phpgw_flags;
  include('../header.inc.php');

  // If you move your site templates, change your path to it here
  $tpl = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);

  $tpl->set_file('news_form' => 'news.tpl');

  $tpl->set_block('news_form','news','news');
  $tpl->set_block('news_form','row','row');
  
  $phpgw->db->query("select * from webpage_news where news_id='$news_id'",__LINE__,__FILE__);
  $phpgw->db->next_record();

  $tpl->set_var('icon',$phpgw->common->image('news_admin','news-corner.gif'));
  $tpl->set_var('subject',$phpgw->db->f('news_subject'));
  $tpl->set_var('submitedby','Submitted by ' . $phpgw->db->f('submittedby') . ' on ' . date('m/d/Y - h:m:s a',$phpgw->db->f('news_date')));
  $tpl->set_var('content',nl2br($phpgw->db->f('news_content')));

  $tpl->parse('rows','row',True);
  $tpl->pparse('out','news');

  $phpgw->common->phpgw_footer();
?>
