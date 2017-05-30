<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.database');
JFormHelper::loadFieldClass('list');

if (!FSJ_Helper::IsJ3())
{
	//require_once(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'joomla25'.DS.'tag.php');
}

class JFormFieldFSJMultiEntry extends JFormFieldList
{
	protected function getInput()
	{
		$id    = isset($this->element['id']) ? $this->element['id'] : null;
		$cssId = '#' . $this->getId($id, $this->element['name']);
		
		if (!FSJ_Helper::IsJ3()) JHtml::addIncludePath(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'html'.DS.'joomla25');
		JHtml::addIncludePath(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'html');
		
		JHtml::_('multientry.ajaxfield', $cssId, $this->element['create']);

		return parent::getInput();
	}
	
	protected function getOptions()
	{
		$options = array();
		
		if (is_array($this->value))
		{
			foreach ($this->value as $value)
			{
				$options[] = JHTML::_('select.option', $value, $value, 'text', 'value');	
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		
		if (isset($this->element->fsjmultientry->sql))
		{
			$db = JFactory::getDBO();
			$db->setQuery($this->element->fsjmultientry->sql);
			
			$options = parent::getOptions();
			
			$options = array_merge($options, $db->loadObjectList());
		}

		return $options;
	}
}