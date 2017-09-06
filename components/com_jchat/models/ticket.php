<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::LAMESSAGES::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group users chat model
 * 
 * @package JCHAT::LAMESSAGES::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelTicket extends JChatModel {
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
	 * @param array $body
	 * @access private
	 * @return boolean
	 */
	private function sendEmail($mailer, $recipients, $subject, $body) {
		// Build e-mail message format
		$mailer->setSender(array($this->componentParams->get('tickets_mailfrom', $this->jConfig->get('mailfrom')), 
								 $this->componentParams->get('tickets_fromname', $this->jConfig->get('fromname'))));
		$mailer->setSubject($subject);
		
		/**
		 * Format a full body for the notification email
		 */
		$bodyText = JText::sprintf('COM_JCHAT_BODY_NAME', $body['name']);
		$bodyText .= JText::sprintf('COM_JCHAT_BODY_EMAIL', $body['email']);
		$bodyText .= JText::sprintf('COM_JCHAT_BODY_MESSAGE', $body['message']);
		
		$mailer->setBody($bodyText);
		$mailer->IsHTML(true);
	
		// Add recipient
		$mailer->addRecipient($recipients);
	
		// Send the Mail
		$rs	= $mailer->sendUsingExceptions();
	
		// Check for an error
		return $rs;
	}
	
	/**
	 * Store ticket entity
	 * 
	 * @access public
	 * @param Object $mailer
	 * @return mixed
	 */
	public function storeEntity($mailer = null) {
		// Init vars
		$table = $this->getTable ('lamessages');
		$connectedUserObject = JFactory::getUser();
		$whereOR = null;
		$subject = null;
		
		if($connectedUserObject->id) {
			$whereOR = "\n OR " . $this->_db->quoteName('userid') . " = " . (int)$connectedUserObject->id;
		}
		
		try {
			// Check if email address or logged in userid is still associated to an open ticket
			$checkQuery = "SELECT id" .
						  "\n FROM #__jchat_lamessages" .
						  "\n WHERE (" . $this->_db->quoteName('email') . " = " . $this->_db->quote(trim($this->app->input->getString('email'))) .
						  $whereOR . ")" .
						  "\n AND " . $this->_db->quoteName('closed_ticket') . " = 0" .
						  "\n ORDER BY id DESC";
			$openedTicket = $this->_db->setQuery($checkQuery)->loadResult();
			// Continue to propagate the currently opened ticket
			if($openedTicket) {
				// Open ticket, so load existing one and add a reply response from customer
				if (! $table->load ( $openedTicket )) {
					throw new JChatException ( $table->getError (), 'error' );
				}
				$table->responses[] = array (
							JDate::getInstance()->toSql(),
							$this->app->input->getString('message'),
							$table->name
						);
				$table->responses = serialize ( $table->responses );
				$subject = JText::sprintf('COM_JCHAT_REPLYSUBJECT', $table->id, $table->sentdate);
			} else {
				// No open ticket, so bind standard and create a new one ticket
				if (! $table->bind ( $this->app->input->getArray(), true )) {
					throw new JChatException ( $table->getError (), 'error' );
				}
				// Additional fields
				$table->sentdate = date('Y-m-d', time());
			}
			// Always try to update the user identifier
			$table->userid = (int)$connectedUserObject->id;
			
			if (! $table->check ()) {
				throw new JChatException ( $table->getError (), 'error' );
			}
				
			if (! $table->store ()) {
				throw new JChatException ( $table->getError (), 'error' );
			}
			
			// Fallback on the new subject id, newly created
			if(!$subject) {
				$subject = JText::sprintf('COM_JCHAT_NEWSUBJECT', $table->id, $table->sentdate);
			}
			
			// Check if email notifications are enabled, and if so send an email to agents addresses
			if($this->componentParams->get('ticket_sent_notify', 0)) {
				// Check for notify email addresses
				$validEmailAddresses = array();
				$emailAddresses = $this->componentParams->get('ticket_notify_emails', '');
				$emailAddresses = explode(',', $emailAddresses);
				if(!empty($emailAddresses)) {
					foreach ($emailAddresses as $validEmail) {
						if(filter_var(trim($validEmail), FILTER_VALIDATE_EMAIL)) {
							$validEmailAddresses[] = trim($validEmail);
						}
					}
				}
				
				// Check if email of ticket memo has to be send also to user sending
				$senderEmail = $this->app->input->getString('email');
				if(filter_var(trim($senderEmail), FILTER_VALIDATE_EMAIL)) {
					$validEmailAddresses[] = trim($senderEmail);
				}
				
				// If valid email addresses detected send the notification
				if(!empty($validEmailAddresses)) {
					// Send mail response
					$this->sendEmail($mailer, 
									 $validEmailAddresses, 
									 $subject,
									 array('message'=>$this->app->input->getString('message'),
									 	   'name'=>$table->name,
									 	   'email'=>$table->email)
					);
				}
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
	 * Class constructor
	 * @access public
	 * @param Object& $wpdb
	 * @param Object& $userObject
	 * @return Object &
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Get component params
		$this->getComponentParams();
		$this->jConfig = JFactory::getConfig();
	}
}