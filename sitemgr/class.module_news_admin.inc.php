<?php
/**
 * news_admin - business object
 *
 * @link http://www.egroupware.org
 * @author Cornelius Weiss <egw@von-und-zu-weiss.de>
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @package news_admin
 * @copyright (c) 2005/6 by Cornelius Weiss <egw@von-und-zu-weiss.de> and Ralf Becker <RalfBecker-AT-outdoor-training.de>
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @version $Id$ 
 */

class module_news_admin extends Module
{
	/**
	 * Instance of the business object of news_admin
	 *
	 * @var bonews
	 */
	var $bonews;

	function module_news_admin()
	{
		$this->bonews =& CreateObject('news_admin.bonews');
		$GLOBALS['egw']->translation->add_app('news_admin');

		$this->arguments = array(
			'category' => array(
				'type' => 'select', 
				'label' => lang('Choose a category'), 
				'options' => array(),	// specification of options is postponed into the get_user_interface function
			),
			'show' => array(
				'type' => 'select', 
				'label' => lang('Which information do you want to show (CSS class)'), 
				'multiple' => 9,
				'options' => array(
					'date'        => lang('Date').' (news_date)',
					'datetime'    => lang('Date and time').' (news_date)',
					'title'       => lang('Headline with link').' (news_title)',
					'headline'    => lang('Headline').' (news_headline)',
					'submitted'   => lang('Submitted by and date').' (news_submitted)',
					'teaser'      => lang('Teaser').' (news_teaser)',
					'teaser_more' => lang('Teaser with read more link').' (news_teaser_more)',
					'content'     => lang('Content').' (news_content)',
					'more'        => lang('More news link').' (news_more_news)',
				),
			),
			'limit' => array(
				'type' => 'textfield', 
				'label' => lang('Number of news items to be displayed on page'),
				'params' => array('size' => 3)
			),
			'rsslink' => array(
				'type' => 'checkbox', 
				'label' => lang('Do you want to publish a RSS feed for this news category'),
			),
		);
		$this->get = array('item','start');
		$this->session = array('item','start');
		$this->properties = array();
		$this->title = lang('%1 module',lang('news_admin'));
		$this->description = lang('This module publishes news from the news_admin application on your website. Be aware of news_admin\'s ACL restrictions.');
	}

	function get_user_interface()
	{
		//we could put this into the module's constructor, but by putting it here, we make it execute only when the block is edited,
		//and not when it is generated for the web site, thus speeding the latter up slightly
		$this->arguments['category']['options'] = array('' => lang('All news'))+$this->bonews->rights2cats(EGW_ACL_READ);
		
		if (isset($this->block->arguments['layout']) && !isset($this->block->arguments['show']))
		{
			$this->block->arguments['show'] = $this->block->arguments['layout'] == 'header' ?
				array('date','title') : array('headline','submitted','teaser','content');
		}
		return parent::get_user_interface();
	}

	/**
	 * Returns a news_admin block
	 *
	 * @param array &$arguments
	 * @param array $properties
	 * @return string
	 */
	function get_content(&$arguments,$properties)
	{
		if (!$arguments['show'])
		{
			$arguments['show'] = $arguments['layout'] == 'header' ?
				array('date','title') : array('headline','submitted','teaser','content');
		}
		$limit = (int)$arguments['limit'] ? $arguments['limit'] : 5;
		$item = (int)$arguments['item'] ? $arguments['item'] : $_GET['item'];

		$html = '<div class="news_items news_items_'.implode('_',$arguments['show']).'">'."\n";

		if ((int)$item)
		{
			$news = $this->bonews->read($item);
			if ($news && ($news['cat_id'] == $arguments['category']))
			{
				$html .= $this->render($news,$arguments['show']);
			}
			else
			{
				$html = "\t<div>".lang('No matching news item')." ($item)!</div>\n";
			}
			$html .= "\t<div class=\"news_more_news\"><a href=\"" . $this->link(array('item'=>0)) . '">' . lang('More news') . "</a></div>\n";
		}
		else
		{
			$filter = $arguments['category'] ? array('cat_id' => $arguments['category']) : array();
			$result = $this->bonews->search('',false,'news_date DESC','','',false,'AND',array((int)$arguments['start'],$limit),$filter);
			
			foreach($result as $news)
			{
				$html .= $this->render($news,$arguments['show']);
			}
			if (in_array('more',$arguments['show']))
			{
				if ($arguments['start'])
				{
					$link_data['start'] = $arguments['start'] - $limit;
					$more = '<a href="' . $this->link($link_data) . '">&lt;&lt;&lt; '.lang('Back').'</a> ';
				}
				if ($this->bonews->total > $arguments['start'] + $limit)
				{
					$link_data['start'] = $arguments['start'] + $limit;
					$more .= '<a href="' . $this->link($link_data) . '">' . lang('More news') . '</a>';
				}
				if ($more) $html .= "\t<div class=\"news_more_news\">$more</div>\n";
			}
			if ($arguments['rsslink'])
			{
				$link = $GLOBALS['egw_info']['server']['webserver_url'] . '/news_admin/website/export.php?cat_id=' . $arguments['category'];
				$link = '<a href="'.$link.'" target="_blank"><img src="images/M_images/rss.png" alt="RSS" /></a>';
				$html .= "\t<div class=\"news_rss\">$link</div>\n";
			}
		}
		$html .= "</div>\n";
		
		return $html;
	}
	
	/**
	 * Return one formatted news item
	 *
	 * @param array $news
	 * @param array $show values: date, title, headline, submitted, teaser, teaser_more, content
	 * @return string
	 */
	function render($news,$show)
	{
		$html = "\t".'<div class="news_item news_item_'.implode('_',$show).'">'."\n";
		
		foreach($show as $name)
		{
			switch($name)
			{
				case 'content':
					// without the table an <img align="left"> displays the image outside the div, strange ...
					if (preg_match('/<img[^>]* align="(left|right)"/i',$news['news_content']))
					{
						$value = '<table><tr><td>'.$news['news_content'].'</td></tr></table>';
						break;
					}
					// fall-through
				case 'headline':
				case 'teaser':
					$value = $news['news_'.$name];
					break;
					
				case 'title':
				case 'teaser_more':
					$link = $this->link(false,false,array(array(
						'module_name' => 'news_admin',
						'arguments' => array(
							'show' => array('headline','submitted','teaser','content'),
							'item' => $news['news_id'],
							'category' => $news['cat_id'],
						),
						'page' => false,
						'area' => false,
						'sort_order' => false
					)));
					$value = $name == 'title' ? '' : $news['news_teaser'].' ';
					$value .= '<a href="'.$link.'" title="'.htmlspecialchars(lang('read more')).'">'.
						($name == 'title' ? $news['news_headline'] : lang('read more')).'</a>';
					break;
					
				case 'submitted':
					$value = lang('Submitted by %1 on %2',$GLOBALS['egw']->common->grab_owner_name($news['news_submittedby']),
						$GLOBALS['egw']->common->show_date($news['news_date'],'',false));
					break;

				case 'date':
				case 'datetime':
					$format = $GLOBALS['egw_info']['user']['preferences']['common']['dateformat'];
					if ($name == 'datetime')
					{
						$format .= ' '.($GLOBALS['egw_info']['user']['preferences']['common']['timeformat'] == 12 ? 'h:i a' : 'H:i');
					}
					$value = $GLOBALS['egw']->common->show_date($news['news_date'],$format,false);
					break;
					
				case 'more':
					continue;	// not displayed per item
			}
			$html .= "\t\t<div class=\"news_$name\">$value</div>\n";
		}
		$html .= "\t</div>\n";
		
		return $html;
	}
}
