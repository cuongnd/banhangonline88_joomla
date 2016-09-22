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

echo $pane->startPane("submedia");

echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_GENERAL' ) , 'general');
echo $this->loadTemplate( 'media_general_joomla' );
echo $pane->endPanel();

echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_APPEARANCE' ) , 'appearance');
echo $this->loadTemplate( 'media_appearance_joomla' );
echo $pane->endPanel();

echo $pane->startPanel( JText::_( 'COM_EASYBLOG_SETTINGS_MEDIA_SUBTAB_MEDIAMANAGER' ) , 'mediamanager');
echo $this->loadTemplate( 'media_mediamanager_joomla' );
echo $pane->endPanel();

echo $pane->endPane();
