<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.database.tableasset');
jimport('joomla.database.table');


require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjplugintypeparams.php');

class JTablefsj_mainplugintype extends JTable{
	public function __construct(&$db)
	{
		parent::__construct('#__fsj_plg_type', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
	 *
	 * @see     JTable::bind
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		$this->array = $array;
		
		
		
		
		
		
		
		


		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{


		/*if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '')
		{
			$this->fulltext = '';
		}*/

		/*if (trim($this->introtext) == '' && trim($this->fulltext) == '')
		{
			$this->setError(JText::_('JGLOBAL_ARTICLE_MUST_HAVE_TEXT'));
			return false;
		}*/





		return true;
	}

	/**
	 * Overrides JTable::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{

		}
		else
		{
		}
		
		if (!parent::store($updateNulls))
			return false;
			

		return true;
	}


	public function delete($pk = null, $children = true)
	{
		$item = $this->load($pk);
			
		$db = JFactory::getDBO();
			

																																								
		// need to check for any cascades to delete, and call the correct table to delete them
		

		// delete actual item
		if (parent::delete($pk))
		{	
			$db = JFactory::getDBO();



																																						

			if (isset($this->asset_id))
			{
				FSJ_Settings::Delete($this->asset_id);
			}	

			return true;
		}

		return false;
		
	}
}
