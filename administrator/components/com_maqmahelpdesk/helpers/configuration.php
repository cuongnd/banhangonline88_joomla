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

class HelpdeskConfigurationAdminHelper
{
	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function addToolbar()
	{
		JToolBarHelper::title(JText::_('GLOBAL_CONFIG') . '<br /><div id="loading" name="loading" style="display:none;"><img src="../components/com_maqmahelpdesk/images/progressbar.gif" border="0" alt="' . JText::_('LOADING') . '" /></div>', 'sc-configuration');
		JToolBarHelper::custom('show_link', 'new.png', 'new_f2.png', JText::_('NEWLINK'), false);
		JToolBarHelper::divider();
		JToolBarHelper::apply('config_apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('config_save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('cpanel', 'JTOOLBAR_CANCEL');
	}

	/**
	 * When MVC conversion is done this will be in view.html.php
	 *
	 * @return void
	 */
	static public function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('GLOBAL_CONFIG'));
	}
}