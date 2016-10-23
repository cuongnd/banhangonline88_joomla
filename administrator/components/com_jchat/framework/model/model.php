<?php
// namespace components\com_jchat\libraries\framework\model;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage model
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

/**
 * Base model responsibilities
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage model
 * @since 1.0
 */
interface IJChatModel {
	/**
	 * Main get data method
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getData();
	
	/**
	 * Counter result set
	 *
	 * @access public
	 * @return int
	 */
	public function getTotal();
	
	/**
	 * Load entity from ORM table
	 * 
	 * @access public
	 * @param int $id        	
	 * @return Object&
	 */
	public function loadEntity($id);
	
	/**
	 * Cancel editing entity
	 *
	 * @param int $id        	
	 * @access public
	 * @return boolean
	 */
	public function cancelEntity($id);
	
	/**
	 * Delete entity
	 *
	 * @param array $ids        	
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($ids);
	
	/**
	 * Storing entity by ORM table
	 *
	 * @access public
	 * @return mixed
	 */
	public function storeEntity();
	
	/**
	 * Publishing state changer for entities
	 *
	 * @access public
	 * @param int $idEntity        	
	 * @param string $state        	
	 * @return boolean
	 */
	public function publishEntities($idEntity, $state);
	
	/**
	 * Change entities ordering
	 *
	 * @access public
	 * @param int $idEntity        	
	 * @param string $state        	
	 * @return boolean
	 */
	public function changeOrder($idEntity, $direction);
	
	/**
	 * Method to move and reorder
	 *
	 * @access public
	 * @return boolean on success
	 * @since 1.5
	 */
	function saveOrder($cid = array(), $order);
	
	/**
	 * Copy existing entity
	 *
	 * @param int $id        	
	 * @access public
	 * @return boolean
	 */
	public function copyEntity($ids);
	
	/**
	 * Return select lists used as filter for listEntities
	 *
	 * @access public
	 * @return array
	 */
	public function getFilters();
	
	/**
	 * Return select lists used as filter for editEntity
	 *
	 * @access public
	 * @param Object $record
	 * @return array
	*/
	public function getLists($record);
}

/**
 * Base concrete model for business logic
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage model
 * @since 1.0
 */
class JChatModel extends JModelLegacy implements IJChatModel {
	/**
	 * Application reference
	 *
	 * @access protected
	 * @var Object
	 */
	protected $app;
	
	/**
	 * Component params with view override
	 *
	 * @access protected
	 * @var Object
	 */
	protected $componentParams;
	
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
	 * Variables in request array name for files
	 *
	 * @access protected
	 * @var Object
	 */
	protected $requestFilesName;
	
	/**
	 * Get a cache object specific for this extension models
	 * already configured and independant from global config
	 *
	 * @access protected
	 * @return object JCache
	 */
	protected function getExtensionCache() {
		// Static cache instance
		static $cache;
		if(is_object($cache)) {
			return $cache;
		}
		jimport ( 'joomla.cache.cache' );
		$handler = 'callback';
		$conf = JFactory::getConfig ();
		$storage = $conf->get ( 'cache_handler', 'file' );
		$options = array (
				'defaultgroup' => $this->getState('option', $this->app->input->get('option')),
				'cachebase' => $conf->get ( 'cache_path', JPATH_CACHE ),
				'lifetime' => (int)$this->componentParams->get('cache_lifetime', 15), // minutes to seconds
				'language' => $conf->get ( 'language', 'en-GB' ),
				'storage' => $storage
		);
		
		$cache = JCache::getInstance ( $handler, $options );
		$cache->setCaching ( $this->componentParams->get('caching', false));
		return $cache;
	}
	
	/**
	 * Main get data method
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		try {
			$result = $this->_db->loadObjectList ();
			if ($this->_db->getErrorNum ()) {
				throw new JChatException ( JText::sprintf ( 'COM_JCHAT_ERROR_RECORDS', $this->_db->getErrorMsg () ), 'error' );
			}
		} catch ( JChatException $e ) {
			$this->app->enqueueMessage ( $e->getMessage (), $e->getErrorLevel () );
			$result = array ();
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->app->enqueueMessage ( $jchatException->getMessage (), $jchatException->getErrorLevel () );
			$result = array ();
		}
		return $result;
	}
	
	/**
	 * Counter result set
	 *
	 * @access public
	 * @return int
	 */
	public function getTotal() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query );
		$result = count ( $this->_db->loadColumn () );
		
		return $result;
	}
	
	/**
	 * Load entity from ORM table and manages session data post error redirect
	 *
	 * @access public
	 * @param int $id        	
	 * @return Object&
	 */
	public function loadEntity($id) {
		// load table record
		$table = $this->getTable ();
		
		// Check for previously set post data after errors
		$context = implode ( '.', array (
				$this->getState ( 'option' ),
				$this->getName (),
				'errordataload' 
		) );
		$sessionData = $this->app->getUserState ( $context );
		
		try {
			// Give priority to session recovered data
			if (! $sessionData) {
				// Load normally from database
				if (! $table->load ( $id )) {
					throw new JChatException ( $this->_db->getErrorMsg (), 'error' );
				}
			} else {
				// Recover and bind/load from session
				if (! $table->bind ( $sessionData, false, true )) {
					throw new JChatException ( $this->_db->getErrorMsg (), 'error' );
				}
				// Delete session data for next request
				$this->app->setUserState ( $context, null );
			}
		} catch ( JChatException $e ) {
			$this->setError ( $e );
			return false;
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->setError ( $jchatException );
			return false;
		}
		return $table;
	}
	
	/**
	 * Cancel editing entity
	 *
	 * @param int $id        	
	 * @access public
	 * @return boolean
	 */
	public function cancelEntity($id) {
		// New record - do null e return true subito
		if (! $id) {
			return true;
		}
		
		$table = $this->getTable ();
		try {
			if (! $table->load ( $id )) {
				throw new JChatException ( $table->getError (), 'error' );
			}
			if (! $table->checkin ()) {
				throw new JChatException ( $table->getError (), 'error' );
			}
		} catch ( JChatException $e ) {
			$this->setError ( $e );
			return false;
		} catch ( Exception $e ) {
			$JChatException = new JChatException ( $e->getMessage (), 'error' );
			$this->setError ( $JChatException );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete entity
	 *
	 * @param array $ids        	
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($ids) {
		$table = $this->getTable ();
		
		// Ciclo su ogni entity da cancellare
		if (is_array ( $ids ) && count ( $ids )) {
			foreach ( $ids as $id ) {
				try {
					$table->id = $id;
					if (! $table->delete ( $id )) {
						throw new JChatException ( $table->getError (), 'error' );
					}
					// Only if table supports ordering
					if (property_exists ( $table, 'ordering' )) {
						$table->reorder ();
					}
				} catch ( JChatException $e ) {
					$this->setError ( $e );
					return false;
				} catch ( Exception $e ) {
					$JChatException = new JChatException ( $e->getMessage (), 'error' );
					$this->setError ( $JChatException );
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Storing entity by ORM table
	 *
	 * @access public
	 * @return mixed
	 */
	public function storeEntity() {
		$table = $this->getTable ();
		try {
			if (! $table->bind ( $this->requestArray[$this->requestName], true )) {
				throw new JChatException ( $table->getError (), 'error' );
			}
			
			if (! $table->check ()) {
				throw new JChatException ( $table->getError (), 'error' );
			}
			
			if (! $table->store ()) {
				throw new JChatException ( $table->getError (), 'error' );
			}
			// Only if table supports ordering
			if (property_exists ( $table, 'ordering' )) {
				$where = null;
				$catidOrdering = property_exists($table, 'catid');
				if($catidOrdering) {
					$where = 'catid = ' . $table->catid;
				}
				$table->reorder ($where);
			}
		} catch ( JChatException $e ) {
			$this->setError ( $e );
			return false;
		} catch ( Exception $e ) {
			$JChatException = new JChatException ( $e->getMessage (), 'error' );
			$this->setError ( $JChatException );
			return false;
		}
		return $table;
	}
	
	/**
	 * Publishing state changer for entities
	 *
	 * @access public
	 * @param int $idEntity        	
	 * @param string $state        	
	 * @return boolean
	 */
	public function publishEntities($idEntity, $state) {
		// Table load
		$table = $this->getTable ( $this->getName (), 'Table' );
		
		if (isset ( $idEntity ) && $idEntity) {
			try {
				// Ensure treat as array
				if(!is_array($idEntity)) {
					$idEntity = array($idEntity);
				}
				$state = $state == 'unpublish' ? 0 : 1;
				if (! $table->publish( $idEntity, $state )) {
					throw new JChatException ( $table->getError (), 'notice' );
				}
			} catch ( JChatException $e ) {
				$this->setError ( $e );
				return false;
			} catch ( Exception $e ) {
				$jchatException = new JChatException ( $e->getMessage (), 'notice' );
				$this->setError ( $jchatException );
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Change entities ordering
	 *
	 * @access public
	 * @param int $idEntity        	
	 * @param int $direction        	
	 * @return boolean
	 */
	public function changeOrder($idEntity, $direction) {
		$where = null;
		if (isset ( $idEntity ) && $idEntity) {
			try {
				$table = $this->getTable ();
				$table->load ( ( int ) $idEntity );
				// Check if ordering where by cats is required
				if(property_exists($table, 'catid')) {
					$where = 'catid = ' . $table->catid;
				}
				if (! $table->move ( $direction, $where)) {
					throw new JChatException ( $table->getError (), 'notice' );
				}
			} catch ( JChatException $e ) {
				$this->setError ( $e );
				return false;
			} catch ( Exception $e ) {
				$jchatException = new JChatException ( $e->getMessage (), 'notice' );
				$this->setError ( $jchatException );
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Method to move and reorder
	 *
	 * @access public
	 * @param array $cid        	
	 * @param array $order        	
	 * @return boolean on success
	 * @since 1.5
	 */
	public function saveOrder($cid = array(), $order) {
		if (is_array ( $cid ) && count ( $cid )) {
			try {
				$table = $this->getTable ();
				$singleReorder = !(property_exists($table, 'catid'));
				// If JTableNested demand to table class the saveorder algo
				if($table instanceof JTableNested) {
					if(!$table->saveorder($cid, $order)) {
						throw new JChatException ( $table->getError (), 'notice' );
					}
				} else {
					// update ordering values
					for($i = 0; $i < count ( $cid ); $i ++) {
						$table->load ( ( int ) $cid [$i] );
						if ($table->ordering != $order [$i]) {
							$table->ordering = $order [$i];
							if (! $table->store ()) {
								throw new JChatException ( $table->getError (), 'notice' );
							}
						}
						// Remember to reorder within position and client_id
						$condition = 'catid = ' . $table->catid;
						$found = false;
						
						foreach ($conditions as $cond) {
							if ($cond[1] == $condition) {
								$found = true;
								break;
							}
						}
						
						if (!$found) {
							$key = $table->getKeyName();
							$conditions[] = array($table->$key, $condition);
						}
					}
				}
				
			} catch ( JChatException $e ) {
				$this->setError ( $e );
				return false;
			} catch ( Exception $e ) {
				$jchatException = new JChatException ( $e->getMessage (), 'notice' );
				$this->setError ( $jchatException );
				return false;
			}
			
			// All went well
			if(!$table instanceof JTableNested && !$singleReorder) {
				// Execute reorder for each category.
				foreach ($conditions as $cond) {
					$table->load($cond[0]);
					$table->reorder($cond[1]);
				}
			} elseif (!$table instanceof JTableNested && $singleReorder) {
				$table->reorder ();
			}
		}
		return true;
	}
	
	/**
	 * Copy existing entity
	 *
	 * @param int $id        	
	 * @access public
	 * @return boolean
	 */
	public function copyEntity($ids) {
		if (is_array ( $ids ) && count ( $ids )) {
			$table = $this->getTable ();
			try {
				foreach ( $ids as $id ) {
					if ($table->load ( ( int ) $id )) {
						$table->id = 0;
						$table->name = JText::_ ( 'COM_JCHAT_COPYOF' ) . $table->name;
						$table->published = 0;
						$table->params = $table->params->toString ();
						if (! $table->store ()) {
							throw new JChatException ( $table->getError (), 'error' );
						}
					} else {
						throw new JChatException ( $table->getError (), 'error' );
					}
				}
				$table->reorder ();
			} catch ( JChatException $e ) {
				$this->setError ( $e );
				return false;
			} catch ( Exception $e ) {
				$jchatException = new JChatException ( $e->getMessage (), 'error' );
				$this->setError ( $jchatException );
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Return select lists used as filter for listEntities
	 *
	 * @access public
	 * @return array
	 */
	public function getFilters() {
		$filters ['state'] = JHTML::_ ( 'grid.state', $this->getState ( 'state' ) );
	
		return $filters;
	}
	
	/**
	 * Return select lists used as filter for editEntity
	 *
	 * @access public
	 * @param Object $record
	 * @return array
	 */
	public function getLists($record) {
		$lists = array ();
		// Grid states
		$lists ['published'] = JHTML::_ ( 'select.booleanlist', 'published', null, $record->published );
	
		return $lists;
	}
	
	/**
	 * Get the component params width view override/merge
	 * @access public
	 * @return Object
	 */
	public function getComponentParams() {
		if(is_object($this->componentParams)) {
			return $this->componentParams;
		}
	
		// Manage Site and Admin application instance to call params with view overrides when needed
		if($this->app instanceof JApplicationSite && $this->option == 'com_jchat') {
			$this->componentParams = $this->app->getParams('com_jchat');
		} else {
			$this->componentParams = JComponentHelper::getParams('com_jchat');
		}
	
		return $this->componentParams;
	}
	
	/**
	 * Class constructor
	 *
	 * @access public
	 * @param $config array        	
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );
		$this->app = JFactory::getApplication ();
		$this->requestArray = &$GLOBALS;
		$this->requestName = '_' . strtoupper('post');
		$this->requestFilesName = '_' . strtoupper('files');
	}
}