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
JHtml::stylesheet('modules/'.$module->module.'/tmpl/css/accordion.css');

// Load custom.css if it exists
$file = 'modules/'.$module->module.'/tmpl/css/custom.css';
if (is_file($file))
{
	JHtml::stylesheet($file);
}

$document =& JFactory::getDocument();
include  "css" . DS . 'styles-accordion.php';
$document->addStyleDeclaration( $style );
include "js" . DS . "accordion.js.php";

if(isset($menus) && count($menus)){
?>
	<ul class='accordion-menu text-<?php echo $textAlign;?> button-<?php echo $bulletAlign;?>' id='accordion_menu_<?php echo $module->id;?>'>
<?php
	$countUlOpened = 1;
	$level = 1;
	$menu = $app->getMenu();
	$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
	$path = $active->tree;

	for($i = 0; $i < count($menus); $i++){
		if($i == 0){$countUlOpened++;
		}
		$classes = array();
		if(in_array($menus[$i]->id, $path)){
			$classes[] = "current";
			$classes[] = "opened";
		}
		if (($i == 0) || ($i > 0 && $menus[$i-1]->level < $menus[$i]->level)) {
			$classes[] = "first";
		}
		// Final menu item or next menu item is a higher-level
		if (($i == count($menus)-1) or ($i < count($menus)-1 && $menus[$i+1]->level < $menus[$i]->level)) {
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
		if (!empty($classes)) {
			$class = " class='" . implode(' ',$classes) . "'";
		}

		$li = "	<li".$class.">\r\n";
		$li .= "		<div class='item-wrapper'>\r\n";
		if($showBullet == "true"){
			$divMenuButton = "			<div class='menu-button'>";
			if($i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level){
				$divMenuButton.="<img class='menuicon' alt='' src='".$bulletImage."'/>";
			}
			$divMenuButton .= "</div>\r\n";
			$li.=$divMenuButton;
		}
		$target = "";
		switch ($menus[$i]->browserNav) :
			case 1:
				$target=" target='_blank'";
				break;
			case 2:
				$target = " on-click=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;\"";
				break;
		endswitch;
		$icon_menu = ($menus[$i]->menu_image != '')?'<img src='.JURI::base(true).DS.$menus[$i]->menu_image.' alt="menu icon" />':'';
		$divLink = "			<div class='menu-link'><a".$target." href='".$menus[$i]->flink."'>".$icon_menu.$menus[$i]->title."</a></div>\r\n";
		$li.=$divLink;
		$li.= "		</div>\r\n";
		echo $li;

		if ($i < count($menus)-1 && $menus[$i+1]->level > $menus[$i]->level) {
			echo "	<div class='ul-wrapper'><ul>\r\n";
			$countUlOpened++;
			$level++;
		}
		if ($i < count($menus)-1 && $menus[$i+1]->level < $menus[$i]->level) {
			echo "		</li>\r\n";
			for($j = 0; $j < $menus[$i]->level - $menus[$i+1]->level; $j++) {
				echo "	</ul></div></li>\r\n";
				$countUlOpened--;
				$level--;
			}
		}
		if ($i < count($menus)-1 && $menus[$i+1]->level == $menus[$i]->level) {
			echo "	</li>\r\n";
		}
	}
	for ($i=0; $i < $countUlOpened - 1; $i++){
		echo "	</li></ul>\r\n";
	}
	if ($countUlOpened > 1) {
		echo "	</li></ul>\r\n";
	}
}
?>