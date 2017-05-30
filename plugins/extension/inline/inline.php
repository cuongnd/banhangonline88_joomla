<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  Extension Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Extension.Inline
 */
 

 
class PlgExtensionInline extends JPlugin
{
	
    
    private $_mainifest = NULL;
    private $_extensionType = '';
	protected $db;
    
    public function onExtensionBeforeInstall($method, $type , $manifest , $extension)
	{
		if($type != 'file')
			return;
		
		if( empty($manifest))
			return;
		
		
		if(isset($manifest->arkeditor))
		{
			
			if(!isset($manifest->arkeditor->extensionName))
					throw new RuntimeException('Failed to find extension name for inline CCK data');	
			
		}	
	}	
	
	
	
	public function onExtensionBeforeUpdate($type, $manifest)
	{
		$this->onExtensionBeforeInstall('update', $type , $manifest , 0);		
	}	
	
		
	public function onExtensionAfterInstall($installer, $eid)
	{
		
		if(!$eid)
			return;
		
		
		if( empty($installer) || empty($installer->manifest))
			return;
		
		if(is_null($installer->manifest->attributes())) 
			return;
		
		$type =  (string) $installer->manifest->attributes()->type;

		if($type != 'file')
			return;

		
		if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/inlineview.php'))
		{
		
			require_once JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/inlineview.php';	

		
            if(!isset($installer->manifest->arkeditor))
				return;
            
         	
			$element = (object) $installer->manifest->arkeditor;
			
			$data = array();	
				

			if($element)
			{
				if(!isset($element->extensionName))
					throw new RuntimeException('Failed to find extension name for inline CCK data');	
				

				
				if(isset($element->parentName))
				{
					$parent = JTable::getInstance('inlineView','ARKTable');
					$parentName = (string) $element->parentName;
					$parent->load(array('element'=>$parentName));
					if(!$parent->element)
						throw new RuntimeException('Core extension plugin not installed. Please install that first');

					$data['context'] 	= 	(string) $parent->context;
					$data['params'] 	= 	(string) $parent->params;
					$data['views'] 		= 	(string) $parent->views;
					$data['parent'] 	=  	$parentName;
				}
				
				$data['element'] = (string) $element->extensionName;
				
				if(isset($element->views))
				{
					$data['views'] = array();
    
					foreach($element->views->children() as $view)
					{
						$data['views'][] = (string) $view;
					}	
				}
				
				if(isset($element->context))
					$data['context'] = (string) $element->context;
                else
                   $data['context'] =  str_replace('com_','', $data['element']);
				
				if(isset($element->types))
				{
					$data['types'] = array();
					foreach($element->types->children() as $type)
					{
						$data['types'][] = (string) $type;
					}	
				}
				
				if(isset($installer->manifest->config))
				{	
					$data['params'] = $installer->getParams();
				}		

                //Store record 
				$row = JTable::getInstance('inlineView','ARKTable');
       			$row->load(array('element'=>$data['element']));
		        $row->bind($data);

                if(empty($row->params))
                    $row->params = $installer->getParams();
                         			
                if(!$row->check())
                    throw new RuntimeException('Supplied inline data for '.$data['element'].'  extension is incorrect');

			    if(!$row->store())
					throw new RuntimeException('Failed to store inline data for '.$data['element'].'  extension');
				
				if(isset($element->versioning))
				{
					$versioning = $element->versioning;
							
					$versions = $versioning->versions;

					if($versioning->JTablePath)
					{	
						$JTablePath =  (string)  $versioning->JTablePath;
						$JTablePath = str_replace('\/','/',$JTablePath);
						JTable::addIncludePath(JPATH_SITE.'/'.$JTablePath);
					}					
					else
						JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/'.  $data['element'].'/tables');
					
					
					foreach($versions->children() as $version)
					{		
						//create ucm type for extension item
						$alias = (string) (isset($version->attributes()->type) ? $version->attributes()->type : $data['context']);
								
						$type_alias = $data['element'] .'.'. $alias;
						$dbtable = (string)  $version->dbtable;	
						if(strpos($dbtable,'#__') === false)
							$dbtable = '#__'.$dbtable;
						$type = (string)  $version->type;
						$prefix = (string)  $version->prefix;
						$formFile = '';
						if(isset($version->formFile))
							$formFile = (string)  $version->formFile;	
											
						
						$table = JTable::getInstance($type, $prefix);
						
						if(empty($table))
						{	
							throw new RuntimeException($data['element'].' extension not installed. Please install that first and then reinstall this app');
						}
						$fields = $table-> getFields();
						
						$hideFields = array();
						foreach( $fields as $field => $value)
							$hideFields[] = $field;
															
						$incFields = array();
					
						foreach( $version->fields->children() as $field)
							$incFields[] = (string) $field;

								
						foreach( $incFields as $incField)
						{
							$key = array_search($incField, $hideFields);
							if($key !== false) 
								unset($hideFields[$key]);
						}	
							
						$hideFields = array_values($hideFields);	
							
						$special = new stdclass;
						$special->special = new stdclass;
						$special->special->dbtable = $dbtable;
						$special->special->key = isset($version->key) ? (string) $version->key :'id';
						$special->special->type = $type;
						$special->special->prefix = $prefix;
											
						$options = new stdclass;
						$options->formFile = $formFile;
						$options->hideFields = $hideFields;
						$options->ignoreChanges = array('checked_out','checked_out_time','params','language'); // default values
						$options->convertToInt = array('publish_up', 'publish_down');// default values
						$options->displayLookup = new stdclass;
						

						$typeTable = JTable::getInstance('Contenttype');
						$typeTable->load(array('type_alias'=>$type_alias));
						$typeTable->type_title = (isset($version->attributes()->type) ?  ucfirst($data['context']).' '.ucfirst($alias) : ucfirst($alias));   
						$typeTable->type_alias = $type_alias;
						$typeTable->table = json_encode($special);
						$typeTable->rules = '';
						$typeTable->field_mappings = '';	
						$typeTable->router = '';	
						$typeTable->content_history_options = json_encode($options);	

						if(!$typeTable->store())
						{
							throw new RuntimeException('Failed to store '.$alias.'  type  data!');
						}
					}
					//create ucm type for extension category
					if($versioning->catdbtable && $versioning->cattype)
					{
						
						$cat_type_alias = $data['element'].'.category';
						$catdbtable = (string)  $versioning->catdbtable;
						if(strpos($catdbtable,'#__') === false)
							$catdbtable = '#__'.$catdbtable;	
						$cattype = (string)  $versioning->cattype;
						$catprefix = (string)  $versioning->catprefix;
						if(isset($versioning->catformFile))
						$catformFile = (string)  $versioning->catformFile;	

						$cattable = JTable::getInstance($cattype, $catprefix);
						
						$catfields = $cattable->getFields();
						
						$cathideFields = array();
						foreach( $catfields as $field => $value)
							$cathideFields[] = $field;
															
						$catincFields = array();
					
						foreach( $versioning->catfields->children() as $field)
							$catincFields[] = (string) $field;

						foreach( $catincFields as $incField)
						{
							$key = array_search($incField, $cathideFields);
							if($key !== false)
								unset($cathideFields[$key]);
						}	
							
						$cathideFields = array_values($cathideFields);	
							
						$catspecial = new stdclass;
						$catspecial->special = new stdclass;
						$catspecial->special->dbtable = $catdbtable;
						$catspecial->special->key = isset($versioning->catkey) ? (string) $versioning->catkey :'id';
						$catspecial->special->type = $cattype;
						$catspecial->special->prefix = $catprefix;
												
						$catoptions = new stdclass;
						$catoptions->formFile = $catformFile;
						$catoptions->hideFields = $cathideFields;
						$catoptions->ignoreChanges = array('checked_out","checked_out_time','params','language'); // default values
						$catoptions->convertToInt = array('publish_up', 'publish_down');// default values
						$catoptions->displayLookup = new stdclass;
						
						
						
						$cattypeTable = JTable::getInstance('Contenttype');
						$cattypeTable->load(array('type_alias'=>$cat_type_alias));
						$cattypeTable->type_title = ucfirst($data['context']) . ' Category';
						$cattypeTable->type_alias = $cat_type_alias;
						$cattypeTable->table = json_encode($catspecial);
						$cattypeTable->rules = '';
						$cattypeTable->field_mappings = '';	
						$cattypeTable->router = '';	
						$cattypeTable->content_history_options = json_encode($catoptions);	

						if(!$cattypeTable->store())
						{
							  throw new RuntimeException('Failed to store '.$data['context'].' category type data!');
						}
					}
				}
			}
		}
	}
	
	public function onExtensionAfterUpdate($installer, $eid)
	{
		$this->onExtensionAfterInstall($installer, $eid);
	}	
	
    public function onExtensionBeforeUninstall($eid)
    {
        if(!$eid)
			return;

           $extension = Jtable::getInstance('extension');
           $extension->load($eid);

           $this->_extensionType = $extension->type;

           if($extension->type != 'file')
             return;

           $name = $extension->element;
           $path = JPATH_MANIFESTS . '/files/'. $name.'.xml';

           if(!JFile::exists($path))
               return;         

           $this->_manifest = JFactory::getXML($path);
       
    }

	public function onExtensionAfterUninstall($installer, $eid,$result)
	{
		
        if(!$result)
			return;
				
		if( $this->_extensionType != 'file')
            return;
	
        if(is_null($this->_manifest))
            throw new Exception('Unable to cache the XML manifest data so unable to inline data delete record from the inline view table');	

	    $type =  (string) $this->_manifest->attributes()->type;

		if($type != 'file')
			return;
	
		
		if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/inlineview.php'))
		{
		
			require_once JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/inlineview.php';	
		
			if(!isset($this->_manifest->arkeditor))
				return;

        	$arkeditor = $this->_manifest->arkeditor;

			if($arkeditor)
			{
				//Delete record from inline view plugins table	
				
				$element =  (string) $arkeditor->extensionName;
				if(isset($arkeditor->context))
					$context = (string) $arkeditor->context;
                else
                   $context =  str_replace('com_','', $element);
					
				$row = JTable::getInstance('inlineView','ARKTable');
				$row->load(array('element'=>$element));
          					
				if($row->element && $row->delete() === false)
					throw new RuntimeException('Failed to delete record from the inline view table for '.$element.' extension');	
												
				if($arkeditor->versioning)
				{	
					$type = JTable::getInstance('Contenttype');
					
					$versioning = $arkeditor->versioning;
							
					$versions = $versioning->versions;
					
					foreach($versions->children() as $version)
					{
						
						$type_alias =  $element .'.'. (string) (isset($version->attributes()->type) ? $version->attributes()->type : strtolower($context));
						$type->load(array('type_alias'=>$type_alias));
						
						if($type->type_id && $type->delete() === false)
							throw new RuntimeException('Failed to delete ucm '.$type_alias.' type record for '.$element);	
				
					}
					if($arkeditor->versioning->catdbtable && $arkeditor->versioning->cattype)
					{	
						$cattype = JTable::getInstance('Contenttype');
						$cattitle_alias =  $element .'.category';
						$cattype->load(array('type_alias'=>$cattitle_alias));
						
						if($cattype->type_id && $cattype->delete() === false)
							throw new RuntimeException('Failed to delete ucm category type record for '.$element);	
					}
				}
			}
		}	
	}
}