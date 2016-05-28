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

use EGroupware\Api;
use EGroupware\Api\Link;
use EGroupware\Api\Framework;
use EGroupware\Api\Egw;
use EGroupware\Api\Acl;

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
		unset($hook_data);	// not used

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
					unset($e);
					// permission error
					continue;
				}
				if (($title = $definition->get_title())) {
					$options[$title] = $title;
				}
				unset($definition);
			}
			$prefs['nextmatch-export-definition'] = array(
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
	 * Hook for sidebox, admin or Api\Preferences menu
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
			display_sidebox($appname, lang('Favorites'), Framework\Favorites::list_favorites($appname));

			$categories = new Api\Categories('',$appname);
			$bo = new news_admin_bo();
			$enableadd = count($bo->rights2cats(Acl::ADD)) > 0;
			foreach((array)$categories->return_sorted_array(0,False,'','','',false) as $cat)
			{
				if ($categories->check_perms(Acl::EDIT,$cat))
				{
					$enableadd = true;
					break;
				}
			}
			$menu_title = $GLOBALS['egw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
			$file = array();
			$file['News list'] = Egw::link('/index.php',array('menuaction' => 'news_admin.news_admin_gui.index'));
			if ($enableadd)
			{
				$file[] = array(
					'text' => lang('Add %1',lang(Link::get_registry($appname, 'entry'))),
					'no_lang' => true,
					'link' => "javascript:egw.open('','$appname','add')"
				);
			}

			display_sidebox($appname,$menu_title,$file);
		}

		// do NOT show export link, if phpgwapi is not installed, as uiexport uses ancient nextmatch from phpgwapi
		if ($GLOBALS['egw_info']['user']['apps']['admin'] && file_exists(EGW_SERVER_ROOT.'/phpgwapi'))
		{
			$title = lang('Administration');
			$file = Array(
				//'Site Configuration' => Egw::link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
				'Configure RSS exports' => Egw::link('/index.php','menuaction=news_admin.uiexport.exportlist')
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
		unset($data);	// not used

		return array(
			'menuaction' => 'news_admin.news_admin_ui.cats',
			'ajax' => 'true',
		);
	}

	/**
	 * Return link registration
	 *
	 * @return array
	 */
	public static function links() {
		return array(
			'query' => 'news_admin.news_admin_bo.link_query',
			'title' => 'news_admin.news_admin_bo.link_title',
			'view' => array(
				'menuaction' => 'news_admin.news_admin_gui.view'
			),
			'view_id' => 'news_id',
			'view_popup'  => '845x390',
			'view_list'	=>	'news_admin.news_admin_gui.index',
			'edit' => array(
				'menuaction' => 'news_admin.news_admin_gui.edit'
			),
			'edit_id' => 'news_id',
			'edit_popup'  => '845x750',
			'add' => array(
				'menuaction' => 'news_admin.news_admin_gui.edit'
			),
			'add_popup'  => '845x750',
			'entry' => 'News'
		);
	}
}
