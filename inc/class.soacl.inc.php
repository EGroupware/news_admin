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
	\**************************************************************************/

	/* $Id$ */

	class soacl
	{
		var $db;

		function soacl()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}

		function get_rights($location)
		{
			$result = array();
			$sql = "select acl_account, acl_rights from phpgw_acl where acl_appname = 'news_admin' and acl_location = '$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$result[$this->db->f('acl_account')] = $this->db->f('acl_rights');
			}
			return $result;
		}

		function remove_location($location)
		{
			$sql = "delete from phpgw_acl where acl_appname='news_admin' and acl_location='$location'";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function get_permissions($user, $inc_groups)
		{
			$groups = $GLOBALS['phpgw']->acl->get_location_list_for_id('phpgw_group', 1, $user);
			$result = array();
			$sql  = 'SELECT acl_location, acl_rights FROM phpgw_acl ';
			$sql .= "WHERE acl_appname = 'news_admin' ";
			if($inc_groups)
			{
				$sql .= 'AND acl_account IN('. intval($user);
				$sql .= ($groups ? ',' . implode(',', $groups) : '');
				$sql .= ')';
			}
			else
			{
				$sql .= 'AND acl_account ='. intval($user);
			}
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$result[$this->db->f('acl_location')] |= $this->db->f('acl_rights');
			}
			return $result;
		}
	}