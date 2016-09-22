<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJImage extends JFormField
{
	var $handlepost = 1;
	
	protected $type = 'FSJImage';

	protected function getInput()
	{
		static $img_pick_js = false;
		
		if (!$img_pick_js)
		{
			FSJ_Page::Script('libraries/fsj_core/assets/js/field/field.pickimage.js');
			FSJ_Page::Script('libraries/fsj_core/assets/js/fsj/fsj.utils.js');
			//FSJ_Page::Style('libraries/fsj_core/assets/css/misc/misc.pickimage.css' );
			
			$js[] = "var img_pick_sitebase = '" . JURI::root(true) . "/';";
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $js));
		}	

		//print_p($this->form);
		$name = (string)$this->element['name'];

		//print_p($this);

		$value = $this->value;
		$path = (string)$this->element['fsjimage_folder'];
		if (!$path)
			$path = (string)$this->element->fsjimage['folder'];
		
		$current = $value ? $value : "None";
		$id='image';

		$imageurl = JURI::root() . "images/" . $value;

		$popupurl = JRoute::_("index.php?option=com_fsj_main&task=pickimage.display&tmpl=component&type=specific&spath=" . $path);

		if (!$value)
		{
			$imageurl = JURI::root() . 'libraries/fsj_core/assets/images/misc/pick_image/no_image-64.png';
		}

		$xid = str_replace("]","_",str_replace("[","_",$this->name));
		$xid = trim($xid, "_");

		$out = '<div class="fsj_pickimage_form">
					<div id="img_'.$xid.'" class="fsj_pickimage_form_image_cont" style="float:left">
						<a title="'.$value.'" class="fsj_img_popup" href="'.$imageurl.'" id="img_'.$xid.'_link">
							<div style="border: 1px solid #ccc;padding: 3px;margin-right: 8px;">
								<div id="img_'.$xid.'_preview" style="width: 64px;height: 64px;background-image: url(\''.$imageurl.'\');background-position: center; 
									background-size: contain;background-repeat: no-repeat;">
								</div>
							</div>
						</a>
					</div>			

					<div class="fsj_pickimage_form_controls_cont" style="margin-left: 70px;">
						<input type="hidden" name="'.$this->name.'" id="'.$xid.'" value="'.$value.'">
						<div class="fsj_pickimage_form_info">
							<div class="fsj_curimg_title">' . JText::_('FSJ_PICK_IMAGE_CURRENT_IMAGE') . '</div>
							<div class="fsj_curimg_name" id="img_'.$xid.'_name">'.str_replace("/"," / ",$current).'</div>
						</div>
						<div class="fsj_pickimage_form_button">
							<a class="btn img_pick_button" href="'.$popupurl.'" id="imgpickbtn|'.$xid.'" path="'.$path.'">
								' . JText::_('FSJ_PICK_IMAGE_CHOOSE_IMAGE') . '						
							</a>
						</div>
					</div>
				</div>';
				
		FSJ_Page::IncludeModal();
	
		return $out;
	}
	
	function AdminDisplay($value, $name, $item)
	{
		static $init = false;
		if (!$init)
		{
			$document = JFactory::getDocument();
			
			$js = array();
			$js[] = "jQuery(document).ready(function () {";
			$js[] = "    jQuery('.fsj_img_popup').click(function (ev) {";
			$js[] = "		ev.preventDefault();";
			$js[] = "		var url = jQuery(this).attr('href');";
			$js[] = "		TINY.box.show({image:url});";
			$js[] = "	});";
			$js[] = "});";

			$document->addScriptDeclaration(implode("\n", $js));
			$init = true;
		}
		
		$imagefile = JURI::root() . "images/fsj/" . $value;
		$path = JPATH_SITE.DS.'images'.DS.'fsj'.DS.$value;
		$path = str_replace("/",DS,$path);
		if ($item->image && file_exists($path)) 
		{
			$size = getimagesize($path);
			$width = $size[0];
			$height = $size[1];
			
			$dest_width = 32;
			$dest_height = 32; 

			if ($width > $height)
			{
				$dest_height = floor($dest_height / ($width/$height));
			} else {
				$dest_width = floor($dest_width * ($width/$height));
			}
			
			$html = '<a title="'. $value .'" class="fsj_img_popup" href="'. $imagefile.'">';
			$html .= '<img src="'. $imagefile.'" width="'. $dest_width.'" height="'. $dest_height.'">';
			$html .= '</a>';
		} else {
			$html = JText::_('FSJ_NO_IMAGE');
		}
		
		return $html;
	}
	
	function Process()
	{
		jimport("fsj_core.lib.fields.pickimage");
		$pickimage = new FSJ_PickImage();
		return $pickimage->Process();
	}
}
