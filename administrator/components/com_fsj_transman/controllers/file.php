<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'trans_helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'general_helper.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');

class fsj_transmanControllerfile extends JControllerLegacy
{
	function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_fsj_transman&view=files', false));
	}
	
	function save()
	{
		return $this->apply();	
	}
	
	function publish()
	{
		return $this->apply();	
	}
	
	function publishclose()
	{
		return $this->apply();	
	}
	
	function apply()
	{
		$task = JRequest::getVar('task');
		
		$file = JRequest::getVar('file');
		$file = FSJ_TM_Helper::SanitizeFilename($file);
		
		list($this->client, $this->file, $this->tag, $this->component) = FSJ_TM_Helper::ParseFileName($file);
		
		$this->basetag = FSJ_TM_Helper::GetBaseLanguage();
		
		$base_file = $this->file;
		$base_file = str_replace($this->tag, $this->basetag, $base_file);
		
		$this->strings = FSJ_TM_Trans_Helper::GetBaseFile($base_file, $this->client, $this->tag, $this->component);
		
		$strings = JRequest::getVar('strings', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$strings = json_decode($strings);
		
		
		$this->strings->LoadSaved($strings);
		
		$header = JRequest::getVar('header', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$this->strings->header_trans = explode("\n", $header);
		
		$path = FSJ_TM_Helper::getPath($this->client, $this->tag, $this->component);
		
		$target_file = $path . $this->tag . "." . $this->file;
		
		if (!file_exists($target_file))
		{
			$target_file = $path . "_unpub." . $this->tag . "." . $this->file;	
		}
		
		if (FSJ_Settings::get('tm_options', 'backup') && file_exists($target_file))
		{
			if (!file_exists($path . "backup"))
			{
				if (!JFolder::create($path . "backup"))
				{
					$this->json_error("Unable to create backup folder");
					return;	
				}
			}
				
			$backup = $path . "backup" . DS . $this->file;
			$timetag = date("Y-m-d-h-i-s", time());
			$backup = str_replace(".ini", "." . $timetag . ".ini", $backup);
				
			if (!JFile::copy($target_file, $backup))
			{
				$this->json_error("Unable to create backup file - '$target_file' to '$backup'");
				return;	
			}
		}
		
		$file = $this->strings->toFile();
		if (!JFile::write($target_file, $file))
		{
			$this->json_error("Unable to save file $target_file");	
			return;
		}
	
		/*echo "<pre>";
		echo $file;
		echo "</pre>";*/
		
		if ($task == "publish" || $task == "publishclose" || $task == "file.publish" || $task == "file.publishclose")
		{
			// publish file here!
			FSJ_TM_File_Helper::publish(JRequest::getVar('file'));
		}
		
	
		$data = new stdClass();
		$data->result = 'success';
		$data->error = $error;
		ob_clean();
		echo json_encode($data);
		exit;
		//$result = parent::save();
	}
	
	function json_error($error)
	{
		$data = new stdClass();
		$data->result = 'error';
		$data->error = $error;
		ob_clean();
		echo json_encode($data);
		exit;
	}
	
	function edit($key = null, $urlVar = null)
	{
		/*if (FSJ_Helper::IsJ3())
		{
			$cid   = $this->input->post->get('cid', array(), 'array');
			$record = (count($cid) ? $cid[0] : $this->input->get("id"));
		} else {*/
			$cid = JRequest::getVar('cid', array(), 'array');
			$record = (count($cid) ? $cid[0] : JRequest::getVar("id"));
		//}
		$this->setRedirect(JRoute::_('index.php?option=com_fsj_transman&view=file&file=' . $record, false));
		return true;
	}
	
	function category()
	{
		$offset = 0;
		$files = array();
		
		$cat = JRequest::getVar('category');

		$file = JRequest::getVar('file' . $offset);		
		while ($file)
		{
			$files[] = $file;
			$offset++;
			$file = JRequest::getVar('file' . $offset);
		}
		
		//print_p($files);
		
		foreach ($files as $file)
		{
			list($client_id, $element, $component, $filename) = explode("|", $file);
			$path = FSJ_TM_Helper::getPath($client_id, $element, $component);	
			//echo "Path : $path<br>";
			FSJ_TM_File_Helper::setCategory($path, $filename, $cat);
		}
		
		exit;
	}
	
	
	function download()
	{
		$file = JRequest::getVar('id');
		$file = FSJ_TM_Helper::SanitizeFilename($file);
		
		list($this->client, $this->file, $this->tag, $this->component) = FSJ_TM_Helper::ParseFileName($file);
		
		$path = FSJ_TM_Helper::getPath($this->client, $this->tag, $this->component);
		
		$target_file = $path . $this->tag . "." . $this->file;
		
		if (!file_exists($target_file))
		{
			$target_file = $path . "_unpub." . $this->tag . "." . $this->file;	
		}
		
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename='.basename($target_file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		readfile($target_file);
		
		exit;
		
	}
}
