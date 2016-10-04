<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
 
/**
 * Script file of CMGroupBuying component
 */
class com_cmgroupbuyingInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_cmgroupbuying');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CMGROUPBUYING_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CMGROUPBUYING_UPDATE_TEXT') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// echo '<p>' . JText::_('COM_CMGROUPBUYING_PREFLIGHT_' . $type . '_TEXT') . '</p>';

		// Big change in database in 2.4.0.
		if($type == 'update')
		{
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('*')
				->from($db->quoteName('#__extensions'))
				->where($db->quoteName('name') . ' = ' . $db->quote('com_cmgroupbuying'));
			$db->setQuery($query);

			$extension 	= $db->loadObject();
			$manifest 	= json_decode($extension->manifest_cache, true);
			$version 	= $manifest['version'];

			if (version_compare($version, '2.4.0', 'lt'))
			{
				$query->clear()
					->select('*')
					->from($db->quoteName('#__cmgroupbuying_deal_option'));
				$db->setQuery($query);
				$options = $db->loadAssocList();

				// Drop table
				$q = 'DROP TABLE #__cmgroupbuying_deal_option';
				$db->setQuery($q);
				$db->execute();

				// Create table
				$q = 'CREATE TABLE IF NOT EXISTS `#__cmgroupbuying_deal_option` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`deal_id` int(11) NOT NULL,
					`option_id` int(11) NOT NULL,
					`name` varchar(255) NOT NULL,
					`original_price` decimal(10,2) NOT NULL,
					`price` decimal(10,2) NOT NULL,
					`advance_price` decimal(10,2) NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
				$db->setQuery($q);
				$db->execute();

				// Import options
				foreach($options as $option)
				{
					$query->clear()
						->insert($db->quoteName('#__cmgroupbuying_deal_option'))
						->columns('deal_id, option_id, name, original_price, price, advance_price')
						->values($db->quote($option['deal_id']) . ', ' .
							$db->quote($option['option_id']) . ', ' .
							$db->quote($option['name']) . ', ' .
							$db->quote($option['original_price']) . ', ' .
							$db->quote($option['price']) . ', ' .
							$db->quote($option['advance_price']));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// echo '<p>' . JText::_('COM_CMGROUPBUYING_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
