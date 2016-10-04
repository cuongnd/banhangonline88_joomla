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

jimport('joomla.application.component.model');

class JAdminModelComponents extends CMSModel
{
	function __construct()
	{
		$this->JAdminModelComponents();
	}

	function JAdminModelComponents()
	{
		parent::__construct();
	}

	function getAll()
	{
		$sql = "SELECT
		    j.`extension_id` AS 'id',
		    j.`name`,
		    CONCAT('option=', j.`element`) AS 'link',
		    '0' AS 'menuid',
		    '0' AS 'parent',
		    CONCAT('option=', j.`element`) AS 'admin_menu_link',
		    j.`name` AS 'admin_menu_alt',
		    j.`element` AS 'option',
		    j.`ordering`,
		    '' AS 'admin_menu_img',
		    j.`protected` AS 'iscore',
		    j.`params`,
		    j.`enabled` 
		FROM #__extensions j
		WHERE type = 'component';";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}
}