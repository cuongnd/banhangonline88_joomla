<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCmgbvariable extends JFormField
{
	protected $type = 'Cmgbvariable';

	protected function getInput()
	{
		$html = array();
		$html[] = '<ul>';
		$html[] .= '<li>{deal_image}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_IMAGE') . '</li>';
		$html[] .= '<li>{deal_name}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_NAME') . '</li>';
		$html[] .= '<li>{deal_short_description}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_SHORT_DESC') . '</li>';
		$html[] .= '<li>{deal_price}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_PRICE') . '</li>';
		$html[] .= '<li>{deal_original_price}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_ORIGINAL_PRICE') . '</li>';
		$html[] .= '<li>{deal_discount}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_DISCOUNT') . '</li>';
		$html[] .= '<li>{deal_save}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_SAVE') . '</li>';
		$html[] .= '<li>{deal_description}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_DESCRIPTION') . '</li>';
		$html[] .= '<li>{deal_highlights}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_HIGHLIGHTS') . '</li>';
		$html[] .= '<li>{deal_terms}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_DEAL_TERMS') . '</li>';
		$html[] .= '<li>{partner_name}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_NAME') . '</li>';
		$html[] .= '<li>{partner_logo}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_LOGO') . '</li>';
		$html[] .= '<li>{partner_about}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_ABOUT') . '</li>';
		$html[] .= '<li>{partner_website}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_WEBSITE') . '</li>';
		$html[] .= '<li>{partner_address}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_ADDRESS') . '</li>';
		$html[] .= '<li>{partner_phone}: ' . JText::_('COM_CMGROUPBUYING_VARIABLE_PARTNER_PHONE') . '</li>';
		$html[] .= '<ul>';
		return implode($html);
	}
}