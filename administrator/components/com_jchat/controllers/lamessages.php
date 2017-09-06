<?php
// namespace administrator\components\com_jchat\controllers;
/**
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Main controller leaved messages
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerLamessages extends JChatController { 
	/**
	 * Setta il model state a partire dallo userstate di sessione
	 * @access protected
	 * @return void
	 */
	protected function setModelState($scope = 'default') {
		// User state specific
		$option= $this->option;
		
		// Get default model
		$defaultModel = $this->getModel();
		
		$filter_order = $this->getUserStateFromRequest( "$option.$scope.filter_order", 'filter_order', 'a.sentdate', 'cmd' );
		$filter_order_Dir = $this->getUserStateFromRequest ( "$option.$scope.filter_order_Dir", 'filter_order_Dir', 'desc', 'word' );
		$fromPeriod = $this->getUserStateFromRequest( "$option.$scope.fromperiod", 'fromperiod');
		$toPeriod = $this->getUserStateFromRequest( "$option.$scope.toperiod", 'toperiod');
		$worked = $this->getUserStateFromRequest( "$option.$scope.workedfilter", 'workedfilter');
		$closed = $this->getUserStateFromRequest( "$option.$scope.closedfilter", 'closedfilter');
		parent::setModelState($scope);
		
		// Set model state  
		$defaultModel->setState('order', $filter_order);
		$defaultModel->setState('order_dir', $filter_order_Dir );
		$defaultModel->setState('fromPeriod', $fromPeriod);
		$defaultModel->setState('toPeriod', $toPeriod);
		$defaultModel->setState('workedfilter', $worked);
		$defaultModel->setState('closedfilter', $closed);
		
		return $defaultModel;
	}
	
	/**
	 * Default listEntities
	 * 
	 * @access public
	 * @param $cachable string
	 *       	 the view output will be cached
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Set model state
		$defaultModel = $this->setModelState('lamessages');
		 
		// Parent construction and view display
		parent::display($cachable);
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
		$fieldsToLoadArray = array(	'a.name'=>JText::_('COM_JCHAT_LAMESSAGE_NAME'),
									'a.email'=>JText::_('COM_JCHAT_LAMESSAGE_EMAIL'),
									'a.message'=>JText::_('COM_JCHAT_MESSAGE'),  
									'a.sentdate'=>JText::_('COM_JCHAT_SENT'),
									'a.worked'=>JText::_('COM_JCHAT_WORKED_STATE'),
									'a.closed_ticket'=>JText::_('COM_JCHAT_CLOSED_TICKET'),
									'u.name AS username_logged'=>JText::_('COM_JCHAT_USERID'),
									'a.id AS msg_id'=>JText::_('ID'));
		$fieldsFunctionTransformation = array();
	
		$model = $this->getModel();
		$data = $model->exportMessages($fieldsToLoadArray, $fieldsFunctionTransformation);
	
		if(!$data) {
			$this->setRedirect('index.php?option=" . $this->option . "&task=lamessages.display', JText::_('NODATA_EXPORT'));
			return false;
		}
	
		// Get view
		$view = $this->getView();
		$view->sendCSVMessages($data, $fieldsFunctionTransformation);
	}
	
	/**
	 * Manage answered worked state for the ticket
	 * 
	 * @access public
	 */
	public function stateFlags() {
		// Access check
		if (! $this->allowEditState ( $this->option )) {
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_ALERT_NOACCESS' ), 'notice' );
			return false;
		}
		
		$cid = $this->app->input->get ( 'cid', array (
				0
		), 'array' );
		$idEntity = ( int ) $cid [0];
		
		$model = $this->getModel ();
		
		if (! $model->changeTicketState($idEntity, $this->task)) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_STATE_CHANGE' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_LAMESSAGE_STATE_CHANGED' ) );
	}
	
	/**
	 * Risponde all'email del richiedente con il messaggio inserito dall'agente
	 * richiedendo la serializzazione delle risposte nell'apposito db field
	 *
	 * @access public
	 * @return void
	 */
	public function responseMessage() {
		$task = $this->app->input->get('task', 'responseMessage');
		$responseSubject = $this->app->input->getString('email_subject');
		$responseText = $this->app->input->getRaw('response', '');
		$idEntity = $this->app->input->getInt('id');
		
		// Response text vuota validazione lato server con return false
		if(!trim($responseSubject)) {
			$controllerTask = 'editEntity&cid[]=' . $idEntity; 
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=lamessages.$controllerTask", JText::_('COM_JCHAT_VALIDATION_ERROR'));
			return false;
		}
		 
		$model = $this->getModel();
		
		// Root controller -> dependency injection
		$mailer = JChatHelpersMailer::getInstance('Joomla');
		if (! $model->sendResponseStore($mailer, $idEntity, $responseSubject, $responseText)) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
				
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".editEntity&cid[]=" . $this->app->input->get ( 'id' ), JText::_ ( 'COM_JCHAT_ERROR_SEND_MESSAGE' ) );
			return false;
		}
		
		$controllerTask = 'editEntity&cid[]=' . $idEntity; 
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=lamessages.$controllerTask", JTEXT::_('COM_JCHAT_SUCCESS_SEND_MESSAGE'));
	}
	
	/**
	 * Class constructor
	 * @return Object&
	 */
	public function __construct($config = array()){
		parent::__construct($config);
	
		// Registering alias task
		$this->registerTask('applyEntity', 'saveEntity');
		$this->registerTask('workedFlagOff', 'stateFlags');
		$this->registerTask('workedFlagOn', 'stateFlags');
		$this->registerTask('closedFlagOff', 'stateFlags');
		$this->registerTask('closedFlagOn', 'stateFlags');
	}
}