<?php
/*------------------------------------------------------------------------
# Copyright (C) 2016-2017 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class JTableObserverARKExtension extends JTableObserver
{

	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);
		return $observer;
	}

	public function onAfterStore(&$result)
	{
		if ($result)
		{
			
			if($this->table->element == 'com_arkmedia')
			{
				$params = new JRegistry($this->table->params);
				
				$locations = (array) $params->get('folder-locations');
				
				$imagePath = $locations['images']; 
				$docPath = $locations['documents']; 
				
			
				$table = JTable::getInstance('extension');
				$table->load(array('element'=>'com_arkeditor'));
				
				if(!$table->extension_id)
					return;
				
				$config =  new JRegistry($table->params);
				
				$update = false;
				if($imagePath != $config->get('imagePath' ,'images')  || $docPath != $config->get('filePath' ,'files') )
					$update = true;
				
				if($update)
				{
					$config->set('imagePath', $imagePath);
					$config->set('filePath', $docPath);
					$bindData = $config->toArray();
					$table->save(array('params'=>$bindData));
				}

			}
			elseif($this->table->element == 'com_arkeditor')
			{
				$params = new JRegistry($this->table->params);
					
				$imagePath = $params->get('imagePath'); 
				$docPath =  $params->get('filePath');  
				
			
				$table = JTable::getInstance('extension');
				$table->load(array('element'=>'com_arkmedia'));
				
				if(!$table->extension_id)
					return;
				
				$config =  new JRegistry($table->params);
				
				$update = false;
				$locations = (array) $config->get('folder-locations',array());
				
				if(!empty($locations))
				{	
					if($imagePath != $locations['images'] || $docPath != $locations['documents'] )
						$update = true;
				}
				else
					$update = true;
				
				if($update)
				{
					$locations['images'] = $imagePath;
					$locations['documents'] = $docPath;
					$config->set('folder-locations', $locations);
					$bindData = $config->toArray();
					$table->save(array('params'=>$bindData));
				}
				
			}	

		}	
	}
}