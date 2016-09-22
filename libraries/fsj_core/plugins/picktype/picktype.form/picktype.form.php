<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_PickType_Form extends FSJ_Plugin_PickType
{
	function InitFromLinked(&$plugin)
	{
		parent::InitFromLinked($plugin);
		$xmlfile = JPATH_ROOT.DS.$this->plugin->path;
		$xmlfile .= DS."{$this->plugin->type}.{$this->plugin->name}.xml";
		
		$xml = simplexml_load_file($xmlfile);
		$data = $xml->pick_form->asXML();
		
		$name = $this->id . "." . $this->source;
		
		$this->form = JForm::getInstance($name, $xml->pick_form->form->asXML());
		
		$this->form->source = $this->source;
		
		$this->showfilter = false;	
	}
}