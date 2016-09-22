<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');


class EMailQueue
{
	function EMailQueue()
	{
		//constructor
	}

	function sendOnPageLoad($max = 5)
	{
		$db 		= EasyBlogHelper::db();
		$config 	= EasyBlogHelper::getConfig();
		$sendHTML   = $config->get('main_mailqueuehtmlformat', 0);

		// Delete existing mails that has already been sent.
		$query  = 'DELETE FROM ' . $db->nameQuote( '#__easyblog_mailq' ) . ' WHERE '
		   . $db->nameQuote( 'status' ) . '=' . $db->Quote( 1 )
		   . ' AND DATEDIFF(NOW(), `created`) >= 7';

		$db->setQuery( $query );
		$db->Query();

		$query  = 'SELECT `id` FROM `#__easyblog_mailq` WHERE `status` = 0';
		$query  .= ' ORDER BY `created` ASC';
		$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if(! empty($result))
		{
			foreach($result as $mail)
			{
				$mailq	= EasyBlogHelper::getTable( 'MailQueue', 'Table' );
				$mailq->load($mail->id);

				// update the status to 1 == proccessed
				$mailq->status  = 1;
				if( $mailq->store() )
				{
					// Send emails out.
					if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
					{
						$mail   	= JFactory::getMailer();
						$mail->sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body, $sendHTML);
					}
					else
					{
						JUtility::sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body, $sendHTML);
					}

				}
			}
		}

	}

}
