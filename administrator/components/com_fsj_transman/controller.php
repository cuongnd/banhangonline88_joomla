<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class fsj_transmanController extends JControllerLegacy
{
	protected $default_view = 'fsj_transman';
	public function display($cachable = false, $urlparams = false)
	{
		$view		= JRequest::getCmd('view', 'fsj_transman');
		$layout 	= JRequest::getCmd('layout', 'fsj_transman');
		$id			= JRequest::getInt('id');
		// setup current admin component so we can maintain toolbars etc when in settings pages
		if ($view == 'fsj_transman')
		{
			$mainframe = JFactory::getApplication();
			$mainframe->setUserState( "com_fsj_main.admin_com", "transman" );
		}
		// Check for edit form.
		if ($view == '' && $layout == 'edit' && !$this->checkEditId('fsj_transman.edit.article', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=fsj_transman&view=', false));
			return false;
		}
		parent::display();
		return $this;
	}
}
