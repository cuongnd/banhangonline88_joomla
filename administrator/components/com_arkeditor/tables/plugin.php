<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class ARKTablePlugin extends JTable
{

	function __construct(& $db) {
		parent::__construct('#__ark_editor_plugins', 'id', $db);
	}

	public function setParent($pluginName = '')
	{
		if($pluginName)
		{
			// Build the query to get the asset id for the parent category.
			$sql = $this->_db->getQuery(true);
			$sql->select('id')
				->from('#__ark_editor_plugins')
				->where('name = '.$this->_db->quote($pluginName));

			$id = $this->_db->setQuery($sql)->loadResult(); 	

			// Return the asset id.
			if($id)
			{
				$this->parentid = $id;
			}
		}
	}

	function bind($array, $ignore = '')
	{
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		return parent::bind($array, $ignore);
	}
}