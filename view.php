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

	$phpgw_info['flags'] = array(
		'currentapp' => 'news_admin',
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	// If you move your site templates, change your path to it here
	//$tpl = new Template($phpgw_info["server"]["server_root"] . "/news_admin/website/templates");
	$tpl = CreateObject('phpgwapi.Template',PHPGW_SERVER_ROOT . SEP . 'news_admin' . SEP . 'website' . SEP . 'templates');

	$tpl->set_file(array(
		'news' => 'news.tpl',
		'row'  => 'news_row.tpl'
	));

	$phpgw->db->query("SELECT * FROM webpage_news WHERE news_id='$news_id'",__LINE__,__FILE__);
	$phpgw->db->next_record();

	$tpl->set_var('icon_dir','website/');
	$tpl->set_var('subject',$phpgw->db->f('news_subject'));
	$tpl->set_var('submitedby','Submitted by ' . $phpgw->db->f('submittedby') . ' on ' . date('m/d/Y - h:m:s a',$phpgw->db->f('news_date')));
	$tpl->set_var('content',nl2br($phpgw->db->f('news_content')));

	$tpl->parse('rows','row',True);
	$tpl->pparse('out','news');

	$phpgw->common->phpgw_footer();
?>
