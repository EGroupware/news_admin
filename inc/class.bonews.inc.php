<?php
/**
 * news_admin - business object
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @copyright (c) 2006 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$ 
 */

require_once(EGW_INCLUDE_ROOT.'/etemplate/inc/class.so_sql.inc.php');

/**
 * Business object of the news_admin
 */
class bonews extends so_sql
{
	/**
	 * Instance of the news_admin acl class
	 *
	 * @var boacl
	 */
	var $acl;
	/**
	 * Timestamps which need to be converted to user-time and back
	 *
	 * @var array
	 */
	var $timestamps = array('news_date','news_begin','news_end');
	/**
	 * offset in secconds between user and server-time,
	 *	it need to be add to a server-time to get the user-time or substracted from a user-time to get the server-time
	 * 
	 * @var int
	 */
	var $tz_offset_s;
	/**
	 * Timestamp with actual user-time
	 * 
	 * @var int
	 */
	var $now;
	/**
	 * Current user
	 *
	 * @var int
	 */
	var $user;
	/**
	 * Labels for the visibility
	 *
	 * @var array
	 */
	var	$visiblity = array(
		'always' => 'Always',
		'never'  => 'Never',
		'date'   => 'By date',
	);

	/**
	 * Constructor
	 *
	 * @return bonews
	 */
	function bonews()
	{
		$this->so_sql('news_admin','egw_news');
		
		$this->acl =& CreateObject('news_admin.boacl');
		
		if (!is_object($GLOBALS['egw']->datetime))
		{
			$GLOBALS['egw']->datetime =& CreateObject('phpgwapi.datetime');
		}
		$this->tz_offset_s = $GLOBALS['egw']->datetime->tz_offset;
		$this->now = time() + $this->tz_offset_s;	// time() is server-time and we need a user-time

		$this->user = $GLOBALS['egw_info']['user']['account_id'];
	}
	
	/**
	 * changes the data from the db-format to your work-format
	 *
	 * reimplemented to adjust the timezone of the timestamps (adding $this->tz_offset_s to get user-time)
	 * Please note, we do NOT call the method of the parent so_sql !!!
	 *
	 * @param array $data if given works on that array and returns result, else works on internal data-array
	 * @return array with changed data
	 */
	function db2data($data=null)
	{
		if (!is_array($data))
		{
			$data = &$this->data;
		}
		foreach($this->timestamps as $name)
		{
			if (isset($data[$name]) && $data[$name]) $data[$name] += $this->tz_offset_s;
		}
		if (!$data['news_begin'])
		{
			$data['visible'] = (string) $data['news_end'] == '0' ? 'never' : 'always';
		}
		else
		{
			$data['visible'] = 'date';
		}
		return $data;
	}

	/**
	 * changes the data from your work-format to the db-format
	 *
	 * reimplemented to adjust the timezone of the timestamps (subtraction $this->tz_offset_s to get server-time)
	 * Please note, we do NOT call the method of the parent so_sql !!!
	 *
	 * @param array $data if given works on that array and returns result, else works on internal data-array
	 * @return array with changed data
	 */
	function data2db($data=null)
	{
		if ($intern = !is_array($data))
		{
			$data = &$this->data;
		}
		switch($data['visible'])
		{
			case 'always':
				$data['news_begin'] = 0;
				$data['news_end'] = null;
				break;
			case 'never':
				$data['news_begin'] = $data['news_end'] = 0;
				break;
			case 'date':
				if (!$data['news_end']) $data['news_end'] = null;
				break;
		}
		foreach($this->timestamps as $name)
		{
			if (isset($data[$name]) && $data[$name]) $data[$name] -= $this->tz_offset_s;
		}
		return $data;
	}
	
	/**
	 * saves the content of data to the db
	 *
	 * @param array $keys if given $keys are copied to data before saveing => allows a save as
	 * @return int 0 on success and errno != 0 else
	 */
	function save($keys=null)
	{
		if ($keys) $this->data_merge($keys);
		
		if (!$this->data['news_id'])	// new entry
		{
			$this->data['news_date'] = $this->now;
			$this->data['news_submittedby'] = $this->user;
		}
		if (!isset($this->data['news_is_html']))
		{
			$this->data['news_is_html'] = 1;
		}
		return parent::save();
	}
	
	/**
	 * Search / list news
	 *
	 * Reimplemented for different defaults and the "visibile" filter:
	 * - "now" (default if not set): currently active news
	 * - "always": always active news
	 * - "never": deactivated news
	 * - "date": news active by date
	 *
	 * @param array/string $criteria array of key and data cols, OR a SQL query (content for WHERE), fully quoted (!)
	 * @param boolean/string/array $only_keys=false True returns only keys, False returns all cols. or 
	 *	comma seperated list or array of columns to return
	 * @param string $order_by='news_date DESC' fieldnames + {ASC|DESC} separated by colons ',', can also contain a GROUP BY (if it contains ORDER BY)
	 * @param string/array $extra_cols='' string or array of strings to be added to the SELECT, eg. "count(*) as num"
	 * @param string $wildcard='' appended befor and after each criteria
	 * @param boolean $empty=false False=empty criteria are ignored in query, True=empty have to be empty in row
	 * @param string $op='AND' defaults to 'AND', can be set to 'OR' too, then criteria's are OR'ed together
	 * @param mixed $start=false if != false, return only maxmatch rows begining with start, or array($start,$num), or 'UNION' for a part of a union query
	 * @param array $filter=null if set (!=null) col-data pairs, to be and-ed (!) into the query without wildcards
	 * @return boolean/array of matching rows (the row is an array of the cols) or False
	 */
	function &search($criteria,$only_keys=false,$order_by='news_date DESC',$extra_cols='',$wildcard='',$empty=False,$op='AND',$start=false,$filter=null)
	{
		if (is_array($filter) && isset($filter['visible']))
		{
			$visible = $filter['visible'];
			unset($filter['visible']);
		}
		elseif(is_array($criteria) && isset($criteria['visible']))
		{
			$visible = $criteria['visible'];
			unset($criteria['visible']);
		}
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		//echo "<p align=right>today=$today</p>\n";
		switch($visible)
		{
			case 'all':
				break;

			default:
			case 'now':
				$filter[] = "(news_begin=0 AND news_end IS NULL OR news_begin <= $today AND $today <= news_end)";
				break;
			
			case 'future':
				$filter[] = "news_begin > $today";
				break;

			case 'old':
				$filter[] = "news_end < $today";
				$filter[] = 'news_end != 0';
				break;
				
			case 'always':
				$filter['news_begin'] = 0;
				$filter[] = 'news_end IS NULL';
				break;
				
			case 'never':
				$filter['news_end'] = 0;
				break;

			case 'date':
				$filter[] = 'news_begin > 0';
				break;
		}
		return parent::search($criteria,$only_keys,$order_by,$extra_cols,$wildcard,$empty,$op,$start,$filter);
	}

	/**
	 * Check if user has the necessary rights for a given operation
	 * 
	 * @param int $rights=EGW_ACL_READ
	 * @param array $data=null array with news or null to use $this->data
	 * @return boolean true if use has the necessary rights, false otherwise
	 */
	function check_acl($rights=EGW_ACL_READ,$data=null)
	{
		if ($rights == EGW_ACL_EDIT || $rights == EGW_ACL_DELETE) $rights = EGW_ACL_ADD;	// no edit or delete rights at the moment

		if (is_null($data)) $data =& $this->data;
		
		if (is_array($data))
		{
			if (!$data['news_id'] && $rights != EGW_ACL_ADD)	// new items can only be added
			{
				return false;
			}
			$cat_id = $data['cat_id'];
		}
		else
		{
			$cat_id = (int) $data;
		}
		return $this->acl->is_permitted($cat_id,$rights);
	}
	
	/**
	 * Returns the cats the user has certain rights to
	 *
	 * @param int $rights
	 * @return array with cat_id => name pairs
	 */
	function rights2cats($rights=EGW_ACL_READ)
	{
		static $all_cats;
		if (!is_array($all_cats))
		{
			$catbo =& CreateObject('phpgwapi.categories','','news_admin');
			if (!($all_cats = $catbo->return_array('all',0,False,'','','cat_name',True))) $all_cats = array();
		}
		if ($rights == EGW_ACL_EDIT) $rights = EGW_ACL_ADD;	// no edit rights at the moment

		$cats = array();
		foreach($all_cats as $cat)
		{
			if ($this->acl->is_permitted($cat['id'],$rights))
			{
				$cats[$cat['id']] = str_repeat('&nbsp;',$cat['level']).stripslashes($cat['name']).
					($cat['app_name'] == 'phpgw' || $cat['owner'] == '-1' ? ' &#9830;' : '');
			}
		}
		return $cats;
	}
}
