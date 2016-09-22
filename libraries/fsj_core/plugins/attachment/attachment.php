<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'fsj_core.lib.fields.attach_handler');

class FSJ_Attachment
{
	static function process()
	{
		$upload_handler = new FSJ_Attach_Handler();
		exit;
	}	
	
	static function thumb()
	{
		$item = self::getFromRequest();		
		$thumbfile = JPATH_ROOT.'/fsj_files/' . $item->source . "/thumbnail/" . $item->params->diskfile;		
		$handler = new FSJ_Attach_Handler(null, false);

		if (!file_exists($thumbfile))
		{
			// need to output a generic file icon
			$thumbfile = JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'assets'.DS.'images'.DS.'misc'.DS.'blank_16.png';
			$item->params->filename = 'blank_16.png';
		}
		
		header('Content-Type: '.$handler->get_file_type($item->params->filename, 'application/octet-stream'));
		header('Last-Modified: '.gmdate('D, d M Y H:i:s T', $item->params->uploaded));
		readfile($thumbfile);	
		exit;	
	}
	
	static function download()
	{
		$item = self::getFromRequest();		
		$file = JPATH_ROOT.'/fsj_files/' . $item->source . "/" . $item->params->diskfile;		
		$handler = new FSJ_Attach_Handler(null, false);
		
		header('X-Content-Type-Options: nosniff');
		header('Content-Type: '.$handler->get_file_type($item->params->filename, 'application/octet-stream'));
		header('Content-Disposition: attachment; filename="'.$item->params->filename.'"');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s T', $item->params->uploaded));
		readfile($file);	
		exit;	
	}
	
	static function getFromRequest()
	{
		$id = JRequest::getCmd('attachid');
		$db = JFactory::getDBO();	
		
		$qry = "SELECT * FROM #__fsj_main_attachment WHERE id = " . (int)$id;
		$db->setQuery($qry);
		
		$item = $db->loadObject();
		if (!$item)
			return null;
		
		$item->params = json_decode($item->params);
		
		return $item;	
	}
	
	static function loadAttach($items, $set)
	{
		$db = JFactory::getDBO();

		$index = array();
		$ids = array();
		
		foreach ($items as $item)
		{
			$index[$item->id] = $item;
			$ids[] = $item->id;
		}			
			
		if (count($ids) < 1)
			return;
		
		$qry = "SELECT * FROM #__fsj_main_attachment WHERE source = '" . $db->escape($set) . "' AND source_id IN (" . implode(", ", $ids) . ") ORDER BY ordering";
		$db->setQuery($qry);
		$files = $db->loadObjectList();
		
		foreach ($files as $file)
		{
			$item = $index[$file->source_id];	

			$params = json_decode($file->params);
			foreach ($params as $key => $value)
				$file->$key = $value;
				
			$file->url = JRoute::_("index.php?option=com_fsj_main&controller=attach&task=attach.download&attachid=" . $file->id,false);
				
			$item->attach[] = $file;
		}
	}
}

/*
class FSJ_Plugin_Type_Attachment extends FSJ_Linked_Edit
{
	var $id = "attachment";
	var $addtext = "FSJ_ATTACH_ADD_ATTACHED_FILE";
	var $addbtntext = 'FSJ_ATTACH_ADD_ATTACHMENT';
	
	function ShowForm($id)
	{
		echo "<div class='fsj fsj_linked fsj_{$this->id}'>";
		$values = array();
		if (array_key_exists($id,$this->data))
		{
			foreach ($this->data[$id] as &$item)
			{
				$values[] = "{$item->dest}={$item->dest_id}";	
			}
		} 
		
		$value = implode("&",$values);
		
		echo "<input type='hidden' id='fsj_{$this->id}_values' name='{$this->id}' value='$value' size=80>";
		
		$baseurl = "index.php?option=" . JRequest::getVar('option') . "&view=" . JRequest::getVar('view') . "&controller=" . JRequest::getVar('controller');
		$baseurl = "index.php?option=" . JRequest::getVar('option') . "&view=" . JRequest::getVar('view') . "&controller=" . JRequest::getVar('controller');
?>
<script>
var fsj_<?php echo $this->id; ?>_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.add&source=" . $this->source . "&source_id=" . $id, false); ?>';
var fsj_<?php echo $this->id; ?>_lookup_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.lookup", false); ?>';
var fsj_<?php echo $this->id; ?>_param_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.param", false); ?>';
</script>
<?php		
		static $file_upload_js = false;
		
		$fuhelper = new FSJ_Attach_Tools(FSJ_Plugin_Handler::GetPlugin('attachdest',$this->source));
		
		if (!$file_upload_js)
		{
			FSJ_Page::Script('libraries/fsj_core/assets/js/jquery/jquery.sortable.js');
			FSJ_Page::Script('libraries/fsj_core/assets/js/plugin/plugin.attachment.js');
			FSJ_Page::Script('libraries/fsj_core/assets/js/plugin/plugin.attachment.attach.js');
			FSJ_Page::Style('libraries/fsj_core/assets/css/plugin/plugin.attachment.less');
			
			$file_upload_js = true;
		}
		
		$out = '		
		<div id="file-uploader"></div>
		';
		
		$script = '<script>
		jQuery(document).ready(function () {  
			jQuery("#attachsubmit").attr("disabled",true);          
            var uploader = new qq.FileUploaderAttach({
                element: document.getElementById("file-uploader"),
                action: "' . JRoute::_('index.php?option=com_fsj_main&task=attachment.upload&plugin=' . $this->source, false) . '",
                template: \'<div class="uploader">\' + 
					\'<div class="upload-drop"><span>'.JText::_('FSJ_ATTACH_UPLOAD_DRAGDROP').'</span></div>\' +
					\'<div class="upload-button btn"><img title="'.JText::_('FSJ_ATTACH_UPLOAD_A_FILE').'" src="' . JURI::root(). 'libraries/fsj_core/assets/images/plugins/attachments/upload-16.png" width="16" height="16"><span>'.JText::_('FSJ_ATTACH_UPLOAD_A_FILE').'</span></div>\' +
					\'<div class="other-button btn"><img title="'.JText::_('FSJ_ATTACH_ADD_OTHER_FILE').'" src="' . JURI::root(). 'libraries/fsj_core/assets/images/plugins/attachments/add_other-16.png" width="16" height="16"><span>'.JText::_('FSJ_ATTACH_ADD_OTHER_FILE').'</span></div>\' +
					\'<div class="fsj_clear"></div>\' +
					\'<input id="max_file_id" type="hidden" name="max_file_id" value="0">\' +
					\'</div>\',
				failText: "' . JText::_('FSJ_ATTACH_UPLOAD_FAIL') . '",
				fileTemplate: \'<div class="item" id="attach_upload_{id}">\' +
					\'<div class="handle btn_back"></div>\' +
					\'<div class="cancel"><img title="'.JText::_('FSJ_ATTACH_UPLOAD_CANCEL').'" src="' . JURI::root(). 'libraries/fsj_core/assets/images/general/close_b-16.png" width="16" height="16" class="fsj_attachment_remove btn_back icon_topright"></div>\' +
					\'<div class="remove"><img title="'.JText::_('FSJ_ATTACH_UPLOAD_REMOVE').'" src="' . JURI::root(). 'libraries/fsj_core/assets/images/general/close_b-16.png" width="16" height="16" class="fsj_attachment_remove btn_back icon_topright" id="attachmentrem_upload_{id}"></div>\' +
					\'<div class="filesize"></div>\' +
					\'<div class="filename"><img src="' . JURI::root(). 'libraries/fsj_core/assets/images/plugins/attachments/file-16.png" width="16" height="16"></div>\' +
					\'<div class="progress progress-info progress-striped"><div class="progress-inner bar"></div></div>\' +
					\'<div class="fail"></div>\' +
					\'<div class="clear"></div>\' +
					\'<div class="form"><div class="title_label">File Title:</div><input name="attachment_title_upload_{id}" value="{filename}" size="60" /><textarea name="attachment_params_upload_{id}" style="display:none;"></textarea></div>\' +
					\'<div class="clear"></div>\' +
					\'</div>\',
				sizeLimit: '.$fuhelper->GetMaxSize().',
				listElement: jQuery(".fsj_file_upload_items")[0],
            });
		
			jQuery(".other-button").click(function (ev) {
				ev.preventDefault();
				var url = fsj_attachment_url;
				TINY.box.show({ iframe: url, width: 800, height: 50, animate: false, iframeresize: true });
			});
	
			fsj_attachment_remove_events();
		});
		</script>
		';
		echo $out . $script;
?>
	<div class="fsj_clear"></div>
	<div id="fsj_<?php echo $this->id; ?>_items" class='fsj_file_upload_items dragsort'>
<?php
		if (array_key_exists($id, $this->data))
		{
			foreach ($this->data[$id] as &$item)
			{
				$item->pluginid = $item->dest;
				//if (!$item->ok) continue;
				$plugin = $this->plugins[$item->pluginid];
				
				$this->decodeParams($plugin, $item);		
				$this->inludePluginEdit($plugin, $item);	
			}
		}
?>
	</div><div style="clear: both;"></div></div>
<?php
	}

}

class FSJ_Attach_Tools
{
	var $plugin;
	var $sizeLimit = 0;
	
	function FSJ_Attach_Tools(&$plugin = null)
	{
		$this->plugin = $plugin;
	}
	
	function GetMaxSize()
	{
		if (empty($this->sizeLimit))
		{
			//list($component,$size_setting) = explode("|",$this->plugin->params['maxsize'],2);

			$sizeLimit = 99999999999999;//FSJ_Attach_Tools::toBytes(FSJ_Settings::GetComponentSetting($component,$size_setting));
			$sizeLimit = min($sizeLimit, FSJ_Attach_Tools::toBytes(ini_get('post_max_size')));
			$sizeLimit = min($sizeLimit, FSJ_Attach_Tools::toBytes(ini_get('upload_max_filesize')));

			$this->sizeLimit = $sizeLimit;
		}
		return $this->sizeLimit;
	}
	
	function GetMaxSizeString()
	{
		$this->GetMaxSize();
		return $this->FormatSize($this->sizeLimit);
	}
	
	function GetFileTypesString()
	{
		$this->GetFileTypes();
		if (count($this->types) == 0)
			return JText::_('Any');
	}
	
	function GetFileTypes()
	{
		if (empty($this->types))
		{
			$this->types = array();	
		}
		return $this->types;
	}
	
	function toBytes($str){
		if ($str == "")
			return 99999999999;
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;        
		}
		return $val;
	}

	function GetPath()
	{
		return JPATH_SITE.'/files/temp/';
	}

	function FormatSize($size)
	{
		$sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		if ($size == 0) 
			return('n/a');
		return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
	}

	function HandleUpload()
	{
		$pluginid = $_GET['plugin'];

		$fuhelper = new FSJ_Attach_Tools(FSJ_Plugin_Handler::GetPlugin('attachdest',$pluginid));
		$sizeLimit = $fuhelper->GetMaxSize();
		$allowedExtensions = $fuhelper->GetFileTypes();
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploader->plugin = $pluginid;
		$result = $uploader->handleUpload($fuhelper->GetPath());
		// to pass data through iframe you will need to encode all html tags

		ob_clean();

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

		exit;		
	}
}
*/
