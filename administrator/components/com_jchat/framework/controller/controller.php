<?php
// namespace components\com_jchat\libraries\framework\controller;
/**
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage controller
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );

/**
 * Base controller class
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage controller
 * @since 1.0
 */
class JChatController extends JControllerLegacy {
	/**
	 * Core name from controller dispatch execute
	 *
	 * @access protected
	 * @var string
	 */
	protected $corename;
	
	/** 
	 * Dispatch option
	 * 
	 * @access protected
	 * @var string
	 */
	protected $option;
	
	/**
	 * Main application reference
	 *
	 * @access protected
	 * @var Object
	 */
	protected $app;
	
	/**
	 * User object for ACL authorise check
	 *
	 * @access protected
	 * @var Object
	 */
	protected $user;
	
	/**
	 * Document object, needed by controllers to instantiate
	 * the right view object based on document format
	 *
	 * @access protected
	 * @var Object
	 */
	protected $document;
	
	/**
	 * Variables in request array
	 *
	 * @access protected
	 * @var Object
	 */
	protected $requestArray;
	
	/**
	 * Variables in request array name
	 *
	 * @access protected
	 * @var Object
	 */
	protected $requestName;
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 *
	 * @return boolean
	 */
	protected function allowDisplay() {
		// Set a default value
		$allow = true;
		$cParams = JComponentHelper::getParams('com_jchat');
	
		// Check for chat access level
		$chatAccessGids = array();
		$chatAccessLevels = $cParams->get('chat_accesslevels', array('0'));
	
		if(is_array($chatAccessLevels) && !in_array(0, $chatAccessLevels, false)) {
			// Check for user groups current user belong to
			$userGroups = $this->user->getAuthorisedGroups();
			$db = JFactory::getDbo();

			// Translate the chat access levels to Joomla users groups sum
			$query = $db->getQuery(true)
						->select('rules')
						->from($db->quoteName('#__viewlevels'))
						->where('id IN (' . implode(',', $chatAccessLevels) . ')');
			// Set the query for execution.
			$db->setQuery($query);
			// Build the view levels array.
			foreach ($db->loadColumn() as $levels) {
				$chatAccessGids = array_merge($chatAccessGids, (array) json_decode($levels));
			}

			// Intersect to recognize chat admins
			$intersectResult = array_intersect($userGroups, $chatAccessGids);
			$allow = (bool)(count($intersectResult));
		}
	
		// Check for menu exclusion
		$menu = $this->app->getMenu()->getActive();
		if(is_object($menu)) {
			$menuItemid = $menu->id;
			$menuExcluded = $cParams->get('chat_exclusions');
			if(is_array($menuExcluded) && !in_array(0, $menuExcluded, false) && in_array($menuItemid, $menuExcluded)) {
				$allow = false;
			}
		}
	
		// Check for IP multiple ranges exclusions
		if($cParams->get ( 'ipbanning', false)) {
			$ipAddressRegex = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/i';
			$clientIP = $_SERVER ['REMOTE_ADDR'];
			$clientIpDec = ( float ) sprintf ( "%u", ip2long ( $clientIP ) );
			$ipRanges = $cParams->get ( 'iprange_multiple', null);
			// Check if data are not null
			if($ipRanges) {
				// Try to load every range, one per row
				$explodeRows = explode(PHP_EOL, $ipRanges);
				if(!empty($explodeRows)) {
					foreach ($explodeRows as $singleRange) {
						// Try to detect single range
						$explodeRange = explode('-', $singleRange);
						if(!empty($explodeRange) && count($explodeRange) == 2) {
							$ipStart = trim($explodeRange[0]);
							$ipEnd = trim($explodeRange[1]);
							$validIpRangeStart = preg_match ( $ipAddressRegex, $ipStart );
							$validIpRangeEnd = preg_match ( $ipAddressRegex, $ipEnd );
							if ($validIpRangeStart && $validIpRangeEnd) {
								$lowerIpDec = ( float ) sprintf ( "%u", ip2long ( $ipStart ) );
								$upperIpDec = ( float ) sprintf ( "%u", ip2long ( $ipEnd ) );
								if (($clientIpDec >= $lowerIpDec) && ($clientIpDec <= $upperIpDec)) {
									$allow = false;
									break;
								}
							}
						}
					}
				}
			}
		}
	
		// Check for hours activation
		$startHour = $cParams->get('start_at_hour', null);
		$stopHour = $cParams->get('stop_at_hour', null);
		if($startHour && $stopHour) {
			if(class_exists('DateTimeZone')) {
				$jTimeZone = JFactory::getConfig ()->get ( 'offset' );
				$dateObject = JFactory::getDate();
				$dateObject->setTimezone(new DateTimeZone($jTimeZone));
				$currentHour = $dateObject->format('G', true);
			} else {
				$currentHour = date('G');
			}
			if($currentHour < $startHour || $currentHour >= $stopHour) {
				$allow = false;
			}
		}
	
		return $allow;
	}
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 *        	
	 * @return boolean
	 *
	 * @since 1.0
	 */
	protected function allowAdmin($assetName) {
		// Initialise variables.
		$allow = $this->user->authorise ( 'core.admin', $assetName );
		
		return $allow;
	}
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 *        	
	 * @return boolean
	 *
	 * @since 1.0
	 */
	protected function allowAdd($assetName) {
		// Initialise variables.
		$allow = $this->user->authorise ( 'core.create', $assetName );
		
		return $allow;
	}
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 * @param string $key
	 *        	The name of the key for the primary key.
	 *        	
	 * @return boolean
	 *
	 * @since 1.0
	 */
	protected function allowEdit($assetName) {
		// Initialise variables.
		$allow = $this->user->authorise ( 'core.edit', $assetName );
		
		return $allow;
	}
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 * @param string $key
	 *        	The name of the key for the primary key.
	 *        	
	 * @return boolean
	 *
	 * @since 1.0
	 */
	protected function allowEditState($assetName) {
		// Initialise variables.
		$allow = $this->user->authorise ( 'core.edit.state', $assetName );
		
		return $allow;
	}
	
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param array $data
	 *        	An array of input data.
	 * @param string $key
	 *        	The name of the key for the primary key.
	 *        	
	 * @return boolean
	 *
	 * @since 1.0
	 */
	protected function allowDelete($assetName) {
		// Initialise variables.
		$allow = $this->user->authorise ( 'core.delete', $assetName );
		
		return $allow;
	}
	
	/**
	 * Set model state from session
	 *
	 * @access protected
	 * @param string $scope        	
	 * @return void
	 */
	protected function setModelState($scope = 'default') {
		$option = $this->option;
		
		$search = $this->getUserStateFromRequest ( "$option.$scope.searchword", 'search', null );
		
		$limit = $this->getUserStateFromRequest ( "$option.$scope.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$limitStart = $this->getUserStateFromRequest ( "$option.$scope.limitstart", 'limitstart', 0, 'int' );
		// Round del limit al change proof
		$limitStart = ($limit != 0 ? (floor ( $limitStart / $limit ) * $limit) : 0);
		
		$filter_order = $this->getUserStateFromRequest ( "$option.$scope.filter_order", 'filter_order', 's.ordering', 'cmd' );
		$filter_order_Dir = $this->getUserStateFromRequest ( "$option.$scope.filter_order_Dir", 'filter_order_Dir', 'asc', 'word' );
		
		// Get default model
		$defaultModel = $this->getModel ();
		
		// Set model state
		$defaultModel->setState ( 'option', $option );
		$defaultModel->setState ( 'limit', $limit );
		$defaultModel->setState ( 'limitstart', $limitStart );
		$defaultModel->setState ( 'searchword', $search );
		$defaultModel->setState ( 'order', $filter_order );
		$defaultModel->setState ( 'order_dir', $filter_order_Dir );
		
		return $defaultModel;
	}
	
	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param string $key
	 *        	The key of the user state variable.
	 * @param string $request
	 *        	The name of the variable passed in a request.
	 * @param string $default
	 *        	The default value for the variable if not found. Optional.
	 * @param string $type
	 *        	Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 * @param boolean $resetPage
	 *        	If true, the limitstart in request is set to zero
	 *        	
	 * @return The request user state.
	 * @since 1.0
	 */
	protected function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true) {
		$app = JFactory::getApplication ();
		$old_state = $app->getUserState ( $key );
		$cur_state = (! is_null ( $old_state )) ? $old_state : $default;
		$new_state = $this->app->input->get ( $request, null, $type );
		
		if (($cur_state != $new_state) && ($resetPage)) {
			$this->app->input->set ( 'limitstart', 0 );
		}
		
		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState ( $key, $new_state );
		} else {
			$new_state = $cur_state;
		}
		
		return $new_state;
	}
	
	/**
	 * Method to get the controller name
	 *
	 * The dispatcher name by default parsed using the classname, or it can be
	 * set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @access public
	 * @return string The name of the dispatcher
	 * @since 1.0
	 */
	public function getNames() {
		$name = $this->name;
		
		if (empty ( $name )) {
			$r = null;
			if (! preg_match ( '/(.*)Controller(.*)/i', get_class ( $this ), $r )) {
				throw new Exception ( JText::_ ( 'JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME' ), 500 );
			}
			$name = ($r [2]);
		}
		
		return ($name);
	}
	
	/**
	 * Method to get the controller name
	 *
	 * The dispatcher name by default parsed using the classname, or it can be
	 * set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @access public
	 * @return string The name of the dispatcher
	 * @since 1.0
	 */
	public function getName() {
		$r = null;
		if (! preg_match ( '/(.*)Controller/i', get_class ( $this ), $r )) {
			throw new Exception ( JText::_ ( 'JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME' ), 500 );
		}
		$name = ($r [1]);
		
		return ($name);
	}
	
	/**
	 * Method to get a reference to the current view and load it if necessary.
	 *
	 * @access public
	 * @param
	 *        	string	The view name. Optional, defaults to the controller
	 *        	name.
	 * @param
	 *        	string	The view type. Optional.
	 * @param
	 *        	string	The class prefix. Optional.
	 * @param
	 *        	array	Configuration array for view. Optional.
	 * @return object to the view or an error.
	 * @since 1.0
	 */
	public function getView($name = null, $type = 'html', $prefix = null, $config = array()) {
		static $views;
		
		if (! isset ( $views )) {
			$views = array ();
		}
		
		if (empty ( $name )) {
			$name = $this->getNames ();
		}
		
		if (empty ( $prefix )) {
			$prefix = $this->getName () . 'View';
		}
		
		if (empty ( $views [$name] )) {
			if ($view = $this->createView ( $name, $prefix, $type, $config )) {
				$views [$name] = $view;
			} else {
				throw new Exception ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $type, $prefix ), 500 );
			}
		}
		
		return $views [$name];
	}
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @access public
	 * @param
	 *        	string	The model name. Optional.
	 * @param
	 *        	string	The class prefix. Optional.
	 * @param
	 *        	array	Configuration array for model. Optional.
	 * @return object model.
	 * @since 1.0
	 */
	public function getModel($name = '', $prefix = '', $config = array()) {
		static $models = array ();
		
		if (empty ( $name )) {
			$name = $this->getNames ();
		}
		
		if (empty ( $prefix )) {
			$prefix = $this->getName () . 'Model';
		}
		
		if (array_key_exists ( $name, $models )) {
			return $models [$name];
		}
		
		if ($model = $this->createModel ( $name, $prefix, $config )) {
			$models [$name] = $model;
			// task is a reserved state
			$model->setState ( 'task', $this->task );
			
			// Lets get the application object and set menu information if its
			// available
			$app = JFactory::getApplication ();
			$menu = $app->getMenu ();
			if (is_object ( $menu )) {
				if ($item = $menu->getActive ()) {
					$params = $menu->getParams ( $item->id );
					// Set Default State Data
					$model->setState ( 'parameters.menu', $params );
				}
			}
		}
		return $model;
	}
	
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @access public
	 * @param $cachable string
	 *        	the view output will be cached
	 * @since 1.0
	 */
	public function display($cachable = false, $urlparams = false) {
		$document = JFactory::getDocument ();
		
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		$viewLayout = $this->app->input->get ( 'layout', 'default' );
		
		$view = $this->getView ( $coreName, $viewType, '', array (
				'base_path' => $this->basePath 
		) );
		
		// Get/Create the model
		if ($model = $this->getModel ( $coreName )) {
			// Push the model into the view (as default)
			$view->setModel ( $model, true );
		}
		
		// Set the layout
		$view->setLayout ( $viewLayout );
		$view->display ();
	}
	
	/**
	 * Edit entity
	 *
	 * @access public
	 * @return void
	 */
	public function editEntity() {
		$this->app->input->set ( 'hidemainmenu', 1 );
		$cid = $this->app->input->get ( 'cid', array (
				0 
		), 'array' );
		$idEntity = ( int ) $cid [0];
		$model = $this->getModel ();
		$model->setState ( 'option', $this->option );
		
		// Try to load record from model
		if (! $record = $model->loadEntity ( $idEntity )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelExceptions = $model->getErrors ();
			foreach ( $modelExceptions as $exception ) {
				$this->app->enqueueMessage ( $exception->getMessage (), $exception->getErrorLevel () );
			}
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_('COM_JCHAT_ERROR_EDITING') );
			return false;
		}
		
		// Additional model state setting
		$model->setState ( 'option', $this->option );
		
		// Check out control on record
		if ($record->checked_out && $record->checked_out != $this->user->id) {
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_CHECKEDOUT_RECORD' ), 'notice' );
			return false;
		}
		
		// Access check
		if ($record->id && ! $this->allowEdit ( $this->option )) {
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_ALERT_NOACCESS' ), 'notice' );
			return false;
		}
		
		if (! $record->id && ! $this->allowAdd ( $this->option )) {
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_ALERT_NOACCESS' ), 'notice' );
			return false;
		}
		
		// Check out del record
		if ($record->id) {
			$record->checkout ( $this->user->id );
		}
		
		// Get view and pushing model
		$view = $this->getView ();
		$view->setModel ( $model, true );
		
		// Call edit view
		$view->editEntity ( $record );
	}
	
	/**
	 * Manage entity apply/save after edit entity
	 *
	 * @access public
	 * @return boolean
	 */
	public function saveEntity() {
		$context = implode ( '.', array (
				$this->option,
				strtolower ( $this->getNames () ),
				'errordataload' 
		) );
		
		// Load della model e bind store
		$model = $this->getModel ();
		
		if (! $result = $model->storeEntity ()) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			
			// Store data for session recover
			$this->app->setUserState ( $context, $this->requestArray[$this->requestName] );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".editEntity&cid[]=" . $this->app->input->get ( 'id' ), JText::_ ( 'COM_JCHAT_ERROR_SAVING' ) );
			return false;
		}
		
		// Security safe if not model record id detected
		if (! $id = $result->id) {
			$id = $this->app->input->get ( 'id' );
		}
		
		// Redirects switcher
		switch($this->task) {
			case 'saveEntity':
				$redirects = array (
								'task' => 'display',
								'msgsufix' => '_SAVING' 
							);
				break;
				
			case 'saveEntity2New':
				$redirects = array (
								'task' => 'editEntity',
								'msgsufix' => '_STORING' 
							);
				
				break;
				
			default:
			case 'applyEntity':
				$redirects = array (
								'task' => 'editEntity&cid[]=' . $id,
								'msgsufix' => '_APPLY'
							);
				break;
		}
		
		$msg = 'COM_JCHAT_SUCCESS' . $redirects ['msgsufix'];
		$controllerTask = $redirects ['task'];
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . "." . $controllerTask, JText::_ ( $msg ) );
		
		return true;
	}
	
	/**
	 * Manage cancel edit for entity and unlock record checked out
	 *
	 * @access public
	 * @return void
	 */
	public function cancelEntity() {
		$id = $this->app->input->get ( 'id' );
		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		if (! $model->cancelEntity ( $id )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_CANCELED_OPERATION' ) );
	}
	
	/**
	 * Copies one or more items
	 *
	 * @access public
	 * @return void
	 */
	public function copyEntity() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );
		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		if (! $model->copyEntity ( $cids )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_DUPLICATING' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_SUCCESS_DUPLICATING' ) );
	}
	
	/**
	 * Delete a db table entity
	 *
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );
		// Access check
		if (! $this->allowDelete ( $this->option )) {
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_ALERT_NOACCESS' ), 'notice' );
			return false;
		}
		// Load della model e checkin before exit
		$model = $this->getModel ();
		
		if (! $model->deleteEntity ( $cids )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_DELETE' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_SUCCESS_DELETE' ) );
	}
	
	/**
	 * Moves the order of a record
	 *
	 * @access public
	 * @param
	 *        	integer The increment to reorder by
	 * @return void
	 */
	public function moveOrder() {
		// Set model state
		$this->setModelState ( $this->corename );
		// ID Entity
		$cid = $this->app->input->get ( 'cid', array (
				0 
		), 'array' );
		$idEntity = $cid [0];
		// Task direction
		$model = $this->getModel ();
		$orderDir = $model->getState ( 'order_dir' );
		
		switch ($orderDir) {
			case 'desc' :
				$orderUp = 1;
				$orderDown = - 1;
				break;
			
			case 'asc' :
			default :
				$orderUp = - 1;
				$orderDown = 1;
				break;
		}
		
		$direction = $this->task == 'moveorder_up' ? $orderUp : $orderDown;
		
		if (! $model->changeOrder ( $idEntity, $direction )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_REORDER' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_SUCCESS_REORDER' ) );
	}
	
	/**
	 * Save ordering
	 *
	 * @access public
	 * @return void
	 */
	public function saveOrder() {
		$cids = $this->app->input->get ( 'cid', array (), 'array' );
		$order = $this->app->input->get ( 'order', array (), 'array' );
		JArrayHelper::toInteger ( $cid );
		JArrayHelper::toInteger ( $order );
		
		$model = $this->getModel ();
		
		if (! $model->saveOrder ( $cids, $order )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_REORDER' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_SUCCESS_REORDER' ) );
	}
	
	/**
	 * Publishing entities
	 *
	 * @access public
	 * @return void
	 */
	public function publishEntities() {
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
		
		if (! $model->publishEntities ( $idEntity, $this->task )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_ERROR_STATE_CHANGE' ) );
			return false;
		}
		
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JCHAT_SUCCESS_STATE_CHANGE' ) );
	}
	
	/**
	 * Constructor.
	 *
	 * @access protected
	 * @param
	 *        	array An optional associative array of configuration settings.
	 *        	Recognized key values include 'name', 'default_task',
	 *        	'model_path', and
	 *        	'view_path' (this list is not meant to be comprehensive).
	 * @since 1.0
	 */
	public function __construct($config = array()) {
		// Initialize private variables
		$this->redirect = null;
		$this->message = null;
		$this->messageType = 'message';
		$this->taskMap = array ();
		$this->methods = array ();
		$this->data = array ();
		$this->app = JFactory::getApplication ();
		$this->user = JFactory::getUser();
		$this->document = JFactory::getDocument();
		$this->option = $this->app->input->get ( 'option' );
		$this->requestArray = &$GLOBALS;
		$this->requestName = '_' . strtoupper('post');
		
		// Get the methods only for the final controller class
		$thisMethods = get_class_methods ( get_class ( $this ) );
		$baseMethods = get_class_methods ( 'JControllerLegacy' );
		$methods = array_diff ( $thisMethods, $baseMethods );
		
		// Add default display method
		$methods [] = 'display';
		
		// Iterate through methods and map tasks
		foreach ( $methods as $method ) {
			if (substr ( $method, 0, 1 ) != '_') {
				$this->methods [] = strtolower ( $method );
				// auto register public methods as tasks
				$this->taskMap [strtolower ( $method )] = $method;
			}
		}
		
		// set the view name
		if (empty ( $this->name )) {
			if (array_key_exists ( 'name', $config )) {
				$this->name = $config ['name'];
				$this->corename = strtolower ( $this->name );
			} else {
				$this->name = $this->getNames ();
				$this->corename = strtolower ( $this->name );
			}
		}
		
		// Set a base path for use by the controller
		if (array_key_exists ( 'base_path', $config )) {
			$this->basePath = $config ['base_path'];
		} else {
			$this->basePath = JPATH_COMPONENT;
		}
		
		// If the default task is set, register it as such
		if (array_key_exists ( 'default_task', $config )) {
			$this->registerDefaultTask ( $config ['default_task'] );
		} else {
			$this->registerDefaultTask ( 'display' );
		}
		
		// set the default model search path
		if (array_key_exists ( 'model_path', $config )) {
			// user-defined dirs
			$this->addModelPath ( $config ['model_path'] );
		} else {
			$this->addModelPath ( $this->basePath . '/models' );
		}
		
		// set the default view search path
		if (array_key_exists ( 'view_path', $config )) {
			// user-defined dirs
			$this->setPath ( 'view', $config ['view_path'] );
		} else {
			$this->setPath ( 'view', $this->basePath . '/views' );
		}
	}
}