<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'general_helper.php');
jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");
jimport( 'fsj_core.lib.utils.parser');
class Fsj_transmanControllerTransman extends JControllerLegacy
{
	public function languages()
	{
		$this->setredirect('index.php?option=com_fsj_transman&view=languages');
	}
// 
	public function download()
	{
		$this->make_file();
		while (ob_get_level() > 0)
			ob_end_clean();
		$data = array(
			"title" => $this->item->title,
			"ver" => $this->item->ver,
			"langcode" => $this->item->langcode,
			"alias" => $this->item->alias,
			"author" => $this->item->author,
			"date" => $this->item->creationDate,
			"email" => $this->item->email,
			"url" => $this->item->url
			);
		$display_file = $this->filename;
		if ($this->item->filename) 
		{
			$target = FSJ_TM_Helper::makePackageFilename($data, $this->item->pubfolder, $this->item->filename);
			$target = pathinfo($target, PATHINFO_FILENAME) . "." . pathinfo($target, PATHINFO_EXTENSION);
			$display_file = $target;
		}	
		if (strtolower(substr($display_file, -4)) != ".zip") $display_file .= ".zip";
		header('Content-type: application/zip');
		header("Content-disposition: attachment; filename=\"".$display_file."\"");
		readfile($this->tmp_path.$this->zipfilename);
		JFile::delete($this->tmp_path.$this->zipfilename);
		exit;
	}
	public function pubfile()
	{
		$this->make_file();
		$data = array(
			"title" => $this->item->title,
			"ver" => $this->item->ver,
			"langcode" => $this->item->langcode,
			"alias" => $this->item->alias,
			"author" => $this->item->author,
			"date" => $this->item->creationDate,
			"email" => $this->item->email,
			"url" => $this->item->url
			);
		$target = FSJ_TM_Helper::makePackageFilename($data, $this->item->pubfolder, $this->item->filename);
		$targetpath = pathinfo($target, PATHINFO_DIRNAME);
		if (!file_exists($targetpath)) JFolder::create($targetpath);
		if (file_exists($target))
		{
			if (!JFile::delete($target)) JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], "Target file exists and unable to replace: " . $target);
		}
		if (!JFile::copy($this->tmp_path.$this->zipfilename, $target)) JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], "Cannot copy to target file: " . $target);
		if (!JFile::delete($this->tmp_path.$this->zipfilename)) JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], "Cannot delete temp file: " . $this->tmp_path.$this->zipfilename);
		JFactory::getApplication()->redirect($_SERVER['HTTP_REFERER'], "File published as " . $target);
		exit;
	}
	private function make_file()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__fsj_transman_package WHERE id = " . $db->escape(JRequest::getVar('id'));
		$db->setQuery($query);
		$item = $db->loadObject();
		$mainframe = JFactory::getApplication();
		$tmp_path = $mainframe->getCfg('tmp_path') . DS;
		$data = json_decode($item->files, true);
		$xml = array();
		$filename = strtolower(FSJ_TM_Helper::SanitizeFilename($item->title)) . "." . $item->langcode;
		$zipfilename = "fsj_trans_man.".$filename.".".mt_rand(100000,999999).".zip";
		$xml[] = "<" . "?xml version='1.0' encoding='UTF-8' ?" . ">";
		$xml[] = "<extension type='file' method='upgrade' version='2.5'>";
		$xml[] = "  <name>{$item->title} - {$item->langcode}</name>";
		$xml[] = "  <version>{$item->ver}</version>";
		$xml[] = "  <creationDate>{$item->creationDate}</creationDate>";
		$xml[] = "  <author>{$item->author}</author>";
		$xml[] = "  <authorEmail>{$item->email}</authorEmail>";
		$xml[] = "  <authorUrl>{$item->url}</authorUrl>";
		$xml[] = "  <copyright>{$item->copyright}</copyright>";
		$xml[] = "  <license>{$item->license}</license>";
		$xml[] = "  <description>";
		$xml[] = "    <![CDATA[{$item->description}]]>";
		$xml[] = "  </description>";
		$xml[] = "  <fileset>";
		$manifest_xml = str_replace('files_', '', JFilterInput::getInstance()->clean("{$item->title} - {$item->langcode}", 'cmd'));
		//print_p($data);
		foreach ($data as $folder => $cats)
		{
			list($client_id, $component) = explode("|", $folder);
			$path = FSJ_TM_Helper::getPath($client_id, $item->langcode, $component);
			$current_path = $path;
			$current_path = str_replace(JPATH_ROOT, "", $current_path);
			$current_path = trim($current_path, "/\\");
			$folder = $this->makeZipFolder($folder);
			$xml[] = "    <files folder='$folder' target='{$current_path}'>";
			foreach($cats as $cat => $files)
			{
				$xml[] = "      <!-- $cat -->";
				foreach ($files as $file)
				{
					$source_file = $path . $item->langcode.".".$file;
					if (file_exists($source_file))
					{
						$xml[] = "      <filename>{$item->langcode}.$file</filename>";
					}
				}	
			}
			$xml[] = "    </files>";
		}
		$xml[] = "  </fileset>";
		if ($item->updateserver)
		{
			$xml[] = "  <updateservers>";
			$xml[] = "    <server type='collection' priority='1' name='{$item->title}'><![CDATA[{$item->updateserver}]]></server>";
			$xml[] = "  </updateservers>";
		}
		$xml[] = "</extension>";
		/*echo "<pre>";
		echo htmlentities(implode("\n",$xml));
		echo "</pre>";
		exit;*/
		$zip = new ZipArchive;
		if (!$zip->open($tmp_path.$zipfilename, ZIPARCHIVE::CREATE)) 
		{ 
			echo "cannot open <$tmp_path$zipfilename>\n";
			exit; 
		}
		$zip->addFromString($manifest_xml . ".xml",  implode("\n",$xml));  
		foreach ($data as $folder => $cats)
		{
			list($client_id, $component) = explode("|", $folder);
			$path = FSJ_TM_Helper::getPath($client_id, $item->langcode, $component);
			$folder = $this->makeZipFolder($folder);
			foreach($cats as $cat => $files)
			{	
				foreach ($files as $file)
				{
					$source_file = $path . $item->langcode.".".$file;
					if (file_exists($source_file))
					{
						$zip->addFile($path . $item->langcode.".".$file, $folder."/".$item->langcode.".".$file);
					}/* else {
						$zip->addFromString($folder."/".$item->langcode.".".$file,  "");  
					}*/
				}
			}
		}
		$zip->close();
		$this->tmp_path = $tmp_path;
		$this->zipfilename = $zipfilename;
		$this->filename = $filename;
		$this->item = $item;
	}
	private function makeZipFolder($in)
	{
		if ($in == "1|g.general") return "admin";
		if ($in == "0|g.general") return "site";
		$in = str_replace("|", ".", $in);
		list ($location, $type, $name) = explode(".", $in);	
		if ($type == "c" && substr($name,0,4) != "com_") $name = "com_" . $name;
		if ($type == "m" && substr($name,0,4) != "mod_") $name = "mod_" . $name;
		if ($type == "t" && substr($name,0,4) != "tpl_") $name = "tpl_" . $name;
		if ($type == "p" && substr($name,0,4) != "plg_") $name = "plg_" . $name;
		return $name;
	}
//
}
