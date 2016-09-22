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

jimport( 'joomla.application.component.view');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogViewLogin extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$mainframe = JFactory::getApplication();

		$my = JFactory::getuser();

		if(empty($my->id))
		{
			$return = JRequest::getVar('return', '');
			EasyBlogHelper::showLogin($return);
			return;
		}
		else
		{
			$showPermissionMsg = JRequest::getVar('showpermissionmsg', '');

			if($showPermissionMsg)
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW') , 'error' );
			}
			else
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_ARE_ALREADY_LOGIN') , 'error' );
				$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=latest'));
			}
		}
	}
}
