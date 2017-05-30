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

class JAdminViewUsers extends CMSView
{
	function display($tpl = null)
	{
		$mainframe = & JFactory::getApplication();

		$uri = & JFactory::getURI();
		$users = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		$this->assign('uri', $uri);
		$this->assign('departments', $users->getDepartments());
		$this->assign('admin_users', $users->getAdminUsers());
		$this->assign('available_permissions', $users->getAvailablePermissions());

		if(JRequest::getCmd('task') == 'edit' && JRequest::getInt('user_id', null, 'method'))
		{
			$this->assign('admin_user', $users->getUser(JRequest::getInt('user_id', null, 'method')));
		}

		$this->_addCss();
		$this->_addJs();

		parent::display($tpl);
	}

	function _addCss()
	{
		$document = & JFactory::getDocument();

		// Add YUI stuff
		$document->addStyleSheet('components/com_jadmin/assets/css/fonts-min.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/paginator.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/datatable.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/tabview.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/menu.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/button.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/autocomplete.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/styles.css');
		$document->addStyleSheet('components/com_jadmin/assets/css/users.css');
	}

	function _addJs()
	{
		$document = & JFactory::getDocument();

		(method_exists('JHtmlBehavior', 'mootools')) ? JHTML::_('behavior.mootools') : JHtml::_('behavior.framework');
		JHTML::_('behavior.tooltip');

		// Add YUI stuff
		$document->addScript('components/com_jadmin/js/yahoo-dom-event.js');
		$document->addScript('components/com_jadmin/js/connection-min.js');
		$document->addScript('components/com_jadmin/js/container_core-min.js');
		$document->addScript('components/com_jadmin/js/json-min.js');
		$document->addScript('components/com_jadmin/js/element-min.js');
		$document->addScript('components/com_jadmin/js/paginator-min.js');
		$document->addScript('components/com_jadmin/js/datasource-min.js');
		$document->addScript('components/com_jadmin/js/datatable-min.js');
		$document->addScript('components/com_jadmin/js/tabview-min.js');
		$document->addScript('components/com_jadmin/js/menu-min.js');
		$document->addScript('components/com_jadmin/js/button-min.js');
		$document->addScript('components/com_jadmin/js/animation-min.js');
		$document->addScript('components/com_jadmin/js/autocomplete-min.js');

		$document->addScript('components/com_jadmin/js/jadmin.js');

		if(JRequest::getCmd('task') == 'new_user' || JRequest::getCmd('task') == 'edit')
		{
			$document->addScript('components/com_jadmin/js/new_user.js');
		}
		else
		{
			$document->addScript('components/com_jadmin/js/users.js');
		}
	}

}