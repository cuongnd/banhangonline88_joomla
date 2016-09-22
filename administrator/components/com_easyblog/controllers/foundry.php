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

class EasyBlogControllerFoundry extends EasyBlogParentController
{
	function getResource()
	{
		$resources = JRequest::getVar( 'resource' );

		foreach( $resources as &$resource )
		{
			$resource = (object) $resource;
			$func = 'get' . ucfirst( $resource->type );
			$result = self::$func( $resource->name );

			if( $result !== false )
			{
				$resource->content = $result;
			}
		}

		header('Content-type: text/x-json; UTF-8');
		$json = new Services_JSON();
		echo $json->encode( $resources );
		exit;
	}

	function getView( $name = '', $type = '', $prefix = '', $config = Array() )
	{
		// Load language support for front end and back end.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$file = $name;

		$dashboard = explode( '/' , $file );

		if( $dashboard[0]=="dashboard" )
		{
			$template 	= new CodeThemes( true );
			$out		= $template->fetch( $dashboard[1] . '.ejs' );
		}
		elseif ( $dashboard[0]=="media" )
		{
			$template 	= new CodeThemes( true );
			$out		= $template->fetch( "media." . $dashboard[1] . '.ejs' );
		}
		else
		{
			$template 	= new CodeThemes();
			$out		= $template->fetch( $file . '.ejs' );
		}

		return $out;
	}

	function getLanguage( $lang )
	{
		// Load language support for front end and back end.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		return JText::_( strtoupper( $lang ) );
	}
}
