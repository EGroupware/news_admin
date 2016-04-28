<?php
/**
 * Egroupware - News Admin - A portlet for displaying a list of entries
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @package news_admin
 * @subpackage home
 * @link http://www.egroupware.org
 * @author Nathan Gray
 * @version $Id$
 */

use EGroupware\Api\Framework;
use EGroupware\Api\Acl;
use EGroupware\Api\Etemplate;

require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.news_ui.inc.php');
/**
 * The news_admin_list_portlet uses a nextmatch / favorite
 * to display a list of entries.
 */
class news_admin_favorite_portlet extends home_favorite_portlet
{
	/**
	 * Construct the portlet
	 *
	 */
	public function __construct(Array &$context = array(), &$need_reload = false)
	{
		$context['appname'] = 'news_admin';

		// Let parent handle the basic stuff
		parent::__construct($context,$need_reload);

		$this->context['template'] = 'news_admin.index.rows';
		$this->nm_settings += array(
			'no_cat'	=> true,
			'get_rows'	=> 'news_admin.news_ui.get_rows',
			// Use a different template so it can be accessed from client side
			'template'	=> 'news_admin.index.rows',
			'default_cols'	=> 'news',
			'session_for'	=> 'home',
			'row_id'        => 'news_id'
		);
	}

	public function exec($id = null, Etemplate &$etemplate = null)
	{
		$ui = new news_ui();

		$this->context['sel_options']['filter'] = array('' => lang('All news'))+$ui->rights2cats(Acl::READ);
		$this->context['sel_options']['filter2'] = array(
			'content'  => 'Content',
			'teaser'   => 'Teaser',
			'headline' => 'Headline',
		);
		$this->context['sel_options'] += array(
			'visible' => array('now' => 'Current','future' => 'Future','old' => 'Old')+$ui->visiblity,
		);
		$this->nm_settings['actions'] = $ui->get_actions($this->nm_settings);

		parent::exec($id, $etemplate);
	}

	/**
	 * Here we need to handle any incoming data.  Setup is done in the constructor,
	 * output is handled by parent.
	 *
	 * @param $content =array()
	 */
	public static function process($content = array())
	{
		parent::process($content);

		// This is just copy+pasted from news_ui line 235, but we don't want
		// the etemplate exec to fire again.
		if ($content['nm']['action'] == 'delete')
		{
			$success = 0;
			foreach($content['nm']['selected'] as $id)
			{
				if ($this->delete(array('news_id' => $id))) $success++;
			}
			if($success)
			{
				Framework::refresh_opener($success . ' ' . lang('News deleted.'),'news_admin');
			}
		}
	}
 }