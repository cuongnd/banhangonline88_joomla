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

class EasyBlogViewTheme extends EasyBlogAdminView
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
		JHTML::_( 'behavior.tooltip' );

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();

		$element 	= JRequest::getWord( 'element' );
		$theme 		= EasyBlogHelper::getThemeObject( $element );

		$blogImageFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $element . DIRECTORY_SEPARATOR . 'image.ini';
		$blogImage 		= false;

		jimport( 'joomla.filesystem.file' );

		if( JFile::exists( $blogImageFile ) )
		{
			$contents		= JFile::read( $blogImageFile );

			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR.  'com_easyblog' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'json.php' );

			$json 	= new Services_JSON();
			$types 	= $json->decode( $contents );

			foreach( $types as $type )
			{
				if( $type->name == 'frontpage' || $type->name == 'entry' )
				{
					$blogImage[ $type->name ]	= $type;
				}
			}
		}

		$this->assign( 'blogimages'	, $this->getBlogImages( $theme->element ) );
		$this->assign( 'param'		, $this->getParams( $theme->element ) );
		$this->assign( 'blogImage'	, $blogImage );
		$this->assign( 'theme' 		, $theme );
		$this->assign( 'config'		, $config );

		parent::display($tpl);
	}

	public function form()
	{
		$app = JFactory::getApplication();
		parent::display('new_theme');
	}

	public function getBlogImages( $theme )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$file		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'image.ini';
		$contents	= JFile::read( $file );

		$json 		= new Services_JSON();
		$sizes 		= $json->decode( $contents );

		return $sizes;
	}

	public function getParams( $theme )
	{
		static $param	= false;

		if( !$param )
		{
			$ini 		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'config.ini';
			$manifest	= EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'config.xml';
			$contents	= JFile::read( $ini );

			$param		= EasyBlogHelper::getRegistry( $contents );

			$themeConfig	= EasyBlogHelper::getTable( 'Configs' );
			$themeConfig->load( $theme );

			// @rule: Overwrite with the settings from the database.
			if( !empty( $themeConfig->params ) )
			{
				$themeParam 	= EasyBlogHelper::getRegistry( $themeConfig->params );

				EasyBlogHelper::getHelper( 'Registry' )->extend( $param , $themeParam );
			}
		}

		return $param;
	}

	public function renderParams( $theme , $group = '_default' )
	{
		$ini 		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'config.ini';
		$manifest	= EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'config.xml';

		$param 		= $this->getParams( $theme );

		$form 		= EasyBlogHelper::getForm( $theme , $param->toString() , $manifest , 'params' );
		$fields		= $form->getFormValues();

		// $param 		= $this->getParams( $theme );
		// $settings	= $param->getParams( 'params' , '_default' );

		$output 		= '';

		foreach( $fields as $field )
		{
			$output		.= '<tr>';
			$output 	.= '<td class="key"><label>' . JText::_( $field->label ) . '</label></td>';
			$output		.= '<td><div class="has-tip">';
			$output 	.= '	<div class="tip"><i></i>' . JText::_( $field->desc ) . '</div>';

			// Cheap hack, to detect if this is a 'select list'.
			if( strtolower( $field->type ) == 'list' )
			{
				$output 	.= $field->input;
			}
			else
			{
				$output 	.= '	' . $this->renderCheckbox( 'params[' . $field->key . ']' , $field->value , $field->key );
			}
			$output 	.= '</div></td>';
			$output 	.= '</tr>';
		}

		return $output;
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}

	function registerToolbar()
	{
		$layout = JRequest::getVar('layout');
		$clearCacheIcon = 'delete';

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$clearCacheIcon = 'refresh';
		}
		
		JToolBarHelper::custom( 'cleanImages', $clearCacheIcon, '', JText::_( 'COM_EASYBLOG_CLEAN_IMAGES' ), false);
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_THEMES_TITLE' ), 'themes' );

		if( $layout == 'form' )
		{
			JToolBarHelper::save( 'addTheme' );
		}
		else
		{
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolbarHelper::trash( 'removeSetting' );
		}
	}
}
