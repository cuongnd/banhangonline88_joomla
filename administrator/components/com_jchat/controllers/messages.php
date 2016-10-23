<?php
// namespace administrator\components\com_jchat\controllers;
/**
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * User messages concrete implementation
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerMessages extends JChatController { 
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
		$msgType = $this->getUserStateFromRequest( "$option.$scope.msg_type", 'msg_type');
		$msgStatus = $this->getUserStateFromRequest( "$option.$scope.msg_status", 'msg_status');
		$roomsFilter = $this->getUserStateFromRequest( "$option.$scope.rooms_filter", 'rooms_filter');
		$filter_order = $this->getUserStateFromRequest("$option.$scope.filter_order", 'filter_order', 'a.sent', 'cmd');
		$filter_order_Dir = $this->getUserStateFromRequest("$option.$scope.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		
		$defaultModel = parent::setModelState('messages');
		
		// Set model state  
		$defaultModel->setState('fromPeriod', $fromPeriod);
		$defaultModel->setState('toPeriod', $toPeriod);
		$defaultModel->setState('msgType', $msgType);
		$defaultModel->setState('msgStatus', $msgStatus);
		$defaultModel->setState('roomsFilter', $roomsFilter);
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
		$defaultModel = $this->setModelState('messages');
		
		// Parent construction and view display
		parent::display($cachable);
	}

	/**
	 * Mostra il dettaglio dell'entity
	 * 
	 * @access public
	 * @return void
	 */
	public function showEntity() {
		$cid = $this->app->input->get ( 'cid', array (
				0 
		), 'array' );
		$idEntity = (int) $cid[0];
		$model = $this->getModel();
		$model->setState('option', $this->option);
		
		// Try to load record from model
		if(!$record = $model->loadEntity($idEntity)) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelExceptions = $model->getErrors();
			foreach ($modelExceptions as $exception) {
				$this->app->enqueueMessage($exception->getMessage(), $exception->getErrorLevel());
			}
			$this->setRedirect ( 'index.php?option=' . $this->option . '&task=messages.display');
			return false;
		}
 
		$view = $this->getView();
		$view->setModel ( $model, true );
		
		$view->showEntity($record); 
	}
 
	/**
	 * Delete a db table entity
	 *
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );
		$option = $this->option;
		
		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		$oldest = $this->task == 'deleteOldestEntities' ? true : false;
		if ($this->task == 'deleteEntity') {
			$result = $model->deleteEntity ( $cids );
		} elseif (in_array ( $this->task, array (
				'deleteEntities',
				'deleteOldestEntities' 
		) )) {
			$result = $model->deleteEntities ( $oldest );
		}
		
		if (! $result) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=$option&task=messages.display", JText::_ ( 'COM_JCHAT_ERROR_DELETE' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=$option&task=messages.display", JText::_ ( 'COM_JCHAT_SUCCESS_DELETE' ) );
	}
	
	/**
	 * Avvia il processo di esportazione records
	 *
	 * @access public
	 * @return void
	 */
	public function exportMessages() { 
		// Set model state 
		$this->setModelState();
		// Mapping fields to load to column header
		$fieldsToLoadArray = array(	'a.actualfrom AS sender_name'=>JText::_('COM_JCHAT_SENDER_NAME'),
									'a.actualto AS receiver_name'=>JText::_('COM_JCHAT_RECEIVER_NAME'),
									'a.message'=>JText::_('COM_JCHAT_MESSAGE'),
									'a.sent'=>JText::_('COM_JCHAT_SENT'),
									'a.read'=>JText::_('COM_JCHAT_READ'),
									'a.type'=>JText::_('COM_JCHAT_TYPE'),
									'a.ipaddress'=>JText::_('COM_JCHAT_IPADDRESS')); 
		$fieldsFunctionTransformation = array();
		
		$model = $this->getModel();
		$model->setState('cparams', JComponentHelper::getParams('com_jchat'));
		
		$data = $model->exportMessages($fieldsToLoadArray, $fieldsFunctionTransformation);
		
		if(!$data) {
			$this->setRedirect('index.php?option=' . $this->option . '&task=messages.display', JText::_('COM_JCHAT_NODATA_EXPORT'));
			return false;
		}
		
		// Get view
		$view = $this->getView();
		$view->setModel($model, true);
		$view->sendCSVMessages($data, $fieldsFunctionTransformation);
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
	 * @since 1.5
	 */
	function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('deleteEntities', 'deleteEntity');
		$this->registerTask('deleteOldestEntities', 'deleteEntity');
	}
}