<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskImap
{
	public $delete = false; // Set if the message should be deleted after being read
	public $thrash = 'Trash'; // Set where the message should be moved if it's not deleted

	const MESSAGE_ID = 0;
	const RECEIVED_DATE = 1;
	const SUBJECT = 2;
	const FROM_ADDRESS = 3;
	const UNREAD = 4;
	const TRY_TO_CONNECT = 3; // Try to connect mail server three times

	/**
	 * This array is resultant array which further contains an associative array as an element.
	 * And the associative array contain attachments, htmlBody, plainBody as its keys.
	 *
	 * @var Array
	 */
	private $result = array();

	/**
	 * This associative array contains attached file name as a key
	 * and corresponding binary file as a value.
	 *
	 * @var Array
	 */
	private $attachments = array();

	/**
	 * This contains standard object of header information
	 *
	 * @var Object
	 */
	private $headers;

	/**
	 * This variable conatains the html part of a message.
	 *
	 * @var String
	 */
	private $htmlBody = '';

	/**
	 * This variable conatains the plain text part of a message.
	 *
	 * @var String
	 */
	private $plainBody = "";

	/**
	 * It consists a mail server name
	 *
	 * @var string
	 */
	private $mailServerName = '';

	/**
	 * User name for the mail account
	 *
	 * @var string
	 */
	private $userName = '';

	/**
	 * Password for the mail account
	 *
	 * @var string
	 */
	private $password = '';


	/**
	 * Parameterised constructor
	 */
	function __construct($mailServerName, $userName, $password)
	{
		$this->mailServerName = $mailServerName;
		$this->userName = $userName;
		$this->password = $password;
	}

	/**
	 * This is the funtion for settion connection to the given mail server.
	 *
	 * @param String $mailbox
	 * @param String $user
	 * @param String $pass
	 * @param IMAP stream as a connection
	 * @return IMAP stream as a connection
	 */
	public function setConnection($mailbox, $user, $pass, $conn = null)
	{

		// If connection is not exist then make a connection
		if (!$conn)
		{
			// If folder name for processing is not present
			if (substr(trim($mailbox), -1) == '}')
			{
				$mailbox = $mailbox . 'INBOX';
			}
			// Try to connect three times
			for ($i = 0; $i < self::TRY_TO_CONNECT; $i++)
			{
				$conn = imap_open($mailbox, $user, $pass);
				if ($conn)
				{
					return $conn;
				}
			}
			$imap_errors = imap_errors();
			$imap_alerts = imap_alerts();
			echo '<p><b>Unable to connect mail server:</b></p>';
			echo '<p>Last error: <br /><span style="color:#f00000;font-weight:bold;">' . imap_last_error() . '</span></p>';
			echo '<p>IMAP Errors: <br /><span style="color:#f00000;font-weight:bold;">' . (is_array($imap_errors) ? implode('<br>', $imap_errors) : 'n/a') . '</span></p>';
			echo '<p>IMAP Alerts: <br /><span style="color:#DBA901;font-weight:bold;">' . (is_array($imap_alerts) ? implode('<br>', $imap_alerts) : 'n/a') . '</span></p>';
			exit(); // User can throw an exception here
		}
		return $conn;
	}

	/**
	 * This is the funtion for getting message
	 *
	 * @param String $conn
	 * @param Integer $messageId
	 */
	public function getMessage($conn, $messageId)
	{

		$structure = imap_fetchstructure($conn, $messageId);

		// If message is not multipart
		if (!isset($structure->parts)) { // && !$structure->parts
			$this->getMessagePart($conn, $messageId, $structure, 0);
		}
		else {
			foreach ($structure->parts as $partno => $part) {
				$this->getMessagePart($conn, $messageId, $part, $partno + 1);
			}
		}
	}

	/**
	 * This is the funtion for parsing messge parts
	 *
	 * @param String $conn
	 * @param Integer $messageId
	 * @param Object $partObj
	 * @param Integer $partno
	 */
	public function getMessagePart($conn, $messageId, $partObj, $partno)
	{

		// If partno is 0 then fetch body as a single part message
		$data = ($partno) ? imap_fetchbody($conn, $messageId, $partno) : imap_body($conn, $messageId);
		/*echo "<p>---$partno---$messageId---</p>";
		print '<pre>';print_r($partObj);print '</pre>';
		if($partno) {
			print '<p>imap_fetchbody:<pre>';
			print_r(imap_fetchbody($conn, $messageId, $partno));
			print '</pre>';
			print '<hr>';
		}else{
			print '<p>imap_body:<pre>';
			print_r(imap_body($conn, $messageId));
			print '</pre>';
			print '<hr>';
		}*/
		// Any part may be encoded, even plain text messages, so decoding it
		if ($partObj->encoding == 4) {
			$data = quoted_printable_decode($data);
		}
		elseif ($partObj->encoding == 3) {
			$data = base64_decode($data);
		}
		$data = str_replace('<o:p>', '', $data);
		$data = str_replace('</o:p>', '', $data);
		$data = preg_replace('/class=".*?"/', '', $data);
		/*print '<pre>';
		echo $data;
		print '</pre>';
		print '<hr>';*/
		// Collection all parameters, like name, filenames of attachments, etc.
		$params = array();
		if ($partObj->parameters) {
			foreach ((array)$partObj->parameters as $x) {
				$params[strtolower($x->attribute)] = $x->value;
			}
		}
		if (isset($partObj->dparameters)) {
			foreach ((array)$partObj->dparameters as $x) {
				$params[strtolower($x->attribute)] = $x->value;
			}
		}

		// Any part with a filename is an attachment
		if (isset($params['filename']) || isset($params['name'])) {
			// Filename may be given as 'Filename' or 'Name' or both
			$filename = isset($params['filename']) ? $params['filename'] : $params['name'];
			$this->attachments[$filename] = $data;
		}

		// Processing plain text message
		if ($partObj->type == 0 && $data) {
			// Messages may be split in different parts because of inline attachments,
			// so append parts together with blank row.
			if (strtolower($partObj->subtype) == 'plain') {
				if (extension_loaded('iconv') && is_array($partObj->parameters) && isset($partObj->parameters[0]->attribute) && strtolower($partObj->parameters[0]->attribute) == 'charset') {
					$this->plainBody .= iconv($partObj->parameters[0]->value, 'utf-8', trim($data));
				} else {
					$this->plainBody .= trim($data);
				}
			}
			else {
				if (extension_loaded('iconv') && is_array($partObj->parameters) && isset($partObj->parameters[0]->attribute) && strtolower($partObj->parameters[0]->attribute) == 'charset') {
					$this->htmlBody .= iconv($partObj->parameters[0]->value, 'utf-8', $data);
				} else {
					$this->htmlBody .= $data;
				}
			}
			/*print '<pre>';
			echo $this->htmlBody;
			print '</pre>';
			print '<hr>';*/
		}

		// Some times it happens that one message embeded in another.
		// This is used to appends the raw source to the main message.
		elseif ($partObj->type == 2 && $data) {
			if (extension_loaded('iconv') && is_array($partObj->parameters) && isset($partObj->parameters[0]->attribute) && strtolower($partObj->parameters[0]->attribute) == 'charset') {
				$this->plainBody .= iconv($partObj->parameters[0]->value, 'utf-8', $data);
			} else {
				$this->plainBody .= $data;
			}
		}

		// Here is recursive call for subpart of the message
		if (isset($partObj->parts)) {
			foreach ((array)$partObj->parts as $partno2 => $part2) {
				$this->getMessagePart($conn, $messageId, $part2, $partno . '.' . ($partno2 + 1));
			}
		}
	}

	/**
	 * This is the funtion for parsing message
	 *
	 * @param String $conn
	 * @param Integer $messageId
	 */
	public function parseMessage($conn, $messageId)
	{
		$this->getMessage($conn, $messageId);
		$this->makeResult();
	}

	/**
	 * Return Received date in to mm-dd-yyyy format
	 *
	 * @param String $date format Thu, 20 Aug 2009 15:55:52 +0530
	 * @return String $date format 08-20-2009
	 */
	private function getRecDate($date)
	{
		$date = substr($date, 5, 20);
		$timestamp = strtotime($date);
		return date('m-d-Y', $timestamp);
	}

	/**
	 * This function is used for checking the particular email id is present
	 * in the from addresses of headder information. It returns true if present,
	 * otherwise returns false.
	 *
	 * @param String $fromAddresses
	 * @param String $userInput
	 */
	private function containsFromAddress($fromAddresses, $userInput)
	{

		if (strpos($fromAddresses, $userInput) != false) {
			return true;
		}
		return false;
	}

	/**
	 * Used for parsing the message by message id.
	 * If flag is false then fuction parse the message with given message id,
	 * otherwise parse only if message with given id is unread.
	 *
	 * @param Integer $messageId
	 * @param Boolean $flag
	 */
	public function parseMessageById($messageId, $flag = false)
	{
		$this->parseMessageByFilter($messageId, self::MESSAGE_ID, $flag);
	}

	/**
	 * Used for parsing unread messages.
	 *
	 */
	public function parseUnreadMessages()
	{
		$this->parseMessageByFilter('', self::UNREAD, true);
	}

	/**
	 * Used for parsing the message by subject of the message.
	 * If flag is false then fuction parse all the messages with given subject,
	 * otherwise parse only unread messages with the given subject.
	 *
	 * @param Integer $subject
	 * @param Boolean $flag
	 */
	public function parseMessagesBySubject($subject, $flag = false)
	{
		$this->parseMessageByFilter($subject, self::SUBJECT, $flag);
	}

	/**
	 * Used for parsing the message by the from address of the messages.
	 * If flag is false then fuction parse all the messages with given from address,
	 * otherwise parse only unread messages with the given from address.
	 *
	 * @param Integer $fromAddress
	 * @param Boolean $flag
	 */
	public function parseMessagesByFromAddress($fromAddress, $flag = false)
	{
		$this->parseMessageByFilter($fromAddress, self::FROM_ADDRESS, $flag);
	}

	/**
	 * Used for parsing the message by received date of the message.
	 * $rdate should be in mm-dd-yyyy format.
	 * If flag is false then fuction parse all the messages with given date,
	 * otherwise parse only unread messages with the given date.
	 *
	 * @param Integer $rdate
	 * @param Boolean $flag
	 */
	public function parseMessagesByRecDate($rdate, $flag = false)
	{
		$this->parseMessageByFilter($rdate, self::RECEIVED_DATE, $flag);
	}

	/**
	 * Used for parsing the message by filter given by user
	 *
	 * @param Integer $userInput
	 * @param Integer $filterType
	 * @param Boolean $flag
	 */
	public function parseMessageByFilter($userInput, $filterType, $flag)
	{
		$conn = $this->setConnection($this->mailServerName, $this->userName, $this->password);
		$emails = imap_search($conn, 'SINCE "' . trim($userInput) . '"', SE_UID);
		$countMsg = imap_num_msg($conn);

		// Iteration on mailbox messages
		for ($i = 1; $i <= $countMsg; $i++) {
			$this->headers = @imap_headerinfo($conn, $i);
			$this->parseMessage($conn, $i);

			if ($this->thrash != '') {
				if ($this->mailServerName == 'pop.gmail.com') {
					imap_mail_move($conn, "$i:$i", $this->thrash);
				}else{
					imap_mail_move($conn, "$i:$i", 'INBOX.' . $this->thrash);
				}
			}

			if ($this->delete) {
				imap_delete($conn, "$i:$i");
			}
		}

		if ($this->delete) {
			imap_expunge($conn);
		}

		imap_close($conn);
	}

	/**
	 * Used for preparing resultant array.
	 *
	 */
	private function makeResult()
	{
		$temp = array();
		$temp['headers'] = $this->headers;
		$temp['attachments'] = isset($this->attachments) ? $this->attachments : array();

		// Body
		if (isset($this->plainBody) && trim($this->plainBody) != '') {
			$temp['body'] = $this->plainBody;
			$temp['body'] = nl2br($temp['body']);
			$temp['body'] = str_replace("<br /><br />", "<br />", $temp['body']);
		} else {
			$temp['body'] = $this->htmlBody;
			$temp['body'] = strip_tags($temp['body'], '<p><br><u><b><i><a>');
			$temp['body'] = str_replace("<br /><br />", "<br />", $temp['body']);
		}
		$temp['body'] = str_replace("\'", "'", $temp['body']);

		$this->result[] = $temp;

		// Unsetting the variables for next message
		unset($this->attachments);
		unset($this->htmlBody);
		unset($this->plainBody);
		unset($this->headers);
	}

	/**
	 * This method returns the resultant array.
	 *
	 * @retuen Array result
	 */
	public function getResult()
	{
		return $this->result;
	}
}
