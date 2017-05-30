<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset>
	<div class="toolbar" id="toolbar" style="float: right;">
		<button class="btn" type="button" onclick="hikamarket.submitform('addfile','hikamarket_form');"><img style="vertical-align:middle" src="<?php echo HIKASHOP_IMAGES; ?>save.png"/><?php echo JText::_('OK'); ?></button>
	</div>
</fieldset>
<form action="<?php echo hikamarket::completeLink('product&task=file'); ?>" method="post" name="hikamarket_form" id="hikamarket_form" enctype="multipart/form-data">
	<table width="100%">
		<tr>
			<td class="key">
				<label for="file_name"><?php echo JText::_('HIKA_NAME'); ?></label>
			</td>
			<td>
				<input type="text" name="data[file][file_name]" value="<?php echo $this->escape(@$this->element->file_name); ?>"/>
			</td>
		</tr>
		<tr>
<?php
	if(empty($this->element->file_path)) {
		if(hikamarket::acl('product/edit/files/upload')) {
?>
		<tr>
			<td class="key">
				<label for="files"><?php
					echo JText::_('HIKA_FILE_MODE');
				?></label>
			</td>
			<td><?php
				$values = array(
					JHTML::_('select.option', 'upload', JText::_('HIKA_FILE_MODE_UPLOAD')),
					JHTML::_('select.option', 'path', JText::_('HIKA_FILE_MODE_PATH'))
				);
				echo JHTML::_('hikaselect.genericlist', $values, "data[filemode]", 'class="inputbox" size="1" onchange="hikashop_switchmode(this);"', 'value', 'text', 'upload');
			?>
			<script type="text/javascript">
			function hikashop_switchmode(el) {
				var d = document, v = el.value, modes = ['upload','path'], e = null;
				for(var i = 0; i < modes.length; i++) {
					mode = modes[i];
					e = d.getElementById('hikashop_'+mode+'_section');
					if(!e) continue;
					if(v == mode) {
						e.style.display = '';
					} else {
						e.style.display = 'none';
					}
				}
			}
			</script>
			</td>
		</tr>
		<tr id="hikashop_path_section" style="display:none;">
			<td class="key">
				<label for="files"><?php
					echo JText::_('HIKA_PATH');
				?></label>
			</td>
			<td>
				<input type="text" name="data[file][file_path]" size="60" value=""/>
			</td>
		</tr>
		<tr id="hikashop_upload_section">
			<td class="key">
				<label for="files"><?php
					echo JText::_('HIKA_FILE');
				?></label>
			</td>
			<td>
				<input type="file" name="files[]" size="30" />
				<?php echo JText::sprintf('MAX_UPLOAD',(hikashop_bytes(ini_get('upload_max_filesize')) > hikashop_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize')); ?>
			</td>
		</tr>
<?php
		}
	} else {
?>
			<td class="key">
				<label for="files"><?php
					echo JText::_('FILENAME');
				?></label>
			</td>
			<td>
				<input type="text" name="data[file][file_path]" size="60" value="<?php echo $this->escape($this->element->file_path); ?>"/>
			</td>
<?php
	}
?>
		</tr>
<?php if(hikamarket::acl('product/edit/files/limit')) { ?>
		<tr>
			<td class="key">
				<label for="file_limit"><?php
					echo JText::_('DOWNLOAD_NUMBER_LIMIT');
				?></label>
			</td>
			<td>
				<input type="text" name="data[file][file_limit]" value="<?php
					if(isset($this->element->file_limit)) {
						if($this->element->file_limit < 0)
							echo JText::_('UNLIMITED');
						else
							echo $this->element->file_limit;
					}
				?>"/><br/>
			</td>
		</tr>
		<tr>
			<td class="key"></td>
			<td>
				0: <?php echo JText::_('DEFAULT_PARAMS_FOR_PRODUCTS');?> (<?php echo $this->shopConfig->get('download_number_limit');?>)<br/>
				-1: <?php echo JText::_('UNLIMITED');?><br/>
			</td>
		</tr>
<?php } ?>
<?php if(hikamarket::acl('product/edit/files/free')) { ?>
		<tr>
			<td class="key">
				<label for="file_free_download"><?php
					echo JText::_('FREE_DOWNLOAD');
				?></label>
			</td>
			<td><?php
				if(empty($this->element))
					$this->element = new stdClass();
				if(!isset($this->element->file_free_download))
					$this->element->file_free_download = $this->config->get('upload_file_free_download', 0);
				echo JHTML::_('hikaselect.booleanlist', "data[file][file_free_download]" , '', $this->element->file_free_download);
			?></td>
		</tr>
<?php } ?>
<?php if(hikamarket::acl('product/edit/files/description')) { ?>
		<tr>
			<td class="key">
				<label for="file_description"><?php
					echo JText::_( 'HIKA_DESCRIPTION' );
				?></label>
			</td>
			<td>
				<textarea name="data[file][file_description]"><?php echo $this->escape(@$this->element->file_description); ?></textarea>
			</td>
		</tr>
<?php } ?>
	</table>
	<div class="clr"></div>
	<input type="hidden" name="data[file][file_type]" value="file" />
	<input type="hidden" name="data[file][file_ref_id]" value="<?php echo $this->product_id; ?>" />
	<input type="hidden" name="cid" value="<?php echo @$this->cid; ?>" />
	<input type="hidden" name="pid" value="<?php echo (int)$this->product_id; ?>" />
	<input type="hidden" name="id" value="<?php echo JRequest::getInt('id');?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="file" />
	<input type="hidden" name="ctrl" value="product" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
