<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport('fsj_core.html.field.fsjstring');

class JFormFieldFSJTime extends JFormFieldFSJString
{
	protected $type = 'FSJTime';

	protected function getInput()
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		if ((int) $this->value && $this->value != JFactory::getDbo()->getNullDate())
		{
			$date = JFactory::getDate($this->value, 'UTC');
			$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
			$this->value = $date->format('H:i', true, false);
		}

		return parent::getInput();
	}
	
	function doSettingSave($field, &$data)
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		$value = $data[$field];

		if ((int) $value && $value != JFactory::getDbo()->getNullDate())
		{
			$date = JFactory::getDate($value, new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
			$date->setTimezone(new DateTimeZone('UTC'));
			$value = $date->format('H:i', true, false);
		}

		$data[$field] = $value;
	}
}
