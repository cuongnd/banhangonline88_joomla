<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.database.tablenested');

/**
 * This does some things with loading and saving settings for the components
 * 
 * Needs to exist!
 **/

class JTableFSJSettings extends JTableNested
{
	public $id = null;
	public $name = null;
	public $title = null;
	public $rules = null;

	public function __construct(&$db)
	{
		parent::__construct('#__fsj_main_settings', 'id', $db);
	}

	public function loadByName($name)
	{
		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);

		// Get the asset id for the asset.
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName($this->_tbl));
		$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote($name));
		$this->_db->setQuery($query);
		$settingId = (int) $this->_db->loadResult();
		if (empty($settingId))
		{
			return false;
		}
		// Check for a database error.
		if ($error = $this->_db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}
		return $this->load($settingId);
	}
	
	public function loadByJAsset($asset_id)
	{
		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);

		// Get the asset id for the asset.
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName($this->_tbl));
		$query->where($this->_db->quoteName('j_asset') . ' = ' . $this->_db->quote($asset_id));
		$this->_db->setQuery($query);
		$settingId = (int) $this->_db->loadResult();
		if (empty($settingId))
		{
			return false;
		}
		// Check for a database error.
		if ($error = $this->_db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}
		return $this->load($settingId);	
	}

	public function check()
	{
		$this->parent_id = (int) $this->parent_id;

		// JTableNested does not allow parent_id = 0, override this.
		if ($this->parent_id > 0)
		{
			// Get the JDatabaseQuery object
			$query = $this->_db->getQuery(true);

			$query->select('COUNT(id)');
			$query->from($this->_db->quoteName($this->_tbl));
			$query->where($this->_db->quoteName('id') . ' = ' . $this->parent_id);
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				return true;
			}
			else
			{
				if ($error = $this->_db->getErrorMsg())
				{
					$this->setError($error);
				}
				else
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_INVALID_PARENT_ID'));
				}
				return false;
			}
		}

		return true;
	}
}
