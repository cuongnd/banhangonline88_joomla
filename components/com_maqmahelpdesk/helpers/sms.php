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

class HelpdeskSMS
{
	static function SendSMS($message, $title=null, $id=0)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$user = JFactory::getUser();

		HelpdeskUtility::ActivityLog('site', 'sms', $title, $id);
		$to = HelpdeskUser::GetMobile($user->id);

		$fields_string = '';
		$url = "http://sms.pswin.com/http4sms/send.asp";
		$fields = array(
			'USER' => $supportConfig->sms_username,
			'PW'   => $supportConfig->sms_password,
			'RCV'  => $to,
			'TXT'  => urlencode($message)
		);

		// Url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_VERBOSE,1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

		// Execute post
		curl_exec($ch);

		// Close connection
		curl_close($ch);
	}
}
