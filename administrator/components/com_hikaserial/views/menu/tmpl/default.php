<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!HIKASHOP_BACK_RESPONSIVE) {
?><div style="line-height:normal">
<span id="hikaserial_menu_title"><?php echo $this->title;?></span>
<div id="hikaserial_menu"<?php if($this->menu_style == 'content_top'){ echo 'class="hikaserial_menu_top"';} ?>>
	<ul class="menu">
<?php
} else {
?><div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<div class="nav">
				<ul id="hikaserial_menu_j3" class="nav">
<?php
}
$config = hikaserial::config();
foreach($this->menus as $menu) {
	$html = '';
	if(!empty($menu['children'])) {
		$i = 1; $cpt = count($menu['children']);
		foreach($menu['children'] as $child) {
			$task = 'view';
			if(!empty($child['task']))
				$task = $child['task'];
			if(empty($child['acl']) || hikaserial::isAllowed($config->get('acl_'.$child['acl'].'_'.$task, 'all'))) {
				$liclasses = '';
				$classes = '';
				if(isset($child['active']) && $child['active']) {
					$classes .= ' sel';
					$liclasses .= ' sel';
				}

				$icon = '';
				if(!empty($child['icon'])) {
					if(!HIKASHOP_BACK_RESPONSIVE) {
						$classes .= ' '.$child['icon'];
					} else {
						$icon = '<i class="'.$child['icon'].'"></i> ';
					}
				}
				if(!isset($child['options']))
					$child['options'] = '';
				if($i++ == $cpt) $classes .= ' last';
				$html .= '<li class="l2'.$liclasses.'"><a class="'.$classes.'" href="'.$child['url'].'" '.$child['options'].'>'.$icon.$child['name'].'</a></li>'."\r\n";
			}
		}
		if(!empty($html)) {
			if(!HIKASHOP_BACK_RESPONSIVE) {
				$html = '<ul>'."\r\n".$html.'</ul>';
			} else {
				$html = '<ul class="dropdown-menu">'."\r\n".$html.'</ul>';
			}
		}
	}

	$task = 'view';
	if(!empty($menu['task']))
		$task = $menu['task'];
	if(!empty($menu['acl']) && !hikaserial::isAllowed($config->get('acl_'.$menu['acl'].'_'.$task, 'all'))) {
		if(empty($html)) {
			continue;
		}
		$menu['url'] = '#';
	}

	$liclasses = '';
	$classes = '';
	if(isset($menu['active']) && $menu['active']) {
		$classes .= ' sel';
		$liclasses .= ' sel';
	}
	$icon = '';
	if(!empty($menu['icon'])) {
		if(!HIKASHOP_BACK_RESPONSIVE) {
			$classes .= ' '.$menu['icon'];
		} else {
			$icon = '<i class="'.$menu['icon'].'"></i> ';
		}
	}
	$caret = '';
	if(!empty($html)) {
		if(!HIKASHOP_BACK_RESPONSIVE) {
			$liclasses .= ' parentmenu';
		} else {
			$caret = '<span class="caret"></span>';
			$menu['url'] = '#';
		}
	}
	if(!isset($menu['options']))
		$menu['options'] = '';

	if(!HIKASHOP_BACK_RESPONSIVE) {
		echo '<li class="l1'.$liclasses.'"><a class="e1'.$classes.'" href="'.$menu['url'].'" '.$menu['options'].'>'.$menu['name'].'</a>'.$html.'</li>';
	} else {
		echo '<li class="dropdown'.$liclasses.'"><a class="dropdown-toggle'.$classes.'" data-toggle="dropdown" href="'.$menu['url'].'" '.$menu['options'].'>'.$icon.$menu['name'].$caret.'</a>'.$html.'</li>';
	}
}
unset($html);

if(!HIKASHOP_BACK_RESPONSIVE) {
?>
	</ul>
</div>
<style type="text/css">
<!--
div#submenu-box { display: none; }
// -->
</style>
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
