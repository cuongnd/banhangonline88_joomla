<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport("joomla.filesystem.folder");

class JFormFieldfsjtmpickfiles extends JFormField
{
	static $js = false;
	protected function getInput()
	{
		/*$code = $this->form->getValue('langcode');
		if ($code == "")
		{
			return JText::_("FSJ_TM_PICK_LANG_FIRST");	
		}
		
		$this->addJS();

		$client = $this->element['fsjtmpickfiles_client'];
	
		$files = explode(";", $this->value);
	
		$output = array();
		$output[] = "<div class='pf_files' id='pf_files_$client'>";
		$output[] = "<button class='btn btn-small add' onclick='tm_pf_add_file($client, \"" . $this->form->getValue('langcode') . "\");return false;'>".JText::_("FSJ_TM_ADD")."</button>";
		foreach ($files as $file)
		{
			$file = trim($file);
			if ($file == "")
				continue;
			$output[] = "<div class='pf_file' client='$client' file='$file'><span>$file</span>&nbsp;&nbsp;<button class='btn btn-mini' onclick='tm_pf_remove_file(\"$file\", $client); return false;'><i class='icon-delete'></i></button></div>";
		}
		$output[] = "</div>";
		$output[] = "<div style='clear: both;'></div>";
		$output[] = "<input type='hidden' name='" . $this->name . "' value='" . $this->value . "' id='" . $this->id . "'>";
		return implode($output);*/
		
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$this->addJS();
		$files = explode(";", $value);
		
		foreach ($files as $offset => $file)
		{
			if (trim($file) == "")
			{
				unset($files[$offset]);
				continue;	
			}
			
			//echo "File : $file<br>";
			$parts = explode(".", $file);
			if (count($parts > 2))
			{
				$parts = array_slice($parts, 1, -1);
			}	
			$files[$offset] = implode(".", $parts);
			
			//echo "New File : {$files[$offset]}<br/><br/>";
		}
		
		if (count($files) > 3)
		{
			$list = array_slice($files, 0, 3);
			$rest = array_slice($files, 3);

			$output[] = implode("<br />", $list);		
			$output[] = "<div class='list_files'>";
			$output[] = "<a href='#' onclick='return false;'>and " . count($rest) . " others</a>";
			$output[] = "<div class='files_popup'>";
			$output[] = implode("<br />", $rest);
			$output[] = "</div>";
			$output[] = "</div>";
			
			return implode($output);
		} else {
			return implode("<br />", $files);
		}
	}
	
	function addJS()
	{
		if (!self::$js)
		{
			$document = JFactory::getDocument();
			$document->addScript( JURI::root().'administrator/components/com_fsj_transman/assets/js/pickfiles.js' );
			if (!FSJ_Helper::IsJ3())
			{
				FSJ_Page::Style("libraries/fsj_core/assets/css/bootstrap/bootstrap_fsjonly.less");
				FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.j25.less");
			}
			
			self::$js = true;	
			
			echo "<div id='add_file_url' style='display: none'>" . JRoute::_('index.php?option=com_fsj_transman&view=pickfiles&client=XXCLIENTXX&tag=XXTAGXX&tmpl=component&existing=XXCURRENTXX') . "</div>";
			echo "<style>div.tinner {padding: 2px;}</style>";
		}
	}
}
