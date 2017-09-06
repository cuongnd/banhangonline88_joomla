<?php
// namespace administrator\components\com_jchat\controllers;
/**
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * User messages concrete implementation
 *
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage controllers
 * @since 2.9
 */
class JChatControllerRecorder extends JChatController { 
	/**
	 * Setta il model state a partire dallo userstate di sessione
	 * @access protected
	 * @param string $scope
	 * @param boolean $ordering
	 * @return void
	 */
	protected function setModelState($scope = 'default') {
		$option = $this->option;
		
		$fromPeriod = $this->getUserStateFromRequest( "$option.$scope.fromperiod", 'fromperiod');
		$toPeriod = $this->getUserStateFromRequest( "$option.$scope.toperiod", 'toperiod');
		$filter_order = $this->getUserStateFromRequest("$option.$scope.filter_order", 'filter_order', 'a.timerecord', 'cmd');
		$filter_order_Dir = $this->getUserStateFromRequest("$option.$scope.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		
		$defaultModel = parent::setModelState($scope);
		
		// Set model state  
		$defaultModel->setState('fromPeriod', $fromPeriod);
		$defaultModel->setState('toPeriod', $toPeriod);
		$defaultModel->setState('order', $filter_order);
		$defaultModel->setState('order_dir', $filter_order_Dir);
	}
	
	/**
	 * Default listEntities
	 * 
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Set model state 
		$defaultModel = $this->setModelState('recorder');
		
		// Parent construction and view display
		parent::display($cachable);
	}
	
	/**
	 * Download video medias
	 *
	 * @access public
	 * @return void
	 */
	public function downloadEntity() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );

		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		if (! $model->downloadEntity ( $cids )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_DOWNLOADING' ) );
			return false;
		}
	}
	
	/**
	 * Constructor.
	 *
	 * @access protected
	 * @param
	 *       	 array An optional associative array of configuration settings.
	 *       	 Recognized key values include 'name', 'default_task',
	 *       	 'model_path', and
	 *       	 'view_path' (this list is not meant to be comprehensive).
	 */
	function __construct($config = array()) {
		parent::__construct($config);
	}
}