<?php
/**
 * news_admin - admin user interface
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @copyright (c) 2007 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.bonews.inc.php');

/**
 * Admin user interface of the news_admin
 */
class news_admin_ui extends bonews
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
	 * @return uinews
	 */
	function news_admin_ui()
	{
		$this->bonews();

		$this->tpl =& CreateObject('etemplate.etemplate');
	}

	/**
	 * Handle a news category
	 *
	 * @param array $content=null submitted etemplate content
	 * @param string $msg=''
	 */
	function cat($content=null,$msg='')
	{
		if (!is_array($content))
		{
			if (!(int) $_GET['cat_id'] || !($content = $this->read_cat($_GET['cat_id'])))
			{
				$content = array(
					'cat_writable' => $this->user,
					'cat_owner' => isset($GLOBALS['egw_info']['user']['apps']['admin']) ? 0 : $this->user,
				);
			}
			if($_GET['parent']) $content['cat_parent'] = (int)$_GET['parent'];
		}
		else
		{
			if ($content['button'])
			{
				list($button) = each($content['button']);
				unset($content['button']);
			}
			elseif($content['delete'])
			{
				list($id) = each($content['button']);
				unset($content['delete']);
				$button = 'delete';
			}

			switch($button)
			{
				case 'delete':
					if ($this->delete_cat($content))
					{
						$msg = lang('Category deleted.');
						echo "<html><body><script>var referer = opener.location;opener.location.href = referer+(referer.search?'&':'?')+'msg=".
							addslashes(urlencode($msg))."'; window.close();</script></body></html>\n";
						$GLOBALS['egw']->common->egw_exit();
					}
					break;

				case 'apply':
				case 'save':
					if ($content['import_url'] && $content['cat_writable'])
					{
						$msg = lang('Imported feeds can NOT be writable!').' ';
					}
					if (($content['cat_id'] = $this->save_cat($content)))
					{
						$msg .= lang('Category saved.');
						$js = "opener.location.href=opener.location.href+'&msg=".addslashes(urlencode($msg))."';";
					}
					else
					{
						$msg .= lang('Error saving the category!');
						$button = '';
					}
					if ($button == 'save')
					{
						$js .= 'window.close();';
						echo "<html>\n<body>\n<script>\n$js\n</script>\n</body>\n</html>\n";
						$GLOBALS['egw']->common->egw_exit();
					}
					elseif ($js)
					{
						$GLOBALS['egw_info']['flags']['java_script'] .= "<script>\n$js\n</script>\n";
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
						$js = "opener.location.href=opener.location.href+'&msg=".addslashes(urlencode($msg))."';";
						$GLOBALS['egw_info']['flags']['java_script'] .= "<script>\n$js\n</script>\n";
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
			foreach($content as $name => $value)
			{
				$readonlys[$name] = true;
			}
			$readonlys['button[import]'] = $readonlys['button[delete]'] = $readonlys['button[save]'] = $readonlys['button[apply]'] = true;
		}
		if(!$GLOBALS['egw_info']['user']['apps']['admin'])
		{
			// Regular users can't give write access to others
			$content['cat_owner'] = $content['cat_writable'] = array($GLOBALS['egw_info']['user']['account_id']);
			$readonlys['cat_writable'] = true;
		}
		if (!$content['cat_id']) $readonlys['button[delete]'] = true;
		if ($content['cat_id']) $readonlys['cat_owner'] = true;	// cat class can only set owner when creating new cats
		if (!$content['import_url'] || !$content['cat_id']) $readonlys['button[import]'] = true;

		$this->tpl->read('news_admin.cat');
		return $this->tpl->exec('news_admin.news_admin_ui.cat',$content,
			array(
				'cat_parent' => $this->rights2cats(EGW_ACL_READ, $content['cat_id']),
			),
			$readonlys,$preserve,2);
	}

	/**
	 * List the categories to administrate them
	 *
	 * @param array $content=null submitted etemplate content
	 * @param string $msg=''
	 * @return string
	 */
	function cats($content=null,$msg='')
	{
		if ($_GET['msg']) $msg = $_GET['msg'];

		if ($content['nm']['rows']['delete'])
		{
			list($id) = each($content['nm']['rows']['delete']);
			$content['nm']['action'] = 'delete';
			$content['nm']['selected'] = array($id);
		} else if ($content['nm']['rows']['update']) {
			list($id) = each($content['nm']['rows']['update']);
			$content['nm']['action'] = 'update';
			$content['nm']['selected'] = array($id);
		}
		if ($content['admin'] && $content['nm']['action'] == 'admin')
		{
			$content['nm']['action'] = $content['admin'];
		}
		if($content['nm']['action'])
		{
			if (!count($content['nm']['selected']) && !$content['nm']['select_all'])
			{
				$msg = lang('You need to select some entries first');
			}
			else
			{
				// Some processing to add values in for links and cats
				$multi_action = $content['nm']['action'];
				// Action has an additional action - add / delete, etc.  Buttons named <multi-action>_action[action_name]
				if(in_array($multi_action, array('reader','writer')))
				{
					$content['nm']['action'] .= '_' . key($content[$multi_action . '_action']);

					if(is_array($content[$multi_action]))
					{
						$content[$multi_action] = implode(',',$content[$multi_action]);
					}
					$content['nm']['action'] .= '_' . $content[$multi_action];
				}
				if ($this->action($content['nm']['action'],$content['nm']['selected'],$content['nm']['select_all'],
					$success,$failed,$action_msg,'cats',$msg,$content['nm']['checkboxes']['no_notifications']))
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
			'nm'  => $GLOBALS['egw']->session->appsession('cats','news_admin'),
		);
		if (!is_array($content['nm']))
		{
			$content['nm'] = array(
				'get_rows'       =>	'news_admin.news_admin_ui.get_cats',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
//				'header_right'   => 'news_admin.index.right',
				'bottom_too'     => false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'start'          =>	0,			// IO position in list
				'no_cat'         =>	true,		// IO category, if not 'no_cat' => True
				'search'         =>	'',			// IO search pattern
				'order'          =>	'news_date',// IO name of the column to sort after (optional for the sortheaders)
				'sort'           =>	'DESC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     =>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'no_filter'      => true,
				'no_filter2'     => true,
				'default_cols'   => '!legacy_actions',
				'row_id'         => 'id',
				'actions'        => $this->get_actions()
			);
		}
		$this->tpl->read('news_admin.cats');
		return $this->tpl->exec('news_admin.news_admin_ui.cats',$content,array(
		),$readonlys);
	}

	/**
	 * rows callback for index nextmatch
	 *
	 * @internal
	 * @param array &$query
	 * @param array &$rows returned rows/cups
	 * @param array &$readonlys eg. to disable buttons based on acl
	 * @return int total number of contacts matching the selection
	 */
	function get_cats(&$query_in,&$rows,&$readonlys,$id_only=false)
	{
		$GLOBALS['egw']->session->appsession('cats','news_admin',$query=$query_in);

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
				'allowOnMultiple' => true,
				'disableClass' => 'rowNoUpdate',
				'group' => $group,
			),
			'delete' => array(
				'caption' => 'Delete',
				'confirm' => 'Delete this category, and all news in it',
                                'confirm_multiple' => 'Delete these categories, and all news in them',
				'allowOnMultiple' => true,
				'nm_action' => 'open_popup',
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
	 * @param string|int $action 'status_to',set status of entries
	 * @param array $checked tracker id's to use if !$use_all
	 * @param boolean $use_all if true use all entries of the current selection (in the session)
	 * @param int &$success number of succeded actions
	 * @param int &$failed number of failed actions (not enought permissions)
	 * @param string &$action_msg translated verb for the actions, to be used in a message like %1 entries 'deleted'
	 * @param string|array $session_name 'index' or 'email', or array with session-data depending if we are in the main list or the popup
	 * @param string &$msg
	 * @param boolean $no_notification
	 * @return boolean true if all actions succeded, false otherwise
	 */
	function action($action,$checked,$use_all,&$success,&$failed,&$action_msg,$session_name,&$msg,$no_notification)
	{
		//echo '<p>'.__METHOD__."('$action',".array2string($checked).','.(int)$use_all.",...)</p>\n";
		$success = $failed = 0;
		if ($use_all)
		{
			// get the whole selection
			@set_time_limit(0);                     // switch off the execution time limit, as it's for big selections to small
			if(!is_array($session_name))
			{
				$old_query = $query = $GLOBALS['egw']->session->appsession($session_name,'news_admin');
			}
			else
			{
				$query = $session_name;
			}
			$query['num_rows'] = -1;        // all
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
			if($old_query) $GLOBALS['egw']->session->appsession($session_name,'news_admin',$old_query);
		}

		list($action, $settings) = explode('_', $action, 2);

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
				list($add_remove, $ids) = explode('_', $settings, 2);
				$ids = explode(',',$ids);
				$field = 'cat_'. ($action == 'reader' ? 'readable' : 'writable');

				foreach($checked as $id)
				{
					if (!$data = $this->read_cat($id)) continue;
					$data[$field] = $add_remove == 'add' ?
						$ids == array(categories::GLOBAL_ACCOUNT) ? $ids : array_merge($data[$field],$ids) :
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
