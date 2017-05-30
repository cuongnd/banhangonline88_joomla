<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJInclAlias extends JFormFieldText
{
	protected $type = 'FSJInclAlias';
	
	protected function getInput()
	{
		$asset_id = $this->form->getValue("asset_id");
		//$this->value = $asset_id;
		
		if ($asset_id > 0)
		{
			$db = JFactory::getDBO();
			$qry = "SELECT * FROM #__fsj_includes_alias WHERE target_asset = " . $db->escape($asset_id);
			$db->setQuery($qry);
			$alias = $db->loadObject();
			
			if ($alias)
			{
				$this->value = $alias->find;	
			}
		}
		return parent::getInput();
	}
	
	function doAfterSave($field, $table)
	{
		$asset_id = $table->asset_id;
		$data = JRequest::getVar('jform');
		
		$value = $data[$field];
		$alias = $table->alias;
		//$data['alias'] = $alias;
		$changeto = FSJ_Helper::ParseDataFields($this->fsjinclalias->changeto, $table);
		$changeto = str_replace("{", "", $changeto);
		$changeto = str_replace("}", "", $changeto);
		
		$db = JFactory::getDBO();
		$qry = "DELETE FROM #__fsj_includes_alias WHERE target_asset = " . $db->escape($asset_id);
		$db->setQuery($qry);
		$db->Query();	
	
		if ($value)
		{
			$data = new stdClass();
			$data->find = $value;
			$data->changeto = $changeto;
			$data->target_asset = $asset_id;
			$data->state = 1;		
			
			$db->insertObject("#__fsj_includes_alias", $data);
		}
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$asset_id = $item->asset_id;
		
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_includes_alias WHERE target_asset = " . $db->escape($asset_id);
		$db->setQuery($qry);
		$alias = $db->loadObject();	
		
		$main = FSJ_Helper::ParseDataFields($this->fsjinclalias->changeto, $item);
		
		//if (isset($this->fsjinclalias->raw))
		//{
			$output = $main;
		/*} else {
			$output = "{fsi " . $main . "}<br />";
			$output .= "{" . $main . "}";
		}*/
		
		if ($alias)
		{
			$output .= "<br />{" . $alias->find . "}";
		}
		return $output;	
	}
	
	function doBeforeDelete($field, $item)
	{
		if ($item->asset_id)
		{		
			$db = JFactory::getDBO();
			$qry = "DELETE FROM #__fsj_includes_alias WHERE target_asset = " . $db->escape($item->asset_id);
			$db->setQuery($qry);
			$db->Query();				
		}
	}
}
