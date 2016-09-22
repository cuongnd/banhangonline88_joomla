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

jimport('joomla.application.component.controller');

require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'oauth.php' );

class EasyBlogControllerOauth extends EasyBlogParentController
{
	function __construct()
	{
		// Include the tables in path
		JTable::addIncludePath( EBLOG_TABLES );

		parent::__construct();
	}

	function request()
	{
		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') , 'error' );
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog' , false ) );
			return;
		}

		$redirect	= JRequest::getVar( 'redirect' , '' );

		if( !empty( $redirect ) )
		{
			$redirect 	= '&redirect=' . $redirect;
		}

		$call 		= JRequest::getWord( 'call' );
		$callUri 	= !empty( $call ) ? '&call=' . $call : '';

		$type		= JRequest::getCmd( 'type' );
		$config		= EasyBlogHelper::getConfig();
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$callback	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=oauth&task=grant&type=' . $type . $redirect . $callUri , false , true );
		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$request	= $consumer->getRequestToken();

		if( empty( $request->token ) || empty( $request->secret ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_OAUTH_KEY_INVALID') , 'error');
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return;
		}

		$oauth				= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$oauth->user_id		= JFactory::getUser()->id;
		$oauth->type		= $type;
		$oauth->created		= EasyBlogHelper::getDate()->toMySQL();

		// Bind the request tokens
		$param				= EasyBlogHelper::getRegistry('');
		$param->set( 'token' , $request->token );
		$param->set( 'secret' , $request->secret );

		$oauth->request_token	= $param->toString();

		$oauth->store();

		$this->setRedirect( $consumer->getAuthorizationURL( $request->token , false , 'popup') );
	}

	/**
	 * This will be a callback from the oauth client.
	 * @param	null
	 * @return	null
	 **/
	public function grant()
	{
		$type		= JRequest::getCmd( 'type' );
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$my			= JFactory::getUser();

		$redirect		= JRequest::getVar( 'redirect' , '' );
		$redirectUri 	= !empty( $redirect ) ? '&redirect=' . $redirect : '';

		// @task: Let's see if caller wants us to go to any specific location or not.
		if( !empty( $redirect ) )
		{
			$redirect	= base64_decode( $redirect );
		}

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') , 'error' );
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog' , false ) );
			return;
		}

		$oauth		= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$loaded		= $oauth->loadByUser( $my->id , $type );

		$denied     = JRequest::getVar( 'denied' , '' );

		$call 		= JRequest::getWord( 'call' );
		$callUri 	= !empty( $call ) ? '&call=' . $call : '';

		if( !empty( $denied ) )
		{
		    $oauth->delete();
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_OAUTH_DENIED_ERROR') , 'error' );
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return;
		}

		if( !$loaded )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_OAUTH_UNABLE_TO_LOCATE_RECORD') , 'error' );
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return;
		}

		$request	= EasyBlogHelper::getRegistry( $oauth->request_token );
		$callback	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=oauth&task=grant&type=' . $type . $redirectUri . $callUri , false , true );
		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$verifier	= $consumer->getVerifier();

		if( empty( $verifier ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			JError::raiseError( 500 , JText::_( 'COM_EASYBLOG_INVALID_VERIFIER_CODE' ) );
		}

		$access		= $consumer->getAccess( $request->get( 'token' ) , $request->get( 'secret' ) , $verifier );

		if( !$access || empty( $access->token ) || empty( $access->secret ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_OAUTH_ACCESS_TOKEN_ERROR') , 'error');
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return;
		}

		$param		= EasyBlogHelper::getRegistry('');
		$param->set( 'token' 	, $access->token );
		$param->set( 'secret'	, $access->secret );

		if( isset( $access->expires ) )
		{
			$param->set( 'expires' , $access->expires );
		}

		$oauth->access_token	= $param->toString();
		$oauth->params			= $access->params;

		$oauth->store();

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_OAUTH_SUCCESS') );

		$url 		=  EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false );

		if( !empty( $redirect ) )
		{
			$url 	= $redirect;
		}

		// @task: Let's see if the oauth client
		if( !empty( $call ) )
		{
			$consumer->$call();
		}
		else
		{
			$this->setRedirect( $url );
		}
	}

	/**
	 * Responsible to revoke access for the specific oauth client
	 *
	 * @param	null
	 * @return	null
	 **/
	public function revoke()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$url		= EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false );
		$redirect	= JRequest::getVar( 'redirect' , '' );
		$type		= JRequest::getWord( 'type' );
		$config		= EasyBlogHelper::getConfig();

		if( !empty( $redirect ) )
		{
			$url	= base64_decode( $redirect );
		}

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') , 'error' );
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog' , false ) );
			return;
		}

		$oauth		= EasyBlogHelper::getTable( 'OAuth' , 'Table' );
		$oauth->loadByUser( $my->id , $type );

		// Revoke the access through the respective client first.
		$callback	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=oauth&task=grant&type=' . $type , false , true );
		$key		= $config->get( 'integrations_' . $type . '_api_key' );
		$secret		= $config->get( 'integrations_' . $type . '_secret_key' );
		$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
		$consumer->setAccess( $oauth->access_token );

		// @task: Only show errors when the user is really authenticated with the respective provider.
		if( !$consumer->revokeApp() && !empty( $oauth->access_token) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_APPLICATION_REVOKED_ERROR') , 'error');
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return;
		}
		$oauth->delete();

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_APPLICATION_REVOKED_SUCCESSFULLY') );
		$this->setRedirect( $url );
	}
}
