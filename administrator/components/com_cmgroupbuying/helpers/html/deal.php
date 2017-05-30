<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

abstract class JHtmlDeal
{
	static function featured($value = 0, $i, $canChange = true)
	{
		$states = array(
			0 => array('disabled.png', 'deals.featured', 'COM_CMGROUPBUYING_DEAL_UNFEATURED', 'COM_CMGROUPBUYING_DEAL_TOGGLE_TO_FEATURE'),
			1 => array('featured.png', 'deals.unfeatured', 'COM_CMGROUPBUYING_DEAL_FEATURED', 'COM_CMGROUPBUYING_DEAL_TOGGLE_TO_UNFEATURE'),
		);
		$state = JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html = JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);

		if($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">' . $html .'</a>';
		}

		return $html;
	}
}
