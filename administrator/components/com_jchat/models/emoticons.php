<?php
// namespace administrator\components\com_jchat\models;
/**
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.filesystem.file' );

/**
 * Emoticons concrete model
 * Operates not on DB but directly on a cached copy of the XML sitemap file
 *
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage models
 * @since 3.2
 */
class JChatModelEmoticons extends JChatModel {
	/**
	 * Restituisce la query string costruita per ottenere il wrapped set richiesto in base
	 * allo userstate, opzionalmente seleziona i campi richiesti
	 * 
	 * @access private
	 * @return string
	 */
	protected function buildListQuery() {
		// WHERE
		$where = array();
		$whereString = null;
				
		// STATE FILTER
		if ($filter_state = $this->state->get ( 'state' )) {
			if ($filter_state == 'P') {
				$where [] = 'published = 1';
			} else if ($filter_state == 'U') {
				$where [] = 'published = 0';
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
		
		$query = "SELECT *" .
				 "\n FROM #__jchat_emoticons AS a" .
				 $whereString .
				 $orderString;
		return $query;
	}
	
	/**
	 * Store info for a given emoticon
	 *
	 * @access public
	 * @param Object $dataObject
	 * @return Object
	 */
	public function saveEmoticon($dataObject) {
		// Response JSON object
		$response = new stdClass();
		$linkUrlQuery = null;

		try {
			// Ensure that the lenght is valid
			if(strlen($dataObject->keycode) < 2) {
				throw new JChatException(JText::_('COM_JCHAT_INVALID_KEYCODE_DESC'), 'error');
			}

			if(isset($dataObject->linkurl)) {
				if(!$dataObject->linkurl) {
					throw new JChatException(JText::_('COM_JCHAT_INVALID_LINKURL_DESC'), 'error');
				}

				$linkUrlQuery = "\n " . $this->_db->quoteName('linkurl') . " = " . $this->_db->quote($dataObject->linkurl) . ",";
			}

			// If the link exists just update it, otherwise insert a new one
			$query = "UPDATE" .
					 "\n " . $this->_db->quoteName('#__jchat_emoticons') .
					 "\n SET " .
					 $linkUrlQuery .
					 "\n " . $this->_db->quoteName('keycode') . " = " . $this->_db->quote($dataObject->keycode) .
					 "\n WHERE " .
					 "\n " . $this->_db->quoteName('id') . " = " . $this->_db->quote($dataObject->id);
			$this->_db->setQuery ( $query );
			$this->_db->execute ();
			if ($this->_db->getErrorNum ()) {
				throw new JChatException(JText::sprintf('COM_JCHAT_ERROR_STORING_DB_DATA', $this->_db->getErrorMsg()), 'error');
			}
	
			// All completed succesfully
			$response->result = true;
			$response->stored_keycode = $dataObject->keycode;
		} catch (JChatException $e) {
			$response->result = false;
			$response->exception_message = $e->getMessage();
			return $response;
		} catch (Exception $e) {
			$JChatException = new JChatException(JText::sprintf('COM_JCHAT_ERROR_STORING_DB_DATA', $e->getMessage()), 'error');
			$response->result = false;
			$response->exception_message = $JChatException->getMessage();
			return $response;
		}
	
		return $response;
	}
	
	/**
	 * Store state for a given emoticon
	 *
	 * @access public
	 * @param Object $dataObject
	 * @param Object[] $additionalModels Array for additional injected models type hinted by interface
	 * @return Object
	 */
	public function stateEmoticon($dataObject) {
		// Response JSON object
		$response = new stdClass();
	
		try {
			$query = "UPDATE" .
					 "\n " . $this->_db->quoteName('#__jchat_emoticons') .
					 "\n SET " .
					 "\n " . $this->_db->quoteName('published') . " = " . (int)($dataObject->published) .
					 "\n WHERE " .
					 "\n " . $this->_db->quoteName('id') . " = " . $this->_db->quote($dataObject->id);
			$this->_db->setQuery ( $query );
			$this->_db->execute ();
			if ($this->_db->getErrorNum ()) {
				throw new JChatException(JText::sprintf('COM_JCHAT_ERROR_STORING_DB_DATA', $this->_db->getErrorMsg()), 'error');
			}
	
			// All completed succesfully
			$response->result = true;
		} catch (JChatException $e) {
			$response->result = false;
			$response->exception_message = $e->getMessage();
			return $response;
		} catch (Exception $e) {
			$JChatException = new JChatException(JText::sprintf('COM_JCHAT_ERROR_STORING_DB_DATA', $e->getMessage()), 'error');
			$response->result = false;
			$response->exception_message = $JChatException->getMessage();
			return $response;
		}
	
		return $response;
	}
	
	/**
	 * Return select lists used as filter for listEntities
	 *
	 * @access public
	 * @return array
	 */
	public function getFilters() {
		$filters = array ();
		$filters ['state'] = JHTML::_ ( 'grid.state', $this->getState ( 'state' ) );
		
		return $filters;
	}
}