<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTableConfiguration extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_configuration', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function check()
	{
		$app = JFactory::getApplication();

		// Check for valid slideshow switch time
		if ($this->slideshow_switch_time <= 0)
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_SLIDESHOW_SWITCH_TIME'), 'error');
			return false;
		}

		// Check for valid slideshow fade time
		if ($this->slideshow_fade_time <= 0)
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_SLIDESHOW_FADE_TIME'), 'error');
			return false;
		}

		// Check for valid slideshow fade time and switch time
		if ($this->slideshow_switch_time <= $this->slideshow_fade_time)
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_SLIDESHOW_SWITCH_FADE_TIME'), 'error');
			return false;
		}

		if($this->facebook_comment == 1)
		{
			// Check for valid Facebook posts
			if ($this->facebook_comment_num_posts <= 0)
			{
				$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_FACEBOOK_NUM_POSTS'), 'error');
				return false;
			}

			// Check for valid Facebook width
			if ($this->facebook_comment_width <= 0)
			{
				$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_FACEBOOK_WIDTH'), 'error');
				return false;
			}
		}

		// Check valid partner folder name
		if(strpbrk($this->partner_folder, "\\/?%*:|\"<>") == TRUE)
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_CONFIGURATION_WARNING_PROVIDE_VALID_PARTNER_FOLDER_NAME'), 'error');
			return false;
		}

		return true;
	}
}