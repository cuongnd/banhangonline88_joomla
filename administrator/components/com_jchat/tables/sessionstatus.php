<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * ORM Table for event entities
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage tables
 * @since 1.0
 */
class TableSessionstatus extends JTable {
	/**
	 *
	 * @var int
	 */
	public $sessionid = null;
	
	/**
	 *
	 * @var string
	 */
	public $status = null;
	
	/**
	 *
	 * @var string
	 */
	public $override_name = null;
	
	/**
	 *
	 * @var string
	 */
	public $email = null;
	
	/**
	 *
	 * @var string
	 */
	public $description = null;
	
	/**
	 *
	 * @var int
	 */
	public $skypeid = null;
	
	/**
	 *
	 * @var int
	 */
	public $roomid = null;
	
	/**
	 *
	 * @var int
	 */
	public $typing = null;
	
	/**
	 *
	 * @var int
	 */
	public $typing_to = null;
	
	/**
	 *
	 * @var string
	 */
	public $geoip = null;
	
	/**
	 *
	 * @var int
	 */
	public $banstatus = null;
	
	/**
	 * Check Table override
	 * @override
	 *
	 * @see JTable::check()
	 */
	public function check() {
		$app = JFactory::getApplication();
		$cparams = $app->getParams('com_jchat');
		
		// Name required
		if (! $this->override_name) {
			$this->setError ( JText::_ ( 'COM_JCHAT_VALIDATION_ERROR_OVERRIDENAME' ) );
			return false;
		}
		
		// Ensure that username does not exists, validation server side
		$userNameFound = false;
		$sessionNameFound = false;
		if($cparams->get('unique_usernames', false)) {
			$query = "SELECT " . $this->_db->quoteName('sessionid') .
					 "\n FROM " . $this->_db->quoteName('#__jchat_sessionstatus') .
					 "\n WHERE " . $this->_db->quoteName('override_name') . " = " . $this->_db->quote($this->override_name) .
					 "\n AND " . $this->_db->quoteName('sessionid') . " != " . $this->_db->quote(session_id());
			$sessionNameFound = $this->_db->setQuery($query)->loadResult();
			
			$query = "SELECT " . $this->_db->quoteName('id') .
					 "\n FROM " . $this->_db->quoteName('#__users') .
					 "\n WHERE " . $this->_db->quoteName('name') . " = " . $this->_db->quote($this->override_name) .
					 "\n OR " . $this->_db->quoteName('username') . " = " . $this->_db->quote($this->override_name);
			$userNameFound = $this->_db->setQuery($query)->loadResult();
		}
		
		if($sessionNameFound || $userNameFound) {
			$this->setError ( JText::_ ( 'COM_JCHAT_VALIDATION_ERROR_OVERRIDENAME_EXISTS' ) );
			return false;
		}
		
		if($cparams->get('validate_email', false) && !$this->email) {
			$this->setError ( JText::_ ( 'COM_JCHAT_VALIDATION_ERROR_EMAIL' ) );
			return false;
		}
		
		// Link url required and to be valid
		if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->setError ( JText::_('COM_JCHAT_VALIDATION_ERROR_EMAIL' ) );
			return false;
		}
		
		if($cparams->get('validate_description', false) && !$this->description) {
			$this->setError ( JText::_ ( 'COM_JCHAT_VALIDATION_ERROR_DESC' ) );
			return false;
		}
		
		// Validate antispam result
		if($cparams->get('show_antispam', false)) {
			// Get addendi and check operations
			$operand1 = $app->input->post->getInt('validation_op1');
			$operand2 = $app->input->post->getInt('validation_op2');
			$result = $app->input->post->getInt('validation_result');
			if(($operand1 + $operand2) != $result) {
				$this->setError ( JText::_('COM_JCHAT_VALIDATION_ERROR_ANTISPAM' ) );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Class constructor
	 *
	 * @param Object& $_db
	 *        	return Object&
	 */
	public function __construct($_db) {
		$primaryKey = 'sessionid';
		
		// Check if the session id is not already present in sessionstatus and if so force an insert store by faking primary key
		$query = "SELECT COUNT(*)" .
				 "\n FROM " . $_db->quoteName('#__jchat_sessionstatus') .
				 "\n WHERE " . $_db->quoteName('sessionid') . " = " . $_db->quote(session_id());
		$sessionRowExists = $_db->setQuery($query)->loadResult();
		if(!$sessionRowExists) {
			$primaryKey = array('sessionid', 'status');
		}
		
		parent::__construct ( '#__jchat_sessionstatus', $primaryKey, $_db );
	}
}