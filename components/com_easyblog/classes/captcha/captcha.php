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

class EasyBlogCaptcha
{
	public static function getHTML()
	{
		$captcha			= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
		$captcha->created	= EasyBlogHelper::getDate()->toMySQL();
		$captcha->store();

		$theme		= new CodeThemes();
		$theme->set( 'id' , $captcha->id );
		return $theme->fetch( 'comment.captcha.php' );
	}

	public static function verify( $response , $id )
	{
		JTable::addIncludePath( EBLOG_TABLES );
		$captcha	= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
		$captcha->load( $id );

		if( empty( $captcha->response ) )
		{
			return false;
		}

		if( !$captcha->verify( $response ) )
		{
			return false;
		}

		return true;
	}

	public function getError( $ajax , $post )
	{
		$ajax->script( EasyBlogCaptcha::getReloadScript( $ajax, $post ) );
		$ajax->script( 'eblog.comment.displayInlineMsg( "error" , "'.JText::_('COM_EASYBLOG_CAPTCHA_INVALID_RESPONSE').'");' );
		$ajax->script( 'eblog.spinner.hide();' );
		$ajax->script( "eblog.loader.doneLoading();" );
		return $ajax->send();
	}

	public static function getReloadScript( $ajax , $post )
	{
		JTable::addIncludePath( EBLOG_TABLES );

		if( isset( $post[ 'captcha-id' ] ) )
		{
			$ref	= EasyBlogHelper::getTable( 'Captcha' , 'Table' );
			$state 	= $ref->load( $post[ 'captcha-id' ] );
			if( $state )
			{
				$ref->delete();
			}
		}

		return 'eblog.captcha.reload();';
	}
}
