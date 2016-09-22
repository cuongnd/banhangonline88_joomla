<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_DBUpdater
{
	function BaseSettings($component)
	{
		// make sure the settings table has its entry for this component
		$log = "Updating base settings entry for $component - ";
	
		JTable::addIncludePath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'tables');
		
		// load default.com_fsj
		$setting = JTable::getInstance('FSJSettings', 'JTable');
		$setting->loadByName("default.com_fsj");	
			
		// default.com_fsj missing, need to add it
		if ($setting->id < 1) 
		{
			$qry = "INSERT INTO #__fsj_main_settings (parent_id, lft, rgt, level, name, title) VALUES (0, 0, 1, 0, 'default.com_fsj', 'Default Global Settings')";
			$db	= JFactory::getDBO();
			$db->setQuery($qry);
			$db->Query();
			$setting->loadByName("default.com_fsj");	
		}
			
		// update default settings and store
		if ($component == "com_fsj_main")
		{
			$com_xml_file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'main.xml';
			$xml = simplexml_load_file($com_xml_file);
			if ($xml)
				$setting->value = (string)$xml->global_defaults;
			$setting->check();
			$setting->store();
		}
		$parentId = $setting->id;
			
		// load com_fsj
		$setting = JTable::getInstance('FSJSettings', 'JTable');		
		$setting->loadByName("com_fsj");
			
		// com_fsj missing?
		if ($setting->id < 1)
		{	
			$setting->name = "com_fsj";
			$setting->title = "Global Settings";
			$setting->setLocation($parentId, 'last-child');
			$setting->store();
		} else if ($setting->parent_id != $parentId)
		{
			$setting->setLocation($parentId, 'last-child');
			$setting->store();
		}

		$parentId = $setting->id;
		
		
		
		// load default settings entry
		$setting = JTable::getInstance('FSJSettings', 'JTable');		
		$setting->loadByName("default.".$component);
		
		if ($setting->id < 1)
		{
			// find out the joomla asset id for the component and add that to the data
			
			// create base settings entry
			$setting->name = "default.".$component;
			$setting->title = $component . " defaults";
			$setting->j_asset = 0;
			$setting->setLocation($parentId, 'last-child');

		} else if ($setting->parent_id != $parentId) {
			$setting->setLocation($parentId, 'last-child');
		}
		
		$com_xml_file = JPATH_ADMINISTRATOR.DS.'components'.DS.$component.DS.str_replace("com_fsj_","",$component).'.xml';
		$xml = simplexml_load_file($com_xml_file);
		if ($xml)
			$setting->value = (string)$xml->settings_defaults;
		$setting->check();
		$setting->store();
		

		$parentId = $setting->id;
		$setting = JTable::getInstance('FSJSettings', 'JTable');		
		$setting->loadByName($component);
		
		if ($setting->id < 1)
		{
			// create settings entry
			$asset = JTable::getInstance('Asset', 'JTable');
			$asset->loadByName($component);
			$setting->name = $component;
			$setting->title = $component;
			$setting->j_asset = $asset->id;
			$setting->setLocation($parentId, 'last-child');
			$setting->check();
			$setting->store();
		} else if ($setting->parent_id != $parentId)
		{
			$setting->setLocation($parentId, 'last-child');
			$setting->check();
			$setting->store();
		}
		
		// got this far, verify that the asset id is same as the component (this gets messeg up sometimes!)
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__assets WHERE name = '" . $db->escape($component) . "'";
		$db->setQuery($qry);
		$base_asset = $db->loadObject();
			
		if ($base_asset && $base_asset->id != $setting->j_asset)
		{
			$setting->j_asset = $base_asset->id;
			$setting->check();
			$setting->store();	
		}
		
		return "";//$log . "Done";	
	}
	
	function DatabaseEntries($component, $path = "")
	{
		//if ($component != "com_fsj_faqs") return;
		/*if ($component != "com_fsj_glossary" && $component != "com_fsj_main" &&
			$component != "com_fsj_announce" && $component != "com_fsj_faqs"
			&& $component != "com_fsj_kb") return;*/
		
		JTable::addIncludePath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'tables');
		$db	= JFactory::getDBO();

		$fsjcom = str_replace("com_fsj_","",$component);
		$updatefile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.$component.DS."$fsjcom.xml";
		
		if (!file_exists($updatefile))
			return "ERROR: Unable to open update file $updatefile\n";	
		
		$db	= JFactory::getDBO();
		
		$log = "";
		
		$xml = simplexml_load_file($updatefile);
		
		if (!$xml)
			return "ERROR: Error in XML file $updatefile\n";	
		
		$hasdata = false;
		
		if ($xml->tables && $xml->tables->table)
		{
			foreach ($xml->tables->table as $table)
			{
				$tlog = array();
				$tablename = $table->attributes()->name;
				$tabler = "#__fsj_{$fsjcom}_{$tablename}";
				
				//echo "Table : $tablename<br>";
				
				if ($table->data)
				{
					foreach ($table->data as $data)
					{
						//print_p($data);
						
						if (!$data->row) continue;
						$key = (string)$data->attributes()->key;
						$overwrite = (int)$data->attributes()->overwrite;
						$hasdata = true;
						
						foreach ($data->row as $row)
						{
							$rowdata = $this->LoadRowFromXML($row);
							$rowdesc = (string)$row->attributes()->rowdesc;
							if ($rowdesc == "")
								$rowdesc = $rowdata[$key];
							
							// check if the row exists
							$query = $db->getQuery(true);
							$query->select($key);
							$query->from($tabler);
							$query->where("$key = '" . $rowdata[$key] . "'");
							
							$db->setQuery($query);
							$result = $db->loadObjectList();
							
							$found = count($result) > 0;
							
							$no_asset = (int)$row->attributes()->no_asset;
							
							if (!$found) // if not insert the row
							{
								//echo "Not Found : Adding - {$data->asset} && !{$no_asset}<br>";
								if ($data->asset && !$no_asset)
								{
									$has_asset = $table->attributes()->asset_id || $table->attributes()->category || $table->attributes()->article;
									//echo "Inserting to $tabler - $has_asset<br>";
									$tableclass = (string)$data->asset->attributes()->table;
									$tableprefix = (string)$data->asset->attributes()->prefix;
									
									if (!$tableprefix)
									{
										$tableprefix = "fsj_" . $fsjcom;
									}
									if ($tableclass == "")
										die( "No table class" );
									
									if ($row->lookup)
									{
										foreach ($row->lookup as $lookup)
										{
											$lu_field = (string)$lookup->attributes()->field;	
											$lu_table = (string)$lookup->attributes()->table;	
											$lu_data_id = (string)$lookup->attributes()->data_id;
											$lu_value = (string)$lookup->attributes()->value;
											
											$luqry = "SELECT {$lu_value} FROM #__fsj_{$lu_table} WHERE data_id = " . (int)$lu_data_id;
											$db->setQuery($luqry);
											$row = $db->loadObject();
											if (!$row)
											{
												echo "Row not found : $luqry<br>";	
											} else {
												//echo "Found $lu_field as {$row->$lu_value}<br>";
												$rowdata[$lu_field] = $row->$lu_value; 	
											}
										}	
									}
									
									$tableclass_full = "JTable{$tableprefix}{$tableclass}";
									if (!class_exists($tableclass_full))
									{
										// load in the table php file
										$file = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$component.DS.'tables'.DS.$tableclass.".php";
										require_once($file);	
									}
									
									$tableobj = JTable::getInstance($tableclass, 'JTable' . $tableprefix);
									
									if (!$tableobj)
									{
										echo "Cant find table JTable{$tableprefix}{$tableclass}<br>";	
									} else {
										
										// if nested, then need to lookup the parent_id
										$tableobj->bind($rowdata);
										
										// use parent id as a parent location is we have one
										if (array_key_exists("parent_id", $rowdata))
										{
											$tableobj->setLocation($rowdata['parent_id'],'first-child');
											//echo "Set location to first-child of {$rowdata['parent_id']}<br>";
										}
										
										$tableobj->check();
										$tableobj->store();
									}
								} else {
									//echo "Basic Entry - no asset<br>";
									unset($rowdata['no_asset']);
									$rowdata_object = (object)$rowdata;
									$db->insertObject($tabler, $rowdata_object);
								}
								$tlog[] = "Inserting row '$rowdesc'\n";
							} else if ($overwrite) // if exists and overwrite then replace the row	
							{
								//$db->updateObject($tabler, $rowdata, $key);
								$tlog[] = "Updating row '$rowdesc'\n";
							}
						}					
					}	
				}
				
				if (count($tlog) > 0)
				{
					$log .= "Table : {$fsjcom}_$tablename\n";
					$log .= implode("\n", $tlog);				
				}
			}
		}
		
		return $log;
	}
	
	function LoadRowFromXML($row)
	{
		$data = array();
		
		foreach($row->attributes() as $key => $value)
		{
			if ($key == "rowdesc") continue;
			$data[$key] = (string)$value;
		}

		return $data;
	}
	
	// update database structure
	function UpdateDatabase($component, $path = "")
	{
		$log = "";
		$fsjcom = str_replace("com_fsj_","",$component);
		$updatefile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.$component.DS."$fsjcom.xml";
		
		if (!file_exists($updatefile))
		{
			$log .= "Unable to open update file $updatefile\n";
			return $log;	
		}
		
		$db	= JFactory::getDBO();
		
		$xml = simplexml_load_file($updatefile);
		
		if (!$xml)
		{
			$log .= "Error in XML file\n";
			return $log;	
		}
		
		$qry = "SHOW TABLES";
		$db->setQuery($qry);
		$existingtables_ = $db->loadObjectList();
		$existingtables = array();
		
		foreach($existingtables_ as $existingtable)
		{
			foreach ($existingtable as $key => $tname)
				$existingtables[$tname] = $tname;
		}
		
		if ($xml->tables)
		{
			foreach ($xml->tables->table as $table)
			{
				$tablename = $table->attributes()->name;
				$tabler = "{$db->getPrefix()}fsj_{$fsjcom}_{$tablename}";
				if ($table->attributes()->prefix)
					$tabler = "{$db->getPrefix()}fsj_{$table->attributes()->prefix}_{$tablename}";
				$logtitle = "\n\nProcessing table $tablename as $tabler\n";
				
				$fields = $this->BuildFieldList($table);
				
				if (array_key_exists($tabler,$existingtables))
				{
					$logcontent = $this->CompareTable($tabler, $table, $fields);
				} else {
					$logcontent = $this->CreateTable($tabler, $table, $fields);
				}
				
				if ($logcontent)
				{
					$log.= $logtitle . $logcontent;
				} 
			}
		}
		
		return $log;
	}
	
	function BuildFieldList($table)
	{
		$fields = array();
		$indexs = array();
		
		if ($table->attributes()->article)
		{
			@$table->addAttribute('id', 1);
			@$table->addAttribute('asset_id', 1);
			@$table->addAttribute('title', 1);
			@$table->addAttribute('alias', 1);
			@$table->addAttribute('state', 1);
			@$table->addAttribute('created', 1);
			@$table->addAttribute('modified', 1);
			@$table->addAttribute('checkout', 1);
			@$table->addAttribute('version', 1);
			@$table->addAttribute('language', 1);
			@$table->addAttribute('access', 1);
			@$table->addAttribute('hits', 1);
			@$table->addAttribute('publish_date', 1);
			@$table->addAttribute('metadata', 1);
			@$table->addAttribute('params', 1);
			@$table->addAttribute('featured', 1);
			
			// add primary id as index
		}
		
		if ($table->attributes()->category)
		{
			@$table->addAttribute('id', 1);
			@$table->addAttribute('data_id', 1);
			@$table->addAttribute('asset_id', 1);
			@$table->addAttribute('title', 1);
			@$table->addAttribute('alias', 1);
			@$table->addAttribute('state', 1);
			@$table->addAttribute('created', 1);
			@$table->addAttribute('checkout', 1);
			@$table->addAttribute('language', 1);
			@$table->addAttribute('access', 1);
			@$table->addAttribute('nested', 1);
			@$table->addAttribute('ordering', 1);
		}

		if ($table->attributes()->id)
		{
			$fields[] = array('name' => 'id', 'type' => 'int(10) unsigned', 'null' => 'NOT NULL', 'extra' => 'AUTO_INCREMENT');
			$indexs[] = array('type' => 'PRIMARY KEY', 'name' => '', 'fields' => array('id'));
		}
		
		if ($table->attributes()->asset_id)
			$fields[] = array('name' => 'asset_id', 'type' => 'int(10) unsigned', 'null' => 'NOT NULL', 'default' => '0');
	
		if ($table->attributes()->data_id)
			$fields[] = array('name' => 'data_id', 'type' => 'int(10) unsigned', 'null' => 'NOT NULL', 'default' => '0');
		//$parts[] = "`asset_id` int(10) NOT NULL DEFAULT '0'";
		
		if ($table->attributes()->nested)
		{
			$fields[] = array('name' => 'parent_id', 'type' => 'int(10) unsigned', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'lft', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'rgt', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'level', 'type' => 'int(10) unsigned', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'path', 'type' => 'varchar(255)', 'null' => 'NOT NULL', 'default' => '');
		}
		
		if ($table->attributes()->title)
			$fields[] = array('name' => 'title', 'type' => 'varchar(255)', 'null' => 'NOT NULL', 'default' => '');
		
		if ($table->attributes()->alias)
			$fields[] = array('name' => 'alias', 'type' => 'varchar(255)', 'null' => 'NOT NULL', 'default' => '');
		
		if ($table->attributes()->state)
		{
			$fields[] = array('name' => 'state', 'type' => 'tinyint(3)', 'null' => 'NOT NULL', 'default' => '0');
			$indexs[] = array('type' => 'KEY', 'name' => 'idx_state', 'fields' => array('state'));
		}
		
		if ($table->attributes()->default)
		{
			$fields[] = array('name' => 'home', 'type' => 'tinyint(3)', 'null' => 'NOT NULL', 'default' => '0');
		}
		
		if ($table->attributes()->created)
		{
			$fields[] = array('name' => 'created', 'type' => 'datetime', 'null' => 'NOT NULL', 'default' => '0000-00-00 00:00:00');
			$fields[] = array('name' => 'created_by', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'created_by_alias', 'type' => 'varchar(255)', 'null' => 'NOT NULL', 'default' => '');
			$indexs[] = array('type' => 'KEY', 'name' => 'idx_createdby', 'fields' => array('created_by'));
		}
		
		if ($table->attributes()->modified)
		{
			$fields[] = array('name' => 'modified', 'type' => 'datetime', 'null' => 'NOT NULL', 'default' => '0000-00-00 00:00:00');
			$fields[] = array('name' => 'modified_by', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
		}
				
		if ($table->attributes()->checkout)
		{
			$fields[] = array('name' => 'checked_out_time', 'type' => 'datetime', 'null' => 'NOT NULL', 'default' => '0000-00-00 00:00:00');
			$fields[] = array('name' => 'checked_out', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
			$indexs[] = array('type' => 'KEY', 'name' => 'idx_checkout', 'fields' => array('checked_out'));
		}
		
		if ($table->attributes()->publish_date)
		{
			$fields[] = array('name' => 'publish_up', 'type' => 'datetime', 'null' => 'NOT NULL', 'default' => '0000-00-00 00:00:00');
			$fields[] = array('name' => 'publish_down', 'type' => 'datetime', 'null' => 'NOT NULL', 'default' => '0000-00-00 00:00:00');
		}
		
		if ($table->attributes()->ordering)
			$fields[] = array('name' => 'ordering', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
		
		if ($table->attributes()->hits)
			$fields[] = array('name' => 'hits', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
		
		if ($table->attributes()->version)
			$fields[] = array('name' => 'version', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
		
		if ($table->attributes()->access)
		{
			$fields[] = array('name' => 'access', 'type' => 'int(10)', 'null' => 'NOT NULL', 'default' => '0');
			$indexs[] = array('type' => 'KEY', 'name' => 'idx_access', 'fields' => array('access'));
		}
		
		if ($table->attributes()->params)
			$fields[] = array('name' => 'params', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '');
		
		if ($table->attributes()->language)
		{
			$fields[] = array('name' => 'language', 'type' => 'char(7)', 'null' => 'NOT NULL', 'default' => '');
			$indexs[] = array('type' => 'KEY', 'name' => 'idx_language', 'fields' => array('language'));
		}

		if ($table->attributes()->metadata)
		{
			$fields[] = array('name' => 'metakey', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '');
			$fields[] = array('name' => 'metadesc', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '');
			$fields[] = array('name' => 'metadata', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '');
		}

		if ($table->attributes()->rating)
		{
			$fields[] = array('name' => 'rating', 'type' => 'int(11)', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'rating_votes', 'type' => 'int(11)', 'null' => 'NOT NULL', 'default' => '0');
			$fields[] = array('name' => 'rating_score', 'type' => 'int(11)', 'null' => 'NOT NULL', 'default' => '0');
		}
		
		if ($table->attributes()->featured)
		{
			$fields[] = array('name' => 'featured', 'type' => 'tinyint(3)', 'null' => 'NOT NULL', 'default' => '0');
		}

		foreach ($table->field as $field)
		{
			$fields[] = array(
				'name' => (string)$field->attributes()->name,
				'type' => (string)$field->attributes()->type,
				'null' => $field->attributes()->null ? 'NULL' : 'NOT NULL',
				'extra' => $field->attributes()->auto_inc ? 'AUTO_INCREMENT' : '',
				'default' => (string)$field->attributes()->default);
		}

		foreach ($table->index as $indexxml)
		{
			$index = array();
			$index['fields'] = explode(";",$indexxml->attributes()->fields);
			
			$name = $indexxml->attributes()->name;
			
			if ($name == "PRIMARY")
			{
				$index['type'] = "PRIMARY KEY";
				$index['name'] = "";
			} else if ($indexxml->attributes()->unique)
			{
				$index['type'] = "UNIQUE KEY";
				$index['name'] = (string)$indexxml->attributes()->name;
			} else if ($indexxml->attributes()->text)
			{
				$index['type'] = "FULLTEXT KEY";
				$index['name'] = (string)$indexxml->attributes()->name;
			} else {
				$index['type'] = "KEY";
				$index['name'] = (string)$indexxml->attributes()->name;
			}

			$indexs[] = $index;			
		}
	
		foreach ($fields as &$field)
		{
			if (strtolower($field['type']) == "text" && !array_key_exists("default",$field))
				$field['default'] = "";
			if (strpos(" ".$field['type'],"varchar") > 0 && !array_key_exists("default",$field)) 
				$field['default'] = 0;
			if (array_key_exists('default', $field))
				if (strpos(" ".$field['type'],"int") > 0 && $field['default'] == "") $field['default'] = 0;	
			if (array_key_exists('extra', $field) && $field['extra'] == "AUTO_INCREMENT")
				unset($field['default']);
		}
	
		return array('fields' => $fields, 'indexs' => $indexs);
	}

	function GetExistingTables()
	{
		if (empty($this->existingtables))
		{
			$this->existingtables = array();
			$db	= JFactory::getDBO();

			$qry = "SHOW TABLES";
			$db->setQuery($qry);
			$existingtables_ = $db->loadResultArray();
			$existingtables = array();
			foreach($existingtables_ as $existingtable)
			{
				$existingtable = str_replace($db->getPrefix(),'#__',$existingtable);
				$this->existingtables[$existingtable] = $existingtable;
			}
		}
		
		return $this->existingtables;
	}
	
	function CompareTable($table, &$stuff, &$fields)
	{
		$db	= JFactory::getDBO();

		$log = "<b><i>Existing table</i></b>\n";
		
		// check for fulltext indexes, if any force MyISAM!
		if (!self::IsMySQL56())
		{
			$db->setQuery("SHOW TABLE STATUS WHERE Name = '$table'");
			$table_info = $db->loadObject();
			
			$has_fulltext = false;
			foreach ($fields['indexs'] as $index)
				if ($index['type'] == "FULLTEXT KEY")
					$has_fulltext = true;
			
			if ($has_fulltext && !self::IsMySQL56() && strtolower($table_info->Engine) != 'myisam')
			{
				$db->setQuery("ALTER TABLE $table ENGINE = MYISAM");
				$db->Query();
			}
		}
		
		$changes = array();
		// COMPARE FIELDS
		{
			$qry = "DESCRIBE $table";
			$db->setQuery($qry);
			$existing_ = $db->loadAssocList();
			
			$existing = array();
			
			foreach ($existing_ as $field)
			{
				$existing[$field['Field']] = $field;	
			}
			
			foreach ($fields['fields'] as $field)
			{
				$fieldname = $field['name'];
				if (array_key_exists($fieldname,$existing))
				{
					$existingfield = $existing[$fieldname];
					
					if (!array_key_exists("extra", $field)) $field['extra'] = "";
					if (!array_key_exists("default", $field)) $field['default'] = "";
					
					$same = true;
					
					// type
					if (strtolower($existingfield['Type']) != strtolower($field['type']))
					{
						//$log .= "Type different - should be {$field['type']}, is {$existingfield['Type']}\n";
						$same = false;
					}
						
					// extra for auto inc etc
					if (strtolower($field['extra']) != strtolower($existingfield['Extra']))
					{
						//$log .= "Extra - should be {$field['extra']}, is {$existingfield['Extra']}\n";
						$same = false;
					}
					
					// default values
					if ($field['default'] != $existingfield['Default'])
					{
						//$log .= "Default - should be {$field['default']}, is {$existingfield['Default']}\n";
						$same = false;
					}
					
					// check for null and not null
					if ($field['null'] == "NULL" && $existingfield['Null'] == "NO")
					{
						//$log .= "NULL - should be {$field['null']}, is {$existingfield['Null']}\n";
						$same = false;
					}
					if ($field['null'] == "NOT NULL" && $existingfield['Null'] == "YES")
					{
						//$log .= "NULL - should be {$field['null']}, is {$existingfield['Null']}\n";
						$same = false;
					}
						
					if (!$same)
					{
						//$log .= print_r($existingfield,true);
						//$log .= print_r($field,true);
						
						$change = "CHANGE `$fieldname` ";
						$change .= $this->FieldToSQL($field);
						$changes[] = $change;
						$log .= "Field <b>$fieldname</b> - different\n";
					}

					//ALTER TABLE `jos_fss_ticket_field` CHANGE `gfda` `iuytoiuyt` INT( 8 ) NOT NULL 
				} else {
					$log .= "Field <b>$fieldname</b> - missing\n";	
					$changes[] = "ADD " . $this->FieldToSQL($field);
				}
			}
		}
		
		// COMPARE INDEXES
		{
			$indexs = array();
						
			$qry = "SHOW INDEX FROM $table";
			$db->setQuery($qry);
			$existing_ = $db->loadAssocList();
			$existing = array();
			foreach ($existing_ as $index)
			{
				$existing[$index['Key_name']][$index['Seq_in_index']] = $index;
			}
	
			foreach ($fields['indexs'] as $index)
			{
				$createindex = false;
				$name = $index['name'];
				if ($index['type'] == "PRIMARY KEY") $name = "PRIMARY";
				
				$fields = $index['fields'];

				if (array_key_exists($name,$existing))
				{
					$existindex = $existing[$name];
					
					// compare indexes and their fields. BORING
					$same = true;
					
					$ex_type = "KEY";
					$existingfields = array();
					foreach ($existindex as $existingfield)
					{
						$existingfields[] = $existingfield['Column_name'];
						if ($existingfield['Key_name'] == "PRIMARY")
							$ex_type = "PRIMARY KEY";
						elseif ($existingfield['Non_unique'] == 0)
							$ex_type = "UNIQUE KEY";
						elseif ($existingfield['Index_type'] == "FULLTEXT")
							$ex_type = "FULLTEXT KEY";
					}
					
					//$log .= print_r($existingfields,true);
						
					if ($ex_type != $index['type'])
					{
						$log .= "Different index type. Should be {$index['type']} is $ex_type\n";
						$same = false;
					}
					
					if (count($existingfields) != count($index['fields']))
					{
						$log .= "Different field count. Should be " . count($index['fields']) . " is " . count($existingfields) . "\n";
						$same = false;
					} else {
						$ex_fields_txt = implode(", ", $existingfields);
						$fields_txt = implode(", ", $index['fields']);
						if ($ex_fields_txt != $fields_txt)
						{
							$log .= "Different fields. Should be $fields_txt is $ex_fields_txt\n";
							$same = false;
						}
					}
									
					if (!$same)
					{
						//print_p($index);
						//print_p($existing_);
						$log .= "Index <b>" . $name . "</b> - different\n";
						//exit;
						$drop = "DROP INDEX `" . $name . "`";
						$changes[] = $drop;
						$createindex = true;
					}
					
				} else {
					$log .= "Index <b>" . $name . "</b> - new\n";
					$createindex = true;
				}
				
				if ($createindex)
				{
					//$log .= "Creating index $name\n";
					$changes[] = "ADD " . $this->IndexToSQL($index);
				}
			}
		}
		
		if (count($changes) > 0)
		{
			$changesql = "ALTER TABLE `$table`\n";
			$changesql .= implode(",\n",$changes);
			
			$log .= "<b><i>Table Wrong - Changing</i></b>\n\n";
			$log .= $changesql."\n\n";
			
			$db->setQuery($changesql);
			$db->Query();
		} else {
			$log = "";
		}
		//exit;

		return $log;
	}

	function FieldToSQL($field)
	{
		if (array_key_exists('extra', $field) && $field['extra'] == "AUTO_INCREMENT")
			unset($field['default']);

		$part = "`{$field['name']}` {$field['type']} {$field['null']} ";
		if (array_key_exists('extra', $field)) $part .= $field['extra'];
		
		if (array_key_exists('default', $field))
		{
			$part .= " DEFAULT '{$field['default']}'";
		}
	
		return $part;	
	}
	
	function IndexToSQL($index)
	{
		return "{$index['type']} {$index['name']} (`" . implode("`, `",$index['fields']) . "`)";
	}

	function CreateTable($table, &$stuff, &$fields)
	{
		$db	= JFactory::getDBO();

		$log = "New table\n";
		
		$create = "CREATE TABLE IF NOT EXISTS `{$table}` (\n";
		
		$parts = array();
		$indexs = array();

		foreach ($fields['fields'] as &$field)
		{
			$parts[] = $this->FieldToSQL($field);
		}
		
		$has_fulltext = false;
		foreach ($fields['indexs'] as $index)
		{
			$parts[] = $this->IndexToSQL($index);
			if ($index['type'] == "FULLTEXT KEY")
				$has_fulltext = true;
		}
	
		$create = $create . implode(",\n",$parts) . "\n) DEFAULT CHARSET=utf8";

		if ($has_fulltext && !self::IsMySQL56())
			$create .= " ENGINE = MyISAM";

		$db->SetQuery($create);
		$db->Query();

		$log .= $create."\n\n";
		
		return $log;
	}
	
	static $is_mysql56;
	function IsMySQL56()
	{
		if (empty(self::$is_mysql56))
		{
			$db	= JFactory::getDBO();
			$qry = "SELECT VERSION() as version";
			$db->setQuery($qry);
			
			$version = $db->loadObject();
			
			self::$is_mysql56 = false;
			if (version_compare($version->version, '5.6.0', '>='))
				self::$is_mysql56 = true;
		}	
		
		return self::$is_mysql56;
	}

}