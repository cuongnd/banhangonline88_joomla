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

require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewThemes extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.theme' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$themes		= $this->getThemes();

		$this->assign( 'default', $config->get( 'layout_theme' ) );
		$this->assign( 'themes'	, $themes );
		$this->assign( 'search' , '' );

		parent::display($tpl);
	}

	public function getThemes()
	{
		$path	= EBLOG_THEMES;

		$result	= JFolder::folders( $path , '.', false , true , $exclude = array('.svn', 'CVS' , '.' , '.DS_Store' ) );
		$themes	= array();


		// Cleanup output
		foreach( $result as $item )
		{
			$name		= basename( $item );

			if( $name != 'dashboard' )
			{
				$obj	= EasyBlogHelper::getThemeObject( $name );

				if( $obj )
				{
					$themes[]	= $obj;
				}
			}
		}

		return $themes;
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_THEMES_TITLE' ), 'themes' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'makedefault' , 'star' , '' , JText::_( 'COM_EASYBLOG_SET_DEFAULT' ) , false );
	}
}
