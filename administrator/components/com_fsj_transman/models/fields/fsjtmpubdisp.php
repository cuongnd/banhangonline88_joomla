<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport( 'fsj_core.lib.utils.parser');

class JFormFieldFSJTMPubDisp extends JFormField
{
	static $js_loaded;
	
	protected function getInput()
	{
		if ($this->form->getValue('pubfolder') == "") return "You must enter a publish folder to be able publish a package.";
		
		$data = array(
			"title" => $this->form->getValue('title'),
			"ver" => $this->form->getValue('ver'),
			"langcode" => $this->form->getValue('langcode'),
			"alias" => $this->form->getValue('alias'),
			"author" => $this->form->getValue('author'),
			"date" => $this->form->getValue('creationDate'),
			"email" => $this->form->getValue('email'),
			"url" => $this->form->getValue('url')
			);

		$target = FSJ_TM_Helper::makePackageFilename($data, $this->form->getValue('pubfolder'), $this->form->getValue('filename'));

		return "<pre>".$target."</pre>";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		echo "";
	}
}
