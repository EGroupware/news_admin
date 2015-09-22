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
			'limit_des_lines' => array(
				'type'   => 'input',
				'size'   => 5,
				'label'  => 'Limit number of description lines (default 5, 0 for no limit)',
				'name'   => 'limit_des_lines',
				'help'   => 'How many describtion lines should be directly visible. Further lines are available via a scrollbar.',
				'xmlrpc' => True,
				'admin'  => False,
				'default'=> 5,
			),
		);
		if ($GLOBALS['egw_info']['user']['apps']['filemanager'])
		{
			$prefs['upload_dir'] = array(
				'type'  => 'vfs_dir',
				'label' => 'VFS upload directory',
				'name'  => 'upload_dir',
				'size'  => 50,
				'help'  => 'Start directory for image browser of rich text editor in EGroupware VFS (filemanager).',
				'xmlrpc' => True,
				'admin'  => False,
			);
		}
		// Import / Export for nextmatch
		if ($GLOBALS['egw_info']['user']['apps']['importexport'])
		{
			$definitions = new importexport_definitions_bo(array(
				'type' => 'export',
				'application' => 'news_admin'
			));
			$options = array(
				'~nextmatch~'	=>	lang('Old fixed definition')
			);
			foreach ((array)$definitions->get_definitions() as $identifier) {
				try {
					$definition = new importexport_definition($identifier);
				} catch (Exception $e) {
					// permission error
					continue;
				}
				if ($title = $definition->get_title()) {
					$options[$title] = $title;
				}
				unset($definition);
			}
			$settings['nextmatch-export-definition'] = array(
				'type'   => 'select',
				'values' => $options,
				'label'  => 'Export definitition to use for nextmatch export',
				'name'   => 'nextmatch-export-definition',
				'help'   => lang('If you specify an export definition, it will be used when you export'),
				'run_lang' => false,
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
			// Magic etemplate2 favorites menu (from nextmatch widget)
			display_sidebox($appname, lang('Favorites'), egw_framework::favorite_list($appname));

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
			$file['News list'] = egw::link('/index.php',array('menuaction' => 'news_admin.news_ui.index'));
			if ($enableadd)
			{
				$file[] = array(
					'text' => lang('Add %1',lang(egw_link::get_registry($appname, 'entry'))),
					'no_lang' => true,
					'link' => "javascript:egw.open('','$appname','add')"
				);
			}

			display_sidebox($appname,$menu_title,$file);
		}

		if ($GLOBALS['egw_info']['user']['apps']['admin'])
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
	 * Hook to tell framework we use standard categories method
	 *
	 * @param string|array $data hook-data or location
	 * @return boolean|array
	 */
	public static function categories($data)
	{
		return array('menuaction' => 'news_admin.news_admin_ui.cats');
	}

	/**
	 * Return link registration
	 *
	 * @return array
	 */
	public static function links() {
		return array(
			'query' => 'news_admin.news_bo.link_query',
			'title' => 'news_admin.news_bo.link_title',
			'view' => array(
				'menuaction' => 'news_admin.news_ui.view'
			),
			'view_id' => 'news_id',
			'view_popup'  => '845x390',
			'view_list'	=>	'news_admin.news_ui.index',
			'edit' => array(
				'menuaction' => 'news_admin.news_ui.edit'
			),
			'edit_id' => 'news_id',
			'edit_popup'  => '845x750',
			'add' => array(
				'menuaction' => 'news_admin.news_ui.edit'
			),
			'add_popup'  => '845x750',
		);
	}
}
