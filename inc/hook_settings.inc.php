<?php
	/**************************************************************************\
	* eGroupWare - Webpage News Admin                                          *
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

	$show_entries = array(
		0 => lang('No'),
		1 => lang('Yes'),
		2 => lang('Yes').' - '.lang('small view'),
	);
	$_show_entries = array(
		0 => lang('No'),
		1 => lang('Yes'),
	);

	$GLOBALS['settings'] = array(
		'homeShowLatest' => array(
			'type'   => 'select',
			'label'  => 'Show news articles on main page?',
			'name'   => 'homeShowLatest',
			'values' => $show_entries,
			'help'   => 'Should News_Admin display the latest article headlines on the main screen.',
			'xmlrpc' => True,
			'admin'  => False
		),
		'homeShowLatestCount' => array(
			'type'    => 'input',
			'label'   => 'Number of articles to display on the main screen',
			'name'    => 'homeShowLatestCount',
			'size'    => 3,
			'maxsize' => 10,
			'xmlrpc'  => True,
			'admin'   => False
		),
		'uploaddir' => array(
			'type'    => 'input',
			'label'   => 'Directory for image upload',
			'name'    => 'uploaddir',
			'default' => EGW_SERVER_ROOT. '/news_admin/uploads',
			'help'    => 'Needs to be writeable by webserver',
			'size'    => 22,
			'xmlrpc'  => True,
			'admin'   => False
		),
		'SendMail' => array( // added by wbshang @ realss, 2005-3-3
			'type'   => 'check',
			'label'  => 'Show newsletter option when add news?',
			'name'   => 'SendMail',
			'help'   => 'Do you want to show the newsletter option when add news?',
			'xmlrpc' => True,
			'admin'  => False
		),
		'SendtohomeEmail' => array(
			'type'   => 'select',
			'label'  => 'Send mail to home_email if the business_email is empty?',
			'name'   => 'SendtohomeEmail',
			'values' => $_show_entries,
			'help'   => 'Should News_Admin send mail to home_email if the business_email is empty.',
			'xmlrpc' => True,
			'admin'  => False
		),
		'EmailFrom' => array(
			'type'   => 'input',
			'label'  => 'Where is the email from',
			'name'   => 'EmailFrom',
			'help'   => 'Where do you want the receiver to see the email is from.',
			'size'   => 22,
			'xmlrpc' => True,
			'admin'  => False
		),
		'EmailReplyto' => array(
			'type'   => 'input',
			'label'  => 'Where to receive the replied email',
			'name'   => 'EmailReplyto',
			'help'   => 'Where the replied email will be sent to.',
			'size'   => 22,
			'xmlrpc' => True,
			'admin'  => False
		)
	);
