<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

$data = $this->left;
if(!empty($this->right)) {
	$data[] = '#RIGHT#';
	$data = array_merge($data, $this->right);
}

?>
<div class="hikam_toolbar">
	<div class="hikam_toolbar_btn hikam_btn_32">
<?php

foreach($data as $key=> $tool) {
	if($tool === '#RIGHT#') {
		echo '<div class="hikam_toolbar_right">';
		continue;
	}

	if(empty($tool['url']) && !empty($tool['sep'])) {
		echo '<div class="sep"></div>';
		continue;
	}

	echo '<div class="btn '.$tool['wrapper_class'].'">';

	$content = '';
	if(!empty($tool['icon'])) { $content .= '<span class="btnIcon iconM-32-'.$tool['icon'].'"></span>'; }
	if(!empty($tool['name'])) { $content .= '<span class="btnName">' . $tool['name'] . '</span>'; }

	if(!empty($tool['url'])) {
		if(empty($tool['popup'])) {
			if(empty($tool['linkattribs']))
				echo '<a href="'.$tool['url'].'">';
			else
				echo '<a href="'.$tool['url'].'" '.$tool['linkattribs'].'>';
			echo $content . '</a>';
		} else {
			echo $this->popup->display(
				$content,
				@$tool['name'],
				$tool['url'],
				$tool['popup']['id'],
				$tool['popup']['width'],
				$tool['popup']['height'],
				@$tool['linkattribs'], '', 'link'
			);
		}
	} else {
		echo $content;
	}
	unset($content);

	echo '</div>';
}
if(!empty($this->right))
	echo '</div>';

?>
		<div style="clear:both"></div>
	</div>
</div>
