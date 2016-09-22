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
defined('_JEXEC') or die( 'Unauthorized Access');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );
jimport( 'joomla.application.component.view');

if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
{
	class EasyBlogParentView extends JViewLegacy
	{

	}
}
else
{
	class EasyBlogParentView extends JView
	{

	}
}

class EasyBlogView extends EasyBlogParentView
{
	function setPathway( $name , $link = '' )
	{
		static $views = null;

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

		$mainframe	= JFactory::getApplication();
		$pathway	= $mainframe->getPathway();


		// set this option to true if the breadcrumb didn't show the EasyBlog root menu.
		$showRootMenuItem   = false;

		if( EasyBlogHelper::getJoomlaVersion() <= '1.5' && $showRootMenuItem)
		{
			$latestPostMenuItemId   = EasyBlogRouter::getItemId('latest');

			if( !empty( $latestPostMenuItemId ) )
			{
				if( empty( $views['latest'] ) )
				{
					$menu 					= JFactory::getApplication()->getMenu();
					$EasyBlogMenuItem   	= $menu->getItem($latestPostMenuItemId);
					$view                   = JRequest::getCmd('view');

					if( $view != 'latest')
					{

						$frontpagelink   = JRoute::_('index.php?option=com_easyblog&view=latest&Itemid=' . $latestPostMenuItemId);
						$pathway->addItem($EasyBlogMenuItem->name, $frontpagelink);

						$views['latest']    = true;
					}
				}
			}
		}

		return $pathway->addItem( $name , $link );
	}

	function getModel( $name = null )
	{
		return EasyBlogHelper::getModel( $name );
	}

	function getView( $name , $tmpl = 'html')
	{
		static $view = array();

		if( !isset( $view[ $name ] ) )
		{
			$file	= JString::strtolower( $name );

			$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'view.'. $tmpl . '.php';

			jimport('joomla.filesystem.path');
			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'View file not found.' );
			}

			$viewClass		= 'EasyBlogView' . ucfirst( $name );

			if( !class_exists( $viewClass ) )
				require_once( $path );


			$view[ $name ] = new $viewClass();
		}

		return $view[ $name ];
	}

	protected function outputJSON( $output = null )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json 	= new Services_JSON();

		echo '<script type="text/json" id="ajaxResponse">' . $json->encode( $output ) . '</script>';
		exit;
	}

	/**
	 * Responsible to modify the title whenever necessary. Inherited classes should always use this method to set the title
	 */
	public function setPageTitle( $title , $pagination = null , $addSitePrefix = false )
	{
		$doc 	= JFactory::getDocument();
		$page 	= null;

		if( $addSitePrefix )
		{
			$config 	= EasyBlogHelper::getConfig();
			$title  	.= ' - ' . JText::_($config->get('main_title'));
		}

		if( $pagination && is_object( $pagination ) )
		{
			// @task: Get current page index.
			$page 		= $pagination->get( 'pages.current' );

			// @task: Append the current page if necessary.
			$title 		.= $page == 1 ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $page );
		}

		// @task: Set the title for the page.
		$doc->setTitle( $title );
	}
}
