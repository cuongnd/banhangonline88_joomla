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

class EasyBlogViewSubscription extends EasyBlogView
{
	/*
	 * Ajax method to show subscriptions form dialog
	 *
	 * @param	void
	 * @return	string	Ajax response
	 **/
	public function showForm( $type , $id = '' )
	{
		$my     		= JFactory::getUser();
		$ajax			= new Ejax();
		$config 		= EasyBlogHelper::getConfig();
		$registration	= JComponentHelper::getParams( 'com_users' )->get( 'allowUserRegistration' ) == 0 ? 0 : $config->get( 'main_registeronsubscribe' );
		$acl			= EasyBlogACLHelper::getRuleSet();

		if( empty( $my->id )  && !$config->get( 'main_allowguestsubscribe' ) )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_SUBSCRIPTION_PLEASE_LOGIN') , JText::_('COM_EASYBLOG_ERROR_DIALOG_LOGIN_TITLE') , '450' );
			return $ajax->send();
		}


		if( !$acl->rules->allow_subscription )
		{
			// For the sake of Joomla 1.5, as they don't have guest group for ACL, we need to ensure that the form doesn't show this when "Allow guest to subscribe" is enabled.
			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' || (EasyBlogHelper::getJoomlaVersion() < '1.6' && !$config->get('main_allowguestsubscribe')) )
			{
				$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG') , JText::_('COM_EASYBLOG_ERROR_DIALOG_TITLE') , '450' );
				return $ajax->send();
			}
		}

		// Someone may try to do something funny.
		if( !is_string( $type ) )
		{
			return;
		}
		
		// Additional permission checks only if exists
		$method			= 'allowSubscribe' . ucfirst( $type );
		if( method_exists( $this , $method ) )
		{
			if( !$this->$method( $id ) )
			{
				$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG') , JText::_('COM_EASYBLOG_ERROR_DIALOG_TITLE') , '450' );
				return $ajax->send();
			}
		}

		switch( $type )
		{
			case EBLOG_SUBSCRIPTION_BLOGGER:
				$title		= JText::_( 'COM_EASYBLOG_SUBSCRIPTION_BLOGGER_DIALOG_TITLE' );
				$message	= JText::_( 'COM_EASYBLOG_SUBSCRIBE_BLOGGER_INFORMATION' );
			break;
			case EBLOG_SUBSCRIPTION_CATEGORY:
				$title		= JText::_( 'COM_EASYBLOG_SUBSCRIPTION_CATEGORY_DIALOG_TITLE' );
				$message	= JText::_('COM_EASYBLOG_SUBSCRIBE_CATEGORY_INFORMATION');
			break;
			case EBLOG_SUBSCRIPTION_TEAMBLOG:
				$title		= JText::_( 'COM_EASYBLOG_SUBSCRIPTION_TEAMBLOG_DIALOG_TITLE' );
				$message	= JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM_INFORMATION');
			break;
			case EBLOG_SUBSCRIPTION_ENTRY:
				$title		= JText::_( 'COM_EASYBLOG_SUBSCRIPTION_ENTRY_DIALOG_TITLE' );
				$message	= JText::_('COM_EASYBLOG_SUBSCRIBE_ENTRY_INFORMATION');
			break;
			default:
			case EBLOG_SUBSCRIPTION_SITE:
				$title		= JText::_( 'COM_EASYBLOG_SUBSCRIPTIONS_SITE_DIALOG_TITLE' );
				$message	= JText::_('COM_EASYBLOG_SUBSCRIPTIONS_SITE_DIALOG_SUBSCRIPTION_DESC');
			break;
		}

		$theme	= new CodeThemes();
		$theme->set( 'registration'	, $registration );
		$theme->set( 'id'			, $id );
		$theme->set( 'message'		, $message );
		$theme->set( 'type'			, $type );

		$content	= $theme->fetch( 'ajax.dialog.subscribe.php' );

		$ajax->dialog( EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , $title )->set( 'content' , $content )->toObject() );
		return $ajax->send();
	}

	/*
	 *
	 * Ajax method to show subscriptions form dialog
	 *
	 * @param	string	$type	The type of subscription.
	 * @param	Array	$post	The posted data.
	 * @return	string	Ajax response
	 **/
	public function submitForm( $type , $post )
	{
		$ejax			= new Ejax();
		$mainframe		= JFactory::getApplication();
		$my				= JFactory::getUser();
		$config 		= EasyBlogHelper::getConfig();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$id				= isset( $post[ 'id' ] ) ? $post[ 'id' ] : '';

		if(empty($acl->rules->allow_subscription) && (empty($my->id) && !$config->get('main_allowguestsubscribe')))
		{
			$theme	= new CodeThemes();
			$theme->set( 'message'	, JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_SUBSCRIBE_BLOG' ) );
			$theme->set( 'type'		, $type );
			$theme->set( 'id'		, $id );
			$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

			$ejax->dialog( $options );
			return $ejax->send();
		}

		$isModerate	= false;
		$userId		= isset( $post['userid'] ) ? $post['userid'] : '';
		$email  	= JString::trim( $post['email'] );


		//registration
		$register   = (isset($post['esregister'])) ? true : false;
		$fullname   = (isset($post['esfullname'])) ? $post['esfullname'] : '';
		$username   = (isset($post['esusername'])) ? $post['esusername'] : '';
		$newId		= '';
		$msg        = '';

		if( JString::trim($email) == '' )
		{
			$theme	= new CodeThemes();
			$theme->set( 'message'	, JText::_( 'COM_EASYBLOG_SUBSCRIPTION_EMAIL_EMPTY_ERROR' ) );
			$theme->set( 'type'		, $type );
			$theme->set( 'id'		, $id );
			$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

			$ejax->dialog( $options );
			return $ejax->send();
		}
		else
		{
			if( ! EasyBlogHelper::getHelper( 'Email' )->isValidInetAddress( $email ) )
			{
				$theme	= new CodeThemes();
				$theme->set( 'message'	, JText::_( 'COM_EASYBLOG_SUBSCRIPTION_EMAIL_INVALID_ERROR' ) );
				$theme->set( 'type'		, $type );
				$theme->set( 'id'		, $id );
				$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

				$ejax->dialog( $options );
				return $ejax->send();
			}
		}

		if(JString::trim($fullname) == '')
		{
			$theme	= new CodeThemes();
			$theme->set( 'message'	, JText::_( 'COM_EASYBLOG_SUBSCRIPTION_NAME_EMPTY_ERROR' ) );
			$theme->set( 'type'		, $type );
			$theme->set( 'id'		, $id );
			$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

			$ejax->dialog( $options );
			return $ejax->send();
		}

		if( $register && $my->id == 0 )
		{
			if(JString::trim($username) == '')
			{
				$theme	= new CodeThemes();
				$theme->set( 'message'	, JText::_( 'COM_EASYBLOG_SUBSCRIPTION_USERNAME_EMPTY_ERROR' ) );
				$theme->set( 'type'		, $type );
				$theme->set( 'id'		, $id );
				$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

				$ejax->dialog( $options );
				return $ejax->send();
			}

			$registor   = EasyBlogHelper::getRegistor();
			$options    = array( 'username' => $username, 'email' => $email );
			$validate	= $registor->validate($options);

			if($validate !== true)
			{
				$theme	= new CodeThemes();
				$theme->set( 'message'	, $validate );
				$theme->set( 'type'		, $type );
				$theme->set( 'id'		, $id );
				$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

				$ejax->dialog( $options );
				return $ejax->send();
			}
			else
			{
				$options['fullname']    = $fullname;
				$newId    = $registor->addUser($options);
				if(! is_numeric($newId) )
				{
					// registration failed.
					$msg    = $newId;
				}
				else
				{
					$userId = $newId;
				}
			}
		}

		// Real logic operation goes here.
		$method = 'subscribe' . ucfirst( $type );

		// @rule: Process mailchimp subscriptions here.
		if ($config->get('subscription_mailchimp') && $config->get('subscription_mailchimp_listid')) {
			EasyBlogHelper::getHelper( 'Mailchimp' )->subscribe( $email , $fullname );
			return;	
		}

		if( !$this->$method( $id , $userId , $email , $fullname ) )
		{
			$theme	= new CodeThemes();
			$theme->set( 'message'	, JText::_('COM_EASYBLOG_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR') );
			$theme->set( 'type'		, $type );
			$theme->set( 'id'		, $id );
			$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_ERROR_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.error.php' ) )->toObject();

			$ejax->dialog( $options );
			return $ejax->send();
		}

		// message
		if($register && is_numeric($newId) )
		{
			$message	= JText::sprintf( 'COM_EASYBLOG_YOU_SUCCESSFULLY_SUBSCRIBED_AND_REGISTERED_AS_MEMBER' );
		}
		else
		{
			$message = JText::sprintf( 'COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBED_SUCCESS' , $email );
		}

		$theme	= new CodeThemes();
		$theme->set( 'message'	, $message );
		$options	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_SUBSCRIPTION_SUCCESS_DIALOG_TITLE') )->set( 'content' , $theme->fetch( 'ajax.dialog.subscribe.success.php' ) )->toObject();
		$ejax->dialog( $options );

		// Send email to notify admin upon successful subscriptions
		$user 	= EasyBlogHelper::getTable( 'Profile' );
		$user->load( $userId );
		$date	= EasyBlogDateHelper::getDate();

		$subscriberName = ($my->id == 0) ? $post['esfullname'] : $user->getName();

		$data	= array(
					'title'				=> JText::_( 'COM_EASYBLOG_SUBSCRIPTION_SUCCESS_DIALOG_TITLE' ),
					'subscriber'		=> $subscriberName,
					'subscriberLink'	=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id , false , true ),
					'subscriberAvatar'	=> $user->getAvatar(),
					'subscriberDate'	=> EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' ),
					'type'				=> $type
		);

		if( $type == 'entry' )
		{
			$data[ 'reviewLink' ] = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $id , false , true );
		}
		if( $type == 'category' )
		{
			$data[ 'reviewLink' ] = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $id , false , true );
		}

		$emailTitle 	= JText::_( 'COM_EASYBLOG_NEW' ) . ' ' . JText::_( 'COM_EASYBLOG_SUBSCRIPTION_TYPE_' . strtoupper( $type ) ) . ' ' . strtolower(JText::_( 'COM_EASYBLOG_SUBSCRIPTION' ));
		$emails			= array();
		$notification 	= EasyBlogHelper::getHelper( 'Notification' );
		$config 		= EasyBlogHelper::getConfig();

		// @rule: if custom_email_as_admin is enabled, use custom email as admin email
		if( $config->get( 'custom_email_as_admin' ) )
		{
			// @rule: Send to custom email addresses
			$notification->getCustomEmails( $emails );
		}
		else
		{
			// @rule: Send to administrator's on the site.
			$notification->getAdminEmails( $emails );
		}
		$notification->send( $emails , $emailTitle , 'email.subscriptions' , $data );


		return $ejax->send();
	}

	/*
	 * Add subscription for site.
	 *
	 * @param	int		$id		The unique identifie
	 * @param	int		$userId	The subject's id.
	 * @param	string	$email	The subject's email address.
	 * @param	string	$fullname	The subject's full name.
	 * @return	boolean	True on success false otherwise.
	 **/
	private function subscribeSite( $id = '' , $userId = 0 , $email , $fullname )
	{
		$model	= $this->getModel( 'Subscription' );

		if( $userId == 0 )
		{
			$sid	= $model->isSiteSubscribedEmail( $email );

			if( !empty( $sid ) )
			{
				return false;
			}
			$model->addSiteSubscription($email, '', $fullname);
			return true;
		}

		$sid	= $model->isSiteSubscribedUser( $userId, $email );

		if( !empty( $sid ) )
		{
			// user found update the email address
			$model->updateSiteSubscriptionEmail($sid, $userId, $email);
			return true;
		}

		// @task: We presume there are no errors here and proceed to add new subscription.
		$model->addSiteSubscription( $email, $userId, $fullname );
		return true;
	}

	/*
	 * Add subscription for category.
	 *
	 * @param	int		$id		The unique identifie
	 * @param	int		$userId	The subject's id.
	 * @param	string	$email	The subject's email address.
	 * @param	string	$fullname	The subject's full name.
	 * @return	boolean	True on success false otherwise.
	 **/
	private function subscribeCategory( $id , $userId = 0 , $email , $fullname )
	{
		$model	= $this->getModel( 'Category' );
		$sid    = '';

		if($userId == 0)
		{
			$sid = $model->isCategorySubscribedEmail( $id , $email);

			if( !empty( $sid ) )
			{
				return false;
			}
			$model->addCategorySubscription( $id , $email, '', $fullname);
			return true;
		}

		$sid = $model->isCategorySubscribedUser( $id , $userId, $email);

		if( !empty( $sid ) )
		{
			// user found update the email address
			$model->updateCategorySubscriptionEmail($sid, $userId, $email);
			return true;
		}
		//add new subscription.
		$model->addCategorySubscription( $id , $email, $userId, $fullname);
		return true;
	}

	/*
	 * Add subscription for blogger.
	 *
	 * @param	int		$id		The unique identifie
	 * @param	int		$userId	The subject's id.
	 * @param	string	$email	The subject's email address.
	 * @param	string	$fullname	The subject's full name.
	 * @return	boolean	True on success false otherwise.
	 **/
	private function subscribeBlogger( $id , $userId = 0 , $email , $fullname )
	{
		$model	= $this->getModel( 'Blogger' );
		$sid    = '';

		if( $userId == 0)
		{
			$sid = $model->isBloggerSubscribedEmail( $id , $email);

			if( !empty( $sid ) )
			{
				return false;
			}
			$model->addBloggerSubscription( $id , $email, '', $fullname);
			return true;
		}

		$sid = $model->isBloggerSubscribedUser( $id , $userId, $email);

		if( !empty( $sid ) )
		{
			// user found update the email address
			$model->updateBloggerSubscriptionEmail($sid, $userId, $email);
			return true;
		}

		//add new subscription.
		$model->addBloggerSubscription( $id , $email, $userId, $fullname);
		return true;
	}

	/*
	 * Additional rule checks for team subscription
	 *
	 * @param	int	$id		The team's subject id.
	 * @return	boolean		True on success, false otherwise.
	 */
	private function allowSubscribeTeam( $id )
	{
		JTable::addIncludePath( EBLOG_TABLES );
		$team	= EasyBlogHelper::getTable( 'Teamblog', 'Table' );
		$team->load( $id );

		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();

		$gid		= EasyBlogHelper::getUserGids();
		return $team->allowSubscription( $team->access , $my->id , $team->isMember( $my->id, $gid ) , $acl->rules->allow_subscription );
	}

	/*
	 * Add subscription for team.
	 *
	 * @param	int		$id		The unique identifie
	 * @param	int		$userId	The subject's id.
	 * @param	string	$email	The subject's email address.
	 * @param	string	$fullname	The subject's full name.
	 * @return	boolean	True on success false otherwise.
	 **/
	function subscribeTeam( $id , $userId = 0 , $email , $fullname )
	{
		// Just in case someone's try to hack through.
		if( !$this->allowSubscribeTeam( $id ) )
		{
			echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM_NO_PERMISSION');
			exit;
		}

		$model	= $this->getModel( 'TeamBlogs' );
		$sid    = '';

		if($userId == 0)
		{
			$sid = $model->isTeamSubscribedEmail( $id , $email);

			if( !empty( $sid ) )
			{
				return false;
			}
			$model->addTeamSubscription( $id , $email, '', $fullname);
			return true;
		}

		$sid = $model->isTeamSubscribedUser( $id , $userId, $email);

		if( !empty( $sid ) )
		{
			// @task: user found, update email address accordingly.
			$model->updateTeamSubscriptionEmail($sid, $userId, $email);
			return true;
		}

		// @task: add new subscription.
		$model->addTeamSubscription($id, $email, $userId, $fullname);
		return true;
	}

	/*
	 * Add subscription for entry.
	 *
	 * @param	int		$id		The unique identifie
	 * @param	int		$userId	The subject's id.
	 * @param	string	$email	The subject's email address.
	 * @param	string	$fullname	The subject's full name.
	 * @return	boolean	True on success false otherwise.
	 */
	function subscribeEntry( $id , $userId = 0 , $email , $fullname )
	{
		$model	= $this->getModel( 'Blog' );
		$sid    = '';

		if( $userId == 0 )
		{
			$sid	= $model->isBlogSubscribedEmail( $id , $email );

			if( !empty( $sid ) )
			{
				return false;
			}
			$model->addBlogSubscription( $id , $email, '', $fullname );
			return true;
		}

		$sid	= $model->isBlogSubscribedUser( $id , $userId , $email);

		if( !empty( $sid ) )
		{
			// @task: User found, update the email address
			$model->updateBlogSubscriptionEmail($sid, $userId, $email);
			return true;
		}
		$model->addBlogSubscription( $id , $email, $userId, $fullname);
		return true;
	}
}
