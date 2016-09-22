<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class OpengraphRenderer
{
	public static function add( $ogTag , $content )
	{
		$doc 	= JFactory::getDocument();
		$doc->addCustomTag( '<meta property="' . $ogTag . '" content="' . $content . '" />' );
	}

	public static function type( $type )
	{
		self::add( 'og:type' , $type );
	}

	public static function title( $title )
	{
		self::add( 'og:title' , $title );
	}

	public static function description( $content )
	{
		self::add( 'og:description' , $content );
	}

	public static function image( $images )
	{
		if( !$images || empty( $images ) )
		{
			return;
		}

		foreach( $images as $image )
		{
			self::add( 'og:image' 			, $image->url );

			if( $image->width )
			{
				self::add( 'og:image:width' 	, $image->width );
			}

			if( $image->height )
			{
				self::add( 'og:image:height'	, $image->height );
			}
		}
	}

	public static function url( $url )
	{
		self::add( 'og:url' , $url );
	}
}
