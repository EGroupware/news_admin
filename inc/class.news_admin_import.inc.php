<?php
/**
 * news_admin - import RSS and Atom feeds
 *
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @copyright (c) 2007-14 by Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$
 */

require_once(EGW_INCLUDE_ROOT.'/news_admin/inc/class.bonews.inc.php');

/**
 * Import RSS and Atom feeds via PEAR's XML_Feed_Parser class
 */
class news_admin_import
{
	/**
	 * Reference to the news_admins's bo
	 *
	 * @var bonews
	 */
	var $bonews;

	/**
	 * Constructor
	 *
	 * @return news_admin_import
	 */
	function news_admin_import($bonews=null)
	{
		if (is_null($bonews))
		{
			$this->bonews = new bonews();
		}
		else
		{
			$this->bonews =& $bonews;
		}
	}

	/**
	 * Read the feed of the given URL
	 *
	 * @param string $url
	 * @param array $context =null
	 * @return XML_Feed_Parser|boolean false on error
	 */
	function read($url, array $context=null)
	{
		$default_lang = $GLOBALS['egw']->preferences->default['common']['lang'];
		$default_context = array(
			'method'=>'GET',
			'header' => 'Accept-Language: '.($default_lang ? $default_lang : 'en').';0.8,en;0.2',
		);
		$parts = parse_url($url);
		if (!in_array($parts['scheme'],array('http','https','ftp'))) return false;	// security!

		if (!($feed_xml = file_get_contents($url, false,
			stream_context_create(array('http' => $context ? $context : $default_context)))) ||
			!@include_once('XML/Feed/Parser.php'))
		{
			return false;
		}
		$matches = null;
		// if the xml-file specifes an encoding, convert it to our own encoding
		if (preg_match('/\<\?xml.*encoding="([^"]+)"/i',$feed_xml,$matches) && $matches[1])
		{
			$feed_xml = preg_replace('/(\<\?xml.*encoding=")([^"]+)"/i','$1'.translation::charset().'"',
				translation::convert($feed_xml, $matches[1]));
		}
		// stop "unsupported encoding" warnings
		error_reporting(($level = error_reporting()) & !E_WARNING);
		try {
		    $parser = new XML_Feed_Parser($feed_xml);
		}
		catch (XML_Feed_Parser_Exception $e)
		{
			unset($e);	// not used
			if (!$context)
			{
				// Try again with a user agent
				$context = array('user_agent' => 'Mozilla/5.0')+$default_context;
				$parser = $this->read($url, $context);
			}
			else
			{
				$parser = false;
			}
		}
		error_reporting($level);

		return $parser;
	}

	/**
	 * Import the feed of one category
	 *
	 * @param int $cat_id
	 * @return array/boolean array(total imported,newly imported) or false on error
	 */
	function import($cat_id)
	{
		if (($cat = $this->bonews->read_cat($cat_id)) === false) return false;
		if (! ($url = $cat['import_url'])) return false;
		if (!isset($cat['keep_imported'])) $cat['keep_imported'] = -1; // keep all was the default.

		$parser = $this->read($url);

		if (!is_object($parser)) return false;

		$imported = $newly = $deleted = 0;

		$news_delete = array();
		if ($cat['keep_imported'] >= 0)
		{
			$check = array('cat_id' => $cat_id);
			$count = 0;
			foreach($this->bonews->search($check,array('news_id'),'news_date DESC') as $news)
			{
				if (++$count > $cat['keep_imported']) {
					$news_delete[$news['news_id']] = true;
				}
			}
		}

		foreach ($parser as $entry)
		{
			$content_is_html = $entry->content && strip_tags($entry->content) != $entry->content;

			/* Update comma to the %ASCII encoding in the link to cope with the etemplate display.
			 * This cannot be done inside the eTemplate as this does not know the content type
			 * (URL / HTML / Plain Text) when converting variables to values in etemplate:expand_name()
			 */
			$entry->link = str_replace(',', '%2C', $entry->link);

			$check = array('cat_id' => $cat_id);
			if ($content_is_html)
			{
				$check['news_teaser'] = $entry->link;
			}
			else
			{
				$check['news_content'] = $entry->link;
			}
			if (($newsitem = $this->bonews->read($check)))
			{
				if (0 == $cat['keep_imported'])
				{
					unset($news_delete[$newsitem['news_id']]);
				}
			} else {
				$this->bonews->init();
				++$newly;
			}
			if ((($date = $entry->updated) || ($date = $entry->pubDate)) && !is_numeric($date))
			{
				$date = strtotime($date);
			}
			if (!($err = $this->bonews->save($item=array(
				'cat_id' => $cat_id,
				'news_date' => $date,
				'news_headline' => $entry->title,
				'news_content' => $content_is_html ? $entry->content : $entry->link,
				'news_is_html' => $content_is_html ? -2 : -1,		// -1 = only link
				'news_teaser' =>  $content_is_html ? $entry->link : ($entry->summary != $entry->title ? $entry->summary : NULL),
				'news_submittedby' => 0,
			),true)))
			{
				++$imported;
			}
			//var_dump($err); print "<li><a href=\"$entry->link\" target=\"_blank\">$entry->title</a></li>\n"; //_debug_array($this->bonews->data);
		}

		$deleted = $news_delete ? $this->bonews->delete(array_keys($news_delete)) : 0;

		/* Update the category timestamp on successful import */
		$cat['import_timestamp'] = $this->bonews->now;
		$this->bonews->save_cat($cat);

		return array($imported,$newly,$deleted);
	}

	/**
	 * Import all categories, called via the async timed service
	 *
	 */
	function async_import()
	{
		$cats = $nul = null;
		if (!$this->bonews->get_cats(array(
			'num_rows' => 999,
			'start' => 0,
		),$cats,$nul,true)) return;

		foreach($cats as $cat)
		{
			if ($cat['import_url'] && $cat['import_frequency'] && !((int)date('H') % $cat['import_frequency']))
			{
				$this->import($cat['id']);
			}
		}
	}
}
