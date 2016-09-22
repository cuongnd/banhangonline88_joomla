<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!HIKAAUCTION_J30) {
?><div style="line-height:normal">
<span id="hikaauction_menu_title"><?php echo $this->title;?></span>
<div id="hikaauction_menu"<?php if($this->menu_style == 'content_top'){ echo 'class="hikaauction_menu_top"';} ?>>
	<ul class="menu">
<?php
} else {
?><div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<div class="nav">
				<ul id="hikaauction_menu_j3" class="nav">
<?php
}
foreach($this->menus as $menu) {
	$html = '';
	if(!empty($menu['children'])) {
		$i = 1;
		$last = count($menu['children']);
		foreach($menu['children'] as $child) {
			$task = 'view';
			if(!empty($child['task']))
				$task = $child['task'];

			$liclasses = '';
			$classes = '';
			if(isset($child['active']) && $child['active']) {
				$classes .= ' sel';
				$liclasses .= ' sel';
				$menu['active'] = true;
			}

			$icon = '';
			if(!empty($child['icon'])) {
				if(!HIKAAUCTION_J30) {
					$classes .= ' '.$child['icon'];
				} else {
					$icon = '<i class="'.$child['icon'].'"></i> ';
				}
			}
			if($i == $last)
				$classes .= ' last';
			if(!isset($child['options']))
				$child['options'] = '';
			if(!empty($child['url'])) {
				$html .= '<li class="l2'.$liclasses.'"><a class="'.$classes.'" href="'.$child['url'].'" '.$child['options'].'>'.$icon.$child['name'].'</a></li>'."\r\n";
			} else {
				$html .= '<li class="l2 divider sep'.$liclasses.'" '.$child['options'].'>'.$icon.$child['name'].'</li>'."\r\n";
			}
			$i++;
		}
		if(!empty($html)) {
			if(!HIKAAUCTION_J30) {
				$html = '<ul>'."\r\n".$html.'</ul>';
			} else {
				$html = '<ul class="dropdown-menu">'."\r\n".$html.'</ul>';
			}
		}
	}

	$task = 'view';
	if(!empty($menu['task']))
		$task = $menu['task'];

	$liclasses = '';
	$classes = '';
	if(isset($menu['active']) && $menu['active']) {
		$classes .= ' sel';
		$liclasses .= ' sel';
	}
	$icon = '';
	if(!empty($menu['icon'])) {
		if(!HIKAAUCTION_J30) {
			$classes .= ' '.$menu['icon'];
		} else {
			$icon = '<i class="'.$menu['icon'].'"></i> ';
		}
	}
	$caret = '';
	if(!empty($html)) {
		if(!HIKAAUCTION_J30) {
			$liclasses .= ' parentmenu';
		} else {
			$caret = '<span class="caret"></span>';
			$menu['url'] = '#';
		}
	}
	if(!isset($menu['options']))
		$menu['options'] = '';

	if(!HIKAAUCTION_J30) {
		echo '<li class="l1'.$liclasses.'"><a class="e1'.$classes.'" href="'.$menu['url'].'" '.$menu['options'].'>'.$menu['name'].'</a>'.$html.'</li>';
	} else {
		echo '<li class="dropdown'.$liclasses.'"><a class="dropdown-toggle'.$classes.'" data-toggle="dropdown" href="'.$menu['url'].'" '.$menu['options'].'>'.$icon.$menu['name'].$caret.'</a>'.$html.'</li>';
	}
}
unset($html);

if(!HIKAAUCTION_J30) {
?>
	</ul>
</div>
<style type="text/css">
<!--
div#submenu-box { display: none; }
// -->
</style>
<div style="clear:left"></div>
</div>
<?php
} else {
?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
}
