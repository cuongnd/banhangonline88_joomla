<?php
// namespace administrator\components\com_jchat\controllers;
/**
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );

/**
 * Users concrete implementation
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage controllers
 * @since 1.6
 */
class JChatControllerUsers extends JChatController { 
	/**
	 * Setta il model state a partire dallo userstate di sessione
	 * @access protected
	 * @param string $scope
	 * @param boolean $ordering
	 * @return void
	 */
	protected function setModelState($scope = 'default') {
		$option = $this->option;
		
		$ban_status = $this->getUserStateFromRequest( "$option.users.banstatus", 'banstatus', '');
		$filter_order = $this->getUserStateFromRequest("$option.users.filter_order", 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $this->getUserStateFromRequest("$option.users.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		
		$defaultModel = parent::setModelState('users');
		
		// Set model state  
		$defaultModel->setState('banstatus', $ban_status);
		$defaultModel->setState('order', $filter_order);
		$defaultModel->setState('order_dir', $filter_order_Dir);
		
		return $defaultModel;
	}
	
	/**
	 * Default listEntities
	 * 
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Set model state 
		$this->setModelState();
		
		// Parent construction and view display
		parent::display();
	}

	/**
	 * Delete a db table entity
	 *
	 * @access public
	 * @return void
	 */
	public function banEntity() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );
		$option = $this->option;
		
		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		$result = $model->banEntity ($cids, $this->task);
		
		if (! $result) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=$option&task=users.display", JText::_ ( 'COM_JCHAT_ERROR_BANSTATUS' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=$option&task=users.display", JText::_ ( 'COM_JCHAT_SUCCESS_BANSTATUS' ) );
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
		
		$this->registerTask('unbanEntity', 'banEntity');
	}
}
