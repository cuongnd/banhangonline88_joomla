<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.plugins.attachment.attachment');
jimport( 'fsj_core.lib.fields.attach_handler');
jimport( 'fsj_core.lib.utils.format');

class JFormFieldFSJAttach extends JFormField
{
	var $handlepost = 1;

	protected $type = 'FSJAttach';

	protected function getInput()
	{
		FSJ_Page::Style('libraries/fsj_core/assets/css/fileupload/jquery.fileupload.css');
		FSJ_Page::Style('libraries/fsj_core/assets/css/fileupload/jquery.fileupload-ui.css');
		FSJ_Page::Style('libraries/fsj_core/assets/css/fileupload/blueimp-gallery.min.css');
		
		FSJ_Page::JQueryUI(array('sortable'));
		
		//FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.ui.widget.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/tmpl.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/load-image.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.blueimp-gallery.min.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/canvas-to-blob.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.iframe-transport.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.fileupload.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.fileupload-process.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.fileupload-image.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.fileupload-validate.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/jquery.fileupload-ui.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fileupload/fileupload.js');

		$post_url = JRoute::_('index.php?option=com_fsj_main&controller=attach&task=attach.process',false);
		$max_size = FSJ_Helper::getMaximumFileUploadSize();
		$file_types = '*';
		
		$main_js = "
jQuery(document).ready(function () {
	jQuery('input[name=\"task\"]').val('attach.process');
    jQuery('#item-form').fileupload({
        url: '$post_url'
    });

    jQuery('#item-form').fileupload('option', {
        maxFileSize: $max_size,
        autoUpload: true
    });
});	
		";
		
		FSJ_Page::ScriptDec($main_js);
		
		// load in any existing attachments and display them!
		
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_main_attachment WHERE source = '" . $db->escape($this->element['fsjattach_attach']) . "' AND source_id = " . (int)$this->form->getValue('id') . " ORDER BY ordering";
		$db->setQuery($qry);
		
		$files = $db->loadObjectList();

		include JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'plugins'.DS.'attachment'.DS.'tmpl_edit'.DS.'attachment.form.php';
	}
	
	function doAfterSave($field, &$data)
	{
		$db = JFactory::getDBO();
		
		$delete = JRequest::getVar('files_delete');
		$delete = explode("|", $delete);
		
		foreach ($delete as $fileid)
		{
			$fileid = (int)$fileid;
			if ($fileid < 1) continue;		
			
			$qry = "SELECT * FROM #__fsj_main_attachment WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$item = $db->loadObject();
			$item->params = json_decode($item->params);

			$thumbfile = JPATH_ROOT.'/fsj_files/' . $item->source . "/thumbnail/" . $item->params->diskfile;		
			$file = JPATH_ROOT.'/fsj_files/' . $item->source . "/" . $item->params->diskfile;		
		
			if (file_exists($thumbfile))
				@unlink($thumbfile);
		
			if (file_exists($file))
				@unlink($file);
			
			$qry = "DELETE FROM #__fsj_main_attachment WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$db->Query();
		}

		// process any existing files
		$fileids = JRequest::getVar('fileid');
		$filetitles = JRequest::getVar('filetitle');
		$fileorders = JRequest::getVar('fileorder');
		
		foreach ($fileids as $offset => $fileid)
		{
			$newtitle = $filetitles[$offset];
			$neworder = $fileorders[$offset];
			
			$qry = "SELECT * FROM #__fsj_main_attachment WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$item = $db->loadObject();
			$params = json_decode($item->params);
			$params->title = $newtitle;
			
			$qry = "UPDATE #__fsj_main_attachment SET ordering = " . (int)$neworder . ", params = '" . $db->escape(json_encode($params)) . "' WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$db->Query();			
		}
		
		// load in new attachments if any	
		$filenames = JRequest::getVar('new_filename');
		$filetitles = JRequest::getVar('new_filetitle');
		$fileorders = JRequest::getVar('new_fileorder');
		
		$handler = new FSJ_Attach_Handler(null, false);
			
		$dest_path = JPATH_ROOT.'/fsj_files/' . $this->fsjattach->attach . "/";
		if (!file_exists($dest_path))
			mkdir($dest_path, 0755, true);
		if (!file_exists($dest_path))
		{
			// TODO : Show message about not being able to create the dest folder for the files!
			return;	
		}	
		
		$dest_thumb_path = $dest_path . "thumbnail/";
		if (!file_exists($dest_thumb_path))
			mkdir($dest_thumb_path, 0755, true);
		
		
		foreach($filenames as $offset => $filename)
		{
			$title = $filetitles[$offset];
			$order = $fileorders[$offset];
			
			// need to move the filename from incoming to the relevant folder		
			
			$source = JPATH_ROOT.'/tmp/fsj/incoming/' . $filename;
			$thumbnail = JPATH_ROOT.'/tmp/fsj/incoming/thumbnail/' . $filename;
			
			$dest_filename = pathinfo($filename, PATHINFO_FILENAME) . "-" . substr(md5($filename . time() . mt_rand(0,999999)),0,8) . "." . pathinfo($filename, PATHINFO_EXTENSION);
			
			$dest = $dest_path . $dest_filename;
			$dest_thumb = $dest_thumb_path . $dest_filename;
			
			$obj = new stdClass();
			$obj->filename = $filename;
			$obj->size = filesize($source);
			$obj->uploaded = time();
			$obj->user = JFactory::getUser()->id;
			$obj->title = $title;
			$obj->diskfile = $dest_filename;
			
			rename($source, $dest);
			rename($thumbnail, $dest_thumb);
			
			$qry = "INSERT INTO #__fsj_main_attachment (params, source, source_id, dest, dest_id, ordering) VALUES (";
			$qry .= "'" . $db->escape(json_encode($obj)) . "', ";
			$qry .= "'" . $db->escape($this->fsjattach->attach) . "', ";
			$qry .= "'" . $db->escape($data->id) . "', ";
			$qry .= "'" . $db->escape('upload') . "', ";
			$qry .= "'" . $db->escape(0) . "', ";
			$qry .= "'" . $db->escape($order) . "')";
			
			$db->setQuery($qry);
			$db->Query();
		}
	}

	function doAfterDelete($field, $pk)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_main_attachment WHERE source = '" . $db->escape($this->fsjattach->attach) . "' AND source_id = " . (int)$pk;
		$db->setQuery($qry);
		
		$items = $db->loadObjectList();
		
		foreach ($items as $item)
		{
			$fileid = $item->id;
			
			$qry = "SELECT * FROM #__fsj_main_attachment WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$item = $db->loadObject();
			$item->params = json_decode($item->params);

			$thumbfile = JPATH_ROOT.'/fsj_files/' . $item->source . "/thumbnail/" . $item->params->diskfile;		
			$file = JPATH_ROOT.'/fsj_files/' . $item->source . "/" . $item->params->diskfile;		
			
			if (file_exists($thumbfile))
			@unlink($thumbfile);
			
			if (file_exists($file))
			@unlink($file);
			
			$qry = "DELETE FROM #__fsj_main_attachment WHERE id = " . (int)$fileid;
			$db->setQuery($qry);
			$db->Query();
		}
	}
}
