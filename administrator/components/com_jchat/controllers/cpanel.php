<?php
// namespace administrator\components\com_jchat\controllers;
/**
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );

/**
 * CPanel controller
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerCpanel extends JChatController {
	/**
	 * Show Control Panel
	 * @access public
	 * @return void
	 */
	function display($cachable = false, $urlparams = false) {
		$view = $this->getView();
		
		// Dependency injection setter on view/model
		$HTTPClient = new JChatHttp();
		$view->set('httpclient', $HTTPClient);
		
		// No operations
		parent::display ($cachable); 
	}
	
	/**
	 * Purge file and db cache
	 * @access public
	 * @return void
	 */
	public function purgeCaches() {
		$option = $this->option;
		//Load model
		$model = $this->getModel ();
		$result = $model->{$this->task}();
		
		if(!$result) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError(null, false);
			$this->app->enqueueMessage($modelException->getMessage(), $modelException->getErrorLevel());
			$this->setRedirect ( "index.php?option=$option&task=cpanel.display", JText::_('COM_JCHAT_ERROR_DELETE_CACHE'));
			return false;
		}
		
		$this->setRedirect ( "index.php?option=$option&task=cpanel.display", JText::_('COM_JCHAT_SUCCESS_DELETE_CACHE') );
	}
	
	/**
	 * Class Constructor
	 *
	 * @access public
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );
		// Register Extra tasks
		$this->registerTask ( 'purgeFileCache', 'purgeCaches' );
		$this->registerTask ( 'purgeDbCache', 'purgeCaches' );
	}
}
?>