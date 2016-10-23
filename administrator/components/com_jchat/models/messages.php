<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Messages model responsibilities contract
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface IMessagesModel {
	/**
	 * Esplica la funzione di esportazione della lista messaggi
	 * in formato CSV per i record estratti dai filtri userstate attivi
	 * @access public
	 * @param array $fieldsToLoadArray
	 * @param array& $fieldsFunctionTransformation
	 * @return Object[]&
	 */
	public function exportMessages($fieldsToLoadArray, &$fieldsFunctionTransformation);
}
 
/**
 * Messages model responsibilities
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
class JChatModelMessages extends JChatModel implements IMessagesModel {
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
			$where[] = "\n (a.actualfrom LIKE " .
						$this->_db->quote('%' . $this->state->get('searchword') . '%') .
						"\n OR a.actualto LIKE " . 
						$this->_db->quote('%' . $this->state->get('searchword'). '%')  . 
						"\n OR a.message LIKE " . 
						$this->_db->quote('%' . $this->state->get('searchword'). '%') . ")";
		}
		
		//Filtro periodo
		if($this->state->get('fromPeriod')) {
			$where[] = "\n a.sent > " . strtotime($this->state->get('fromPeriod'));
		}
		
		if($this->state->get('toPeriod')) {
			$where[] = "\n a.sent < " . (strtotime($this->state->get('toPeriod')) + 60*60*24);
		}
		
		if($this->state->get('msgType')) {
			$where[] = "\n a.type = " .  $this->_db->quote($this->state->get('msgType'));
		}
		
		if($this->state->get('msgStatus')) {
			$status = (int)$this->state->get('msgStatus') - 1;
			switch($status) {
				case 1:
				case 0:
					$where[] = "\n a.read = $status AND a.to != " . $this->_db->quote('0');
					break;
					
				case -2:
					$where[] = "\n a.read = 0 AND a.to = " . $this->_db->quote('0');
					break;
			}
		}
		
		if($this->state->get('roomsFilter')) {
			$where[] = "\n a.sentroomid = " . (int)$this->state->get('roomsFilter');
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
		
		$query = "SELECT $fields, r.name AS roomname" .
				 "\n FROM #__jchat AS a" .
				 "\n LEFT JOIN #__jchat_rooms AS r" .
				 "\n ON a.sentroomid = r.id" .
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
				throw new JChatException(JText::_('COM_JCHAT_ERROR_RETRIEVING_MESSAGES') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
			$result = array();
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->app->enqueueMessage($jchatException->getMessage(), $jchatException->getErrorLevel());
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
		 
		$types[] = JHTML::_('select.option',  '0', '- '. JText::_('COM_JCHAT_MESSAGE_TYPE' ) .' -' ); 
		$types[] = JHTML::_('select.option', 'file', JText::_('COM_JCHAT_FILE_MESSAGE' ) );
		$types[] = JHTML::_('select.option', 'message', JText::_('COM_JCHAT_TEXT_MESSAGE' ) );
		$lists['type'] 	= JHTML::_('select.genericlist', $types, 'msg_type', 'class="inputbox input-medium hidden-phone" size="1" onchange="document.adminForm.task.value=\'messages.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('msgType'));
			
		$status[] = JHTML::_('select.option',  '', '- '. JText::_('COM_JCHAT_STATUS_MSGS' ) .' -' );
		$status[] = JHTML::_('select.option', '2', JText::_('COM_JCHAT_DISPLAYED_MSGS' ) );
		$status[] = JHTML::_('select.option', '1', JText::_('COM_JCHAT_NOT_DISPLAYED_MSGS' ) );
		$status[] = JHTML::_('select.option', '-1', JText::_('COM_JCHAT_TO_GROUP_MSGS' ) );
		$lists['status'] = JHTML::_('select.genericlist', $status, 'msg_status', 'class="inputbox input-medium hidden-phone" size="1" onchange="document.adminForm.task.value=\'messages.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('msgStatus'));
			
		// Select rooms to filter messages exchanged
		$query = "SELECT" .
				 "\n " . $this->_db->quoteName('id') . " AS value," .
				 "\n " . $this->_db->quoteName('name') . " AS text" .
				 "\n FROM #__jchat_rooms" .
				 "\n WHERE" .
				 "\n " . $this->_db->quoteName('published') . " = 1";
		$rooms = $this->_db->setQuery($query)->loadObjectList();
		array_unshift($rooms, JHTML::_('select.option',  '', '- '. JText::_('COM_JCHAT_ROOMS_FILTER' ) .' -' ));
		$lists['rooms'] = JHTML::_('select.genericlist', $rooms, 'rooms_filter', 'class="inputbox input-medium hidden-phone" size="1" onchange="document.adminForm.task.value=\'messages.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('roomsFilter'));
		
		return $lists;
	}
 
	/**
	 * Esplica la funzione di esportazione della lista messaggi
	 * in formato CSV per i record estratti dai filtri userstate attivi
	 * @access public
	 * @param array $fieldsToLoadArray
	 * @param array& $fieldsFunctionTransformation
	 * @return Object[]&
	 */
	public function exportMessages($fieldsToLoadArray, &$fieldsFunctionTransformation) { 
		$fieldsName = array(); 
		if(is_array($fieldsToLoadArray) && count($fieldsToLoadArray)) {
			$arrayIter = new ArrayIterator($fieldsToLoadArray);
			while ($arrayIter->valid()) { 
				$fieldName = $arrayIter->key();
				$transformedFieldName = $arrayIter->current();
				// Assegnamento duplice name->transformation
				$fieldsName[] = $fieldName;
				$fieldsFunctionTransformation[] = $transformedFieldName;
		
				// Increment pointer
				$arrayIter->next();
			}
		}
		
		$fieldsFunctionTransformation[] = JText::_('COM_JCHAT_ROOM');
		$joinedFieldsName = implode(',', $fieldsName);
		
		// Obtain query string
		$query = $this->buildListQuery($joinedFieldsName);
		$this->_db->setQuery($query, $this->getState('limitstart'), $this->getState('limit') );
		$resultSet = $this->_db->loadAssocList();
		
		if(!is_array($resultSet) || !count($resultSet)) {
			return false;
		}
		
		return $resultSet;
	}
	
	/**
	 * Purge the cache of all messages in a single operation
	 * 
	 * @access public
	 * @param boolean $oldest
	 * @return boolean
	 */
	public function deleteEntities($oldest) {
		$where = null;
		try {
			if($oldest) {
				$daysSaved = (int)($this->getState('cparams')->get('keep_latest_msgs', 7));
				if($daysSaved) {
					$where = "\n WHERE chat.sent < " . strtotime("-$daysSaved days", time());
				}
			}
			$query = "DELETE " . 
					 $this->_db->quoteName('chat') . "," .
					 $this->_db->quoteName('readmsgs') . "," .
					 $this->_db->quoteName('deletedmsgs') .
					 "\n FROM #__jchat AS chat" .
					 "\n LEFT JOIN #__jchat_public_readmessages AS readmsgs" .
					 "\n ON chat.id = readmsgs.messageid" .
					 "\n LEFT JOIN #__jchat_messaging_deletedmessages AS deletedmsgs" .
					 "\n ON chat.id = deletedmsgs.messageid" .
					 $where;
			$this->_db->setQuery($query);
			if(!$this->_db->execute()) {
				throw new JChatException($this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->setError($e);
			return false;
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->setError($jchatException);
			return false;
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

		$componentParams = JComponentHelper::getParams($this->option);
		$this->setState('cparams', $componentParams);
	}
} 