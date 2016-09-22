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

class EasyBlogCaptchaHelper
{
	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function getHTML()
	{
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();

		// @task: If no captcha is enabled, we always default to true.
		if( !$config->get( 'comment_recaptcha' ) && !$config->get( 'comment_captcha' ) )
		{
			return false;
		}

		if( !$config->get( 'comment_captcha_registered' ) && $my->id > 0 )
		{
			return false;
		}
		$output		= '';
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			return EasyBlogRecaptcha::getHTML( $config->get( 'comment_recaptcha_public') , $config->get('comment_recaptcha_theme') , $config->get('comment_recaptcha_lang') , null, $config->get('comment_recaptcha_ssl') );
		}

		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		return EasyBlogCaptcha::getHTML();
	}

	public function verify( $post )
	{
		$config		= EasyBlogHelper::getConfig();
		$output		= '';
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';
		$my			= JFactory::getUser();

		// @task: If no captcha is enabled, we always default to true.
		if( !$config->get( 'comment_recaptcha' ) && !$config->get( 'comment_captcha' ) )
		{
			return true;
		}

		if( !$config->get( 'comment_captcha_registered' ) && $my->id > 0 )
		{
			return true;
		}

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			return EasyBlogRecaptcha::recaptcha_check_answer( $config->get( 'comment_recaptcha_private') , @$_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] )->is_valid;
		}

		// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );

		if( !isset( $post[ 'captcha-response' ] ) || !isset( $post[ 'captcha-id' ] ) )
		{
			return false;
		}

		return EasyBlogCaptcha::verify( $post[ 'captcha-response' ] , $post[ 'captcha-id' ] );
	}

	/**
	 * Throws error message and reloads the captcha image.
	 * @param	Ejax	$ejax	Ejax object
	 * @return	string	The json output for ajax calls
	 **/
	public function getError( $ajax , $post )
	{
		$config		= EasyBlogHelper::getConfig();
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			return EasyBlogRecaptcha::getError( $ajax , $post );
		}

		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		return EasyBlogCaptcha::getError( $ajax , $post );
	}

	/**
	 * Reload the captcha image.
	 * @param	Ejax	$ejax	Ejax object
	 * @return	string	The javascript action to reload the image.
	 **/
	public function reload( $ajax , $post )
	{
		$config		= EasyBlogHelper::getConfig();
		$adapters	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'captcha';

		// If no captcha is enabled, ignore it.
		if( !$config->get('comment_recaptcha') && !$config->get( 'comment_captcha' ) )
		{
			return true;
		}

		$public		= $config->get( 'comment_recaptcha_public');
		$private	= $config->get( 'comment_recaptcha_private');

		if( $config->get( 'comment_recaptcha' ) && $config->get('comment_recaptcha') && $config->get('comment_recaptcha_public') )
		{
			require_once( $adapters . DIRECTORY_SEPARATOR . 'recaptcha.php' );
			$ajax->script( EasyBlogRecaptcha::getReloadScript( $ajax , $post ) );
			return true;
		}

		// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
		// Generate a new captcha
		if( isset( $post[ 'captcha-id' ] ) )
		{
			$ref	= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
			$ref->load( $post[ 'captcha-id' ] );

			$state = $ref->delete();
			if( $state )
			{
				// we need to unset this variable so that when calling EasyBlogCaptcha::getReloadScript, EB will not run the deletion again.
				unset( $post[ 'captcha-id' ] );
			}
		}

		require_once( $adapters . DIRECTORY_SEPARATOR . 'captcha.php' );
		$ajax->script( EasyBlogCaptcha::getReloadScript( $ajax , $post ) );
		return true;
	}
}
