<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="item" id="relateddiv_<?php echo $item->pluginid; ?>_<?php echo $item->dest_id; ?>">
		<div class="handle btn_back"></div>
		<div class="fsj_related_remove btn_back icon_right" id='relatedrem_<?php echo $item->pluginid; ?>_<?php echo $item->dest_id; ?>'>
			<img src='<?php echo JURI::root() ?>libraries/fsj_core/assets/images/general/close_b-16.png'>
		</div>
		<img src='<?php echo JURI::root() ?>images/<?php echo $plugin->params->settings->plugin->image; ?>' width='16' height='16'>
		<span><?php echo $item->title; ?></span>
		<textarea style="display:none;" name='<?php echo $this->id; ?>_params_<?php echo $item->pluginid; ?>_<?php echo $item->dest_id; ?>'><?php echo FSJ_Helper::MakeINIParams($item->params); ?></textarea>
</div>
