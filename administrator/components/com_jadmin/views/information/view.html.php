<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
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

class JAdminViewInformation extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$settings = & CMSModel::getInstance('SettingAdmin', 'JAdminModel');
		$uri = & JFactory::getURI();

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
		$document->addStyleSheet('components/com_jadmin/assets/css/fonts-min.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/tabview.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/menu.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/button.css');

		$document->addStyleSheet('components/com_jadmin/assets/css/styles.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/troubleshoot.css');
	}

	function _addJs()
	{
		$document = & JFactory::getDocument();

		(method_exists('JHtmlBehavior', 'mootools')) ? JHTML::_('behavior.mootools') : JHtml::_('behavior.framework');
		JHTML::_('behavior.tooltip');

		// YUI Stuff
		$document->addScript('components/com_jadmin/js/yahoo-dom-event.js');
		$document->addScript('components/com_jadmin/js/container_core-min.js');
		$document->addScript('components/com_jadmin/js/menu-min.js');
		$document->addScript('components/com_jadmin/js/element-min.js');
		$document->addScript('components/com_jadmin/js/tabview-min.js');
		$document->addScript('components/com_jadmin/js/button-min.js');

		$document->addScript('components/com_jadmin/js/jadmin.js');
		$document->addScript('components/com_jadmin/js/troubleshoot.js');
	}

}
