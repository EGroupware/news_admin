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
	create_select_box('Show news articles on main page?','homeShowLatest',$show_entries,
		'Should News_Admin display the latest article headlines on the main screen.');
	unset($show_entries);

	create_input_box('Number of articles to display on the main screen','homeShowLatestCount',
			'The number of articles to display on the main screen.','10',3);
	
	create_input_box('Directory for image upload ','uploaddir',
			'Needs to be writeable by webserver',EGW_SERVER_ROOT. '/news_admin/uploads',22);
			
	// added by wbshang @ realss, 2005-3-3
	create_check_box('Show newsletter option when add news?','SendMail','Do you want to show the newsletter option when add news?');

	$show_entries = array(
		0 => lang('No'),
		1 => lang('Yes'),
	);
	create_select_box('Send mail to home_email if the business_email is empty?','SendtohomeEmail',$show_entries,
		'Should News_Admin send mail to home_email if the business_email is empty.');
	unset($show_entries);

	create_input_box('Where is the email from','EmailFrom',
			'Where do you want the receiver to see the email is from.','',22);

	create_input_box('Where to receive the replied email','EmailReplyto',
			'Where the replied email will be sent to.','',22);
