<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * Menu table
 *
 * @since  11.1
 */
class JTableMenu extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   11.1
	 */
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__menu', 'id', $db);

		// Set the default access level.
		$this->access = (int) JFactory::getConfig()->get('access');
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind()
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		// Verify that the default home menu is not unset
		if ($this->home == '1' && $this->language == '*' && ($array['home'] == '0'))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT_DEFAULT'));

			return false;
		}

		// Verify that the default home menu set to "all" languages" is not unset
		if ($this->home == '1' && $this->language == '*' && ($array['language'] != '*'))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT'));

			return false;
		}

		// Verify that the default home menu is not unpublished
		if ($this->home == '1' && $this->language == '*' && $array['published'] != '1')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNPUBLISH_DEFAULT_HOME'));

			return false;
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}


	/**
	 * Gets the ID of the root item in the tree
	 *
	 * @return  mixed  The primary id of the root row, or false if not found and the internal error is set.
	 *
	 * @since   11.1
	 */
	public function getRootIdMenu()
	{
		if ((int) self::$root_id > 0)
		{
			return self::$root_id;
		}

		// Get the root item.
		$k = $this->_tbl_key;

		// Test for a unique record with parent_id = 0
		$query = $this->_db->getQuery(true)
			->select($k)
			->from($this->_tbl)
			->where('parent_id = 0');
		$result = $this->_db->setQuery($query)->loadColumn();

		if (count($result) == 1)
		{
			self::$root_id = $result[0];
			return self::$root_id;
		}

		// Test for a unique record with lft = 0
		$query->clear()
			->select($k)
			->from($this->_tbl)
			->where('lft = 0');

		$result = $this->_db->setQuery($query)->loadColumn();

		if (count($result) == 1)
		{
			self::$root_id = $result[0];

			return self::$root_id;
		}

		$fields = $this->getFields();

		if (array_key_exists('alias', $fields))
		{
			// Test for a unique record alias = root
			$query->clear()
				->select($k)
				->from($this->_tbl)
				->where('alias = ' . $this->_db->quote('root'));

			$result = $this->_db->setQuery($query)->loadColumn();

			if (count($result) == 1)
			{
				self::$root_id = $result[0];

				return self::$root_id;
			}
		}

		$e = new UnexpectedValueException(sprintf('%s::getRootId', get_class($this)));
		$this->setError($e);
		self::$root_id = false;

		return false;
	}

	public function menu_rebuild()
	{
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->clear()
			->select('menu.id,menu.parent_id,menu.alias')
			->from('#__menu AS menu')
			->order('ordering,lft')
		;
		$db->setQuery($query);

		$list_menu_item = $db->loadObjectList();
		$children_menu_item = array();
		foreach ($list_menu_item as $v) {
			$pt = $v->parent_id;
			$list = @$children_menu_item[$pt] ? $children_menu_item[$pt] : array();
			array_push($list, $v);
			$children_menu_item[$pt] = $list;
		}
		// Get the root item.
		$parentId = $this->getRootIdMenu();

		if ($parentId === false)
		{
			return false;
		}


		$start_rebuild= function($function_callback,&$list_query=array(),$children_menu_item,$parentId = null, $leftId = 0,$ordering=0, $level = 0, $path = '')
		{
			$children = $children_menu_item[$parentId];
			// The right value of this node is the left value + 1
			$rightId = $leftId + 1;
			$ordering1=1;
			// Execute this function recursively over all children
			foreach ($children as $node)
			{
				/*
				 * $rightId is the current right value, which is incremented on recursion return.
				 * Increment the level for the children.
				 * Add this item's alias to the path (but avoid a leading /)
				 */
				$rightId = $function_callback($function_callback,$list_query,$children_menu_item,$node->id, $rightId,$ordering1, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);
				$ordering1++;

				// If there is an update failure, return false to break out of the recursion.
				if ($rightId === false)
				{
					return false;
				}
			}

			// We've got the left value, and now that we've processed
			// the children of this node we also know the right value.
			$object_query=new stdClass();
			$object_query->lft=$leftId;
			$object_query->rgt=$rightId;
			$object_query->level=$level;
			$object_query->ordering=$ordering;
			$object_query->path=$path;
			$object_query->id=$parentId;
			$list_query[]=$object_query;

			// Return the right value of this node + 1.
			return $rightId + 1;
		};


		$list_query=array();

		$start_rebuild($start_rebuild,$list_query,$children_menu_item,$parentId);
		$sql="";
		$sql.="UPDATE #__menu \n";
		$sql.="SET lft = CASE \n";
		$list_menu_item_id=array();
		foreach($list_query as $item_query)
		{
			$sql.="WHEN id = $item_query->id THEN $item_query->lft \n";
			$list_menu_item_id[]=$item_query->id;
		}
		$sql.="ELSE '' \n";
		$sql.="END \n";

		$sql.="SET rgt = CASE \n";
		foreach($list_query as $item_query)
		{
			$sql.="WHEN id = $item_query->id THEN $item_query->rgt \n";
		}
		$sql.="ELSE '' \n";
		$sql.="END \n";

		$sql.="SET level = CASE \n";
		foreach($list_query as $item_query)
		{
			$sql.="WHEN id = $item_query->id THEN $item_query->level \n";
		}
		$sql.="ELSE '' \n";
		$sql.="END \n";

		$sql.="SET path = CASE \n";
		foreach($list_query as $item_query)
		{
			$path=$query->quote($item_query->path);
			$sql.="WHEN id = $item_query->id THEN $path \n";
		}
		$sql.="ELSE '' \n";
		$sql.="END \n";

		$sql.="SET ordering = CASE \n";
		foreach($list_query as $item_query)
		{
			$sql.="WHEN id = $item_query->id THEN $item_query->ordering \n";
		}
		$sql.="ELSE '' \n";
		$sql.="END \n";
		$str_list_menu_item_id=implode(',',$list_menu_item_id);
		$sql.="WHERE id IN ($str_list_menu_item_id) \n";
		$query=$db->getQuery(true);
		foreach($list_query as $item_query)
		{
			$query->clear();
			$path=$query->quote($item_query->path);
			$query->update('#__menu')
				->set("lft=$item_query->lft")
				->set("rgt=$item_query->rgt")
				->set("path=$path")
				->set("ordering=$item_query->ordering")
				->where("id=$item_query->id")
				;
			$db->setQuery($query);
			$ok=$db->execute();
			if(!$ok)
			{
				throw new Exception("there are some error:".$db->getErrorMsg());
			}
		}
		return true;

	}
	/**
	 * Method to update order of table rows
	 *
	 * @param   array  $idArray    id numbers of rows to be reordered.
	 * @param   array  $lft_array  lft values of rows to be reordered.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure.
	 *
	 * @since   11.1
	 * @throws  Exception on database error.
	 */

	public function saveorder($idArray = null, $lft_array = null)
	{
		try
		{
			$query = $this->_db->getQuery(true);

			// Validate arguments
			if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array))
			{
				for ($i = 0, $count = count($idArray); $i < $count; $i++)
				{
					// Do an update to change the lft values in the table for each id
					$query->clear()
						->update('#__menu')
						->where( 'id = ' . (int) $idArray[$i])
						->set('ordering = ' . (int) $lft_array[$i]);

					$this->_db->setQuery($query)->execute();

					// @codeCoverageIgnoreStart
					if ($this->_debug)
					{
						$this->_logtable();
					}
					// @codeCoverageIgnoreEnd
				}

				return true;
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			$this->_unlock();
			throw $e;
		}
	}


	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure
	 *
	 * @since   11.1
	 * @throws  RuntimeException on database error.
	 */

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success
	 *
	 * @see     JTable::check()
	 * @since   11.1
	 */
	public function check()
	{
		// Check for a title.
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_MENUITEM'));

			return false;
		}

		// Set correct component id to ensure proper 404 messages with separator items
		if ($this->type == "separator")
		{
			$this->component_id = 0;
		}

		// Check for a path.
		if (trim($this->path) == '')
		{
			$this->path = $this->alias;
		}
		// Check for params.
		if (trim($this->params) == '')
		{
			$this->params = '{}';
		}
		// Check for img.
		if (trim($this->img) == '')
		{
			$this->img = ' ';
		}

		// Cast the home property to an int for checking.
		$this->home = (int) $this->home;

		// Verify that a first level menu item alias is not 'component'.
		if ($this->parent_id == 1 && $this->alias == 'component')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_COMPONENT'));

			return false;
		}

		// Verify that a first level menu item alias is not the name of a folder.
		jimport('joomla.filesystem.folder');

		if ($this->parent_id == 1 && in_array($this->alias, JFolder::folders(JPATH_ROOT)))
		{
			$this->setError(JText::sprintf('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_FOLDER', $this->alias, $this->alias));

			return false;
		}

		// Verify that the home item a component.
		if ($this->home && $this->type != 'component')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_COMPONENT'));

			return false;
		}

		return true;
	}

	/**
	 * Overloaded store function
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  mixed  False on failure, positive integer on success.
	 *
	 * @see     JTable::store()
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$db = JFactory::getDbo();

		// Verify that the alias is unique
		$table = JTable::getInstance('Menu', 'JTable', array('dbo' => $this->getDbo()));

		$originalAlias = trim($this->alias);
		$this->alias   = !$originalAlias ? $this->title : $originalAlias;
		$this->alias   = JApplicationHelper::stringURLSafe(trim($this->alias), $this->language);

		// If alias still empty (for instance, new menu item with chinese characters with no unicode alias setting).
		if (empty($this->alias))
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		else
		{
			$itemSearch = array('alias' => $this->alias, 'parent_id' => $this->parent_id, 'client_id' => (int) $this->client_id);
			$errorType  = '';

			// Check if the alias already exists. For multilingual site.
			if (JLanguageMultilang::isEnabled())
			{
				// If not exists a menu item at the same level with the same alias (in the All or the same language).
				if (($table->load(array_replace($itemSearch, array('language' => '*'))) && ($table->id != $this->id || $this->id == 0))
					|| ($table->load(array_replace($itemSearch, array('language' => $this->language))) && ($table->id != $this->id || $this->id == 0))
					|| ($this->language == '*' && $table->load($itemSearch) && ($table->id != $this->id || $this->id == 0)))
				{
					$errorType = 'MULTILINGUAL';
				}
			}
			// Check if the alias already exists. For monolingual site.
			else
			{
				// If not exists a menu item at the same level with the same alias (in any language).
				if ($table->load($itemSearch) && ($table->id != $this->id || $this->id == 0))
				{
					$errorType = 'MONOLINGUAL';
				}
			}

			// The alias already exists. Send an error message.
			if ($errorType)
			{
				$message = JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS' . ($this->menutype != $table->menutype ? '_ROOT' : ''));
				$this->setError($message);

				return false;
			}
		}

		if ($this->home == '1')
		{
			// Verify that the home page for this menu is unique.
			if ($table->load(
					array(
					'menutype' => $this->menutype,
					'client_id' => (int) $this->client_id,
					'home' => '1'
					)
				)
				&& ($table->language != $this->language))
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_UNIQUE_IN_MENU'));

				return false;
			}

			// Verify that the home page for this language is unique
			if ($table->load(array('home' => '1', 'language' => $this->language)))
			{
				if ($table->checked_out && $table->checked_out != $this->checked_out)
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_DEFAULT_CHECKIN_USER_MISMATCH'));

					return false;
				}

				$table->home = 0;
				$table->checked_out = 0;
				$table->checked_out_time = $db->getNullDate();
				$table->store();
			}
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		// Get the new path in case the node was moved
		$pathNodes = $this->getPath();
		$segments = array();

		foreach ($pathNodes as $node)
		{
			// Don't include root in path
			if ($node->alias != 'root')
			{
				$segments[] = $node->alias;
			}
		}

		$newPath = trim(implode('/', $segments), ' /\\');

		// Use new path for partial rebuild of table
		// Rebuild will return positive integer on success, false on failure
		return ($this->rebuild($this->{$this->_tbl_key}, $this->lft, $this->level, $newPath) > 0);
	}
}
