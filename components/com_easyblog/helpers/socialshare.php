<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'oauth.php' );

class EasyBlogSocialShareHelper
{
	/**
	 * Shares a story through 3rd party oauth clients
	 *
	 * @param	TableBlog	$blog	A blog table object
	 * @param	string		$type	The type of oauth client
	 *
	 * @return	boolean		True on success and false otherwise.
	 **/
	public function share( $blog , $type , $useCentralized = false )
	{
	    JTable::addIncludePath( EBLOG_TABLES );
		$oauth			= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$oauths			= array();

		$config			= EasyBlogHelper::getConfig();

		// @rule: Process centralized notifications, be it a page or an account update
		if( $useCentralized )
		{
			$userId		= $config->get( 'integrations_' . strtolower( $type ) . '_centralized_userid' );
			$message	= $config->get('integrations_'.JString::strtolower($type).'_centralized_auto_post');
			$auto		= $config->get('integrations_'.JString::strtolower($type).'_centralized_auto_post');

			//defined _centralized_send_updates option
			$autoUpdate = $config->get('integrations_'.JString::strtolower($type).'_centralized_send_updates');

			$oauth->loadByUser( $userId , $type );

			// For legacy fix prior to 3.0, we need to set system=1
			if( $oauth->id && !$oauth->system )
			{
				$oauth->system	= 1;
				$oauth->store();

			}

			// @task: Now we try to load the real object
			$oauth->loadSystemByType( $type );

			if( $oauth->id )
			{
				$oauths[]	= $oauth;
			}

		}
		else
		{
			if( $config->get( 'integrations_'.JString::strtolower($type).'_centralized_and_own' ) )
			{
			    JTable::addIncludePath( EBLOG_TABLES );
				$oauth			= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
				$oauth->loadByUser( $blog->created_by , $type );

				$oauths[]	= $oauth;
			}
		}

		$key	= $config->get( 'integrations_' . $type . '_api_key' );
		$secret	= $config->get( 'integrations_' . $type . '_secret_key' );

		// @rule: Set callback URLs
		$callback	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=oauth&task=grant&type=' . $type , false , true );

		if( $oauths )
		{
			foreach( $oauths as $oauth )
			{
				// Skip processing anything that does not have an access token
				if( !$oauth->access_token )
				{
					continue;
				}

				$acl = EasyBlogACLHelper::getRuleSet($blog->created_by);
				$rule = 'update_'.$type;

				// @task: If entry is already shared or automatic postings is disabled or auto updated disable do not share this entry.
				if(($oauth->isShared($blog->id , $useCentralized) && !$autoUpdate) || (!$acl->rules->{$rule} && !EasyBlogHelper::isSiteAdmin()))
				{
					continue;
				}

				// @rule: Retrieve the consumer object for this oauth client.
				$consumer	= EasyBlogOauthHelper::getConsumer( $type , $key , $secret , $callback );
				$consumer->setAccess( $oauth->access_token );

				if( $consumer->share( $blog, $oauth->message , $oauth , $useCentralized ) )
				{
					// @task: mark this as sent!
					$oauthPost	= EasyBlogHelper::getTable( 'OauthPost' , 'Table' );
					$oauthPost->loadByOauthId( $blog->id , $oauth->id );
					$date					= EasyBlogHelper::getDate();
					$oauthPost->post_id		= $blog->id;
					$oauthPost->oauth_id	= $oauth->id;
					$oauthPost->created		= $date->toMySQL();
					$oauthPost->modified	= $date->toMySQL();
					$oauthPost->sent		= $date->toMySQL();
					$oauthPost->store();
				}
			}
		}
		return true;
	}

	//kiv for the time being
	public static function getLink($type, $id)
	{
		if(empty($type) || empty($id))
		{
			return false;
		}

		//prevent jtable is not loading incase overwritten by other component.
		JTable::addIncludePath(EBLOG_TABLES);

		$oauth	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$oauth->loadByUser( $id , $type );

		$param 		= EasyBlogHelper::getRegistry();
		$param->load( $oauth->params );

		$screenName = $param->get( 'screen_name', '');

		$acl	= EasyBlogACLHelper::getRuleSet($id);
		$rule	= 'update_'.$type;

		if(!$acl->rules->{$rule} )
		{
			return false;
		}

		switch($type)
		{
			case 'twitter':
				$link = empty($screenName)? '' : 'http://twitter.com/'.$screenName;
				break;
			case 'facebook':
				$link = '';
				break;
			case 'linkedin':
				$link = '';
				break;
		}

		return $link;
	}

	/**
	 * Determines if the user has enabled the auto updates settings.
	 *
	 * @param	int		$userId		The subject user.
	 * @param	string	$type		The type of social sharing.
	 */
	public function hasAutoPost( $userId , $type )
	{
		//check if centralized, then use centralized.
	    $config			= EasyBlogHelper::getConfig();

		JTable::addIncludePath( EBLOG_TABLES );
		$social	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$social->loadByUser( $userId , constant( 'EBLOG_OAUTH_' . strtoupper( $type ) ) );

		return $social->auto;
	}

 /*
	 * Determines whether the selected user has associated their accounts or not
	 *
	 * @param	int		$userId		The subject user.
	 * @param	string	$type		The type of social sharing.
	 */
	public function isAssociated( $userId , $type )
	{
	    //check if centralized, then use centralized.
 	    $config		= EasyBlogHelper::getConfig();

 	    $allowed	= $config->get( 'integrations_' . strtolower( $type ) . '_centralized_and_own' );

 	    if( !$allowed )
 	    {
 	    	return false;
 	    }

 	    $oauth	= EasyBlogHelper::getTable( 'Oauth' );
 	    return $oauth->loadByUser( $userId , constant( 'EBLOG_OAUTH_' . strtoupper( $type ) ) );
	}
}
