<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'general_helper.php');

class FSJ_TM_File_Helper
{
	static $files;
	static $basefiles; 
	
	static $client_id;
	static $element;
	static $component;
	
	static $count;
	static $model;
	
	static $error = array();
	
	static $current_path;
	static $catlist;
	
	static $getfiles;
	static function GetFiles($sort_filter = true, $element = null, $xpath = null)
	{
		$key = "$sort_filter|$element|$xpath";
		
		if (!empty(self::$getfiles[$key]))
			return self::$getfiles[$key];
		
		$app = JFactory::getApplication();

		if ($element)
		{
			self::$element = $element;
		} else {
			self::$element = $app->getUserStateFromRequest('files.filter.element', 'filter_element', null);
		}
		
		if (!$xpath)
		{
			$xpath = $app->getUserStateFromRequest('files.filter.xpath', 'filter_xpath', null);
		}
		
		if ($xpath == "" && $sort_filter == 2)
			$xpath = "0|g.general";
		
		if (self::$element == "" || $xpath == "")
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage("Please select a 'Path' and 'Language' using the filter to list ini files.", "notice");
			return array();
		}
		
		list (self::$client_id, self::$component) = explode("|", $xpath);
	
		$path = FSJ_TM_Helper::getPath(self::$client_id, self::$element, self::$component);
		self::$current_path = $path;
		self::$current_path = str_replace(JPATH_ROOT, "", self::$current_path);
		self::$current_path = trim(self::$current_path, "/\\");
			
		if (file_exists($path))
		{			
			$file_list = JFolder::files($path, ".ini");	
		} else {
			$file_list = array();	
		}
			
		self::$files = self::loadBaseFiles(self::$element);

		foreach ($file_list as $file)
		{
			$ofile = $file;
			$is_unpub = 0;
				
			if (substr($file, 0, 7) == "_unpub.")
			{
				$is_unpub = true;
				$file = substr($file, 7);	
			}
								
			$file = str_ireplace(self::$element . ".", "", $file);
	
			if (!array_key_exists($file, self::$files))
			{
				$obj = new stdClass();
				$obj->filename = $file;
				$obj->tstate = 1; // file in translated but not base
				$obj->no_base = 1;
				$obj->status = "";
				$obj->phrases = "";
				$obj->basetag = self::$element;
				$obj->ARGH = "ARGH";
				$obj->category = "";
				$obj->base_cat = '';
				$obj->is_base_cat = false;
					
				$obj->phrases_orig = 0;
			} else {
				$obj = self::$files[$file];
				$obj->tstate = 1; // file in translated				
			}
			
			if ($is_unpub)
				$obj->tstate = 3; // if filename is xx. so unpublished
				
			$obj->id = self::$client_id . "|" . self::$element . "|" . self::$component . "|" . $file;
			$obj->tag = self::$element;
								
			$obj->phrases_tran = FSJ_TM_File_Helper::PhraseCount($path.DS.$ofile);				
			$obj->ofile = $ofile;

			if ($obj->tag != $obj->basetag)
			{
				$obj->phrases = $obj->phrases_tran . " / " . $obj->phrases_orig;
				if ($obj->phrases_orig == 0)
				{
					$obj->status = 100;	
				} else if ($obj->phrases_orig == 0)
				{
					$obj->status = 0;	
				} else {
					$obj->status = round($obj->phrases_tran / $obj->phrases_orig * 100);
				}
			} else {
				$obj->phrases = $obj->phrases_tran;
				$obj->status = 100;
			}
				
			if ($obj->status > 100)
				$obj->status = 100;
				
			if ( ($obj->phrases_tran < $obj->phrases_orig) && $obj->status == 100)
				$obj->status = 99;
					
			if ($obj->phrases_tran > $obj->phrases_orig)
				$obj->phrases = $obj->phrases_orig . " / " . $obj->phrases_orig . " + " . ($obj->phrases_tran - $obj->phrases_orig);
					
			self::$files[$file] = $obj;				
		}
		
		$cats = self::loadCategories($path);
		self::$catlist = array();
		
		foreach (self::$files as $file)
		{		
			if (array_key_exists($file->filename, $cats))
			{
				$file->category = $cats[$file->filename];	
				$file->is_base_cat = false;
			}
			
			if ($file->category)
				self::$catlist[$file->category] = $file->category;
		}
		
		sort(self::$catlist);
		
		self::$getfiles[$key] = self::$files;
		
		if ((int)$sort_filter == 2)
		{
			self::SortCatTitle();
		} else if ($sort_filter)
		{
			self::SortAndFilter();
		}

		self::$getfiles[$key] = self::$files;

		//print_p(self::$files);
		return self::$files;
	}	
	
	static function SetModel($model)
	{
		self::$model = $model;	
	}
	
	static function loadCategories($path)
	{
		$filename = $path.DS."fsj_transman.cat.lst";
		
		$data = array();
		
		if (file_exists($filename))
		{
			$lines = file_get_contents($filename);
			$lines = explode("\n", $lines);
			foreach ($lines as $line)
			{
				if (strpos($line, "=") < 1) continue;
				
				list ($filename, $cat) = explode("=", $line, 2);
				$cat = trim($cat, "\"\n\r");
				$filename = trim($filename);
				
				if ($filename == "") continue;
				if ($cat == "") continue;
		
				$data[$filename] = $cat;
			}				
		}	
		
		return $data;
	}
	
	static function SortAndFilter()
	{

		$app = JFactory::getApplication();

		$state = $app->getUserStateFromRequest('com_fsj_transman.files.filter.f_state', 'filter_f_state', null);
		$status = $app->getUserStateFromRequest('com_fsj_transman.files.filter.f_status', 'filter_f_status', null);
		
		$offset = $app->getUserStateFromRequest('com_fsj_transman.files.limitstart', 'limitstart', 0);
		$limit = $app->getUserStateFromRequest('global.list.limit', 'list.limit', $app->getCfg('list_limit'), 'uint');
		
		$limit = self::$model->getState('list.limit');
		if ($limit == 0) $offset = 0;
		
		$order = $app->getUserStateFromRequest('com_fsj_transman.files.ordercol', 'filter_order', 'filename');
		$dir = $app->getUserStateFromRequest('com_fsj_transman.files.orderdirn', 'filter_order_Dir', 'asc');
		
		$search = $app->getUserStateFromRequest('com_fsj_transman.files.filter.search', 'filter_search', '');
		$category = $app->getUserStateFromRequest('com_fsj_transman.files.filter.category', 'filter_category', '');
		
		if (JRequest::getVar('search','XXX') != 'XXX')
			$search = JRequest::getVar('search');
		
	
		if (JRequest::getVar('order','XXX') != 'XXX')
			$order = JRequest::getVar('order');
		
		if (JRequest::getVar('orderdir','XXX') != 'XXX')
			$dir = JRequest::getVar('orderdir');
		
		if ($limit == 0)
			$limit = 100000;

		if (is_string($state) && $state == "")
			$state = -1;
		if (is_string($status) && $status == "")
			$status = -1;
		
		foreach (self::$files as $filename => $obj)
		{
			if ($state > -1)
			{
				if ($state == 2) 
				{
					if ($obj->no_base < 1)
					{
						unset(self::$files[$filename]);	
						continue;
					}
				} elseif ($state != $obj->tstate)
				{
					unset(self::$files[$filename]);	
					continue;
				}
			}
			
			if ($category == "--none--")
			{
				if ($obj->category != "")
				{
					unset(self::$files[$filename]);	
					continue;
				}
			} else if ($category != "")
			{
				if ($obj->category != $category)
				{
					unset(self::$files[$filename]);	
					continue;
				}
			}	
			
			if ($status > -1)
			{
				if ($status == 0 && $obj->status > 0)
				{
					unset(self::$files[$filename]);	
					continue;
				} else if ($status == 1 && ($obj->status == 0 || $obj->status == 100))
				{
					unset(self::$files[$filename]);	
					continue;
				} else if ($status == 99 && $obj->status == 100 && $obj->tstate == 1)
				{
					unset(self::$files[$filename]);	
					continue;
				} else if ($status == 100 && $obj->status != 100)
				{
					unset(self::$files[$filename]);	
					continue;
				}
			}
					
			if ($search != "")
			{
				// load in descriptions
				require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_transman'.DS.'models'.DS.'fields'.DS.'fsjtmdesc.php');				
				$tmd = new JFormFieldFSJTMDesc();
				
				$obj->description = $tmd->buildDescription($filename, "", $obj);

				if (stripos($filename, $search) === FALSE && 
					stripos($obj->description, $search) === FALSE && 
					$search != (string)$obj->phrases_orig  && 
					$search != (string)$obj->phrases_tran  && 
					$search != (string)$obj->phrases )
				{
					unset(self::$files[$filename]);	
					continue;
				}		
			}

		}
		
		self::$count = count(self::$files);
		
		$order = str_replace("a.", "", $order);
		if ($order == "phrases")
			$order = "phrases_orig";
		
		if ($order == "")
			$order = "filename";		
		
		FSJ_Helper::ArrayObjSort(self::$files, $order, $dir);
		
		// sort
		/*if ($order == "a.tstate")
		{
			usort(self::$files, function($a,$b){
					return $a->tstate > $b->tstate;
				});	
		}*/
		
		// paginate
		$num = -1;
		foreach (self::$files as $filename => $obj)
		{
			$num++;

			if ($num < $offset)
			{
				unset(self::$files[$filename]);	
				continue;
			}
			
			if ($num > $offset + $limit - 1)	
			{
				unset(self::$files[$filename]);	
				continue;
			}
		}
	}
	
	static function SortCatTitle()
	{		
		
        $sort = array();
        
        $sort_layer = new stdClass();
        $sort_layer->field = 'category';
        $sort_layer->dir = 'asc';
        $sort[] = $sort_layer;
       
        $sort_layer = new stdClass();
		$sort_layer->field = 'filename';
		$sort_layer->dir = 'asc';
		$sort[] = $sort_layer;
		
		FSJ_Helper::ArrayObjSortMulti(self::$files, $sort);
	}
	
	static function getCount()
	{
		return self::$count;
	}
		
	static $basefilesset;
	static function loadBaseFiles($tag)
	{
		/*$key = self::$client_id."|".self::$component;
		echo "Base Key : " . $key . "<br>";
		
		if (!empty(self::$basefilesset[$key]))
			return self::$basefilesset[$key];*/
		
		self::$basefiles = array();
			
		$basetag = FSJ_TM_Helper::GetBaseLanguage();
		//echo "Base Tag : $basetag<br>";
	
		$path = FSJ_TM_Helper::getPath(self::$client_id, $basetag, self::$component);
		
		//echo "Base Path : $path<br>";
		
		if (file_exists($path))
		{
			$file_list = JFolder::files($path, ".ini");
		} else {
			$file_list = array();	
		}
			
		$cats = self::loadCategories($path);

		foreach ($file_list as $file)
		{
			$ofile = $file;
			$file = str_ireplace($basetag . ".", "", $file);
			$file = str_ireplace("_unpub.", "", $file);
			$obj = new stdClass();
			$obj->filename = $file;
			$obj->ofile = $ofile;
			$obj->base_filename = $ofile;
			$obj->tstate = 0; // state 0 - file in base but not in translated
			$obj->status = 0;
			//$obj->phrases = "";
			$obj->phrases_orig = FSJ_TM_File_Helper::PhraseCount($path.DS.$ofile);
			$obj->id = self::$client_id . "|" . self::$element . "|" . self::$component . "|" . $file;
			$obj->basetag = $basetag;
			$obj->phrases = "0 / ". $obj->phrases_orig;
			$obj->phrases_tran = 0;
			$obj->no_base = 0;
				
			$obj->category = "";
			$obj->base_cat = "";
			$obj->is_base_cat = false;
				
			if (array_key_exists($obj->filename, $cats))
			{
				$obj->category = $cats[$obj->filename];
				$obj->base_cat = $obj->category;
				$obj->is_base_cat = true;
			}
				
			self::$basefiles[$file] = $obj;	
		}
		//self::$basefilesset[$key] = self::$basefiles;
		
		return self::$basefiles;
	}
	
	static function PhraseCount($filename)
	{
		$contents = @file_get_contents($filename);
		$lines = explode("\n", $contents);
		
		$count = 0;
		
		$keys = array();
		
		foreach ($lines as $line)
		{
			if (trim($line) == "")
				continue;
			
			if (FSJ_TM_Helper::isComment($line))
				continue;
			
			$parsed = FSJ_TM_Helper::ParseLine($line);
			if (!$parsed)
				continue;
			
			$keys[$parsed->key] = $parsed->value;
		}
		return count($keys);	
	}

	static function publish($filename)
	{
		list($client, $filename, $tag, $component) = FSJ_TM_Helper::ParseFileName($filename);
				
		$path = FSJ_TM_Helper::getPath($client, $tag, $component);
		
		$pubfile = $path . $tag . "." . $filename;
		$unpubfile = $path . "_unpub.".$tag . "." . $filename;
		
		if (!file_exists($unpubfile) && !file_exists($pubfile))
		{
			self::$error[] = "Cannot find file to publish - " . $unpubfile;
			return false;	
		}
		
		if (file_exists($pubfile) && file_exists($unpubfile))
		{
			self::$error[] = "Published file already exists - " . $pubfile;
			return false;	
		}
		
		if (!JFile::move($unpubfile, $pubfile))
		{
			self::$error[] = "Error moving file " . $unpubfile . " to " . $pubfile;
			return false;	
		}
		return true;
	}
	
	static function unpublish($filename)
	{
		list($client, $filename, $tag, $component) = FSJ_TM_Helper::ParseFileName($filename);
		
		$path = FSJ_TM_Helper::getPath($client, $tag, $component);
		
		$pubfile = $path . $tag . "." . $filename;
		$unpubfile = $path . "_unpub.".$tag . "." . $filename;
		
		if (!file_exists($pubfile) && !file_exists($unpubfile))
		{
			self::$error[] = "Cannot find file to unpublish - " . $pubfile;
			return false;	
		}
		if (file_exists($unpubfile) && file_exists($pubfile))
		{
			self::$error[] = "Unpublished file already exists - " . $unpubfile;
			return false;	
		}
		
		if (!JFile::move($pubfile, $unpubfile))
		{
			self::$error[] = "Error moving file " . $pubfile . " to " . $unpubfile;
			return false;	
		}
		return true;
	}
	
	static function createbase($filename)
	{
		$filenames = JRequest::getVar('cid', array(), 'array');

		$error = array();
		
		foreach ($filenames as $filename)
		{
			list($client, $filename, $tag, $component) = FSJ_TM_Helper::ParseFileName($filename);
			
			$source_path = FSJ_TM_Helper::getPath($client, $tag, $component);
			
			$base_path = FSJ_TM_Helper::getPath($client, FSJ_TM_Helper::GetBaseLanguage(), $component);
			
			$source_pub = $source_path . $tag . "." . $filename;
			$source_unpub = $source_path . "_unpub.".$tag . "." . $filename;

			$base_file = $base_path . FSJ_TM_Helper::GetBaseLanguage() . "." . $filename;
			
			if (file_exists($base_file))
			{
				self::$error[] = "Base file already exists - " . $base_file;
				//return false;	
			} else {
				if (file_exists($source_pub))
				{
					if (!JFile::copy($source_pub, $base_file))
					{
						self::$error[] = "Error copying file " . $source_pub . " to " . $base_file;
						//return false;	
					}
					//return true;
				} else if (file_exists($source_unpub))
				{
					if (!JFile::copy($source_unpub, $base_file))
					{
						self::$error[] = "Error copying file " . $source_unpub . " to " . $base_file;
						//return false;	
					}
					//return true;
				}
			}
		}
		
		if (count(self::$error) > 0)
		{
			if (count($filenames) > 1)
				self::$error = array();
			return true;
		}
		
		return true;
	}
	
	static function setCategory($path, $filename, $category)
	{
		$cats = self::loadCategories($path);
		
		//print_p($cats);
		
		if ($category == "--none--")
		{
			if (array_key_exists($filename, $cats))
				unset($cats[$filename]);
		} else {
			$cats[$filename] = $category;
		}
		
		//print_p($cats);
		
		self::saveCategories($path, $cats);
	}	
	
	static function saveCategories($path, $cats)
	{
		$filename = $path.DS."fsj_transman.cat.lst";
		
		$lines = array();
		foreach ($cats as $key => $value)
			$lines[] = "$key=\"$value\"";
		
		$text = implode("\n", $lines);
		
		file_put_contents($filename, $text);
	}
}