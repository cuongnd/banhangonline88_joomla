<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingViewRSSFeeds extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$type = $jinput->get('type', null, 'word');
		$locationId = $jinput->get('location', null, 'int');

		if($type != null)
		{
			switch($type)
			{
// ------------------------- Today deal -------------------------  //
				case 'today':
					$items = array();
					$item = array();
					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getTodayDeal();

					if(empty($deal))
					{
						$item['title'] = JText::_('COM_CMGROUPBUYING_RSS_TODAY_DEAL_NOT_FOUND');
						$item['description'] = '';
						$item['link'] = '';
						$item['pubDate'] = '';
					}
					else
					{
						$dealImages = array();

						for($j = 1; $j <= 5; $j++)
						{
							$columnName = 'image_path_' . $j;

							if($deal[$columnName] != '')
							{
								$dealImages[] = JURI::root() . $deal[$columnName];
							}
						}

						if(!empty($dealImages))
						{
							$image = '<img align="right" alt="' . str_replace("\"", "'", $deal['short_description']) . '" src="' . $dealImages[0] . '" width="300px" />';
						}
						else
						{
							$image = '';
						}

						$link = JURI::root() . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], false);
						$name = $deal['name'];
						$item['title'] = $name;
						$item['description'] = $image . $deal['description'];
						$item['link'] = $link;
						$item['pubDate'] = '';
					}
					
					$title = JFactory::getApplication()->getCfg('sitename');
					$pubDate = CMGroupBuyingHelperDateTime::getCurrentDateTime(); 
					$description = JText::_('COM_CMGROUPBUYING_RSS_ALL_DEAL_DESCRIPTION');
					$items[] = $item;
					$this->generateRSS($title, $description, $pubDate, $items);
					jexit();
					break;

// ------------------------- All deals -------------------------  //

				case 'all':
					$items = array();
					$item = array();

					if($locationId > 0)
					{
						$location = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getLocationById($locationId);

						isset($location['name']) ? $locationName = $location['name'] : $locationName = '';
						$description = JText::sprintf('COM_CMGROUPBUYING_RSS_ALL_DEALS_LOCATION_DESCRIPTION', $locationName);                        
						$locationString = CMGroupBuyingHelperDeal::getDealsInLocation($locationId);
					}
					else
					{
						$description = JText::_('COM_CMGROUPBUYING_RSS_ALL_DEAL_DESCRIPTION');
						$locationString = null;
					}

					$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(false, false, false, null, null, $locationString, false);

					if(empty($deals))
					{
						$item['title'] = JText::_('COM_CMGROUPBUYING_RSS_ALL_DEALS_NOT_FOUND');
						$item['description'] = '';
						$item['link'] = '';
						$item['pubDate'] = '';
						$items[] = $item;
					}
					else
					{
						foreach($deals as $deal)
						{
							$dealImages = array();

							for($j = 1; $j <= 5; $j++)
							{
								$columnName = 'image_path_' . $j;

								if($deal[$columnName] != '')
								{
									$dealImages[] = JURI::root() . $deal[$columnName];
								}
							}

							if(!empty($dealImages))
							{
								$image = '<img align="right" alt="' . str_replace("\"", "'", $deal['short_description']) . '" src="' . $dealImages[0] . '" width="300px" />';
							}
							else
							{
								$image = '';
							}

							$link = JURI::root() . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], false);
							$name = $deal['name'];
							$item['title'] = $name;
							$item['description'] = $image . $deal['description'];
							$item['link'] = $link;
							$item['pubDate'] = '';
							$items[] = $item;
						}
					}

					$title = JFactory::getApplication()->getCfg('sitename');
					$pubDate = CMGroupBuyingHelperDateTime::getCurrentDateTime();
					$this->generateRSS($title, $description, $pubDate, $items);
					jexit();
					break;

// ------------------------- All active deals -------------------------  // 

				case 'active':
					$items = array();
					$item = array();

					if($locationId > 0)
					{
						$location = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getLocationById($locationId);
						
						isset($location['name']) ? $locationName = $location['name'] : $locationName = '';
						$description = JText::sprintf('COM_CMGROUPBUYING_RSS_ACTIVE_DEALS_LOCATION_DESCRIPTION', $locationName);
						$locationString = CMGroupBuyingHelperDeal::getDealsInLocation($locationId);
					}
					else
					{
						$description = JText::_('COM_CMGROUPBUYING_RSS_ACTIVE_DEAL_DESCRIPTION');
						$locationString = null;
					}

					$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, null, null, $locationString);

					if(empty($deals))
					{
						$item['title'] = JText::_('COM_CMGROUPBUYING_RSS_ACTIVE_DEALS_NOT_FOUND');
						$item['description'] = '';
						$item['link'] = '';
						$item['pubDate'] = '';
						$items[] = $item;
					}
					else
					{
						foreach($deals as $deal)
						{
							$dealImages = array();

							for($j = 1; $j <= 5; $j++)
							{
								$columnName = 'image_path_' . $j;

								if($deal[$columnName] != '')
								{
									$dealImages[] = JURI::root() . $deal[$columnName];
								}
							}

							if(!empty($dealImages))
							{
								$image = '<img align="right" alt="' . str_replace("\"", "'", $deal['short_description']) . '" src="' . $dealImages[0] . '" width="300px" />';
							}
							else
							{
								$image = '';
							}

							$link = JURI::root() . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'], false);
							$name = $deal['name'];
							$item['title'] = $name;
							$item['description'] = $image . $deal['description'];
							$item['link'] = $link;
							$item['pubDate'] = '';
							$items[] = $item;
						}
					}

					$title = JFactory::getApplication()->getCfg('sitename');
					$pubDate = CMGroupBuyingHelperDateTime::getCurrentDateTime();
					$this->generateRSS($title, $description, $pubDate, $items);
					jexit(); 
					break;
			}
		}

		$pageTitle = JText::_('COM_CMGROUPBUYING_RSS_FEEDS_PAGE_TITLE');
		$this->assignRef('pageTitle', $pageTitle);
		$locations = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getPublishedLocations();
		$this->assignRef('locations', $locations);
		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/' . "components" . '/' . "com_cmgroupbuying" . '/' ."layouts" . '/' . $layout . '/');
		$this->_layout = "rssfeeds";
		parent::display($tpl);
	}

	function generateRSS($title, $description, $pubDate, $items)
	{
		$rss = '<?xml version="1.0" encoding="UTF-8"?>
				<rss version="2.0">
					<channel>
						<title><![CDATA[' . $title . ']]></title>
						<description><![CDATA[' . $description . ']]></description>
						<pubDate><![CDATA[' . $pubDate . ']]></pubDate>';

		foreach($items as $item)
		{
			$rss .= '<item>
						<title><![CDATA[' . $item['title'] . ']]></title>
						<description><![CDATA[' . $item['description'] . ']]></description>
						<link><![CDATA[' . $item['link'] . ']]></link>
						<pubDate><![CDATA[' . $item['pubDate'] . ']]></pubDate>
					</item>';
		}

		$rss .= '</channel>
			</rss>';
		echo $rss;
	}
}