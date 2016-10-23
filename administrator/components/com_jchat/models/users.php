<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

/**
 * Users model responsibilities
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage models
 * @since 1.6
 */
interface IJChatModelUsers {
	/**
	 * Update the entity status
	 *
	 * @access public
	 * @param array $ids
	 * @param string $task
	 * @return array[] &
	 */
	public function banEntity($ids, $task);
}

/**
 * Users model responsibilities
 *
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage models
 * @since 1.6
 */
class JChatModelUsers extends JChatModel implements IJChatModelUsers {
	/**
	 * Restituisce la query string costruita per ottenere il wrapped set richiesto in base
	 * allo userstate, opzionalmente seleziona i campi richiesti
	 * 
	 * @access private
	 * @return string
	 */
	protected function buildListQuery($fields = 'a.*') {
		// WHERE
		$where = array();
		$whereString = null;
				
		//Filtro testo
		if($this->state->get('searchword')) {
			$where[] = "\n (a.email LIKE " .
						$this->_db->quote('%' . $this->state->get('searchword') . '%') .
						"\n OR a.name LIKE " . 
						$this->_db->quote('%' . $this->state->get('searchword'). '%') . ")";
		}
		
		if($this->state->get('banstatus', '') !== '') {
			$banStatus = (int)$this->state->get('banstatus');
			if($banStatus == 1) {
				$where[] = "\n (u.banstatus = " . $banStatus . ")";
			} elseif($banStatus == 0) {
				$where[] = "\n (u.banstatus = 0 OR ISNULL(u.banstatus))";
			}
		}
		
		if (count($where)) {
			$whereString = "\n WHERE " . implode ("\n AND ", $where);
		}
		
		// ORDERBY
		if($this->state->get('order')) {
			$orderString = "\n ORDER BY " . $this->state->get('order') . " ";
		}
		
		//Filtro testo
		if($this->state->get('order_dir')) {
			$orderString .= $this->state->get('order_dir');
		}
		
		
		$query = "SELECT $fields, u.banstatus" .
				 "\n FROM #__users AS a" .
				 "\n LEFT JOIN #__jchat_userstatus AS u" .
				 "\n ON a.id = u.userid" .
				 $whereString . 
				 $orderString;
		return $query;
	}

	/**
	 * Main get data method
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		try {
			$result = $this->_db->loadObjectList ();
			
			if($this->_db->getErrorNum()) {
				throw new JChatException(JText::_('COM_JCHAT_ERROR_RECORDS') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
			$result = array();
		} catch (Exception $e) {
			$JChatException = new JChatException($e->getMessage(), 'error');
			$this->app->enqueueMessage($JChatException->getMessage(), $JChatException->getErrorLevel());
			$result = array();
		}
		return $result;
	}
	
	/**
	 * Restituisce le select list usate dalla view per l'interfaccia
	 * @access public
	 * @return array
	 */
	public function getFilters() {
		$lists = array();
		 
		$types[] = JHTML::_('select.option',  '', '- '. JText::_('COM_JCHAT_USERS_ALL' ) .' -' ); 
		$types[] = JHTML::_('select.option', '1', JText::_('COM_JCHAT_USERS_BANNED' ) );
		$types[] = JHTML::_('select.option', '0', JText::_('COM_JCHAT_USERS_NOTBANNED' ) );
		 
		$lists['banstatus'] = JHTML::_('select.genericlist', $types, 'banstatus', 'class="inputbox hidden-phone" size="1" onchange="document.adminForm.task.value=\'users.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('banstatus'));
			
		return $lists;
	}
	
	/**
	 * Purge the cache of all messages in a single operation
	 * 
	 * @access public
	 * @param array $cids
	 * @return boolean
	 */
	
	/**
	 * Update the entity status
	 *
	 * @access public
	 * @param array $ids
	 * @param string $task
	 * @return array[] &
	 */
	public function banEntity($ids, $task) {
		// Ciclo su ogni entity da cancellare
		if (is_array ( $ids ) && count ( $ids )) {
			// Get the first entity always
			$entityId = (int)$ids[0];
			
			// Determine the ban status for the user
			$statusVarValue = $task == 'banEntity' ? 1 : 0;
			
			try {
		 		// Delete session status still not active session for Joomla session lifetime
		 		$queryStatus = 	"INSERT INTO #__jchat_userstatus (userid, banstatus) VALUES (" .
									$entityId . ", " .
									$statusVarValue . ") " .
									"ON DUPLICATE KEY UPDATE " . $this->_db->quoteName('banstatus') . " = " . $this->_db->quote($statusVarValue);
		 		// Purge session status
		 		$this->_db->setQuery($queryStatus)->execute();
		 	} catch ( Exception $e ) {
		 		$JChatException = new JChatException ( $e->getMessage (), 'error' );
		 		$this->setError ( $JChatException );
		 		return false;
		 	}
		}
	
		return true;
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
	
		$componentParams = $this->getComponentParams();
		$this->setState('cparams', $componentParams);
	}
} 