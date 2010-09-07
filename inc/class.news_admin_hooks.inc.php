<?php
/**
 * news_admin - hooks
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

/**
 * Static hooks for news admin
 */
class news_admin_hooks
{
	/**
	 * Settings hook
	 *
	 * @param array|string $hook_data
	 */
	static public function settings($hook_data)
	{
		$show_entries = array(
			0 => lang('No'),
			1 => lang('Yes'),
			2 => lang('Yes').' - '.lang('small view'),
		);
		$_show_entries = array(
			0 => lang('No'),
			1 => lang('Yes'),
		);

		$prefs = array(
			'homeShowLatest' => array(
				'type'   => 'select',
				'label'  => 'Show news articles on main page?',
				'name'   => 'homeShowLatest',
				'values' => $show_entries,
				'help'   => 'Should News_Admin display the latest article headlines on the main screen.',
				'xmlrpc' => True,
				'admin'  => False,
				'default'=> '2',
			),
			'homeShowLatestCount' => array(
				'type'    => 'input',
				'label'   => 'Number of articles to display on the main screen',
				'name'    => 'homeShowLatestCount',
				'size'    => 3,
				'maxsize' => 10,
				'help'    => 'Number of articles to display on the main screen',
				'xmlrpc'  => True,
				'admin'   => False,
				'default' => 5,
			),
			'homeShowCats' => array(
				'type'   => 'multiselect',
				'label'  => 'Categories to displayed on main page?',
				'name'   => 'homeShowCats',
				'values' => ExecMethod('news_admin.bonews.rights2cats',EGW_ACL_READ),
				'help'   => 'Which news categories should be displayed on the main screen.',
				'xmlrpc' => True,
				'admin'  => False,
			),
			'rtfEditorFeatures' => array(
				'type'   => 'select',
				'label'  => 'Features of the editor?',
				'name'   => 'rtfEditorFeatures',
				'values' => array(
					'simple'   => lang('Simple'),
					'extended' => lang('Regular'),
					'advanced' => lang('Everything'),
				),
				'help'   => 'You can customize how many icons and toolbars the editor shows.',
				'xmlrpc' => True,
				'admin'  => False,
				'default'=> 'extended',
			),
		);
		if ($GLOBALS['egw_info']['user']['apps']['filemanager'])
		{
			$prefs['upload_dir'] = array(
				'type'  => 'input',
				'label' => 'VFS upload directory',
				'name'  => 'upload_dir',
				'size'  => 50,
				'help'  => 'Start directory for image browser of rich text editor in EGroupware VFS (filemanager).',
				'xmlrpc' => True,
				'admin'  => False,
			);
		}
		return $prefs;
	}

	/**
	 * Hook for sidebox, admin or preferences menu
	 *
	 * @param array|string $hook_data
	 */
	public static function all_hooks($hook_data)
	{
		$location = is_array($hook_data) ? $hook_data['location'] : $hook_data;
		$appname = 'news_admin';
		
		if ($location == 'sidebox_menu')
		{
			$categories = new categories('',$appname);
			$enableadd = false;
			foreach((array)$categories->return_sorted_array(0,False,'','','',false) as $cat)
			{
				if ($categories->check_perms(EGW_ACL_EDIT,$cat))
				{
					$enableadd = true;
					break;
				}
			}
			$menu_title = $GLOBALS['egw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
			$file = array();
			if ($enableadd)
			{
				$file = Array(
					array(
						'text' => '<a class="textSidebox" href="'.egw::link('/index.php',array('menuaction' => 'news_admin.uinews.edit')).
							'" onclick="window.open(this.href,\'_blank\',\'dependent=yes,width=700,height=580,scrollbars=yes,status=yes\');
							return false;">'.lang('Add').'</a>',
						'no_lang' => true,
						'link' => false
					));
			}
			$file['Read news'] = egw::link('/index.php',array('menuaction' => 'news_admin.uinews.index'));
	
			display_sidebox($appname,$menu_title,$file);
		}
		if ($location != 'admin' && $GLOBALS['egw_info']['user']['apps']['preferences'])
		{
			$title = lang('Preferences');
			$file = array();
			$file['Preferences'] = egw::link('/index.php','menuaction=preferences.uisettings.index&appname=' . $appname);
			$file['Categories'] = egw::link('/index.php','menuaction=news_admin.news_admin_ui.cats');
			
			if ($location == 'sidebox_menu')
			{
				display_sidebox($appname,$title,$file);
			}
			else
			{
				display_section($appname,$title,$file);
			}
		}
		if($location != 'preferences' && $GLOBALS['egw_info']['user']['apps']['admin'])
		{
			$title = lang('Administration');
			$file = Array(
				//'Site Configuration' => egw::link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
				'Configure RSS exports' => egw::link('/index.php','menuaction=news_admin.uiexport.exportlist')
			);

			if ($location == 'sidebox_menu')
			{
				display_sidebox($appname,$title,$file);
			}
			else
			{
				display_section($appname,$title,$file);
			}
		}
	}

	/**
	 * Hook for admin menu
	 *
	 * @deprecated use all_hooks
	 * @param array|string $hook_data
	 */
	public static function admin($hook_data)
	{
		return self::all_hooks($hook_data);
	}

	/**
	 * Hook for preferences menu
	 *
	 * @deprecated use all_hooks
	 * @param array|string $hook_data
	 */
	public static function preferences($hook_data)
	{
		return self::all_hooks($hook_data);
	}

	/**
	 * Hook for sidebox menu
	 *
	 * @deprecated use all_hooks
	 * @param array|string $hook_data
	 */
	public static function sidebox_menu($hook_data)
	{
		return self::all_hooks($hook_data);
	}	
}
