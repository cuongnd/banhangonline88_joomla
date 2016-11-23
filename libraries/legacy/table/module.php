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
 * Module table
 *
 * @since  11.1
 */
class JTableModule extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  Database driver object.
	 *
	 * @since   11.1
	 */
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__modules', 'id', $db);

		$this->access = (int) JFactory::getConfig()->get('access');
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_modules.module.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}
	public function enable_lazyload($pks = null, $state_lazyload = 1, $userId = 0)
	{
		// Sanitize input
		$userId = (int) $userId;
		$state_lazyload  = (int) $state_lazyload;

		if (!is_null($pks))
		{
			if (!is_array($pks))
			{
				$pks = array($pks);
			}

			foreach ($pks as $key => $pk)
			{
				if (!is_array($pk))
				{
					$pks[$key] = array($this->_tbl_key => $pk);
				}
			}
		}

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			$pk = array();

			foreach ($this->_tbl_keys AS $key)
			{
				if ($this->$key)
				{
					$pk[$key] = $this->$key;
				}
				// We don't have a full primary key - return false
				else
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

					return false;
				}
			}

			$pks = array($pk);
		}

		$paramsField = $this->getColumnAlias('params');
		foreach ($pks as $pk)
		{
			$query = $this->_db->getQuery(true);
			$query->select('params')
				->from('#__modules')
				->where('id='.(int)$pk);
			$params=$this->_db->setQuery($query)->loadResult();
			$temp = new Registry;
			$temp->loadString($params);
			$temp->set('lazyload',$state_lazyload);
			$params=$temp->toString();
			// Update the publishing state for rows with the given primary keys.
			$query = $this->_db->getQuery(true)
				->update($this->_tbl)
				->set($this->_db->quoteName($paramsField) . ' = ' .$query->q($params));
			// Build the WHERE clause for the primary keys.
			$this->appendPrimaryKeys($query, $pk);
			$this->_db->setQuery($query);
			try
			{
				$this->_db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

		}

		$this->setError('');

		return true;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetId = null;

		// This is a module that needs to parent with the extension.
		if ($assetId === null)
		{
			// Build the query to get the asset id of the parent component.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('id'))
				->from($this->_db->quoteName('#__assets'))
				->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_modules'));

			// Get the asset id from the database.
			$this->_db->setQuery($query);

			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Overloaded check function.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @see     JTable::check()
	 * @since   11.1
	 */
	public function check()
	{
		// Check for valid name
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_MODULE'));

			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if ((int) $this->publish_down > 0 && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		return true;
	}

	/**
	 * Overloaded bind function.
	 *
	 * @param   array  $array   Named array.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind()
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
		$position = str_replace(' ', '-', $array['position']);
		$position=JString::vn_str_filter($position);
		$position=JString::clean($position);
		$position=strtolower($position);
		$array['position']= $position;

		return parent::bind($array, $ignore);
	}
}
