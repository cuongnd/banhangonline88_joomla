<?php
/*
* Pixel Point Creative - Cinch Menu Module
* License: GNU General Public License version
* See: http://www.gnu.org/copyleft/gpl.html
* Copyright (c) Pixel Point Creative LLC.
* More info at http://www.pixelpointcreative.com
* Last Updated: 5/13/14
* Additional improvements by Paul @ Protopia.co.uk
*/
// No direct access.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');
JHtml::stylesheet('modules/'.$module->module.'/tmpl/css/flyout.css');

// Load custom.css if it exists
$file = 'modules/'.$module->module.'/tmpl/css/custom.css';
if (is_file($file))
{
	JHtml::stylesheet($file);
}

$document =& JFactory::getDocument();
include "css" . DS . "styles-flyout.php";
$document->addStyleDeclaration( $style );
include "js" . DS . "flyout.js.php";

$direction = $params->get('stype_layout') == "vertical" ? "vertical" : "horizontal";

if (isset($menus) && count($menus)){
	$id = "flyout_menu_$module->id";
	$class = "flyout-menu {$direction} flyout-{$menu_direction} text-{$textAlign} button-{$bulletAlign}";
?>
<!--[if lte IE 6]><ul class="<?php echo $class;?> msie6" id="<?php echo $id;?>"><![endif]-->
<!--[if IE 7]><ul class="<?php echo $class;?> msie7" id="<?php echo $id;?>"><![endif]-->
<!--[if IE 8]><ul class="<?php echo $class;?> msie8" id="<?php echo $id;?>"><![endif]-->
<!--[if IE 9]><ul class="<?php echo $class;?> msie9" id="<?php echo $id;?>"><![endif]-->
<!--[if gt IE 9]><!--><ul class="<?php echo $class;?>" id="<?php echo $id;?>"><!--<![endif]-->
<?php
	$countUlOpened = 1;
	$level = 1;
	$menu = $app->getMenu();
	$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
	$path = $active->tree;

	for ($i = 0; $i < count($menus); $i++){
		$classes = array();
		if(in_array($menus[$i]->id, $path)){
			$classes[] = "current";
		}
		if(($i == 0) || ($i > 0 && $menus[$i-1]->level < $menus[$i]->level)){
			$classes[] = "first";
		}
		// Final menu item or next menu item is a higher-level
		if(($i == count($menus)-1) or ($i < count($menus)-1 && $menus[$i+1]->level < $menus[$i]->level)){
			$classes[] = "last";
		}
		// Next menuitem is a sub-menu - so skip forward until next non-sub-menu is same or higher level
		if($i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level){
			for($j = $i+1; $j < count($menus) && ($menus[$j]->level != $menus[$i]->level); $j++) {
				if($j == count($menus)-1 || $menus[$j]->level < $menus[$i]->level) {
					$classes[] = "last";
					break;
				}
			}
		}
			
		if ($menus[$i]->type == "separator") {
			$classes[] = "separator";
		}		
		$class = "";
		if(!empty($classes)){
			$class = " class='" . implode(' ',$classes) . "'";
		}
		
		$li = "	<li".$class.">\r\n";
		$li .= "		<div class='item-wrapper'>\r\n";
		if(($i == count($menus)-1) or ($i < count($menus)-1 && $menus[$i+1]->level < $menus[$i]->level)){
			$classes[] = "last";
		}
		if($showBullet == "true" && $i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level){
			$divMenuButton = "			<div class='menu-button'>";
			if($i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level){
				$divMenuButton .= "<img class='menuicon' alt='' src='".$bulletImage."'/>";
			}
			$divMenuButton .= "</div>\r\n";
			$li.=$divMenuButton;
		}
		$target = "";
		switch ($menus[$i]->browserNav) :
			case 1:
				$target=" target='_blank' ";
				break;
			case 2:
				$target = " onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;\"";
				break;
		endswitch;
		$icon_menu = ($menus[$i]->menu_image != '')?'<img src='.JURI::base(true).DS.$menus[$i]->menu_image.' alt="menu icon" />':'';
		$divLink = "			<div class='menu-link'><a".$target." href='".$menus[$i]->flink."'>".$icon_menu.$menus[$i]->title."</a></div>\r\n";
		$li.=$divLink;
		if($direction=='horizontal'){
			$li.="			<div style='clear:both;'></div>\r\n";
		}
		$li.= "		</div>\r\n";
		echo $li;

		if($i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level){
			echo "	<div class='ul-wrapper'><ul>\r\n";
			$countUlOpened++;
			$level++;
		}
		if($i < count($menus)-1 && $menus[$i+1]->level < $menus[$i]->level){
			echo "	</li>\r\n";
			for($j = 0; $j < $menus[$i]->level - $menus[$i+1]->level; $j++){
				echo "	</ul></div></li>\r\n";
				$countUlOpened--;
				$level--;
			}
		}
		if($i < count($menus)-1 && $menus[$i+1]->level == $menus[$i]->level){
			echo "	</li>\r\n";
		}
	}
	for ($i=0; $i < $countUlOpened - 1; $i++){
		echo "	</li></ul></div>\r\n";
	}
	if($countUlOpened > 1){
		echo "	</li>\r\n<div style='clear:both;' /></ul>\r\n";
	}
}
?>