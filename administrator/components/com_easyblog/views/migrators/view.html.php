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

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

require_once( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.migrator' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		//check if myblog installed or not.
		$myblogInstalled	= $this->myBlogExists();
		$myBlogSection		= '';
		if($myblogInstalled)
		{
			require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_myblog' . DIRECTORY_SEPARATOR . 'config.myblog.php');
			$myblogConfig	= new MYBLOG_Config();
			$myBlogSection	= $myblogConfig->get('postSection');
		}

		JHTML::_( 'behavior.tooltip' );

		$categories[]	= JHTML::_('select.option', '0', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_CATEGORY').' -');
		$authors[]		= JHTML::_('select.option', '0', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_AUTHOR').' -', 'created_by', 'name');

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$lists['sectionid'] = array();

			$articleCat		= JHtml::_('category.options', 'com_content');

			$articleAuthors	= $this->get( 'ArticleAuthors16' );
		}
		else
		{
			// get list of sections for dropdown filter
			$lists['sectionid'] = $this->section($myBlogSection, 'sectionId', -1, '');

			// get article categories from model
			$model	= $this->getModel( 'Migrators' );
			$articleCat		= $model->getArticleCategories( $myBlogSection );

			// get article authors from model
			$articleAuthors		= $this->get( 'ArticleAuthors' );
		}

		$categories		= array_merge($categories, $articleCat);
		$lists['catid'] = JHTML::_('select.genericlist',  $categories, 'catId', 'class="inputbox"', 'value', 'text', '');

		$authors 	= array_merge($authors, $articleAuthors);
		$lists['authorid'] = JHTML::_('select.genericlist',  $authors, 'authorId', 'class="inputbox"', 'created_by', 'name', 0);


		// state filter
		$state			= $this->getDefaultState();

		//$state			= array('P' => 'Published', 'U' => 'Unpublished', 'A' => 'Archived');

		$articleState	= array();
		foreach($state as $key => $val)
		{
			$obj		= new stdClass();
			$obj->state	= $val;
			$obj->value	= $key;

			$articleState[]	= $obj;
		}

		$stateList		= array();
		$stateList[]	= JHTML::_('select.option', '*', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_STATE').' -', 'value', 'state');

		$stateList		= array_merge($stateList, $articleState);
		$lists['state']	= JHTML::_('select.genericlist',  $stateList, 'stateId', 'class="inputbox"', 'value', 'state', '*');

		//check if wordpress installed or not.
		$lists['wpblogs']	= array();
		$wpInstalled		= $this->wpExists();
		$wpBlogsList		= '';
		if($wpInstalled)
		{
			$wpBlogsList		= $this->getWPBlogs();
			$lists['wpblogs']	= JHTML::_('select.genericlist',  $wpBlogsList, 'wpBlogId', 'class="inputbox"', 'value', 'state', '');
		}

		// Fetch K2 categories
		$lists[ 'k2cats' ]		= $this->getK2Categories();
		// get wp xml files
		$wpxmlfiles  = $this->getXMLFiles( 'wordpress' );
		$lists['wpxmlfiles']	= JHTML::_('select.genericlist',  $wpxmlfiles, 'wpxmlfiles', 'class="inputbox"', 'value', 'state', '');

		$bloggerxmlfiles  = $this->getXMLFiles( 'blogger' );
		$lists['bloggerxmlfiles']	= JHTML::_('select.genericlist',  $bloggerxmlfiles, 'bloggerxmlfiles', 'class="inputbox"', 'value', 'state', '');

		$smartblogInstalled		= $this->smartBlogExists();
		$lyftenbloggieInstalled	= $this->lyftenBloggieExists();
		$jomcommentInstalled	= $this->jomcommentExists();

		$this->assignRef( 'smartblogInstalled' , $smartblogInstalled );
		$this->assignRef( 'lyftenbloggieInstalled' , $lyftenbloggieInstalled );
		$this->assignRef( 'jomcommentInstalled' , $jomcommentInstalled );
		$this->assignRef( 'myblogInstalled' , $myblogInstalled );
		$this->assignRef( 'myBlogSection' 	, $myBlogSection );
		$this->assignRef( 'wpInstalled' 	, $wpInstalled );

		$this->assignRef( 'lists' , $lists );
		parent::display($tpl);
	}

	function section($excludeSection='', $name, $active = NULL, $javascript = NULL, $order = 'ordering', $uncategorized = true, $scope = 'content' )
	{
		$db = EasyBlogHelper::db();

		$categories[] = JHTML::_('select.option',  '-1', '- '. JText::_( 'COM_EASYBLOG_MIGRATORS_SELECT_SECTION' ) .' -' );

		if ($uncategorized) {
			$categories[] = JHTML::_('select.option',  '0', JText::_( 'Uncategorized' ) );
		}

		$excludeSQL = '';
		if( !empty($excludeSection) )
		{
			$excludeSQL = ' AND id != ' . $db->Quote($excludeSection);
		}

		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__sections'
		. ' WHERE published = 1'
		. ' AND scope = ' . $db->Quote($scope)
		. $excludeSQL
		. ' ORDER BY ' . $order
		;
		$db->setQuery( $query );
		$sections = array_merge( $categories, $db->loadObjectList() );

		$category = JHTML::_('select.genericlist',   $sections, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $category;
	}

	function getDefaultState()
	{
		$state			= null;
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$state = array('1' => 'Published', '0' => 'Unpublished', '2' => 'Archived', '-2' => 'Trash');
		}
		else
		{
			$state = array('P' => 'Published', 'U' => 'Unpublished', 'A' => 'Archived');
		}
		return $state;
	}

	function smartBlogExists()
	{
		if(! JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_blog' . DIRECTORY_SEPARATOR . 'blog.php'))
		{
			return false;
		}
		return true;
	}

	public function jomcommentExists()
	{
		return JFolder::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jomcomment' );
	}

	function myBlogExists()
	{
		if(! JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_myblog' . DIRECTORY_SEPARATOR . 'config.myblog.php'))
		{
			return false;
		}
		return true;
	}

	function lyftenBloggieExists()
	{
		if(! JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_lyftenbloggie' . DIRECTORY_SEPARATOR . 'lyftenbloggie.php'))
		{
			return false;
		}
		return true;
	}

	function getWPBlogs()
	{
		$db = EasyBlogHelper::db();

		$query		= 'select * from `#__wp_blogs`';
		$db->setQuery( $query );

		$result		= $db->loadObjectList();

		$htmlList	= array();
		if( count($result) > 0)
		{
			foreach( $result as $item)
			{
				$htmlList[]	= JHTML::_('select.option', $item->blog_id, $item->domain . $item->path, 'value', 'state');
			}
		}

		if( count( $htmlList ) <= 0 )
		{
			//this could be single site wordpress.
			$query  = 'SHOW TABLES LIKE ' . $db->Quote( '%wp_posts%' );
			$db->setQuery( $query );

			$result = $db->loadObjectList();
			if( count( $result ) > 0 )
			{
				$htmlList[]	= JHTML::_('select.option', '1', 'Single site WordPress', 'value', 'state');
			}
		}

		return $htmlList;
	}

	public function getEasyBlogCategories()
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT id, title FROM `#__easyblog_category` where `published` = ' . $db->Quote('1');
		$db->setQuery( $query );
		$items	= $db->loadObjectList();


		$lists	= array();

		//default list
		$lists[]	= JHTML::_( 'select.option' , '' , ' -- Please select category -- ' , 'value' , 'state' );

		foreach( $items as $item )
		{
			$lists[]	= JHTML::_( 'select.option' , $item->id , $item->title , 'value' , 'state' );
		}

		return JHTML::_('select.genericlist',  $lists , 'categoryid', 'class="inputbox"', 'value', 'state', '');
	}


	public function getK2Categories()
	{
		$db	= EasyBlogHelper::db();

		jimport( 'joomla.filesystem.folder' );

		if( !JFolder::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_k2' ) )
		{
			return false;
		}

		$query	= 'SELECT * FROM `#__k2_categories`';
		$db->setQuery( $query );
		$items	= $db->loadObjectList();

		if( !$items )
		{
			return false;
		}

		$lists	= array();

		foreach( $items as $item )
		{
			$lists[]	= JHTML::_( 'select.option' , $item->id , $item->name , 'value' , 'state' );
		}

		return JHTML::_('select.genericlist',  $lists , 'k2category', 'class="inputbox"', 'value', 'state', '');
	}

	function getXMLFiles( $type = 'wordpress' )
	{
		$fixedLocation	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'xmlfiles';

		if( $type == 'blogger' )
			$fixedLocation .= DIRECTORY_SEPARATOR . 'blogger';

		$htmlList		= array();

		if( JFolder::exists($fixedLocation) )
		{
			$files	= JFolder::files( $fixedLocation, '.xml');

			if( count( $files ) > 0 )
			{
				foreach( $files as $file)
				{
					$htmlList[]	= JHTML::_('select.option', $file, $file , 'value', 'state');
				}
			}
		}
		return $htmlList;
	}


	function wpExists()
	{
		if(! JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_wordpress' . DIRECTORY_SEPARATOR . 'admin.wordpress.php'))
		{
			return false;
		}
		return true;
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_MIGRATORS' ), 'migrators' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'purge', 'delete.png', 'delete_f2.png', JText::_( 'COM_EASYBLOG_PURGE_HISTORY') , false );
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
