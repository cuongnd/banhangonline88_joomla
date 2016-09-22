<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'comment.php' );

class EasyBlogViewEntry extends EasyBlogView
{
	var $err	= null;

	public function likesComment( $contentId, $status, $likesId)
	{
		$my		= JFactory::getUser();
		$ejax	= new Ejax();

		if( $my->id <= 0 )
		{
			$ejax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
			$ejax->send();
			return;
		}

		$jsLink = '';

		// When the user likes a comment, send a notification to the comment author
		if ($status) {

			$table = EasyBlogHelper::getTable('Comment');
			$table->load($contentId);
			$table->notifyLikes($my->id);
		}

		if($status)
		{
			// add likes
			$id	= EasyBlogHelper::addLikes($contentId, 'comment', $my->id);
			$jsLink = "<a href=\"javascript:eblog.comment.likes('" . $contentId . "', '0', '" . $id . "');\" class=\"likes\">" . JText::_('COM_EASYBLOG_UNLIKE') . "</a>";
		}
		else
		{
			// remove likes
			EasyBlogHelper::removeLikes($likesId);
			$jsLink = '<b>&middot;</b>  ' . "<a href=\"javascript:eblog.comment.likes('" . $contentId . "', '1', '0');\" class=\"likes\">" . JText::_('COM_EASYBLOG_LIKES') . "</a>";
		}

		

		//now reformat the likes authors
		$authors	= EasyBlogHelper::getLikesAuthors($contentId, 'comment', $my->id );

		$ejax->assign('likes-'.$contentId, $jsLink);
		$ejax->assign('likes-container-'.$contentId, '<b>&middot;</b>  ' . $authors);

		if(empty($authors))
		{
            $ejax->script("$('#likes-container-".$contentId."').hide();");
		}
		else
		{
	    	$ejax->script("$('#likes-container-".$contentId."').show();");
		}

		$totalLikes = EasyBlogHelper::getModel('comment')->getCommentTotalLikes($contentId);

		$ejax->script("$('#comment-likescounter-".$contentId."').html('Likes: ".$totalLikes."');");
		$ejax->script( "eblog.loader.doneLoading();" );
		$ejax->send();
		return;
	}

	function showTnc()
	{
		$config	= EasyBlogHelper::getConfig();
		$ejax	= new Ejax();

		$options		  = new stdClass();
		$options->title   = JText::_('COM_EASYBLOG_TERMS_AND_CONDITIONS');
		$options->content = nl2br($config->get('comment_tnctext'));

		$ejax->dialog( $options );

		$ejax->send();
		return;
	}

	/**
	 * This method is invoked when a user submits a comment via ajax.
	 *
	 * @access	public
	 * @params	Array	$post 	An array of posted data.
	 * @return	null
	 */
	public function commentSave( $post )
	{
		$ajax		= new Ejax();
		$app 		= JFactory::getApplication();
		$my 		= JFactory::getUser();
		$config 	= EasyBlogHelper::getConfig();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		if( empty($acl->rules->allow_comment) && (empty($my->id) && !$config->get('main_allowguestcomment')) )
		{
			$ajax->script('eblog.spinner.hide()');
			$ajax->script( "eblog.loader.doneLoading();" );
			$ajax->script( 'eblog.comment.displayInlineMsg( "error" , "'.JText::_('COM_EASYBLOG_NO_PERMISSION_TO_POST_COMMENT').'");' );
			$ajax->send();
		}

		$isModerated	= false;
		$parentId		= $post['parent_id'];
		$commentDepth	= $post['comment_depth'];
		$blogId			= $post['id'];
		$subscribeBlog	= isset($post['subscribe-to-blog']) ? true : false;

		// @task: Cleanup posted values.
		array_walk($post, array($this, '_trim') );
		array_walk($post, array($this, '_revertValue') );

		if( !$config->get( 'comment_require_email' ) && !isset( $post['esemail'] ) )
		{
			$post['esemail']	= '';
		}

		// @task: Run some validation tests on the posted values.
		if(! $this->_validateFields($post))
		{
			// @task: Reload captcha if necessary
			EasyBlogHelper::getHelper( 'Captcha' )->reload( $ajax , $post );

			$ajax->script( "eblog.loader.doneLoading();" );
			$ajax->script('eblog.spinner.hide()');
			$ajax->script( '$("#'.$this->err[1].'").addClass("input-error");' );
			$ajax->script( "eblog.element.focus('".$this->err[1]."');" );
			$ajax->script( 'eblog.comment.displayInlineMsg(\'error\', \''.$this->err[0].'\');' );
			$ajax->send();
			return;
		}

		// @task: Akismet detection service.
		if( $config->get( 'comment_akismet' ) )
		{
			$data = array(
					'author'    => $post['esname'],
					'email'     => $post['esname'],
					'website'   => JURI::root() ,
					'body'      => $post['comment'] ,
					'permalink' => EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $post['id'] )
				);

			if( EasyBlogHelper::getHelper( 'Akismet' )->isSpam( $data ) )
			{
				$ajax->script( 'eblog.comment.displayInlineMsg(\'error\', \''.JText::_('COM_EASYBLOG_SPAM_DETECTED_IN_COMMENT').'\');' );
				$ajax->script( "eblog.loader.doneLoading();" );
				$ajax->script('eblog.spinner.hide();');
				$ajax->send();
				return false;
			}
		}

		// @task: Retrieve the comments model
		$model		= $this->getModel( 'Comment' );

		// @task: Retrieve the comment's table
		$comment	= EasyBlogHelper::getTable( 'Comment' );

		// We need to rename the esname and esemail back to name and email.
		$post['name']	= $post['esname'];
		$post['email']	= $post['esemail'];

		unset($post['esname']);
		unset($post['esemail']);

		// @task: Bind posted values into the table.
		$comment->bindPost( $post );

		if( !EasyBlogHelper::getHelper( 'Captcha' )->verify( $post ) )
		{
			return EasyBlogHelper::getHelper( 'Captcha' )->getError( $ajax , $post );
		}

		// @task: Process registrations
		$registerUser	= isset( $post[ 'esregister' ] ) ? true : false;
		$fullname 		= isset( $post[ 'name' ] ) ? $post['name'] : '';
		$username 		= isset( $post[ 'esusername' ] ) ? $post[ 'esusername' ] : '';
		$email			= $post[ 'email' ];
		$message 		= '';
		$newUserId 		= 0;

		// @task: Process registrations if necessary
		if( $registerUser && $my->id <= 0 )
		{
			$state		= $this->processRegistrations( $post , $username , $email , $ajax );

			if( !is_numeric( $state ) )
			{
				$ajax->script( "eblog.loader.doneLoading();" );
				$ajax->script( 'eblog.comment.displayInlineMsg( "error" , "' . $state . '");' );
				EasyBlogHelper::getHelper( 'Captcha' )->reload( $ajax , $post );

				return $ajax->send();
			}

			$newUserId	= $state;
		}

		$totalComments 		= empty( $post[ 'totalComment' ] ) ? 1 : $post[ 'totalComment' ];

		$date 	= EasyBlogHelper::getDate();

		$comment->set( 'created' 	, $date->toMySQL() );
		$comment->set( 'modified'	, $date->toMySQL() );
		$comment->set( 'published'	, 1 );
		$comment->set( 'parent_id'	, $parentId );
		$comment->set( 'sent'		, 0 );
		$comment->set( 'created_by'	, $my->id );

		// @rule: Update the user's id if they have just registered earlier.
		if( $newUserId != 0 )
		{
			$comment->set( 'created_by' , $newUserId );
		}

		// @rule: Update publish status if the comment requires moderation
		if ((($config->get('comment_moderatecomment') == 1) || ($my->guest && $config->get('comment_moderateguestcomment') == 1)) && !EasyblogHelper::isSiteAdmin()) {
			$comment->set( 'published' , EBLOG_COMMENT_STATUS_MODERATED );
			$isModerated	= true;
		}

		$blog	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load($blogId);

		// If moderation for author is disabled, ensure that the comment is published.
		// If the author is the owner of the blog, it should never be moderated.
		if( !$config->get( 'comment_moderateauthorcomment' ) && $blog->created_by == $my->id )
		{
			$comment->set( 'published' , 1 );
			$isModerated	= false;
		}

		if( !$comment->store() )
		{
			//$ejax->alert( JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->script( 'eblog.comment.displayInlineMsg(\'error\', \''.JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE').'\');' );

			return $ajax->send();
		}

		// @task: Clean up the comment form
		$ajax->script('$(\'#title\').val(\'\');');
		$ajax->script('$(\'#url\').val(\'\');');
		$ajax->script('$(\'#comment\').val(\'\');');
		$ajax->script('$(\'#esusername\').val(\'\');');
		$ajax->script('$(\'#esregister\').attr(\'checked\',false);');

		$message		= JText::_('COM_EASYBLOG_COMMENTS_SUCCESS');

		if( $newUserId != 0 && $registerUser )
		{
			$message 	= JText::_('COM_EASYBLOG_COMMENTS_SUCCESS_AND_REGISTERED');
		}

		// @rule: Process subscription for blog automatically when the user submits a new comment and wants to subscribe to the blog.
		if( $subscribeBlog && $config->get( 'main_subscription' ) && $blog->subscription )
		{
			$isSubscribed   = false;
			$userId     	= $my->id;
			$blogModel		= EasyblogHelper::getModel('Blog');

			if( $userId == 0 )
			{
				$sid	= $blogModel->isBlogSubscribedEmail( $blog->id , $email );

				if( empty( $sid ) )
				{
					$isSubscribed = $blogModel->addBlogSubscription( $blog->id , $email, '', $fullname );
				}
			}
			else
			{
				$sid	= $blogModel->isBlogSubscribedUser( $blog->id , $userId , $email);
				if( !empty( $sid ) )
				{
					// @task: User found, update the email address
					$blogModel->updateBlogSubscriptionEmail($sid, $userId, $email);
				}
				else
				{
					$isSubscribed = $blogModel->addBlogSubscription( $blog->id , $email, $userId, $fullname);
				}
			}

			if( $isSubscribed )
			{
			    $message    .= ' ' . JText::_('COM_EASYBLOG_ENTRY_AUTO_SUBSCRIBED_SUCCESS');
				$sid	= $blogModel->isBlogSubscribedUser( $blog->id , $userId , $email);

				$html = '';
				$html .= '<div id="unsubscription-box" class="unsubscription-box">';
				$html .= '	'.JText::_('COM_EASYBLOG_ENTRY_AUTO_SUBSCRIBE_SUBSCRIBED_NOTE');
				$html .= '	<a href="javascript:void(0);" title="" onclick="eblog.blog.unsubscribe( \''.$sid.'\', \''.$blog->id.'\' );">';
				$html .= '		'.JText::_('COM_EASYBLOG_UNSUBSCRIBE_BLOG');
				$html .= '	</a>';
				$html .= '</div>';

				$ajax->append('subscription-box', $html);
				$ajax->script('$(\'#subscription-message\').remove();');
			}

		}

		$row 		= $comment;
		$creator	= EasyBlogHelper::getTable( 'Profile' );
		$creator->load( $my->id );

		$row->poster		= $creator;
		$row->comment   	= nl2br($row->comment);
		$row->comment   	= EasyBlogCommentHelper::parseBBCode($row->comment);
		$row->depth 		= (is_null($commentDepth)) ? '0' : $commentDepth;
		$row->likesAuthor   = '';

		// @rule: Process notifications
		$comment->processEmails( $isModerated , $blog );

		if( $isModerated )
		{
			$tpl = new CodeThemes();
			$tpl->set('comment', $row );
			$tpl->set('totalComment', $totalComments );
			$tpl->set( 'config' , $config );
			$tpl->set( 'my' , $my );

			$commentHtml	= $tpl->fetch( 'blog.comment.moderate.php' );

			if($parentId != 0)
			{
			    $ajax->after('comment-' . $parentId, $commentHtml);
				$ajax->script('eblog.comment.cancel(\''.$parentId.'\')');
			}
			else
			{
				$ajax->append('blog-comment', $commentHtml);
			}

			// Reload recaptcha image once the comment is saved.
			EasyBlogHelper::getHelper( 'Captcha' )->reload( $ajax , $post );

			$ajax->script( "eblog.loader.doneLoading();" );
			$ajax->script( 'eblog.comment.displayInlineMsg(\'info\', \''.$message.'\');' );

			$ajax->send();
			return;
		}

		$tpl = new CodeThemes();
		$tpl->set('comment', $row );
		$tpl->set('totalComment', $totalComments );
		$tpl->set( 'config' , $config );
		$tpl->set( 'my' , $my );
		$tpl->set( 'acl' , $acl );

		$commentHtml	= $tpl->fetch( 'blog.comment.ejax.php' );

		if($parentId != 0)
		{
		    $ajax->after('comment-' . $parentId, $commentHtml);
			$ajax->script('eblog.comment.cancel(\''.$parentId.'\')');
		}
		else
		{
			$ajax->append('blog-comment', $commentHtml);
		}

		//update the sent flag to sent
		$comment->updateSent();

		// Reload whichever captcha necessary for the next run
		EasyBlogHelper::getHelper( 'Captcha' )->reload( $ajax , $post );

		$ajax->script( "eblog.loader.doneLoading();" );

		// update comment total count text on blog post
		if($comment->published == 1)
		{
			$commentText = $tpl->getNouns( 'COM_EASYBLOG_COMMENT_COUNT' , $totalComments , true );
			$ajax->script('$(\'.blog-comments a\').text(\''.$commentText.'\');');
		}

		//update the comment total count

		$ajax->script('$(\'#comment-total-count\').text(\''.($totalComments).'\');');
		//the next count.
		$ajax->script('$(\'#totalComment\').val(\''.($totalComments + 1).'\');');

		//$ejax->alert( $message, JText::_('COM_EASYBLOG_INFO') , '450', 'auto');
		$ajax->script( 'eblog.comment.displayInlineMsg(\'info\', \''.$message.'\');' );

		$ajax->send();
	}


	/**
	 * Processes registration
	 */
	private function processRegistrations( &$post , $username , $email , $ajax )
	{
		$registration 	= EasyBlogHelper::getRegistor();
		$options		= array( 'username' => $username, 'email' => $email );

		$state 			= $registration->validate( $options );

		if( $state !== true )
		{
			return $state;
		}


		$options[ 'fullname' ]	= $post[ 'name' ];
		$id 	= $registration->addUser( $options , 'comment' );

		if( !is_numeric( $id ) )
		{
			return $id;
		}

		return (int) $id;
	}

	/**
	 * Cleanup array values
	 */
	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	function _revertValue(&$text)
	{
		if( $text == JText::_('COM_EASYBLOG_TITLE') ||
			$text == JText::_('COM_EASYBLOG_USERNAME') ||
			$text == JText::_('COM_EASYBLOG_NAME') ||
			$text == JText::_('COM_EASYBLOG_EMAIL') ||
			$text == JText::_('COM_EASYBLOG_WEBSITE'))
		{
			$text = '';
		}
	}

	function _validateFields($post)
	{
		$config = EasyBlogHelper::getConfig();
		$my     = JFactory::getUser();

		if( !isset( $post[ 'comment' ] ) )
		{
			return false;
		}

		if(JString::strlen($post['comment']) == 0)
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_IS_EMPTY');
			$this->err[1]	= 'comment';
			return false;
		}

		if( $config->get('comment_requiretitle') && (JString::strlen($post['title']) == 0 || $post['title'] == JText::_('COM_EASYBLOG_TITLE')))
		{
			$this->err[0]	= JText::_( 'COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY' );
			$this->err[1]	= 'title';
			return false;
		}

		if(isset($post['esregister']) && isset($post['esusername']))
		{
			if(JString::strlen($post['esusername']) == 0 || $post['esusername'] == JText::_('COM_EASYBLOG_USERNAME'))
			{
				$this->err[0]	= JText::_('COM_EASYBLOG_SUBSCRIPTION_USERNAME_IS_EMPTY');
				$this->err[1]	= 'esusername';
				return false;
			}
		}

		if(JString::strlen($post['esname']) == 0 || $post['esname'] == JText::_('COM_EASYBLOG_NAME'))
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_NAME_IS_EMPTY');
			$this->err[1]	= 'esname';
			return false;
		}


		// @rule: Only check for valid email when the email is really required
		if( $config->get( 'comment_require_email' ) && (JString::strlen($post['esemail']) == 0 || $post['esemail'] == JText::_('COM_EASYBLOG_EMAIL') ) )
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY');
			$this->err[1]	= 'esemail';
			return false;
		}
		else if( isset( $post['subscribe-to-blog']) && (JString::strlen($post['esemail']) == 0 || $post['esemail'] == JText::_('COM_EASYBLOG_EMAIL') ))
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY');
			$this->err[1]	= 'esemail';
			return false;
		}
		else
		{
			if( (! EasyBlogHelper::getHelper( 'email' )->isValidInetAddress( $post['esemail'] )) && ($config->get( 'comment_require_email' ) || isset( $post['subscribe-to-blog']) ))
			{
				$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_EMAIL_INVALID');
				$this->err[1]	= 'esemail';
				return false;
			}
		}

		if($config->get('comment_tnc') == true && ( ( $config->get('comment_tnc_users') == 0 && $my->id <=0) || ( $config->get('comment_tnc_users') == 1 && $my->id >= 0) || ( $config->get('comment_tnc_users') == 2) ) )
		{
			if(empty($post['tnc']))
			{
				$this->err[0]	= JText::_( 'COM_EASYBLOG_YOU_MUST_ACCEPT_TNC' );
				$this->err[1]	= 'tnc';
				return false;
			}
		}

		return true;
	}

	/**
	 * Allows caller to reload recaptcha provided that the previous recaptcha reference
	 * is given. This is to avoid any spams on the system.
	 *
	 * @param	$previousId		The previous recaptcha id reference.
	 * @return	Ejax	The json response.
	 **/
	public function reloadCaptcha( $previousId )
	{
		// Delete old references to avoid spamming of database.
		JTable::addIncludePath( EBLOG_TABLES );
		$ref	= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
		$state  = $ref->load( $previousId );
		if( $state )
		{
			$ref->delete();
		}

		// Generate a new captcha
		$captcha			= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
		$captcha->created	= EasyBlogHelper::getDate()->toMySQL();
		$captcha->store();

		$ajax		= new Ejax();
		$ajax->script( 'eblog.captcha.reloadImage( "' . $captcha->id . '" , "' . EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=captcha&captcha-id=' . $captcha->id . '&no_html=1&tmpl=component', false) . '")' );

		return $ajax->send();
	}

	/**
	 * comment save function used by cb.easyblog plugin.
	 */
	function saveCBcomment( $post )
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
		$mainframe		= JFactory::getApplication();
		$my				= JFactory::getUser();
		$config 		= EasyBlogHelper::getConfig();

		$ajax		= new Ejax();

		if(JString::strlen($post['comment']) == 0)
		{
			$ajax->script( 'easyblogApp.comment.notify("'.$post[ 'id' ].'","'.JText::_( 'Comment is empty' ).'","'.'error'.'")');
			$ajax->script( 'easyblogApp.spinner.hide()');
			$ajax->script( 'easyblogApp.element.focus("comment")');

			return $ajax->send();
		}

		// We don't require a title here.
		$post[ 'title' ]	= '';

		//real work start here.
		$isModerate		= false;
		$parentId		= "0";
		$commentDepth	= $post['comment_depth'];
		$blogId			= $post['id'];

		//we need to rename the esname and esemail back to name and email.
		$post['url']	= '';
		$post['name']	= $post['esname'];
		$post['email']	= $post['esemail'];

		unset($post['esname']);
		unset($post['esemail']);

		JTable::addIncludePath( EBLOG_TABLES );
		$db     	= EasyBlogHelper::db();
		$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
		$comment->bindPost($post);

		$now                	= EasyBlogHelper::getDate();
		$totalComment			= (empty($post['totalComment'])) ? 1 : $post['totalComment'];
		$comment->created		= $now->toMySql();
		$comment->modified		= $now->toMySql();
		$comment->published		= 1;
		$comment->parent_id		= $parentId;
		$comment->created_by	= $my->id;

		if(($my->id != 0 && $config->get( 'comment_moderatecomment') == 1) || ($my->id == 0 && $config->get( 'comment_moderateguestcomment') == 1))
		{
			 $comment->published	= 0;
			 $isModerate			= true;
		}

		jimport( 'joomla.application.component.model' );
		JLoader::import( 'Comment' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		$model			= JModel::getInstance( 'Comment' , 'EasyBlogModel' );
		$latestComment	= $model->getLatestComment( $blogId , $parentId );
		$left			= 1;
		$right			= 2;

		if( !empty( $latestComment ) )
		{
			$left		= $latestComment->rgt + 1;
			$right		= $latestComment->rgt + 2;

			$model->updateCommentSibling( $blogId , $latestComment->rgt );
		}
		$comment->lft	= $left;
		$comment->rgt	= $right;

		if( !$comment->store() )
		{
			$ajax->script( 'easyblogApp.comment.notify("'.$post[ 'id' ].'","'.JText::_( 'Comment add failed' ).'","'.'error'.'")');
			$ajax->script( 'easyblogApp.spinner.hide()');
			$ajax->$ajax( 'easyblogApp.element.focus("comment")');
			return $ajax->send();
		}

		$profile	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$profile->load( $comment->created_by );

		$comment->creator 	= $profile;

		$date	= EasyBlogDateHelper::dateWithOffSet( $comment->created );
		$comment->formattedDate	= $date->toFormat( $config->get('layout_dateformat', '%A, %d %B %Y') );
		$text		= (JString::strlen( $comment->comment) > 50) ? JString::substr( strip_tags( $comment->comment), 0, 50) . '...' : strip_tags($comment->comment);

		$commentText	= '
				<li>
					<div class="blog-comment-avatar">
						<a href="' . $comment->creator->getProfileLink() . '"><img src="'  . $comment->creator->getAvatar() . '" width="32" /></a>
					</div>
					<div class="blog-comment-item eztc">
						<div class="small">
							<a href="' . $comment->creator->getProfileLink() . '">' . $comment->creator->getName() . '</a>
							' . JText::_( 'on' ) . '
							<span class="small">' . $comment->formattedDate . '</span>
						</div>
						' . $text . '
					</div>
					<div style="clear: both;"></div>
				</li>';

		$ajax->prepend( 'comments-wrapper' . $blogId, $commentText);
		$ajax->script( 'easyblogApp.comment.cancel("'.$blogId.'")');
		$ajax->script( 'easyblogApp.spinner.hide()');
		$ajax->script( 'easyblogApp.comment.notify("'.$blogId.'","'.JText::_( 'Comment Added' ).'","'.'success'.'")');

		return $ajax->send();
	}

	/*
	 * @since 2.0.3300
	 * AJAX method to edit a comment
	 *
	 * @param	int		$id		The comment subject.
	 * @return	string	JSON encoded stiring.
	 */
	public function editComment( $id )
	{
		$config     = EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();

		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
		$comment->load( $id );

		if( ( ( !$acl->rules->edit_comment || $my->id != $comment->created_by ) || $my->id == 0 ) && !EasyBlogHelper::isSiteAdmin() && !$acl->rules->manage_comment )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_COMMENT'), JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
		}

		$tpl = new CodeThemes();
		$tpl->set('comment'	, $comment );

		$options		  = new stdClass();
		$options->title   = JText::_('COM_EASYBLOG_DASHBOARD_EDIT_COMMENT');
		$options->content = $tpl->fetch( 'ajax.dialog.comments.edit.php' );

		$ajax->dialog( $options );
		$ajax->send();
	}

	/*
	 * @since 2.0.3471
	 * AJAX method to delete a comment
	 *
	 * @param	int		$id		The comment subject.
	 * @return	string	JSON encoded stiring.
	 */
	public function deleteComment( $id )
	{
		$config     = EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();

		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
		$comment->load( $id );

		if( ( $my->id == 0 || $my->id != $comment->created_by || !$acl->rules->delete_comment ) && !EasyBlogHelper::isSiteAdmin() )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_COMMENT'), JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
		}

		$tpl = new CodeThemes();
		$tpl->set('comment'	, $comment );

		$options		  = new stdClass();
		$options->title   = JText::_('COM_EASYBLOG_DASHBOARD_DELETE_COMMENT');
		$options->content = $tpl->fetch( 'ajax.dialog.comments.delete.php' );

		$ajax->dialog( $options );
		$ajax->send();
	}

	/*
	 *
	 *
	 *
	 */
	public function confirmUnsubscribeBlog( $subscriptionId, $blogId )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();

		if( $my->id == 0 || !$subscriptionId || !$blogId )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		$themes		= new CodeThemes();
		$themes->set( 'subscription_id'	, $subscriptionId );
		$themes->set( 'blog_id'	, $blogId );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_UNSUBSCRIBE_BLOG_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.blog.unsubscribe.php' );
		$ajax->dialog( $options );

		//$ajax->script( 'easyblogApp.element.focus("subscribtion-message).hide()' );

		return $ajax->send();
	}
}
