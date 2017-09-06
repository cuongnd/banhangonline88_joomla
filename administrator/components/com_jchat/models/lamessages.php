<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Messages model responsibilities contract
 *
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface ILAMessagesModel { 
	/**
	 * Modifica lo stato lavorato del record
	 * @param int $id
	 * @param string $state
	 * @access public
	 * @return boolean
	 */
	public function changeTicketState($id, $state);
	
	/**
	 * Esplica la funzione di esportazione della lista messaggi
	 * in formato CSV per i record estratti dai filtri userstate attivi
	 * @access public
	 * @param array $fieldsToLoadArray
	 * @param array& $fieldsFunctionTransformation
	 * @return Object[]&
	 */
	public function exportMessages($fieldsToLoadArray, &$fieldsFunctionTransformation);
	
	/**
	 * Storing record tramite $table
	 * @param Object& $mailer
	 * @param int $idEntity
	 * @param string $subjectMessage
	 * @param string $textMessage
	 * @access public
	 * @return mixed
	 */
	public function sendResponseStore($mailer, $idEntity, $subjectMessage, $textMessage);
}

/**
 * Offline messages concrete model
 *
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
class JChatModelLamessages extends JChatModel implements ILAMessagesModel {
	/**
	 * Dataset records
	 * @var Object[]
	 * @access private
	 */
	private $records;
	
	/**
	 * Component configuration pointer
	 *
	 * @access private
	 * @var Object&
	 */
	private $cParams;
	
	/**
	 * Joomla configuration pointer
	 *
	 * @access private
	 * @var Object&
	 */
	private $jConfig;
	  
	/**
	 * Effettua l'invio della mail di risposta al customer
	 *
	 * @param Object& $mailer
	 * @param string $recipient
	 * @param string $subject
	 * @param string $text
	 * @access private
	 * @return boolean
	 */
	private function sendEmail($mailer, $recipient, $subject, $text, $originalMessage) {
		// Build e-mail message format
		$mailer->setSender(array($this->componentParams->get('tickets_mailfrom', $this->jConfig->get('mailfrom')), 
								 $this->componentParams->get('tickets_fromname', $this->jConfig->get('fromname'))));
		$mailer->setSubject($subject);
		$mailer->setBody($text . JText::sprintf('COM_JCHAT_ORIGINAL_MSG', $originalMessage));
		$mailer->IsHTML(true);
	
		// Add recipient
		$mailer->addRecipient($recipient);
	
		// Send the Mail
		$rs	= $mailer->sendUsingExceptions();
	
		// Check for an error
		return $rs;
	}
	
	/**
	 * Restituisce la query string costruita per ottenere il wrapped set richiesto in base
	 * allo userstate, opzionalmente seleziona i campi richiesti
	 * 
	 * @access protected
	 * @return string
	 */
	protected function buildListQuery($fields = 'a.*') {
		// WHERE
		$where = array();
		$whereString = null;
				
		//Filtro testo 
		if($this->state->get('searchword')) {
			$where[] = "\n (a.name LIKE " .
					$this->_db->Quote('%' . $this->state->get('searchword') . '%') .
					"\n OR a.email LIKE " .
					$this->_db->Quote('%' . $this->state->get('searchword'). '%') . ")";
		}
		
		//Filtro periodo
		if($this->state->get('fromPeriod')) {
			$where[] = "\n a.sentdate >= " . $this->_db->Quote($this->state->get('fromPeriod'));
		}
		
		if($this->state->get('toPeriod')) {
			$where[] = "\n a.sentdate <= " . $this->_db->Quote($this->state->get('toPeriod'));
		}
		
		if((int)$this->state->get('closedfilter') == 1) {
			$where[] = "\n a.closed_ticket = 1";
		} elseif((int)$this->state->get('closedfilter') == -1) {
			$where[] = "\n (a.closed_ticket = 0)";
		}
		
		if((int)$this->state->get('workedfilter') == 1) {
			$where[] = "\n a.worked = 1";
		} elseif((int)$this->state->get('workedfilter') == -1) {
			$where[] = "\n (a.worked != 1 OR ISNULL(a.worked))";
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
		
		// Fallback order
		$orderString .= "\n ,a.id DESC";
		
		$query = "SELECT $fields, u.name AS username_logged"
				. "\n FROM #__jchat_lamessages AS a"
				. "\n LEFT JOIN #__users AS u"
				. "\n ON a.userid = u.id"
				. $whereString 
				. $orderString;
		return $query;
	}

	/**
	 * Restituisce le select list usate dalla view per l'interfaccia
	 * @access public
	 * @return array
	 */
	public function getLists($record) {
		$lists = array();

		// Edit boolean lists
		$lists['worked'] = JHTML::_('select.booleanlist', 'worked', null, $record->worked);
		$lists['closed'] = JHTML::_('select.booleanlist', 'closed_ticket', null, $record->closed_ticket);
		
		return $lists;
	}
	
	/**
	 * Restituisce le select list usate dalla view per l'interfaccia
	 * @access public
	 * @return array
	 */
	public function getFilters() {
		$answered = array();
		$answered[] = JHTML::_('select.option',  '', '- '. JText::_('COM_JCHAT_ALL_TICKETS' ) .' -' );
		$answered[] = JHTML::_('select.option', '1', JText::_('COM_JCHAT_ANSWERED_TICKETS' ) );
		$answered[] = JHTML::_('select.option', '-1', JText::_('COM_JCHAT_NOTANSWERED_TICKETS' ) );
		$lists['answered'] = JHTML::_('select.genericlist', $answered, 'workedfilter', 'class="inputbox input-medium hidden-phone" size="1" onchange="document.adminForm.task.value=\'lamessages.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('workedfilter'));
		
		$closed = array();
		$closed[] = JHTML::_('select.option',  '', '- '. JText::_('COM_JCHAT_ALL_TICKETS' ) .' -' );
		$closed[] = JHTML::_('select.option',  '-1', JText::_('COM_JCHAT_OPENED_TICKETS' ));
		$closed[] = JHTML::_('select.option',  '1', JText::_('COM_JCHAT_CLOSED_TICKETS' ));
		$lists['closed'] = JHTML::_('select.genericlist', $closed, 'closedfilter', 'class="inputbox input-medium hidden-phone" size="1" onchange="document.adminForm.task.value=\'lamessages.display\';document.adminForm.submit( );"', 'value', 'text', $this->state->get('closedfilter'));
		
		return $lists;
	}
	
	/**
	 * Modifica lo stato lavorato del record
	 * @param int $id
	 * @param string $state
	 * @access public
	 * @return boolean
	 */
	public function changeTicketState($idEntity, $state) {
		// Table load
		$table = $this->getTable ( $this->getName (), 'Table' );
		
		if (isset ( $idEntity ) && $idEntity) {
			try {
				if (! $table->load( $idEntity )) {
					throw new JChatException ( $table->getError (), 'notice' );
				}
				switch($state){
					case 'workedFlagOff':
						$table->worked = null;
						break;
						
					case 'workedFlagOn':
						$table->worked = 1;
						break;
						
					case 'closedFlagOff':
						$table->closed_ticket = null;
						break;
					
					case 'closedFlagOn':
						$table->closed_ticket = 1;
						break;
				}
				if (! $table->store(true)) {
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
				// Assegnamento duplice name0>transformation
				$fieldsName[] = $fieldName;
				$fieldsFunctionTransformation[] = $transformedFieldName;
	
				// Increment pointer
				$arrayIter->next();
			}
		}
	
		$joinedFieldsName = implode(',', $fieldsName);
	
		// Obtain query string
		$query = $this->buildListQuery($joinedFieldsName);
		$this->_db->setQuery($query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ));
		$resultSet = $this->_db->loadAssocList();
	
		if(!is_array($resultSet) || !count($resultSet)) {
			return false;
		}
	
		return $resultSet;
	}
	
	/**
	 * Storing record tramite $table
	 * 
	 * @param Object& $mailer
	 * @param int $idEntity
	 * @param string $subjectMessage
	 * @param string $textMessage
	 * @access public
	 * @return mixed
	 */
	public function sendResponseStore($mailer, $idEntity, $subjectMessage, $textMessage) {
		$table = $this->getTable('Lamessages');
		try {
			$table->load($idEntity);			 
			
			// Send mail response
			if (!$this->sendEmail($mailer, $table->email, $subjectMessage, $textMessage, $table->message)) {
				if(is_object($mailer->exception)) {
					throw new JChatException($mailer->exception->getMessage (), 'warning');
				} else {
					$mailer->errorDetails = $mailer->errorDetails ? $mailer->errorDetails : null;
					throw new JChatException($mailer->errorDetails, 'warning');
				}
			}
			
			if (! $table->bind ($this->requestArray[$this->requestName], array(), true)) {
				throw new JChatException($table->getError ());
			}
	 		// Forza messaggio worked
			$table->worked = 1;
			if (! $table->store (false)) {
				throw new JChatException($table->getError ());
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
	 * Class contructor
	 *
	 * @access public
	 * @return Object&
	 */
	public function __construct() {
		parent::__construct();
	 
		$this->getComponentParams();
		$this->jConfig = JFactory::getConfig();
	}
}
?>