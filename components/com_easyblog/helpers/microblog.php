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

class EasyBlogMicroBlogHelper
{
	function process()
	{
		$this->processCommentMailbox();
		echo "<br />";
		$this->processMailbox();
		echo "<br />";
		$this->processTwitter();
	}


	public function processMailbox()
	{
		/*
		 * Check enabled
		 */
		$config		= EasyBlogHelper::getConfig();
		$debug		= JRequest::getBool( 'debug', false );

		if (!$config->get( 'main_remotepublishing_mailbox' ) && !$config->get( 'main_comment_email' ) )
		{
			return;
		}

		/*
		 * Check Prerequisites setting
		 */
		$userid 	= 0;

		if( $config->get( 'main_remotepublishing_mailbox_userid' ) == 0 && !$config->get( 'main_remotepublishing_mailbox_syncuser' ) )
		{
			echo 'Mailbox: Unspecified default user id.' . "<br />\n";
			return false;
		}

		/*
		 * Check time interval
		 */

		$interval	= (int) $config->get( 'main_remotepublishing_mailbox_run_interval' );
		$nextrun	= (int) $config->get( 'main_remotepublishing_mailbox_next_run' );
		$nextrun	= EasyBlogHelper::getDate($nextrun)->toUnix();
		$timenow	= EasyBlogHelper::getDate()->toUnix();

		if ($nextrun !== 0 && $timenow < $nextrun)
		{
			if (!$debug)
			{
				echo 'time now: ' . EasyBlogHelper::getDate( $timenow )->toMySQL() . "<br />\n";
				echo 'next email run: ' . EasyBlogHelper::getDate( $nextrun )->toMySQL() . "<br />\n";
				return;
			}
		}

		$txOffset	= EasyBlogDateHelper::getOffSet();
		$newnextrun	= EasyBlogHelper::getDate('+ ' . $interval . ' minutes', $txOffset)->toUnix();

		// use $configTable to avoid variable name conflict
		$configTable		= EasyBlogHelper::getTable('configs');
		$configTable->load('config');
		$parameters = EasyBlogHelper::getRegistry($configTable->params);

		$parameters->set( 'main_remotepublishing_mailbox_next_run' , $newnextrun );
		$configTable->params = $parameters->toString('ini');

		$configTable->store();

		/*
		 * Connect to mailbox
		 */
		require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'mailbox.php');
		$mailbox		= new EasyblogMailbox;
		if (!$mailbox->connect())
		{
			$mailbox->disconnect();
			echo 'Mailbox: Could not connect to mailbox.';
			return false;
		}

		/*
		 * Get data from mailbox
		 */
		$total_mails	= $mailbox->getMessageCount();

		if ($total_mails < 1)
		{
			// No mails in mailbox
			$mailbox->disconnect();
			echo 'Mailbox: No emails found.';
			return false;
		}

		// Let's get the correct mails
		$prefix			= $config->get( 'main_remotepublishing_mailbox_prefix' );

		$search_criteria	= 'UNSEEN';

		if( !empty( $prefix ) )
		{
			$search_criteria	.= ' SUBJECT "'.$prefix.'"';
		}

		$sequence_list	= $mailbox->searchMessages( $search_criteria );

		if( $sequence_list===false )
		{
			// Email with matching subject not found
			$mailbox->disconnect();
			echo 'Mailbox: No matching mails found. ' . $search_criteria;
			echo ($debug) ? ' criteria: '.$search_criteria.' ' : '';
			return false;
		}

		/*
		 * Found the mails according to prefix,
		 * Let's process each of them
		 */
		$total	= 0;
		$enable_attachment	= $config->get( 'main_remotepublishing_mailbox_image_attachment' );
		$format				= $config->get( 'main_remotepublishing_mailbox_format' );
		$limit			 	= $config->get( 'main_remotepublishing_mailbox_fetch_limit' );

		// there's not limit function for imap, so we work around with the array
		// get the oldest message first
		sort($sequence_list);
		$sequence_list	= array_slice($sequence_list, 0, $limit);

		foreach ($sequence_list as $sequence)
		{
			// first, extract from the header
			$msg_info	= $mailbox->getMessageInfo($sequence);

			if ($msg_info === false)
			{
				// If unrecognized email from mailbox, mark as seen.
				$this->markAsSeen($mailbox, $sequence);

				echo 'Mailbox: Could not get message header.';
				echo ($debug) ? ' sequence:'.$sequence.' ' : '';
				continue;
			}

			$uid		= $msg_info->message_id;
			$date		= $msg_info->MailDate;
			$udate		= $msg_info->udate;
			$size		= $msg_info->Size;
			$subject	= $msg_info->subject;
			$from       = '';
			if( isset( $msg_info->from ) )
			{
				$senderInfo	= $msg_info->from[0];
				if( !empty( $senderInfo->mailbox ) && ! empty($senderInfo->host) )
					$from       = $senderInfo->mailbox . '@' . $senderInfo->host;
			}

			if( empty( $from ) )
			{
				$from		= $msg_info->fromemail;
			}

			// @rule: Try to map the sender's email to a user email on the site.
			if( $config->get( 'main_remotepublishing_mailbox_syncuser' ) )
			{
				$db		= EasyBlogHelper::db();
				$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'email' ) . '=' . $db->Quote( $from );
				$db->setQuery( $query );
				$userid 	= $db->loadResult();

				// Check if they have permissions
				if( $userid )
				{
					$acl = EasyBlogACLHelper::getRuleSet( $userid );

					if( !$acl->rules->add_entry )
					{
						continue;
					}
				}
			}
			else
			{
				// sync user email is not require. use the default selected user.
				$userid		= $config->get( 'main_remotepublishing_mailbox_userid' );
			}

			if( $userid == 0 )
			{
				// If unrecognized email from mailbox, mark as seen.
				$this->markAsSeen($mailbox, $sequence);

				echo 'Mailbox: Unable to detect the user based on the email ' . $from . "<br />\n";
				echo ($debug) ? ' sequence:'.$sequence.' ' : '';
				continue;
			}

			$date		= EasyBlogHelper::getDate($date);
			$date		= $date->toMySQL();

			$subject	= str_ireplace($prefix, '', $subject);
			$filter		= JFilterInput::getInstance();
			$subject	= $filter->clean($subject, 'string');

			// @task: If subject is empty, we need to append this with a temporary string. Otherwise user can't edit it from the back end.
			if( empty( $subject ) )
			{
				$subject	= JText::_( 'COM_EASYBLOG_MICROBLOG_EMPTY_SUBJECT' );
			}

			// filter email according to the whitelist
			$filter		= JFilterInput::getInstance();
			$whitelist	= $config->get( 'main_remotepublishing_mailbox_from_whitelist' );
			$whitelist	= $filter->clean($whitelist, 'string');
			$whitelist	= trim($whitelist);

			if (!empty($whitelist))
			{
				// Ok. I bluffed we only accept comma seperated values. *wink*
				$pattern	= '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';

				preg_match_all( $pattern, $whitelist, $matches );
				$emails		= $matches[0];

				if (!in_array($from, $emails))
				{
					// If unrecognized email from mailbox, mark as seen.
					$this->markAsSeen($mailbox, $sequence);
									
					echo 'Mailbox: Message sender is block: #'.$sequence.' '.$subject;
					continue;
				}
			}


			// this is the magic
			$message	= new EasyblogMailboxMessage($mailbox->stream, $sequence);
			$message->getMessage();

			$html		= $message->getHTML();
			$plain		= $message->getPlain();
			$plain		= nl2br($plain);
			$body		= ($format=='html') ? $html : $plain;
			$body		= $body ? $body : $plain;


			// If plain text is empty, just fall back to html
			if( empty($plain ) )
			{
				$body 	= nl2br( strip_tags( $html ) );
			}
			
			$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
			// JFilterInput doesn't strip css tags
			$body	= preg_replace("'<style[^>]*>.*?</style>'si", '', $body);
			$body	= $safeHtmlFilter->clean($body, 'html');
			$body	= trim($body);

			$attachments	= array();

			if ($enable_attachment)
			{
				$attachments	= $message->getAttachment();

				// process attached images
				if (!empty($attachments))
				{
					$config				= EasyBlogHelper::getConfig();
					$main_image_path	= $config->get('main_image_path');
					$main_image_path	= rtrim($main_image_path, '/');

					$rel_upload_path	= $main_image_path . '/' . $userid;

					$userUploadPath		= JPATH_ROOT . DIRECTORY_SEPARATOR . $main_image_path . DIRECTORY_SEPARATOR . $userid;
					$userUploadPath		= JPath::clean($userUploadPath);

					$dir				= $userUploadPath . DIRECTORY_SEPARATOR;
					$tmp_dir			= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

					$uri				= JURI::base().$main_image_path.'/'.$userid.'/';

					if(! JFolder::exists($dir))
					{
						JFolder::create($dir);
					}

					foreach ($attachments as $attachment)
					{

						// clean up file name
						if(strpos($attachment['name'], '/') !== FALSE)
						{
							$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'/')+1 );
						}
						elseif(strpos($attachment['name'], '\\' !== FALSE))
						{
							$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'\\')+1 );
						}

						// @task: check if the attachment has file extension. ( assuming is images )
						$imgExts        = array( 'jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'jpeg', 'JPEG' );
						$imageSegment   = explode('.', $attachment['name']);

						if( ! in_array( $imageSegment[ count( $imageSegment ) - 1 ], $imgExts ) )
						{
							$attachment['name'] = $attachment['name'] . '.jpg';
						}

						// @task: Store the file into a temporary location first.
						$attachment['tmp_name']	= $tmp_dir . $attachment['name'];
						JFile::write( $attachment['tmp_name'], $attachment['data']);


						require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );

						// @task: Ensure that images goes through the same resizing format when uploading via media manager.
						$media 				= new EasyBlogMediaManager();
						$result 			= $media->upload( $dir , $uri , $attachment , '/', 'user' );

						// get the image file name and path
						if( is_object($result) && property_exists($result, 'title') )
						{
							$atmTitle = $result->title;
							$atmURL = $result->url;
						}
						else
						{
							$atmTitle = $attachment['name'];
							$atmURL	= $uri.$attachment['name'];
						}

						// @task: Once the attachment is processed, delete the temporary file.
						JFile::delete( $attachment['tmp_name'] );

						// now we need to replace the img tag in the email which the source is an attachment id :(
						$attachId   = $attachment['id'];
						if(! empty($attachId) )
						{
							$attachId   = str_replace('<', '', $attachId);
							$attachId   = str_replace('>', '', $attachId);

							$imgPattern  = array('/<div><img[^>]*src="[A-Za-z0-9:^>]*' . $attachId . '"[^>]*\/><\/div>/si',
												'/<img[^>]*src="[A-Za-z0-9:^>]*' . $attachId . '"[^>]*\/>/si');

							$imgReplace = array('','');
							$body		= preg_replace($imgPattern, $imgReplace, $body);
						}

						// insert image into blog post
						$body .= '<p><a class="easyblog-thumb-preview" href="'.$atmURL.'" title="'.$atmTitle.'"><img width="'.$config->get('main_thumbnail_width').'" title="'.$atmTitle.'." alt="" src="'.$atmURL.'" /></a></p>';
					}
				}
			}

			if ($format	== 'plain')
			{
				$body	= nl2br($body);
			}

			// tidy up the content so that the content do not contain incomplete html tag.
			$body   = EasyBlogHelper::getHelper('string')->tidyHTMLContent( $body );

			$type	= $config->get( 'main_remotepublishing_mailbox_type' );

			// insert $body, $subject, $from, $date
			$blog	= EasyBlogHelper::getTable( 'Blog' , 'Table' );

			// @task: Store the blog post
			$blog->set( 'title' 	, $subject );
			$blog->set( 'permalink' , EasyBlogHelper::getPermalink($blog->title) );
			$blog->set( 'source'	, 'email' );
			$blog->set( 'created_by', $userid );
			$blog->set( 'created'	, $date );
			$blog->set( 'modified'	, $date );
			$blog->set( 'publish_up', $date );
			$blog->set( $type		, $body );
			$blog->set( 'category_id', $config->get( 'main_remotepublishing_mailbox_categoryid' ) );
			$blog->set( 'published' , $config->get( 'main_remotepublishing_mailbox_publish' ) );
			$blog->set( 'frontpage'	, $config->get( 'main_remotepublishing_mailbox_frontpage' ) );
			$blog->set( 'send_notification_emails' , $config->get( 'main_remotepublishing_mailbox_publish' ) );
			$blog->set( 'issitewide', true );

			// @task: Set the blog's privacy here.
			$blog->set( 'private'	, $config->get( 'main_remotepublishing_mailbox_privacy' ) );

			// Store the blog post
			if (!$blog->store())
			{
				// If unrecognized email from mailbox, mark as seen.
				$this->markAsSeen($mailbox, $sequence);

				echo 'Mailbox: Message store failed. > ' . $subject . ' :: ' . $blog->getError();
				continue;
			}

			// If unrecognized email from mailbox, mark as seen.
			$this->markAsSeen($mailbox, $sequence);

			// @rule: Autoposting to social network sites.
			if( $blog->published == POST_ID_PUBLISHED )
			{
				$blog->autopost( array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER ) , array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER ) );

				$blog->notify( false );
			}

			$total++;
		}


		/*
		 * Disconnect from mailbox
		 */
		$mailbox->disconnect();

		/*
		 * Generate report
		 */
		echo JText::sprintf( '%1s blog posts fetched from mailbox: ' . $config->get( 'main_remotepublishing_mailbox_remotesystemname' ) . '.' , $total );
	}

	public function markAsSeen($mailbox, $sequence)
	{
		if($mailbox->service == 'pop3')
		{
			$mailbox->deleteMessage($sequence);
		}

		if($mailbox->service == 'imap')
		{
			$mailbox->setMessageFlag($sequence, '\Seen');
		}
	}	

	public function processCommentMailbox()
	{
		/*
		 * Check enabled
		 */
		$config		= EasyBlogHelper::getConfig();
		$debug		= JRequest::getBool( 'debug', false );

		if(!$config->get( 'main_comment_email' ) )
		{
			return;
		}

		$interval	= (int) $config->get( 'main_remotepublishing_mailbox_run_interval' );
		$nextrun	= (int) $config->get( 'main_remotepublishing_mailbox_next_run' );
		$nextrun	= EasyBlogHelper::getDate($nextrun)->toUnix();
		$timenow	= EasyBlogHelper::getDate()->toUnix();

		if ($nextrun !== 0 && $timenow < $nextrun)
		{
			if (!$debug)
			{
				echo 'time now: ' . EasyBlogHelper::getDate( $timenow )->toMySQL() . "<br />\n";
				echo 'next email run: ' . EasyBlogHelper::getDate( $nextrun )->toMySQL() . "<br />\n";
				return;
			}
		}

		$txOffset	= EasyBlogDateHelper::getOffSet();
		$newnextrun	= EasyBlogHelper::getDate('+ ' . $interval . ' minutes', $txOffset)->toUnix();

		// use $configTable to avoid variable name conflict
		$configTable		= EasyBlogHelper::getTable('configs');
		$configTable->load('config');
		$parameters = new JParameter($configTable->params);

		$parameters->set( 'main_remotepublishing_mailbox_next_run' , $newnextrun );
		$configTable->params = $parameters->toString('ini');

		$configTable->store();

		/*
		 * Connect to mailbox
		 */
		require_once(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'classes'.DS.'mailbox.php');
		$mailbox		= new EasyblogMailbox();
		if (!$mailbox->connect())
		{
			$mailbox->disconnect();
			echo 'Comment Mailbox: Could not connect to mailbox.';
			return false;
		}

		$total 	= 0;

		/*
		 * Get data from mailbox
		 */
		$total_mails	= $mailbox->getMessageCount();

		if ($total_mails < 1)
		{
			// No mails in mailbox
			$mailbox->disconnect();
			echo 'Comment Mailbox: No emails found.';
			return false;
		}

		// Let's get the correct mails
		$messages 	= $mailbox->searchMessages( 'UNSEEN' );

		if( $messages )
		{
			$prefix 	= '/\[\#(.*)\]/is';
			$filter		= JFilterInput::getInstance();
			$db			= EasyBlogHelper::db();

			foreach( $messages as $messageSequence )
			{
				$info 		= $mailbox->getMessageInfo( $messageSequence );
				$from		= $info->fromemail;
				$senderName	= $info->from[0]->personal;
				$subject	= $filter->clean( $info->subject );

				// @rule: Detect if this is actually a reply.
				preg_match( '/\[\#(.*)\]/is' , $subject , $matches );

				// If the title doesn't match the comment specific title, just continue the block.
				if( empty( $matches ) )
				{
					continue;
				}


				
				$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'email' ) . '=' . $db->Quote( $from );
				$db->setQuery( $query );

				$userId 	= $db->loadResult();

				$commentId 	= $matches[1];

				$refComment 	= EasyBlogHelper::getTable('Comment' );
				$refComment->load( $commentId );

				// Get the message contents.
				$message	= new EasyblogMailboxMessage($mailbox->stream, $messageSequence);
				$message->getMessage();
				$content	= $message->getPlain();

				// If guest commenting is not allowed, and user's email does not exist in system, pass this.
				if( !$config->get( 'main_allowguestcomment' ) && !$userId )
				{
					continue;
				}


				// Apply akismet filtering
				if( $config->get( 'comment_akismet' ) )
				{
					$data = array(
							'author'    => $senderName,
							'email'     => $from,
							'website'   => JURI::root() ,
							'body'      => $content,
							'permalink' => EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $refComment->post_id )
						);

					if( EasyBlogHelper::getHelper( 'Akismet' )->isSpam( $data ) )
					{
						continue;
					}
				}

				$model 	= EasyBlogHelper::getModel( 'Comment' );

				$comment 		= EasyBlogHelper::getTable( 'Comment' );
				$comment->name 	= $senderName;
				$comment->email = $from;
				$comment->comment 	= $content;
				$comment->post_id 	= $refComment->post_id;

				$date 	= EasyBlogHelper::getDate();
				$comment->created 	= $date->toMySQL();
				$comment->modified	= $date->toMySQL();
				$comment->published	= 1;

				if( $userId )
				{
					$comment->created_by 	= $userId;	
				}
				
				$comment->sent 		= 0;

				$isModerated		= false;
				// Update publish status if the comment requires moderation
				if( ($config->get( 'comment_moderatecomment') == 1) || ( !$userId && $config->get( 'comment_moderateguestcomment') == 1) )
				{
					$comment->set( 'published' , EBLOG_COMMENT_STATUS_MODERATED );
					$isModerated	= true;
				}

				$blog	= EasyBlogHelper::getTable( 'Blog' );
				$blog->load( $comment->post_id );

				// If moderation for author is disabled, ensure that the comment is published.
				// If the author is the owner of the blog, it should never be moderated.
				if( !$config->get( 'comment_moderateauthorcomment' ) && $blog->created_by == $userId )
				{
					$comment->set( 'published' , 1 );
					$isModerated	= false;
				}

				if( !$comment->store() )
				{
					echo 'Error storing comment: ' . $comment->getError();
					return;
				}

				echo '* Added comment for post <strong>' . $blog->title . '</strong><br />';

				// @rule: Process notifications
				$comment->processEmails( $isModerated , $blog );

				// Update the sent flag
				$comment->updateSent();

				$total++;
			}
		}

		/*
		 * Disconnect from mailbox
		 */
		$mailbox->disconnect();

		/*
		 * Generate report
		 */
		echo JText::sprintf( 'Comment Mailbox: %1s comments fetched from mailbox: ' . $config->get( 'main_remotepublishing_mailbox_remotesystemname' ) . '.' , $total );
	}

	public function processTwitter()
	{
		// @rule: Find all oauth accounts
		$db		= EasyBlogHelper::db();
		$config	= EasyBlogHelper::getConfig();

		$key	= $config->get( 'integrations_twitter_api_key' );
		$secret	= $config->get( 'integrations_twitter_secret_key' );

		$query 	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_oauth' );
		$query	.= ' WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'twitter' );
		$query 	.= ' AND ' . $db->nameQuote( 'system' ) . '=' . $db->Quote( 0 );

		$db->setQuery( $query );

		$accounts	= $db->loadObjectList();

		$hashes			= $config->get( 'integrations_twitter_microblog_hashes' );

		// If hashes are empty, do not try to run anything since we wouldn't be able to find anything.
		if( empty( $hashes ) )
		{
			return false;
		}

		$hashes			= explode( ',' , $hashes );
		$totalHashes	= count( $hashes );
		$search			= '';
		$categoryId		= $config->get( 'integrations_twitter_microblog_category' );
		$published		= $config->get( 'integrations_twitter_microblog_publish' );
		$frontpage		= $config->get( 'integrations_twitter_microblog_frontpage' );

		$total		= 0;

		if (!$accounts) {
			return;
		}

		foreach ($accounts as $account) {

			$query = 'SELECT `id_str` FROM ' . $db->nameQuote( '#__easyblog_twitter_microblog' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $account->id ) . ' '
					. 'ORDER BY `created` DESC';

			$db->setQuery($query);
			$result = $db->loadObject();

			$jparam = EasyBlogHelper::getRegistry( $account->params );
			$screen = $jparam->get( 'screen_name' );

			// If we can't get the screen name, do not try to process it.
			if (!$screen) {
				continue;
			}

			// @rule: Retrieve the consumer object for this oauth client.
			$consumer = EasyBlogHelper::getHelper('Oauth')->getConsumer('twitter', $key, $secret, '');
			$consumer->setAccess($account->access_token);

			$params = array();
			$params['q'] = 'screen_name=' . $screen;
			$params['include_rts'] = false;

			if ($result) {
				$params['since_id'] = $result->id_str;
			}

			$tweets = $consumer->get('statuses/user_timeline', $params);

			if (!$tweets) {
				continue;
			}

			foreach( $tweets as $tweet ) {
				foreach ($hashes as $hash) {
					if (JString::stristr($tweet->text, $hash) === false) {
					continue;
				}	
			}

				// Remove hashtag from the content since it would be pointless to show it.
				$tweet->text	= str_ireplace( $hashes , '' , $tweet->text );
				$blog			= EasyBlogHelper::getTable( 'Blog' );
				$title			= JString::substr( $tweet->text , 0 , 20 ) . '...';
				$created		= EasyBlogHelper::getDate( $tweet->created_at );
				$createdDate	= $created->toMySQL();
				$content		= $tweet->text;

				// @task: Store the blog post
				$blog->set( 'title' 	, $title );
				$blog->set( 'source'	, 'twitter' );
				$blog->set( 'created_by', $account->user_id );
				$blog->set( 'created'	, $createdDate );
				$blog->set( 'modified'	, $createdDate );
				$blog->set( 'publish_up', $createdDate );
				$blog->set( 'intro'		, $content );
				$blog->set( 'category_id', $categoryId );
				$blog->set( 'published' , $published );
				$blog->set( 'frontpage'	, $frontpage );
				$blog->set( 'issitewide', true );
				$blog->set( 'source'	, EBLOG_MICROBLOG_TWITTER );

				// Store the blog post
				if (!$blog->store()) {
					var_dump($blog->getError());
				}

				// @task: Add a history item
				$history	= EasyBlogHelper::getTable( 'TwitterMicroBlog' , 'Table' );
				$history->set( 'id_str' , $tweet->id_str );
				$history->set( 'post_id' , $blog->id );
				$history->set( 'oauth_id', $account->id );
				$history->set( 'created' , $createdDate );
				$history->set( 'tweet_author' , $screen );

				$history->store();

				$total++;
			}
		}

		echo JText::sprintf( '%1s blog posts fetched from Twitter' , $total );
	}
}
