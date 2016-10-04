<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelper
{
	public static function addSubmenu($vName)
	{
		if(version_compare(JVERSION, '3.0.0', 'ge')):
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_CONFIGURATION'),
				'index.php?option=com_cmgroupbuying&view=configuration',
				$vName == 'configuration'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_USER_PROFILE'),
				'index.php?option=com_cmgroupbuying&view=profile',
				$vName == 'profile'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_MANAGEMENT_PERMISSIONS'),
				'index.php?option=com_cmgroupbuying&view=management',
				$vName == 'management'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_CATEGORIES'),
				'index.php?option=com_cmgroupbuying&view=categories',
				$vName == 'categories'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_LOCATIONS'),
				'index.php?option=com_cmgroupbuying&view=locations',
				$vName == 'locations'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_PARTNERS'),
				'index.php?option=com_cmgroupbuying&view=partners',
				$vName == 'partners'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_PRODUCTS'),
				'index.php?option=com_cmgroupbuying&view=products',
				$vName == 'products'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_DEALS'),
				'index.php?option=com_cmgroupbuying&view=deals',
				$vName == 'deals'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_FREE_COUPONS'),
				'index.php?option=com_cmgroupbuying&view=freecoupons',
				$vName == 'freecoupons'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_ORDERS'),
				'index.php?option=com_cmgroupbuying&view=orders',
				$vName == 'orders'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_COUPONS'),
				'index.php?option=com_cmgroupbuying&view=coupons',
				$vName == 'coupons'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_MAIL_TEMPLATES'),
				'index.php?option=com_cmgroupbuying&view=mailtemplates',
				$vName == 'mailtemplates'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATOR_SITES'),
				'index.php?option=com_cmgroupbuying&view=aggregatorsites',
				$vName == 'aggregatorsites'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'),
				'index.php?option=com_cmgroupbuying&view=aggregationlinks',
				$vName == 'aggregationlinks'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_REPORTS'),
				'index.php?option=com_cmgroupbuying&view=reports',
				$vName == 'reports'
			);
		else:
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_CONFIGURATION'),
				'index.php?option=com_cmgroupbuying&view=configuration',
				$vName == 'configuration'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_USER_PROFILE'),
				'index.php?option=com_cmgroupbuying&view=profile',
				$vName == 'profile'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_MANAGEMENT_PERMISSIONS'),
				'index.php?option=com_cmgroupbuying&view=management',
				$vName == 'management'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_CATEGORIES'),
				'index.php?option=com_cmgroupbuying&view=categories',
				$vName == 'categories'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_LOCATIONS'),
				'index.php?option=com_cmgroupbuying&view=locations',
				$vName == 'locations'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_PARTNERS'),
				'index.php?option=com_cmgroupbuying&view=partners',
				$vName == 'partners'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_PRODUCTS'),
				'index.php?option=com_cmgroupbuying&view=products',
				$vName == 'products'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_DEALS'),
				'index.php?option=com_cmgroupbuying&view=deals',
				$vName == 'deals'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_FREE_COUPONS'),
				'index.php?option=com_cmgroupbuying&view=freecoupons',
				$vName == 'freecoupons'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_ORDERS'),
				'index.php?option=com_cmgroupbuying&view=orders',
				$vName == 'orders'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_COUPONS'),
				'index.php?option=com_cmgroupbuying&view=coupons',
				$vName == 'coupons'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_MAIL_TEMPLATES'),
				'index.php?option=com_cmgroupbuying&view=mailtemplates',
				$vName == 'mailtemplates'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATOR_SITES'),
				'index.php?option=com_cmgroupbuying&view=aggregatorsites',
				$vName == 'aggregatorsites'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'),
				'index.php?option=com_cmgroupbuying&view=aggregationlinks',
				$vName == 'aggregationlinks'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_CMGROUPBUYING_DASHBOARD_REPORTS'),
				'index.php?option=com_cmgroupbuying&view=reports',
				$vName == 'reports'
			);
		endif;
	}

	public static function getTemplateNames()
	{
		$db = JFactory::getDbo();
		$query = "SELECT name FROM #__extensions WHERE type = 'template' AND client_id = 0";
		$db->setQuery($query);
		$result = $db->loadAssocList();
		return $result;
	}
}
?>
