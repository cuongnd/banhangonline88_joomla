<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

	<?php $thumb_url = JRoute::_('index.php?option=com_fsj_main&controller=attach&task=attach.thumb&attachid=' . $file->id, false); ?>
	<?php $file_url = JRoute::_('index.php?option=com_fsj_main&controller=attach&task=attach.download&attachid=' . $file->id, false); ?>
	<tr class="template-download" id="fsj_attach_<?php echo $file->id; ?>" style='cursor: move'>
		<td width='48'>
			<span class="preview">
				<a href='<?php echo $file_url; ?>'>
					<img src="<?php echo $thumb_url; ?>" width='48' height='48'>
				</a>
			</span>
		</td>
		<td>
			<div>
				<input type='text' name='filetitle[]' value='<?php echo $file->params->title; ?>' class='input-xxlarge'>
				<input type='hidden' name='fileid[]' value='<?php echo $file->id; ?>'>
				<input type='hidden' name='fileorder[]' class='order' value=''>
			</div>
			<div style='padding: 3px 6px'>
				<span class="name">
					<a href='<?php echo $file_url; ?>'>
						<?php echo $file->params->filename; ?>
					</a>
				</span>, 
				<span class='size'><?php echo FSJ_Format::Size($file->params->size); ?></span>
			</div>
		</td>
		<td colspan='2' style='text-align: right'>
				<button class="btn" onclick='fsj_attach_remove(<?php echo $file->id; ?>);return false;'>
					&times;
				</button>
		</td>
	</tr>