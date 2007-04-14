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

require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.bonews.inc.php');

/**
 * User interface of the news_admin
 */
class uinews extends bonews
{
	/**
	 * Methods callable via menuaction
	 *
	 * @var array
	 */
	var $public_functions = array(
		'edit'  => true,
		'index' => true,
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
	function uinews()
	{
		$this->bonews();
		
		$this->tpl =& CreateObject('etemplate.etemplate');
	}
	
	/**
	 * Edit a news
	 *
	 * @param array $content=null submitted etemplate content
	 * @param string $msg=''
	 */
	function edit($content=null,$msg='')
	{
		if (!is_array($content))
		{
			if (!(int) $_GET['news_id'] || !$this->read($_GET['news_id']))
			{
				$this->init();
				$this->data['visible'] = 'always';
			}
		}
		else
		{
			list($button) = each($content['button']);
			unset($content['button']);
			$this->data = $content;

			switch($button)
			{
				case 'delete':
					if ($this->check_acl(EGW_ACL_DELETE))
					{
						$this->delete($this->data['news_id']);
						$msg = lang('News deleted.');
						echo "<html><body><script>var referer = opener.location;opener.location.href = referer+(referer.search?'&':'?')+'msg=".
							addslashes(urlencode($msg))."'; window.close();</script></body></html>\n";
						$GLOBALS['egw']->common->egw_exit();
					}
					break;

				case 'apply':
				case 'save':
					if ($this->check_acl($this->data['news_id'] ? EGW_ACL_EDIT : EGW_ACL_ADD))
					{
						if (!isset($this->data['news_is_html']))
						{
							$this->data['news_is_html'] = $this->tpl->html->htmlarea_availible();
						}
						if (($err = $this->save()) == 0)
						{
							$msg = lang('News saved.');
							$js = "opener.location.href=opener.location.href+'&msg=".addslashes(urlencode($msg))."';";
						}
						else
						{
							$msg = lang('Error saving the news!');
							$button = '';
						}
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
					
				case 'cancel':	// should never happen
					break;
			}
		}
		$content = $preserve = $this->data;
		$content['msg'] = $msg;
		if (!($content['rtfEditorFeatures'] = $GLOBALS['egw_info']['user']['preferences']['news_admin']['rtfEditorFeatures']))
		{
			$content['rtfEditorFeatures'] = 'extended';	// better default than simple for news_admin
		}
		$content['upload_dir'] = $this->_get_upload_dir();
		$sel_options = array(
			'cat_id' => $this->rights2cats($this->data['news_id'] ? EGW_ACL_EDIT : EGW_ACL_ADD),
			'visible' => $this->visiblity,
		);
		if (!$content['cat_id']) list($content['cat_id']) = @each($sel_options['cat_id']);

		$readonly = $this->data['news_id'] ? !$this->check_acl(EGW_ACL_EDIT) : !$sel_options['cat_id'];
		$readonlys = array(
			'button[delete]' => !$this->data['news_id'] || !$this->check_acl(EGW_ACL_DELETE),
			'button[save]'   => $readonly,
			'button[apply]'  => $readonly,
		);
		if ($readonly)
		{
			foreach($this->data as $name => $value)
			{
				$readonlys[$name] = true;
			}
		}
		$this->tpl->read('news_admin.edit');
		return $this->tpl->exec('news_admin.uinews.edit',$content,$sel_options,$readonlys,$preserve,2);
	}
	
	/**
	 * Read the upload dir from site configuration
	 *
	 * @return string
	 */
	function _get_upload_dir()
	{
		include_once(EGW_API_INC.'/class.config.inc.php');
		$config = new config('news_admin');
		$config->read_repository();
		
		return $config->config_data['upload_dir'];
	}
	
	/**
	 * List the news
	 *
	 * @param array $content=null submitted etemplate content
	 * @param string $msg=''
	 * @return string
	 */
	function index($content=null,$msg='')
	{
		if ($_GET['msg']) $msg = $_GET['msg'];
		
		$content = array(
			'msg' => $msg,
			'nm'  => $GLOBALS['egw']->session->appsession('index','news_admin'),
		);
		if (!$this->rights2cats(EGW_ACL_ADD))
		{
			$readonlys['add'] = $readonlys['nm']['add'] = true;
		}
		if (!is_array($content['nm']))
		{
			$content['nm'] = array(
				'get_rows'       =>	'news_admin.uinews.get_rows',	// I  method/callback to request the data for the rows eg. 'notes.bo.get_rows'
				'header_right'   => 'news_admin.index.right',
				'bottom_too'     => false,		// I  show the nextmatch-line (arrows, filters, search, ...) again after the rows
				'start'          =>	0,			// IO position in list
				'no_cat'         =>	true,		// IO category, if not 'no_cat' => True
				'search'         =>	'',			// IO search pattern
				'order'          =>	'news_date',// IO name of the column to sort after (optional for the sortheaders)
				'sort'           =>	'DESC',		// IO direction of the sort: 'ASC' or 'DESC'
				'col_filter'     =>	array(),	// IO array of column-name value pairs (optional for the filterheaders)
				'filter_label'   =>	lang('Category'),// I  label for filter    (optional)
				'filter'         =>	'',	// =All	// IO filter, if not 'no_filter' => True
				'filter_no_lang' => True,		// I  set no_lang for filter (=dont translate the options)
				'filter2_label'  => 'Show',		// I  label for filter2
				'filter2'        =>	'content',	// IO filter2, if not 'no_filter2' => True
				'options-filter2' => array(
					'content'  => 'Content',
					'teaser'   => 'Teaser',
					'headline' => 'Headline',
				),
				'col_filter'     => array('visible' => 'now'),
			);
		}
		$this->tpl->read('news_admin.index');
		return $this->tpl->exec('news_admin.uinews.index',$content,array(
			'filter' => array('' => lang('All news'))+$this->rights2cats(EGW_ACL_READ),
			'visible' => array('now' => 'Current','future' => 'Future','old' => 'Old')+$this->visiblity,
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
	function get_rows(&$query_in,&$rows,&$readonlys,$id_only=false)
	{
		$GLOBALS['egw']->session->appsession('index','news_admin',$query=$query_in);
		
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
		
		$readonlys = array();
		foreach($rows as $k => $row)
		{
			$readonlys['edit['.$row['news_id'].']']   = !$this->check_acl(EGW_ACL_EDIT,$row);
			$readonlys['delete['.$row['news_id'].']'] = !$this->check_acl(EGW_ACL_DELETE,$row);
			
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
		return $total;
	}
}
