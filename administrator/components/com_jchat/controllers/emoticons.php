<?php
// namespace administrator\components\com_jchat\controllers;
/**
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Main emoticons controller manager
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage controllers
 * @since 3.2
 */
class JChatControllerEmoticons extends JChatController {
	/**
	 * Set model state from session userstate
	 * 
	 * @access protected
	 * @param string $scope        	
	 * @return void
	 */
	protected function setModelState($scope = 'default', $ordering = true) {
		$option = $this->option;
		
		$filter_order = $this->getUserStateFromRequest("$option.$scope.filter_order", 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $this->getUserStateFromRequest("$option.$scope.filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		$filter_state = $this->getUserStateFromRequest ( "$option.$scope.filterstate", 'filter_state', null );
		
		$defaultModel = parent::setModelState($scope);
		
		// Set model state  
		$defaultModel->setState('order', $filter_order);
		$defaultModel->setState('order_dir', $filter_order_Dir);
		$defaultModel->setState('state', $filter_state );
		
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
	function display($cachable = false, $urlparams = false) {
		// Set model state
		$defaultModel = $this->setModelState('emoticons');
		 
		// Parent construction and view display
		parent::display($cachable, $urlparams = false);
	}
	
	/**
	 * AS SMVC entity here we treat HTTP request and identifier map
	 * @access public
	 * @return void
	 */
	function storeEmoticon() {
		// Id entità risposta ajax che identifica il subtask da eseguire in questo caso
		$params = json_decode($this->app->input->getString('data', null));
		$userData = new stdClass();
		
		// This model maps Remote Procedure Call
		$model = $this->getModel ();
		if(method_exists($model, $params->idtask)) {
			$filter = JFilterInput::getInstance();
			if(isset($params->param->linkurl)) {
				$params->param->linkurl = $filter->clean($params->param->linkurl, 'path');
			}
			if(isset($params->param->keycode)) {
				$params->param->keycode = $filter->clean(strip_tags($params->param->keycode), 'username');
				$params->param->keycode = JString::str_ireplace(array('/'), '', $params->param->keycode);
			}
			$userData = $model->{$params->idtask} ($params->param);
		}
	
		// Format response for JS client as requested
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
	
		$view =  $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		$view->display ($userData);
	}

	/**
	 * Class Constructor
	 * 
	 * @access public
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );
		$this->registerTask ( 'moveorder_up', 'moveOrder' );
		$this->registerTask ( 'moveorder_down', 'moveOrder' );
	}
}