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

	class bonews
	{
		var $sonews;

		function bonews()
		{
			$this->sonews = CreateObject('news_admin.sonews');
		}

		function get_NumNewsInCat($cat_id)
		{
			return $this->sonews->get_NumNewsInCat($cat_id);
		}

		function get_newslist($cat_id=0, $oldnews=false, $start=0, $total=5)
		{
			return $this->sonews->get_newslist($cat_id, $oldnews, $start, $total);
		}

		function get_news($news_id)
		{
			return $this->sonews->get_news($news_id);
		}
	}
