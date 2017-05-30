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

$pane	= JPane::getInstance('Tabs');

echo $pane->startPane("subnotifications");
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_GENERAL' ) , 'general');
echo $this->loadTemplate( 'notifications_general_joomla' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_BLOGS' ) , 'blogs');
echo $this->loadTemplate( 'notifications_blogs_joomla' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_COMMENTS' ) , 'comments');
echo $this->loadTemplate( 'notifications_comments_joomla' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_SUBTAB_TEMPLATES' ) , 'templates');
echo $this->loadTemplate( 'notifications_templates_joomla' );
echo $pane->endPanel();
echo $pane->endPane();