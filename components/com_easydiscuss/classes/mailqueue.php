<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_HELPERS . '/router.php';

class DMailQueue
{
	function sendOnPageLoad()
	{
		$db 	= DiscussHelper::getDBO();
		$config	= DiscussHelper::getConfig();
		$max	= (int) $config->get('main_mailqueuenumber');

		// Delete existing mails that has already been sent.
		$query		= 'DELETE FROM ' . $db->nameQuote( '#__discuss_mailq' ) . ' WHERE '
					. $db->nameQuote( 'status' ) . '=' . $db->Quote( 1 )
					. ' AND DATEDIFF(NOW(), `created`) >= 30';

		$db->setQuery( $query );
		$db->Query();

		$query  = 'SELECT `id` FROM `#__discuss_mailq` WHERE `status` = 0';
		$query  .= ' ORDER BY `created` ASC';
		$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if(! empty($result))
		{
			foreach($result as $mail)
			{
				$mailq	= DiscussHelper::getTable( 'MailQueue' );
				$mailq->load($mail->id);

				// update the status to 1 == proccessed
 				$mailq->status  = 1;
 				if( $mailq->store() )
				{
					if( DiscussHelper::getJoomlaVersion() > '1.6' )
					{
						$mail   	= JFactory::getMailer();
						$result 	= $mail->sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body , $mailq->ashtml );
	 				}
					else
					{
						JUtility::sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body , $mailq->ashtml );
					}

				}

				//end foreach
			}
		}

	}

	public function parseEmails()
	{
		require_once DISCUSS_CLASSES . '/mailbox.php';

		$mailer	= new DiscussMailer();
		$state	= $mailer->connect();

		// @task: Only search for messages that are new.
		$unread	= $mailer->searchMessages( 'UNSEEN' );

		if( !$state )
		{
			echo JText::_( 'COM_EASYDISCUSS_UNABLE_TO_CONNECT_TO_MAIL_SERVER' );
			return false;
		}

		if( !$unread )
		{
			echo JText::_( 'COM_EASYDISCUSS_NO_EMAILS_TO_PARSE' );
			return false;
		}

		$filter		= JFilterInput::getInstance();
		$config		= DiscussHelper::getConfig();
		$total		= 0;

		$replyBreaker = $config->get('mail_reply_breaker');

		foreach( $unread as $sequence )
		{
			$info		= $mailer->getMessageInfo( $sequence );
			$from		= $info->from;
			$senderName	= $from[0]->personal;
			$subject	= $filter->clean( $info->subject );

			// @rule: Detect if this is actually a reply.
			preg_match( '/\[\#(.*)\]/is' , $subject , $matches );

			$isReply	= !empty( $matches );
			$message	= new DiscussMailerMessage( $mailer->stream , $sequence );

			$post		= DiscussHelper::getTable( 'Post' );
			$post->set( 'title'		, $subject );
			$post->set( 'content' 	, $message->getPlain() );
			$post->set( 'published'	, DISCUSS_ID_PUBLISHED );
			$post->set( 'created'	, DiscussHelper::getDate()->toMySQL() );
			$post->set( 'replied'	, DiscussHelper::getDate()->toMySQL() );
			$post->set( 'modified'	, DiscussHelper::getDate()->toMySQL() );


			if( $isReply && !$config->get( 'main_email_parser_replies') )
			{
				continue;
			}

			if( $isReply )
			{
				$parentId	= (int) $matches[1];
				$post->set( 'parent_id' , $parentId );

				// Trim content, get text before the defined line
				if( $replyBreaker ) {
					if( $pos = JString::strpos($post->content, $replyBreaker) ) {
						$post->content = JString::substr($post->content, 0, $pos);
					}
				}

				$parent		= DiscussHelper::getTable( 'Post' );
				$parent->load( $parentId );

				$post->set( 'category_id' , $parent->category_id );
			}
			else
			{
				// @TODO: Make this category configurable from the back end?
				$post->set( 'category_id' , $config->get( 'main_email_parser_category' ) );
			}

			// @rule: Map the sender's email with the user in Joomla?
			$replyToEmail	= $info->fromemail;

			// Lookup for the user based on their email address.
			$user			= DiscussHelper::getUserByEmail( $replyToEmail );

			if( $user instanceof JUser )
			{
				$post->set( 'user_id'	, $user->get( 'id' ) );
				$post->set( 'user_type'	, DISCUSS_POSTER_MEMBER );
			}
			else
			{
				// Guest posts
				$post->set( 'user_type' 	, DISCUSS_POSTER_GUEST );
				$post->set( 'poster_name'	, $senderName );
				$post->set( 'poster_email'	, $replyToEmail );
			}

			// check if guest can post question or not. if not skip the processing.
			if( $post->get( 'user_type') == DISCUSS_POSTER_GUEST)
			{
				$acl = DiscussHelper::getHelper( 'ACL', '0' );
				if(! $acl->allowed('add_question') )
					continue;
			}

			if( $config->get( 'main_email_parser_moderation' ) )
			{
				$post->set( 'published' , DISCUSS_ID_UNPUBLISHED );
			}

			// @rule: Process the post
			$post->store();

			// Send notification email to the subscribers in the thread.
			if( $isReply && $post->get( 'published') == DISCUSS_ID_PUBLISHED )
			{
				self::replyNotifyUsers( $post , $user , $senderName );
			}


			// @task: Increment the count.
			$total	+= 1;

			// @rule: Only send autoresponders when it's a new post.
			if( !$isReply && $config->get( 'main_email_parser_receipt' ) && $post->get( 'published' ) == DISCUSS_ID_PUBLISHED )
			{
				$sendAsHTML	= (bool) $config->get( 'notify_html_format' );

				$theme		= new DiscussThemes();
				$postId		= $post->get( 'id' );

				if( $isReply )
				{
					$postId	= $parentId;
				}

				$url		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $postId , false , true );


				$emailData 				= array();
				$emailData['postLink']	= $url;

				if( $post->get( 'user_type') == DISCUSS_POSTER_GUEST )
				{
					$emailData[ 'postAuthor' ]	= $senderName;
				}
				else
				{
					$profile 	= DiscussHelper::getTable( 'Profile' );
					$profile->load( $user->id );

					$emailData['postAuthor' ]	= $profile->getName();
				}

				require_once DISCUSS_CLASSES . '/notification.php';
				$notification	= new DNotification();
				$output 		= $notification->getEmailTemplateContent( 'email.accepted.responder.php' , $emailData );

				$app		= JFactory::getApplication();

				if( !$sendAsHTML )
				{
					$output	= strip_tags( $output );
				}

				// @rule: Send confirmation message.
				JUtility::sendMail( $app->getCfg( 'mailfrom' ) , $app->getCfg( 'fromname' ) , $replyToEmail , '[#' . $post->id . ']: ' . $subject , $output , $sendAsHTML );
			}
		}

		echo JText::sprintf( 'COM_EASYDISCUSS_EMAIL_PARSED' , $total );

		return true;
	}

	public function replyNotifyUsers( $reply , $user , $senderName )
	{
		//send notification to all comment's subscribers that want to receive notification immediately
		$notify		= DiscussHelper::getNotification();
		$emailData	= array();
		$config		= DiscussHelper::getConfig();

		$parent		= DiscussHelper::getTable( 'Post' );
		$parent->load( $reply->parent_id );

		if( $reply->get( 'user_type') == DISCUSS_POSTER_GUEST )
		{
			$emailData['postAuthor']	= $senderName;
			$emailData['commentAuthor']	= $senderName;
			$emailData['replyAuthorAvatar' ]	= '';
		}
		else
		{
			$profile 	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $user->id );

			$emailData['replyAuthor' ]			= $profile->getName();
			$emailData['commentAuthor']			= $profile->getName();
			$emailData['replyAuthorAvatar' ]	= $profile->getAvatar();
		}

		$emailContent = $reply->content;

		if( $reply->content_type != 'html' )
		{
			// the content is bbcode. we need to parse it.
			$emailContent	= EasyDiscussParser::bbcode( $emailContent);
			$emailContent	= EasyDiscussParser::removeBrTag( $emailContent);
		}

		// If reply is html type we need to strip off html codes.
		if( $reply->content_type == 'html' )
		{
			$emailContent 			= strip_tags( $emailContent );
		}

		$emailContent	= $parent->trimEmail( $emailContent );

		$emailData['postTitle']		= $parent->title;
		$emailData['comment']		= $reply->content;
		$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parent->id, false, true);
		$emailData['replyContent']	= $reply->content;


		$excludeEmails = array();
		$subscriberEmails			= array();

		if( ($config->get('main_sitesubscription') ||  $config->get('main_postsubscription') ) && $config->get('notify_subscriber') && $reply->published == DISCUSS_ID_PUBLISHED)
		{
			$emailData['emailTemplate']	= 'email.subscription.reply.new.php';
			$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);

			$emailData['post_id'] = $parent->id;
			$emailData['cat_id'] = $parent->category_id;
			$subcribersEmails = DiscussHelper::getHelper( 'Mailer' )->notifyThreadSubscribers( $emailData );

			$excludeEmails		= array_merge( $excludeEmails, $subcribersEmails);
		}

		//notify post owner.
		$postOwnerId	= $parent->user_id;
		$postOwner		= JFactory::getUser( $postOwnerId );
		$ownerEmail		= $postOwner->email;

		if( $parent->user_type != 'member' )
		{
			$ownerEmail 	= $parent->poster_email;
		}

		if( $config->get( 'notify_owner' ) && $reply->published	== DISCUSS_ID_PUBLISHED && ($postOwnerId != $user->id) && !in_array( $ownerEmail , $subscriberEmails ) && !empty( $ownerEmail ) )
		{
			$emailData['owner_email'] = $ownerEmail;
			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.new.php';
			DiscussHelper::getHelper( 'Mailer' )->notifyThreadOwner( $emailData );

			$excludeEmails[] = $ownerEmail;
		}

		// Notify Participants
		if( $config->get( 'notify_participants' ) && $table->published	== DISCUSS_ID_PUBLISHED )
		{
			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.new.php';
			DiscussHelper::getHelper( 'Mailer' )->notifyThreadParticipants( $emailData, $excludeEmails );
		}

		//if reply under moderation, send owner a notification.
		if( $reply->published == DISCUSS_ID_PENDING )
		{
			// Generate hashkeys to map this current request
			$hashkey		= DiscussHelper::getTable( 'Hashkeys' );
			$hashkey->uid	= $reply->id;
			$hashkey->type	= DISCUSS_REPLY_TYPE;
			$hashkey->store();

			$approveURL	= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
			$rejectURL	= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
			$emailData[ 'moderation' ]	= '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
			$emailData[ 'moderation' ] .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
			$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
			$emailData[ 'moderation' ] .= '</div>';

			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.moderation.php';

			DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin' ), $config->get( 'notify_moderator' ) );

		} elseif( $table->published	== DISCUSS_ID_PUBLISHED ) {

			$emailData['emailTemplate']	= 'email.post.reply.new.php';
			$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['post_id'] = $parent->id;

			DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin_onreply' ), $config->get( 'notify_moderator_onreply' ) );
		}
	}
}
