<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JLiveChatViewTroubleshoot extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');
		$uri = & JFactory::getURI();

		$this->assign('module_object', $settings->getModuleXMLObject());
		$this->assign('plugin_object', $settings->getPluginXMLObject());

		$this->assignRef('settings', $settings);
		$this->assignRef('uri', $uri);

		$this->_addCss();
		$this->_addJs();

		parent::display($tpl);
	}

	function _addCss()
	{
		$document = & JFactory::getDocument();

		// YUI Stuff
		$document->addStyleSheet('components/com_jlivechat/assets/css/fonts-min.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/tabview.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/menu.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/button.css');

		$document->addStyleSheet('components/com_jlivechat/assets/css/styles.css');
		$document->addStyleSheet('components/com_jlivechat/assets/css/troubleshoot.css');
	}

	function _addJs()
	{
		(method_exists('JHtmlBehavior', 'mootools')) ? JHTML::_('behavior.mootools') : JHtml::_('behavior.framework');
		JHTML::_('behavior.tooltip');
		
		$document = & JFactory::getDocument();
		
		// YUI Stuff
		$document->addScript('components/com_jlivechat/js/yahoo-dom-event.js');
		$document->addScript('components/com_jlivechat/js/container_core-min.js');
		$document->addScript('components/com_jlivechat/js/menu-min.js');
		$document->addScript('components/com_jlivechat/js/element-min.js');
		$document->addScript('components/com_jlivechat/js/tabview-min.js');
		$document->addScript('components/com_jlivechat/js/button-min.js');

		$document->addScript('components/com_jlivechat/js/jlivechat.js');
		$document->addScript('components/com_jlivechat/js/troubleshoot.js');
	}
}
