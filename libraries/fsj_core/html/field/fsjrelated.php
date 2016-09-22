<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.plugins.related.related');

class JFormFieldFSJRelated extends JFormField
{
	var $handlepost = 1;
	protected $type = 'FSJRelated';

	protected function getInput()
	{
		$related = (string)$this->element['fsjrelated_related'];
		$name = (string)$this->element['name'];

		$this->related = new FSJ_Plugin_Type_Related($related,true);
		$item = array('id' => $this->form->getValue("id"));
		$this->related->LoadSingle($item);
		
		$this->related->ShowForm($this->form->getValue("id"));
	}
	
	function Process(&$item)
	{
		$this->related = new FSJ_Plugin_Type_Related($this->fsjrelated->related);
		$this->related->LoadSingle($item);
		return $this->related->Process();
	}
	
	function doAfterSave($field, &$data)
	{
		$this->related = new FSJ_Plugin_Type_Related($this->fsjrelated->related);
		$this->related->LoadSingle($item);
		$this->related->Save($this->fsjrelated->related, $data->id);
	}	
	
	function doAfterDelete($field, $pk)
	{
		$this->related = new FSJ_Plugin_Type_Related($this->fsjrelated->related);
		$this->related->Delete($pk);
	}
}
