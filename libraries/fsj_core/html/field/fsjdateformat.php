<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJDateFormat extends JFormFieldText
{
	protected $type = 'FSJGtring';
	
	function __construct()
	{
		parent::__construct();
	}

	protected function getLabel()
	{
		$j = $this->fieldname;

		switch ($this->fieldname)
		{
			case 'date_dt_short':
				$j = JText::_('DATE_FORMAT_LC4') . ', H:i';
				break;
			case 'date_dt_long':
				$j = JText::_('DATE_FORMAT_LC3') . ', H:i';
				break;
			case 'date_d_short':
				$j = JText::_('DATE_FORMAT_LC4');
				break;
			case 'date_d_long':
				$j = JText::_('DATE_FORMAT_LC3');
				break;
			case 'date_t_short':
				$j = 'H:i';
				break;
			case 'date_t_long':
				$j = 'H:i';
				break;
		}
		
		return parent::getLabel() . "<div class='small'>Joomla: $j</div>";
	}

	protected function getInput()
	{
		return parent::getInput() . "<div id='" . $this->fieldname . "_result' style='display:none;'>xxxx</div>";
	}	
	
	function AdminDisplay($value, $name, $item)
	{
		return $value;	
	}
}
