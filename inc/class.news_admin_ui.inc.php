<?php
/**
 * news_admin - admin user interface
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @copyright (c) 2007-16 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

use EGroupware\Api;
use EGroupware\Api\Framework;
use EGroupware\Api\Acl;
use EGroupware\Api\Etemplate;

/**
 * Admin user interface of the news_admin
 */
class news_admin_ui extends news_admin_bo
{
	/**
	 * Methods callable via menuaction
	 *
	 * @var array
	 */
	var $public_functions = array(
		'cat'  => true,
		'cats' => true,
	);
	/**
	 * Instance of the etemplate object
	 *
	 * @var etemplate
	 */
	var $tpl;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tpl = new Etemplate();
	}

	/**
	 * Edit a news category
	 *
	 * @param array $content =null submitted etemplate content
	 * @param string $msg =''
	 */
	public function cat($content=null,$msg='')
	{
		if (!is_array($content))
		{
			if (empty($_GET['cat_id']) || !($content = $this->read_cat($_GET['cat_id'])))
			{
				$content = array(
					'cat_writable' => $this->user,
					'cat_owner' => isset($GLOBALS['egw_info']['user']['apps']['admin']) ? 0 : $this->user,
				);
			}
			if (!empty($_GET['parent'])) $content['cat_parent'] = (int)$_GET['parent'];
		}
		elseif (!empty($content['button']))
		{
			$button = key($content['button']);
			unset($content['button']);

			switch($button)
			{
				case 'delete':
					if ($this->delete_cat($content))
					{
						$msg = lang('Category deleted.');
						Framework::refresh_opener($msg,'news_admin',$content['cat_id']);
						exit();
					}
					break;

				case 'apply':
				case 'save':
					if ($content['import_url'] && $content['cat_writable'])
					{
						$msg = lang('Imported feeds can NOT be writable!').' ';
					}
					if(in_array(Api\Categories::GLOBAL_ACCOUNT,$content['cat_readable']))
					{
						$content['cat_readable'] = array(Api\Categories::GLOBAL_ACCOUNT);
					}
					if (($content['cat_id'] = $this->save_cat($content)))
					{
						$msg .= lang('Category saved.');
						Framework::refresh_opener($msg,'news_admin',$content['cat_id']);
					}
					else
					{
						$msg .= lang('Error saving the category!');
						Framework::refresh_opener($msg,'news_admin',$content['cat_id'],'edit',null,null,null,'error');
						$button = '';
					}
					if ($button == 'save')
					{
						Framework::window_close();
						exit();
					}
					break;

				case 'import':
					require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.news_admin_import.inc.php');
					$import = new news_admin_import($this);
					if ((list($imported,$newly,$deleted) = $import->import($content['cat_id'])) === false)
					{
						$msg = lang('Error importing the feed!');
					}
					else
					{
						$msg = lang('%1 news imported (%2 new, %3 deleted).',$imported,$newly,$deleted);
						Framework::refresh_opener($msg,'news_admin');
					}
					break;

				case 'cancel':	// should never happen
					break;
			}
		}
		$preserve = $content;
		$content['msg'] = $msg;
		$content['is_admin'] = isset($GLOBALS['egw_info']['user']['apps']['admin']);
		$content['import_available'] = $this->import_available();
		if (!$content['import_frequency']) $content['import_frequency'] = 4;	// every 4h

		if (!$content['keep_imported']) $content['keep_imported'] = 0; // Keep all
		$content['options-keep_imported'] = array(
			0 => lang('As imported'),
			-1 => lang('Keep all'),
			10 => '10',
			20 => '20',
			30 => '30',
			50 => '50',
			75 => '75',
			100 => '100',
		);

		$readonlys = array();
		if ($content['cat_id'] && !$this->admin_cat($content))
		{
			$readonlys['__ALL__'] = true;
			$readonlys['button[cancel]'] = false;
		}
		if(!$GLOBALS['egw_info']['user']['apps']['admin'])
		{
			// Regular users can't give write access to others
			$content['cat_owner'] = $content['cat_writable'] = array($GLOBALS['egw_info']['user']['account_id']);
			$content['read_accounts'] = 'accounts';
			$readonlys['cat_writable'] = true;
		}
		else
		{
			$content['read_accounts'] = 'both';
		}

		if (!$content['cat_id']) $readonlys['button[delete]'] = true;
		if (!$content['import_url'] || !$content['cat_id']) $readonlys['button[import]'] = true;

		$this->tpl->read('news_admin.cat');
		return $this->tpl->exec('news_admin.news_admin_ui.cat',$content,
			array(
				'cat_parent' => $this->rights2cats(Acl::READ, $content['cat_id']),
				// Include global account option to prevent errors looking account 0
				'cat_readable' => array(Api\Categories::GLOBAL_ACCOUNT=>lang('all users'))
			),
			$readonlys,$preserve,2);
	}

	/**
	 * List the categories to administrate them
	 *
	 * @param array $_content =null submitted etemplate content
	 * @param string $msg =''
	 * @return string
	 */
	function cats($_content=null,$msg='')
	{
		if ($_GET['msg']) $msg = $_GET['msg'];

		if ($_content['admin'] && $_content['nm']['action'] == 'admin')
		{
			$_content['nm']['action'] = $_content['admin'];
		}
		if($_content['nm']['action'])
		{
			if (!count($_content['nm']['selected']) && !$_content['nm']['select_all'])
			{
				$msg = lang('You need to select some entries first');
			}
			else
			{
				// Some processing to add values in for links and cats
				$multi_action = $_content['nm']['action'];
				// Action has an additional action - add / delete, etc.  Buttons named <multi-action>_action[action_name]
				if(in_array($multi_action, array('reader','writer')))
				{
					$_content['nm']['action'] .= '_' . key($_content[$multi_action.'_popup'][$multi_action . '_action'] ?? []);

					if(is_array($_content[$multi_action.'_popup'][$multi_action]))
					{
						$_content[$multi_action] = implode(',',$_content[$multi_action.'_popup'][$multi_action]);
					}
					$_content['nm']['action'] .= '_' . $_content[$multi_action];
					unset($_content['nm'][$multi_action]);
				}
				$success = $failed = $action_msg = null;
				if ($this->action($_content['nm']['action'],$_content['nm']['selected'],$_content['nm']['select_all'],
					$success,$failed,$action_msg,'cats',$msg,$_content['nm']['checkboxes']['no_notifications']))
				{
					$msg .= lang('%1 entries %2',$success,$action_msg);
				}
				elseif(is_null($msg))
				{
					$msg .= lang('%1 entries %2, %3 failed because of insufficent rights !!!',$success,$action_msg,$failed);
				}
			}
		}
		$content = array(
			'msg' => $msg,
			'nm'  => Api\Cache::getSession('news_admin', 'cats'),
		);
		if (!is_array($content['nm']))
		{
			$content['nm'] = array(
				'get_rows'   =>	'news_admin.news_admin_ui.get_cats',    // I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'start'      =>	0,            // IO position in list
				'no_cat'     =>	true,        // IO category, if not 'no_cat' => True
				'search'     =>	'',            // IO search pattern
				'order'      => 'news_date',// IO name of the column to sort after (optional for the sortheaders)
				'sort'       => 'DESC',        // IO direction of the sort: 'ASC' or 'DESC'
				'col_filter' => array(),    // IO array of column-name value pairs (optional for the filterheaders)
				'no_filter'  => true,
				'no_filter2' => true,
				'row_id'     => 'id',
				'actions'    => $this->get_actions()
			);
		}
		$this->tpl->read('news_admin.cats');
		if($_GET['user'])
		{
			$this->tpl->set_dom_id("news_admin-cats-user");
		}
		return $this->tpl->exec('news_admin.news_admin_ui.cats', $content, array(
			'owner' => array(Api\Categories::GLOBAL_ACCOUNT => lang('All users'))
		));
	}

	/**
	 * rows callback for index nextmatch
	 *
	 * @internal
	 * @param array $query
	 * @param array &$rows returned rows/cups
	 * @param array &$readonlys eg. to disable buttons based on acl
	 * @return int total number of contacts matching the selection
	 */
	function get_cats($query,&$rows,&$readonlys=null,$ignore_acl=false)
	{
		Api\Cache::setSession('news_admin', 'cats', $query);

		$total = parent::get_cats($query,$rows);

		$readonlys = array();
		foreach($rows as $k => $row)
		{
			$readonlys['edit['.$row['id'].']'] = $readonlys['delete['.$row['id'].']'] = !$this->admin_cat($row);
			if(!$this->admin_cat($row)) $rows[$k]['class'] .= ' rowNoEdit rowNoDelete';
			if($row['import_url'])
			{
				$rows[$k]['class'] .= ' rowNoWriters'; // Imported news can't be edited
			}
			if($row['import_url'] && !$this->admin_cat($row) || !$row['import_url'])
			{
				$readonlys['update['.$row['id'].']'] = true;
				$rows[$k]['class'] .= ' rowNoUpdate';
			}
		}
		return $total;
	}

	public function get_actions()
	{
		$actions = array(
			'open' => array(        // does edit if allowed, otherwise view
				'caption' => 'Open',
				'default' => true,
				'allowOnMultiple' => false,
				'url' => 'menuaction=news_admin.news_admin_ui.cat&cat_id=$id',
				'popup' => '600x380',
				'group' => $group=1,
			),
			'add' => array(
				'caption' => 'Add',
				'allowOnMultiple' => false,
				'icon' => 'new',
				'url' => 'menuaction=news_admin.news_admin_ui.cat',
				'popup' => '600x380',
				'group' => $group,
			),
			'sub' => array(
				'caption' => 'Add sub',
				'allowOnMultiple' => false,
				'icon' => 'new',
				'url' => 'menuaction=news_admin.news_admin_ui.cat&parent=$id',
				'popup' => '600x380',
				'group' => $group,
				'disableClass' => 'rowNoSub',
			),
			'change' => array(
				'caption' => 'Change',
				'group' => $group,
				'disableClass' => 'rowNoEdit',
				'children' => array(
				'reader' => array(
					'caption' => 'Read permissions',
					'icon' => 'users',
					'nm_action' => 'open_popup',
					'group' => $group,
				),
				'writer' => array(
					'caption' => 'Write permissions',
					'icon' => 'users',
					'nm_action' => 'open_popup',
					'group' => $group,
					'disableClass' => 'rowNoWriters'
				)),
			),
			'update' => array(
				'caption' => 'Update RSS feed',
				'icon' => 'down2',
				'allowOnMultiple' => true,
				'disableClass' => 'rowNoUpdate',
				'group' => $group,
			),
			'delete' => array(
				'caption' => 'Delete',
				'confirm' => 'Delete this category, and all news in it',
					'confirm_multiple' => 'Delete these categories, and all news in them',
				'allowOnMultiple' => true,
				'group' => ++$group,
				'disableClass' => 'rowNoDelete',
			),
		);

		if(!$GLOBALS['egw_info']['user']['apps']['admin'])
		{
			unset($actions['change']['children']['writer']);
		}

		return $actions;
	}

	/**
	 * apply an action to multiple entries
	 *
	 * @param string|int $_action 'status_to',set status of entries
	 * @param array $checked tracker id's to use if !$use_all
	 * @param boolean $use_all if true use all entries of the current selection (in the session)
	 * @param int &$success number of succeded actions
	 * @param int &$failed number of failed actions (not enought permissions)
	 * @param string &$action_msg translated verb for the actions, to be used in a message like %1 entries 'deleted'
	 * @param string|array $session_name 'index' or 'email', or array with session-data depending if we are in the main list or the popup
	 * @param string &$msg
	 * @return boolean true if all actions succeded, false otherwise
	 */
	function action($_action,$checked,$use_all,&$success,&$failed,&$action_msg,$session_name,&$msg)
	{
		//error_log(__METHOD__ . "($_action, " . array2string($checked) . ",$use_all)");
		$success = $failed = 0;
		if ($use_all)
		{
			// get the whole selection
			@set_time_limit(0);                     // switch off the execution time limit, as it's for big selections to small
			if(!is_array($session_name))
			{
				$old_query = $query = Api\Cache::getSession('news_admin', $session_name);
			}
			else
			{
				$query = $session_name;
			}
			$query['num_rows'] = -1;        // all
			$result = $readonlys = null;
			$this->get_rows($query,$result,$readonlys);
			$checked = array();
			foreach($result as $key => $info)
			{
				if(is_numeric($key))
				{
					$checked[] = $info['id'];
				}
			}
			// Reset query
			if($old_query) Api\Cache::setSession('news_admin', $session_name, $old_query);
		}

		list($action, $settings) = explode('_', $_action, 2);

		switch($action)
		{
			case 'delete':
				$action_msg = lang('deleted');
				foreach($checked as $id) {
					if ($this->delete_cat($id))
					{
						$success++;
					}
					else
					{
						$failed++;
					}
				}
				break;
			case 'update':
				require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.news_admin_import.inc.php');
				$import = new news_admin_import($this);
				$imported = $newly = $deleted = 0;
				$action_msg = lang('updated');
				foreach($checked as $id) {
					if ((list($_imported,$_newly,$_deleted) = $import->import($id)) === false)
					{
						$msg .= "\n".lang('Error importing the feed!')."\n";
						$failed++;
					}
					else
					{
						$success++;
						$imported += $_imported;
						$newly += $_newly;
						$deleted += $_deleted;
					}
				}
				$msg .= lang('%1 news imported (%2 new, %3 deleted).',$imported,$newly,$deleted);
				break;
			case 'reader':
			case 'writer':
				$action_msg = lang('updated');
				list($add_remove, $ids_str) = explode('_', $settings, 2);
				$ids = explode(',',$ids_str);
				$field = 'cat_'. ($action == 'reader' ? 'readable' : 'writable');

				foreach($checked as $id)
				{
					if (!$data = $this->read_cat($id)) continue;
					$data[$field] = $add_remove == 'add' ?
						$ids == array(Api\Categories::GLOBAL_ACCOUNT) ? $ids : array_merge($data[$field],$ids) :
						array_diff($data[$field],$ids);
					$data[$field] = array_unique($data[$field]);
					if ($this->save_cat($data))
					{
						$success++;
						if($action == 'reader' && $add_remove == 'delete' && $kept = @array_intersect($ids, $data['cat_writable']))
						{
							$msg .= $data['cat_name'] . ': ' . lang('Kept %1 because of write permissions.  Remove them first.',count($kept)). "\n";
						}
					}
					else
					{
						$failed++;
					}
				}
				break;
		}
		return $failed == 0;
	}
}
