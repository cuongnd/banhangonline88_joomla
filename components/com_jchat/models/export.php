<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::EXPORT::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Save conversation model public responsibilities
 *
 * @package JCHAT::EXPORT::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface IExportModel {
	/**
	 * Get data to export and send to output buffer, no view needed
	 *
	 * @access public
	 * @return void
	 */
	public function getData();
}

/**
 * Save conversation model
 * 
 * @package JCHAT::EXPORT::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelExport extends JChatModel implements IExportModel{
	/**
	 * @access private
	 * @var Object&
	 */
	private $user;
	
	/**
	 * @access private
	 * @var Object&
	 */
	private $myUserObject;
	
	/**
	 * @access private
	 * @var Object&
	 */
	private $nameType;
	
	/**
	 * @access private
	 * @var Object&
	 */
	private $userConversation;
	
	/**
	 * SESSION array reference
	 * @access private
	 * @var array&
	 */
	private $session;
	
	/**
	 * SESSION array reference
	 * @access private
	 * @var array&
	 */
	private $componentConfig;
	
	/**
	 * User session table Object
	 * @access private
	 * @var Object &
	 */
	private $userSessionTable;
	
	/**
	 * Get data to export and send to output buffer, no view needed
	 * 
	 * @access public
	 * @param array $conversation
	 * @return void
	 */
	public function getData($conversation = null) {
		// Standard load conversation by session
		if(!$conversation) {
			$userChatID = $this->getState('userChatId');
			// Try to load other user conversation user name if logged in
			if($userChatID != 'wall') {
				$this->userSessionTable->load($userChatID);
				$otherUser = JFactory::getUser($this->userSessionTable->userid);
				if(!$this->userConversation = $otherUser->{$this->nameType}) {
					$this->userConversation = JChatHelpersUsers::generateRandomGuestNameSuffix($this->userSessionTable->session_id, $this->componentConfig);
				}
			} else {
				$userChatID = 'wall';
				$this->userConversation = '-groupchat-';
			}
			
			$conversation = $this->session['jchat_user_' . $userChatID];
		} else {
			// Render explicit conversation by private messaging
		}
		$exportConversationString = '';
		if(is_array($conversation)) {
			foreach ($conversation as $message) {
				// Decisione sul contenuto del messaggio
				switch (@$message['type']){
					case 'file':
						$renderedMessage = 'FILE[' . $message['message'] . ']';
						break;
						
					case 'message':
					default:
						$renderedMessage = JChatHelpersMessages::purifyMessage($message['message'], $this->componentConfig);
						break;
				}
				// Decisione sul sender del messaggio
				if(!isset($message['self'])) {
					$message['self'] = (bool)($message['fromuserid'] == $this->myUserObject->id); 
				}
				if(!(bool)$message['self']) {
					if($this->userConversation !== '-groupchat-') {
						$sender = $this->userConversation;
					} else {
						// Get sender from message at the moment of sending, to get always last if changed it would require re-evaluation for each message here
						$sender = $message['fromuser'];
					}
				} else {
					$sender = $this->user;
				}
				$exportConversationString .= $sender . ": " . $renderedMessage . PHP_EOL;
			}
		}

		// Export file txt
		$cont_dis = 'attachment';
		$mimeType = 'text/plain';
		$filename = $this->user . '-' . $this->userConversation . '-' . date('Y-m-d') . '.txt';
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get ( 'zlib.output_compression' )) {
			ini_set ( 'zlib.output_compression', 'Off' );
		} 
		$size = strlen($exportConversationString);	
	 	$mod_date = date ( 'r' ); 
	 	//Output del file 
	 	header ( "Pragma: public" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Expires: 0" ); 
		header ( 'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $size . ';' ); //RFC2183
		header ( "Content-Type: " . $mimeType ); // MIME type
		header ( "Content-Length: " . $size ); 
	 	echo $exportConversationString;
	 	exit();
	}
	
	/**
	 * Get data to export and send to output buffer, no view needed
	 *
	 * @access public
	 * @param int $userID
	 * @param int $oldestMessageID
	 * @return void
	 */
	public function getDataMessaging($fromLoggedID, $oldestMessageID) {
		// Load the other username
		$otherUserObject = JFactory::getUser($fromLoggedID);
		$this->userConversation = $otherUserObject->{$this->nameType};

		// Load messages in the current private messaging conversation between 2 registered users
		$sql = 	"SELECT cchat.id, cchat.from, cchat.fromuser AS " . $this->_db->quoteName('fromuserid') . "," .
				"\n cchat.message, cchat.sent, cchat.read, cchat.type, cchat.status, u.id AS userid, " .
				"\n u.{$this->nameType} AS " . $this->_db->quoteName('fromuser') .
				"\n FROM #__jchat AS cchat" .
				"\n INNER JOIN #__users AS u ON cchat.fromuser = u.id" .
				"\n WHERE ((cchat.touser = ". $this->_db->quote($this->myUserObject->id) .
				"\n AND cchat.fromuser = " . $this->_db->quote($fromLoggedID) . ")" .
				"\n OR (cchat.fromuser = ". $this->_db->quote($this->myUserObject->id) .
				"\n AND cchat.touser = " . $this->_db->quote($fromLoggedID) . "))" .
				"\n AND cchat.id >= " . (int)$oldestMessageID .
				"\n ORDER BY cchat.id ASC";
	
		$this->_db->setQuery($sql);
		$conversation = $this->_db->loadAssocList();

		// Render the explicit conversation
		$this->getData($conversation);
	}
	
	/**
	 * Class contructor
	 *
	 * @access public
	 * @param $config array
	 * @return Object
	 */
	public function __construct($config = array()) {
		$this->session = $_SESSION;
		$this->componentConfig = JComponentHelper::getParams('com_jchat');
		// Config per esportazione nomi utenti
		$this->nameType = $this->componentConfig->get('usefullname');
		
		// User session table instance
		$this->userSessionTable = $config['sessiontable'];
		
		// Try to load my user name by session ID if user logged in
		$myUser = JFactory::getUser();
		$this->myUserObject = $myUser;
		if(!$this->user = $myUser->{$this->nameType}) {
			$this->user = JChatHelpersUsers::generateRandomGuestNameSuffix($this->userSessionTable->session_id, $this->componentConfig);
		}
		
		parent::__construct($config);
	}
}