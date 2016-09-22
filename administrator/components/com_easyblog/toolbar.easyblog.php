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

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'toolbar.php' );

$submenus	= array(
						'easyblog'		=> JText::_('COM_EASYBLOG_TAB_HOME'),
						'settings'		=> JText::_('COM_EASYBLOG_HOME_SETTINGS'),
						'autoposting'	=> JText::_( 'COM_EASYBLOG_HOME_AUTOPOSTING' ),
						'blogs'			=> JText::_('COM_EASYBLOG_HOME_BLOG_ENTRIES'),
						'pending'		=> JText::_('COM_EASYBLOG_HOME_PENDING_POSTS'),
						'categories'	=> JText::_('COM_EASYBLOG_HOME_CATEGORIES'),
						'tags'			=> JText::_('COM_EASYBLOG_HOME_TAGS'),
						'comments'		=> JText::_('COM_EASYBLOG_HOME_COMMENTS'),
						'users'			=> JText::_('COM_EASYBLOG_HOME_BLOGGERS'),
						'teamblogs'		=> JText::_('COM_EASYBLOG_HOME_TEAM_BLOGS'),
						'themes'		=> JText::_('COM_EASYBLOG_HOME_THEMES'),
						'acls'			=> JText::_('COM_EASYBLOG_HOME_ACL'),
						'reports'		=> JText::_( 'COM_EASYBLOG_HOME_REPORTS' )
					);

$current	= JRequest::getVar( 'view' , 'easyblog' );

// @task: For the frontpage, we just show the the icons.
if( $current == 'easyblog' )
{
	$submenus	= array( 'easyblog' => JText::_('COM_EASYBLOG_TAB_HOME') );
}
foreach( $submenus as $view => $title )
{
	$isActive	= ( $current == $view );

	// @rule: Test for user access if on 1.6 and above
	if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
	{

		switch( $view )
		{
			case 'settings':
				$value 	= 'setting';
				break;
			case 'blogs':
				$value 	= 'blog';
				break;
			case 'categories':
				$value 	= 'category';
				break;
			case 'tags':
				$value 	= 'tag';
				break;
			case 'users':
				$value 	= 'user';
				break;
			case 'comments':
				$value 	= 'comment';
				break;
			case 'teamblogs':
				$value 	= 'teamblog';
				break;
			case 'themes':
				$value 	= 'theme';
				break;
			case 'acls':
				$value 	= 'acl';
				break;
			case 'reports':
				$value 	= 'report';
				break;
			default:
				$value 	= $view;
				break;
		}

		if(!JFactory::getUser()->authorise('easyblog.manage.' . $value , 'com_easyblog') )
		{
			continue;
		}
	}

 	JSubMenuHelper::addEntry( $title , 'index.php?option=com_easyblog&view=' . $view , $isActive );
}
