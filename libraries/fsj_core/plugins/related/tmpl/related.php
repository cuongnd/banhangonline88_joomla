<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="fsj_related_item">
	<a href='<?php echo $link; ?>' title='<?php echo JText::_($plugin->params['name']);?>'>
		<img src='<?php echo $image; ?>'>
		<span><?php echo $item->title; ?></span>
	</a>
</div>