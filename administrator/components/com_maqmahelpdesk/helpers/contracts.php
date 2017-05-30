<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskContractsAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function addToolbar($task, $function=null)
	{
		switch ($task)
		{
			case "config":
				if ($function == 'contracts')
				{
					JToolBarHelper::title(JText::_('contract_notification'), 'sc-contracts');
					JToolBarHelper::save('addon-contracts_saveconfig');
					JToolBarHelper::cancel('');
				}
				else
				{
					JToolBarHelper::title(JText::_('sla_escalation_config'), 'sc-contracts');
					JToolBarHelper::save('addon-escalation_saveconfig');
					JToolBarHelper::cancel('cpanel');
				}
				break;

			case "saveconfig":
				if ($function == 'escalation')
				{
					JToolBarHelper::title(JText::_('sla_escalation_config'), 'sc-contracts');
					JToolBarHelper::save('addon-escalation_saveconfig');
					JToolBarHelper::cancel('cpanel');
				}
				break;

			case "fields":
				JToolBarHelper::title(JText::_('contract_custom_field'), 'sc-contracts');
				JToolBarHelper::addNew('contracts_fields_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('contracts_fields_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'contracts_fields_remove', 'JTOOLBAR_DELETE');
				break;

			case "fields_new":
				JToolBarHelper::title(JText::_('NEW'), 'sc-contracts');
				JToolBarHelper::save('contracts_fields_save');
				JToolBarHelper::cancel('contracts_fields');
				break;

			case "fields_edit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-contracts');
				JToolBarHelper::save('contracts_fields_save');
				JToolBarHelper::cancel('contracts_fields');
				break;

			case "new":
			case "edit":
				JToolBarHelper::title(JText::_('EDIT'), 'sc-contracts');
				JToolBarHelper::save('contracts_save');
				JToolBarHelper::cancel('contracts');
				break;

			default:
				JToolBarHelper::title(JText::_('contract_types_manager'), 'sc-contracts');
				JToolBarHelper::addNew('contracts_new', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('contracts_edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::deleteList('', 'contracts_remove', 'JTOOLBAR_DELETE');
				break;
		}
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument($task, $function=null)
	{
		$document = JFactory::getDocument();
		switch ($task)
		{
			case "config":
				if ($function == 'contracts')
				{
					$document->setTitle(JText::_('contract_notification'));
				}
				else
				{
					$document->setTitle(JText::_('sla_escalation_config'));
				}
				break;

			case "fields":
				$document->setTitle(JText::_('contract_custom_field'));
				break;

			case "fields_new":
				$document->setTitle(JText::_('NEW'));
				break;

			case "fields_edit":
				$document->setTitle(JText::_('EDIT'));
				break;

			case "new":
			case "edit":
				$document->setTitle(JText::_('EDIT'));
				break;

			default:
				$document->setTitle(JText::_('contract_types_manager'));
				break;
		}
	}
}