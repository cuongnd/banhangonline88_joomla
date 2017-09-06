<?php
// namespace administrator\components\com_jchat\controllers;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );

/**
 * Config controller concrete implementation
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerConfig extends JChatController {

	/**
	 * Show configuration
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		parent::display($cachable);
	}

	/**
	 * Save config entity
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		$model = $this->getModel();
		$option = $this->option;
		
		if(!$model->storeEntity()) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError(null, false);
			$this->app->enqueueMessage($modelException->getMessage(), $modelException->getErrorLevel());
			$this->setRedirect ( "index.php?option=$option&task=config.display", JText::_('COM_JCHAT_ERROR_SAVING_PARAMS'));
			return false;
		}
		$this->setRedirect( "index.php?option=$option&task=config.display", JText::_('COM_JCHAT_SAVED_PARAMS'));
	}
}