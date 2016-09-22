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

class EasyBlogViewMeta extends EasyBlogAdminView
{

	var $_id	= null;
	var $_type	= null;

	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.meta' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		//Load pane behavior
		jimport('joomla.html.pane');

		$metatId		= JRequest::getVar( 'id' , '' );

		$meta		= EasyBlogHelper::getTable( 'meta' , 'Table' );

		$meta->load( $metatId );

		// assign title
		$meta->title = $this->_getItemTitle($meta->id);

		$this->meta	=& $meta;

		$this->assignRef( 'meta'		, $meta );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_META_TAG_EDIT' ), 'meta' );

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}


	function _getItemTitle($id)
	{
// 		$db = EasyBlogHelper::db();

		$title = '';

		switch ( $id )
		{
			case 1:
				$title = JText::_('COM_EASYBLOG_LATEST_POSTS_PAGE');
				break;

			case 2:
				$title = JText::_('COM_EASYBLOG_CATEGORIES_PAGE');
				break;

			case 3:
				$title = JText::_('COM_EASYBLOG_TAGS_PAGE');
				break;

			case 4:
				$title = JText::_('COM_EASYBLOG_BLOGGERS_PAGE');
				break;

			case 5:
				$title = JText::_('COM_EASYBLOG_TEAM_BLOGS_PAGE');
				break;

			case 6:
				$title = JText::_('COM_EASYBLOG_FEATURED_POSTS_PAGE');
				break;

			case 7:
				$title = JText::_('COM_EASYBLOG_ARCHIVE_PAGE');
				break;

			case 8:
				$title = JText::_('COM_EASYBLOG_SEARCH_PAGE');
				break;

			default:
				$title = $this->_getTitle( $id );

		}

		return $title;
	}


	function _getTitle( $id )
	{
		$db = EasyBlogHelper::db();

		$query = 'SELECT `type`, `content_id` FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_meta') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result)
		{
			$result	= new stdClass;
			$result->type	= '';
		}

		$query = '';

		switch ( $result->type )
		{
			case 'post':
				$query = 'SELECT `title` FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_post') . ' WHERE id = ' . $db->Quote( $result->content_id );
				break;

			case 'blogger':
				$query = 'SELECT `name` AS title  FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__users') . ' WHERE id = ' . $db->Quote( $result->content_id );
				break;

			case 'team':
				$query = 'SELECT `title`  FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_team') . ' WHERE id = ' . $db->Quote( $result->content_id );
				break;

			case 'category':
				$query = 'SELECT `title`  FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_category') . ' WHERE id = ' . $db->Quote( $result->content_id );
				break;
			default:
				return 'unknown';
				break;
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
