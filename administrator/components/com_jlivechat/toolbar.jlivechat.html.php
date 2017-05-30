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
defined('_JEXEC') or die('Restricted access');

class TOOLBAR_livechat
{
	function _listOperators()
	{
		JToolBarHelper::addNew();

		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::deleteList();
	}

	function _listSettings()
	{
		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}

	function _listHistory()
	{
		JToolBarHelper::divider();

		JToolBarHelper::deleteList();
	}

	function _listRoutes()
	{
		JToolBarHelper::addNew();

		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::deleteList();
	}

	function _newRoute()
	{
		JToolBarHelper::divider();

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function _editRoute()
	{
		JToolBarHelper::divider();

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function _listResponses()
	{
		JToolBarHelper::addNew();

		JToolBarHelper::divider();

		JToolBarHelper::apply();
		JToolBarHelper::deleteList();
	}

	function _newResponse()
	{
		JToolBarHelper::divider();

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function _editResponse()
	{
		JToolBarHelper::divider();

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function _newOperator()
	{
		JToolBarHelper::divider();

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function _troubleshoot()
	{
		//
	}

	function _DEFAULT()
	{
		TOOLBAR_livechat::_listSettings();
	}
}