<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Class for handling image select popup. 
 * 
 * THIS SHOULD NOT BE IN THIS FOLDER!
 **/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class FSJ_PickImage
{
	function PickImage()
	{
		FSJ_Page::Style('libraries/fsj_core/assets/css/field/field.pickimage.less');
		FSJ_Page::Script('libraries/fsj_core/assets/js/field/field.pickimage.js');

		$session = JFactory::getSession();
		$last_path = $session->get('fsjimage_lastpath');
		$this->path = JRequest::getVar('path', $last_path);
		$session->set('fsjimage_lastpath', $this->path);
		$offset = JRequest::getVar('offset',0);
		$this->type = 'site';
		$this->spath = JRequest::getVar('spath');
		if ($this->spath == "" && $this->type == "specific") $this->type = "site";
		
		$this->cols = 6;
		$this->dest_width = 64;
		$this->dest_height = 64;
		$this->rows = 4;
		$filter = '(.jpg$|.png$|.gif$|.jpeg$)';
	
		$uri = JURI::getInstance();
		$uri->delVar('type');
		$uri->delVar('offset');
		$uri->delVar('path');
		
		$this->baselink = "index.php". $uri->toString(array("query"));
		
		$this->show_folders = true;
		
		$this->base_path = '';
		
		$perpage = $this->cols * $this->rows;

		$fullpath = JPATH_SITE.DS.'images'.DS.$this->base_path.$this->path;
		if ($this->path)
			$fullpath .= DS;
		$fullpath = str_replace("/",DS,$fullpath);
		$fullpath = str_replace(DS.'..',DS,$fullpath);
		$fullpath = str_replace(DS.'.',DS,$fullpath);
			
		if (!file_exists($fullpath))
		{			
			$files = array();
			$folders = array();
		} else {

			$folders = JFolder::folders($fullpath);
			$files = JFolder::files($fullpath);
			
			if ($filter)
			{
				foreach ($files as $id => $file)
				{
					if (!preg_match("/$filter/i", $file))
						unset($files[$id]);
				}
			}
		}
		
		if ($this->path && $this->path != "/")
		{
			$parent = array();
			$parent[] = "..";
			$folders = array_merge($parent,$folders);
		}
	
		$none = array();
		$none[] = "";
		
		$files = array_merge($folders, $files);
		$files = array_merge($none, $files);
		$totalcount = count($files);
		
		$lastimage = $offset + $perpage;
		$imgno = 0;
		foreach ($files as $id => $file)
		{
			if ($imgno < $offset)
				unset($files[$id]);
			if ($imgno >= $lastimage)
				unset($files[$id]);
			$imgno++;
		}

		$this->fullpath = $fullpath;
		$this->files = $files;
		$this->perpage = $perpage;
		$this->link = $this->baselink."&path=".urlencode($this->path)."&type=" . $this->type;
		$this->totalpages = ceil($totalcount/$perpage);
		$this->currentpage = floor($offset/$perpage) + 1;

	 	//parent::display("pick_image");	
		include JPATH_LIBRARIES.DS.'fsj_core'.DS.'tmpl'.DS.'pickimage'.DS.'pickimage.php';

		return true;
	}
}