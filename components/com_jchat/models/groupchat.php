<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::GROUPCHAT::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group users chat model
 * 
 * @package JCHAT::GROUPCHAT::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelGroupchat extends JChatModel {
	/**
	 * Owner user ID
	 * @access private
	 * @var int
	 */
	private $ownerID;
	
	/**
	 * Contact user ID
	 * @access private
	 * @var int
	 */
	private $contactID;
	
	/**
	 * 
	 * Store contact user ID for current owner
	 * 
	 * @param int $contactID
	 * @access public
	 * @return boolean
	 */
	public function storeEntity($contactID = null) {
		$query = "INSERT INTO #__jchat_public_sessionrelations (ownerid, contactid)" .
				 "\n VALUES (" . $this->_db->quote($this->ownerID) . ',' . 
				 $this->_db->quote($contactID) . ')';
		$this->_db->setQuery($query);
		
		try {
			$this->_db->execute ();
			if($this->_db->getErrorNum()) {
				throw new JChatException(JText::_('COM_JCHAT_ERROR_STORE_CONTACT_GROUPCHAT') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->response['storing'] = array('status'=>false, 'details'=>$e->getMessage());
			return $this->response;
				
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->response['storing'] = array('status'=>false, 'details'=>$jchatException->getMessage());
			return $this->response;
		}
		
		$this->response['storing'] = array('status'=>true);
		
		return $this->response;
	}
 
	/**
	 * Delete contact user id for current owner
	 * 
	 * @param int $contactID
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($contactID) {
		$query = "DELETE FROM #__jchat_public_sessionrelations" . 
				 "\n WHERE (ownerid = " . $this->_db->quote($this->ownerID) .
				 "\n AND contactid = " . $this->_db->quote($contactID) . ")" .
				 "\n OR (contactid = " . $this->_db->quote($this->ownerID) .
				 "\n AND ownerid = " . $this->_db->quote($contactID) . ")";
		$this->_db->setQuery($query);

		try {
			$this->_db->execute ();
			if($this->_db->getErrorNum()) {
				throw new JChatException(JText::_('COM_JCHAT_ERROR_DELETE_CONTACT_GROUPCHAT') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->response['storing'] = array('status'=>false, 'details'=>$e->getMessage());
			return $this->response;
				
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->response['storing'] = array('status'=>false, 'details'=>$jchatException->getMessage());
			return $this->response;
		}
		
		$this->response['storing'] = array('status'=>true);
		
		return $this->response;
	}
	
	/**
	 * Class constructor
	 * @access public
	 * @param Object& $wpdb
	 * @param Object& $userObject
	 * @return Object &
	 */
	public function __construct($config = array()) {
		$this->ownerID = $config['sessiontable']->session_id;
		
		parent::__construct($config);
	}
}