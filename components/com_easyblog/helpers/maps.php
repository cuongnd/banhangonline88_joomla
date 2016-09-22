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

class EasyBlogMapsHelper
{
	public function getHTML( $sensor = false , $address , $latitude , $longitude , $width = '450' , $height = '300', $tooltips = '' , $elementId , $options = array() )
	{
		static $loaded	= array();

		/*$sensor		= $sensor ? 'true' : 'false';

		if( !isset( $loaded[ $sensor ] ) )
		{
			$document	= JFactory::getDocument();
			$lang		= EasyBlogHelper::getConfig()->get( 'main_locations_blog_language' );

			$document->addScript( 'https://maps.googleapis.com/maps/api/js?sensor=' . $sensor . '&language=' . $lang );
			$loaded[ $sensor ]	= true;
		}*/


		// Do not try to process anything that's invalid
		if( $latitude == '' || $longitude == '' )
		{
			return false;
		}

		// Now get the appropriate html codes
		$config		= EasyBlogHelper::getConfig();
		$static		= $config->get( 'main_locations_static_maps' ) ? '.static' : '';
		$lang		= $config->get( 'main_locations_blog_language', 'en' );
		$mapType	= $config->get( 'main_locations_map_type' );
		$maxZoom	= $config->get( 'main_locations_max_zoom_level' );
		$minZoom	= $config->get( 'main_locations_min_zoom_level' );
		$defaultZoom	= $config->get( 'main_locations_default_zoom_level' );

		$theme		= new CodeThemes();
		$theme->set( 'uid'			, uniqid() );
		$theme->set( 'defaultZoom'	, $defaultZoom );
		$theme->set( 'maxZoom'		, $maxZoom );
		$theme->set( 'minZoom'		, $minZoom );
		$theme->set( 'mapType'	, $mapType );
		$theme->set( 'lang'		, $lang );
		$theme->set( 'address'	, $address );
		$theme->set( 'latitude' , $latitude );
		$theme->set( 'width'	, $width );
		$theme->set( 'sensor'	, $sensor ? 'true' : 'false' );
		$theme->set( 'height'	, $height );
		$theme->set( 'longitude' , $longitude );
		$theme->set( 'elementId'	, $elementId );
		$theme->set( 'options'		, $options );
		$theme->set( 'tooltips'		, $tooltips );

		// Determine the locale 
		$doc 		= JFactory::getDocument();
		$language	= $doc->getLanguage();
		$language	= explode( '-' , $language );

		if( count( $language ) != 2 )
		{
			$language	= array( 'en' , 'GB' );
		}

		$locale 	= $language[0];

		$theme->set( 'locale' , $locale );
		
		return $theme->fetch( 'blog.map' . $static . '.php' );
	}
}
