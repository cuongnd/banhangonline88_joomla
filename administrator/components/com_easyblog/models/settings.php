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

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class EasyBlogModelSettings extends EasyBlogModelParent
{
	function __construct()
	{
		parent::__construct();
	}

	function getThemes( $type = 'client' )
	{
		static $themes	= array();

		if( !isset( $themes[ $type ] ) )
		{
			if( $type == 'dashboard' )
			{
				$themes[ $type ]	= JFolder::folders( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' );
			}
			else
			{
				$themes[ $type ]	= JFolder::folders( EBLOG_THEMES , '.' , false , false , array( 'dashboard' ) );
			}
		}

		return $themes[ $type ];
	}

	function save( $data )
	{
		$config	= EasyBlogHelper::getTable( 'Configs' , 'Table' );
		$config->load( 'config' );

		$registry 		= EasyBlogHelper::getRegistry( $this->_getParams() );


		foreach( $data as $index => $value )
		{
			$registry->set( $index , $value );
		}

		// Get the complete INI string
		$config->params	= $registry->toString( 'INI' );

		// Save it
		if(!$config->store() )
		{
			return false;
		}
		return true;
	}

	function &_getParams( $key = 'config' )
	{
		static $params	= null;

		if( is_null( $params ) )
		{
			$db		= EasyBlogHelper::db();

			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'params' ) . ' '
					. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_configs' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'name' ) . '=' . $db->Quote( $key );

			$db->setQuery( $query );

			$params	= $db->loadResult();
		}

		return $params;
	}

	function &getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			$params		=& $this->_getParams( 'config' );


			$config		= EasyBlogHelper::getRegistry( $params );
		}

		return $config;
	}
}
