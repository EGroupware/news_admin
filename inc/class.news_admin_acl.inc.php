<?php
/**
 * news_admin - ACL
 *
 * @link http://www.egroupware.org
 * @package news_admin
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 */

use EGroupware\Api;
use EGroupware\Api\Acl;

class news_admin_acl
{
	/**
	 * @var Api\Categories
	 */
	var $catbo;
	/**
	 * Constructor
	 */
	function __construct()
	{
		//error_log(__METHOD__."($name)".function_backtrace());
		$this->permissions = $this->get_permissions(True);
	}

	function get_rights($cat_id)
	{
		return $GLOBALS['egw']->acl->get_all_rights('L'.$cat_id,'news_admin');
	}

	function is_permitted($cat_id,$right)
	{
		if($right & Acl::READ)
		{
			if(empty($this->catbo))
			{
				$this->catbo = new Api\Categories('','news_admin');
			}
			return $this->catbo->check_perms($right, $cat_id);
		}
		if($right & Acl::ADD)
		{
			return $this->permissions['L'.$cat_id] & $right;
		}
		//echo __METHOD__ . ':' . __LINE__ . ' Different perms: ' . $right . '<br />';
	}

	function is_readable($cat_id)
	{
		return $this->is_permitted($cat_id,Acl::READ);
	}

	function is_writeable($cat_id)
	{
		return $this->is_permitted($cat_id,Acl::ADD);
	}

	function set_rights($cat_id,$read,$write)
	{
		unset($read);	// not used

		$GLOBALS['egw']->acl->delete_repository('news_admin', 'L'.$cat_id, false);

		foreach($write ? $write : array() as $account_id)
		{
			$GLOBALS['egw']->acl->add_repository('news_admin', 'L'.$cat_id, $account_id, Acl::ADD);
		}
	}

	//access permissions for current user
	function get_permissions($inc_groups = False)
	{
		return $GLOBALS['egw']->acl->get_all_location_rights($GLOBALS['egw_info']['user']['account_id'],'news_admin',$inc_groups);
	}
}