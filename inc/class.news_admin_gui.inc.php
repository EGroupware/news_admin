<?php
/**
 * news_admin - user interface
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
use EGroupware\Api\Acl;
use EGroupware\Api\Etemplate;

/**
 * User interface of the news_admin
 */
class news_admin_gui extends news_admin_bo
{
	/**
	 * Methods callable via menuaction
	 *
	 * @var array
	 */
	var $public_functions = array(
		'view'	=> true,
		'edit'  => true,
		'index' => true,
	);
	/**
	 * Instance of the etemplate object
	 *
	 * @var Etemplate
	 */
	var $tpl;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		$this->tpl = new Etemplate();
	}

	/**
	 * View a news item
	 *
	 */
	public function view($_content = array())
	{
		$news_id = $_content['news_id'] ? $_content['news_id'] : $_GET['news_id'];
		if(!$this->read($news_id))
		{
		}

		$content = array_merge($this->data, $_content);
		if ($content['delete'] && $this->check_acl(Acl::DELETE))
		{
			$this->delete(array('news_id' => $this->data['news_id']));
			Framework::refresh_opener(lang('News deleted.'), 'news_admin', $this->data['news_id'], 'delete');
			Framework::window_close();
			return;
		}
		$sel_options = array(
			'cat_id' => $this->rights2cats($this->data['news_id'] ? Acl::EDIT : Acl::ADD),
			'visible' => $this->visiblity,
		);
		if (!$content['cat_id']) $content['cat_id'] = @key($sel_options['cat_id']);
		$readonlys['edit'] = !$this->check_acl(Acl::EDIT);
		$readonlys['delete'] = !$this->check_acl(Acl::DELETE);

		$preserve['news_id'] = $news_id;
		$this->tpl->read('news_admin.view');
		return $this->tpl->exec('news_admin.news_admin_gui.view',$content,$sel_options,$readonlys,$preserve,2);
	}

	/**
	 * Edit a news
	 *
	 * @param array $_content =null submitted etemplate content
	 * @param string $msg =''
	 */
	function edit($_content=null,$msg='')
	{
		$referer = Api\Header\Referer::get();
		if (!is_array($_content))
		{
			if (!(int) $_GET['news_id'] || !$this->read($_GET['news_id']))
			{
				$this->init();
				$this->data['visible'] = 'always';
			}
		}
		elseif (!empty($content['button']))
		{
			$button = key($_content['button']);
			unset($_content['button']);
			$this->data = $_content;

			switch($button)
			{
				case 'delete':
					if ($this->check_acl(Acl::DELETE))
					{
						$this->delete(array('news_id' => $this->data['news_id']));
						Framework::refresh_opener(lang('News deleted.'), 'news_admin', $this->data['news_id'], 'delete');
						return;
					}
					break;

				case 'apply':
				case 'save':
					if ($this->check_acl($this->data['news_id'] ? Acl::EDIT : Acl::ADD))
					{
						if (!isset($this->data['news_is_html']))
						{
							$this->data['news_is_html'] = Api\Html::htmlarea_availible();
						}
						if ($_content['news_id'] && ($_content['old_lang'] != $_content['news_lang']))	// lang changed --> save as new
						{
							//first save from Default Language
							if ($_content['old_lang'] == null && $_content['news_source_id'] == null)
							{
								$this->data['news_source_id'] = $_content['news_id'];
								$_content['news_source_id'] = $_content['news_id'];
							}
							//set old Language new Default Language
							if  ($_content['old_lang'] != null && $_content['news_source_id'] == null && $_content['news_lang'] == null)
							{
								$_content['set_new_default'] = true;
								$_content['set_old_news_id'] = $_content['news_id'];
							}

							unset($this->data['news_id']);
							// check if we already have an entry for the new lang
							if ($_content['news_source_id'] && ($lang_entry = $this->search(array(),true,'','','',false,'AND',false,array(
									'news_lang' => $_content['news_lang'],'news_source_id' => $_content['news_source_id']))))
							{
								$this->data['news_id'] =$lang_entry[0]['news_id'];
							}
						}
						if (($err = $this->save()) == 0)
						{
							// make sure $_content gets all the data of the new/updated entry
							$_content = $this->data;

							Framework::refresh_opener(lang('News saved.'), 'news_admin', $this->data['news_id'], 'update');

							if ($_content['set_new_default'])	// created a new default lang for an existing entry
							{
								$this->set_default($_content['set_old_news_id']);	// set the new default in all existing translations
								unset($_content['set_old_news_id']);
							}
						}
						else
						{
							Framework::message(lang('Error saving the news!'),'error');
							$button = '';
						}
					}
					if ($button == 'save')
					{
						Framework::window_close();
						exit();
					}
					//break; // fall through, as the user did hit apply. So we want to redisplay our new or modified article
				case 'reload':
					$source_id = $_content['news_source_id'] ? $_content['news_source_id'] : $_content['news_id'];
					if (!$this->read(array('news_id'=>$source_id,'news_lang'=>$_content['news_lang'])))
					{
						$this->data['news_source_id'] = $source_id;
						$this->data['news_lang'] = $_content['news_lang'];
						if (!$_content['news_source_id'] && $_content['old_lang'])
						{
							$this->data['set_new_default'] = true;	// remember to set a this entry as new default when saving
						}
						$msg = lang('There no such translation.');
					}
					$this->data['referer'] = $_content['referer'];
					break;
				case 'cancel':	// should never happen
					break;
			}
			// set the referer info to null, so the original referer will be preserved
			$referer = NULL;
		}
		$content = $preserve = $this->data;
		$preserve['old_lang'] = $this->data['news_lang'];	// remember old lang
		if ($referer) $content['referer'] = $preserve['referer'] = $referer;
		$content['msg'] = $msg;
		$content['upload_dir'] = $GLOBALS['egw_info']['user']['preferences']['news_admin']['upload_dir'];
		$sel_options = array(
			'cat_id' => $this->rights2cats($this->data['news_id'] ? Acl::EDIT : Acl::ADD),
			'visible' => $this->visiblity,
		);
		if (!$content['cat_id'])
		{
			$content['cat_id'] = @key($sel_options['cat_id']);
		}

		$readonly = $this->data['news_id'] ? !$this->check_acl(Acl::EDIT) : !$sel_options['cat_id'];
		$readonlys = array(
			'button[delete]' => !$this->data['news_id'] || !$this->check_acl(Acl::DELETE),
			'button[save]'   => $readonly,
			'button[apply]'  => $readonly,
		);
		if ($readonly)
		{
			foreach(array_keys($this->data) as $name)
			{
				$readonlys[$name] = true;
			}
			if (!isset($sel_options['cat_id'][$content['cat_id']]))
			{
				$sel_options['cat_id'][$content['cat_id']] = $this->cats->id2name($content['cat_id']);
			}
		}
		$this->tpl->read('news_admin.edit');
		return $this->tpl->exec('news_admin.news_admin_gui.edit',$content,$sel_options,$readonlys,$preserve,2);
	}

	/**
	 * List the news
	 *
	 * @param array $_content =null submitted etemplate content
	 * @param string $msg =''
	 * @return string
	 */
	public function index($_content=null,$msg='')
	{
		if ($_GET['msg']) $msg = $_GET['msg'];

		if ($_content['nm']['action'] == 'delete')
		{
			$success = 0;
			foreach($_content['nm']['selected'] as $id)
			{
				if ($this->delete(array('news_id' => $id))) $success++;
			}
			if($success)
			{
				Framework::refresh_opener($success . ' ' . lang('News deleted.'),'news_admin');
			}
		}
		$content = array(
			'msg' => $msg,
			'nm'  => Api\Cache::getSession('news_admin', 'index'),
		);
		if (count($this->rights2cats(Acl::ADD)) == 0)
		{
			$readonlys['add'] = true;
		}
		if (!is_array($content['nm']))
		{
			$content['nm'] = array(
				'get_rows'       =>	'news_admin.news_admin_gui.get_rrows',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'bottom_too'     => false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'start'          =>	0,			// IO position in list
				'no_cat'         =>	true,		// IO category, if not 'no_cat' => True
				'search'         =>	'',			// IO search pattern
				'order'          =>	'news_date',// IO name of the column to sort after (optional for the sortheaders)
				'sort'           =>	'DESC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     =>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'filter'         =>	'',	// =All	// IO filter, if not 'no_filter' => True
				'filter_no_lang' => True,		// I  set no_lang for filter (=dont translate the options)
				'filter2'        =>	'content',	// IO filter2, if not 'no_filter2' => True
				'favorites'	=> true,           // Enable favorites
				'options-filter2' => array(
					'content'  => 'Content',
					'teaser'   => 'Teaser',
					'headline' => 'Headline',
				),
				'col_filter'     => array(
					'visible' => 'now',
					'news_lang' => $this->lang,
				),
				'row_id'	=> 'news_id',
			);
		}
		$content['nm']['actions'] = $this->get_actions();
		if (is_numeric($_GET['cat_id'])) $content['nm']['filter'] = (int) $_GET['cat_id'];

		 // add scrollbar to long description, if user choose so in his prefs
		$prefs = $GLOBALS['egw_info']['user']['preferences']['news_admin'];
		if ($prefs['limit_des_lines'] > 0 || (string)$prefs['limit_des_lines'] == '')
		{
			$content['css'] .= '<style type="text/css">@media screen { .news_content {  '.
					' max-height: '.
					(($prefs['limit_des_lines'] ? $prefs['limit_des_lines'] : 5) * 1.35).       // dono why em is not real lines
					'em; overflow: auto; }}</style>';
		}

		$this->tpl->read('news_admin.index');
		return $this->tpl->exec('news_admin.news_admin_gui.index',$content,
			array(
			'filter' => array('' => lang('All news'))+$this->rights2cats(Acl::READ),
			'visible' => array('now' => 'Current','future' => 'Future','old' => 'Old')+$this->visiblity,
			),
			$readonlys);
	}

	/**
	 * rows callback for index nextmatch
	 *
	 * @internal
	 * @param array &$query
	 * @param array &$rows returned rows/cups
	 * @param array &$readonlys eg. to disable buttons based on acl
	 * @return int total number of news matching the selection
	 */
	function get_rrows(&$query_in,&$rows,&$readonlys)
	{
		Api\Cache::setSession('news_admin', $query_in['session_for'] ? $query_in['session_for'] : 'index', $query=$query_in);

		if ((int)$query['filter'])
		{
			$query['col_filter']['cat_id'] = $query['filter'];
		}
		else
		{
			unset($query['col_filter']['cat_id']);
		}
		if (!$query['col_filter']['news_submittedby'])
		{
			unset($query['col_filter']['news_submittedby']);
		}
		if (!$query['col_filter']['visible']) $query['col_filter']['visible'] = 'all';

		$total = parent::get_rows($query,$rows,$readonlys);

		foreach($rows as $k => $row)
		{
			if(!$this->check_acl(Acl::EDIT,$row))
			{
				$rows[$k]['class'] .= 'rowNoEdit ';
			}
			if(!$this->check_acl(Acl::DELETE,$row))
			{
				$rows[$k]['class'] .= 'rowNoDelete ';
			}

			switch($query['filter2'])
			{
				case 'headline':
					unset($rows[$k]['news_teaser']);
					// fall through
				case 'teaser':
					unset($rows[$k]['news_content']);
			}
		}
		//_debug_array($rows);
		return (int)$total;
	}

	/**
	 * Nextmatch actions
	 * see nextmatch_widget::get_actions()
	 */
	public function get_actions()
	{
		$actions = array(
			'open' => array(
				'caption' => 'Open',
				'default' => true,
				'allowOnMultiple' => false,
				'url' => 'menuaction=news_admin.news_admin_gui.edit&news_id=$id',
				'popup' => Link::get_registry('news_admin', 'edit_popup'),
				'group' => $group=1,
			),
			'add' => array(
				'caption' => 'Add',
				'url' => 'menuaction=news_admin.news_admin_gui.edit',
				'popup' => Link::get_registry('news_admin', 'add_popup'),
				'group' => $group,
				'disabled' => !$this->rights2cats(Acl::ADD),
			),
			'view' => array(
				'caption' => 'View',
				'allowOnMultiple' => false,
				'url' => 'menuaction=news_admin.news_admin_gui.view&news_id=$id',
				'popup' => Link::get_registry('news_admin', 'view_popup'),
				'group' => $group,
			),
			'delete' => array(
				'caption' => 'Delete',
				'confirm' => 'Delete this entry',
				'confirm_multiple' => 'Delete these entries',
				'group' => $group,
				'disableClass' => 'rowNoDelete',
			),
		);
		return $actions;
	}
}
