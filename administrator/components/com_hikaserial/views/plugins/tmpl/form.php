<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<div>
	<form action="index.php?option=<?php echo HIKASERIAL_COMPONENT; ?>&amp;ctrl=plugins" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php
	if(!empty($this->plugin->pluginView)) {
		$this->setLayout($this->plugin->pluginView);
		echo $this->loadTemplate();
	} else if(empty($this->plugin->noForm)) {
		$type = $this->plugin_type;
		$upType = strtoupper($type);
		$plugin_name = $type . '_name';
		$plugin_name_input = $plugin_name . '_input';
		$plugin_images = $type . '_images';
?>
<input type="hidden" name="subtask" value="<?php echo JRequest::getCmd('subtask','');?>"/>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
<table class="admintable table" style="width:100%">
	<tr>
		<td style="width:50%;vertical-align:top">
<?php } else { ?>
<div class="row-fluid">
	<div class="span6">
<?php } ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('MAIN_INFORMATION'); ?></legend>
				<table class="admintable table">
<?php if($this->multiple_plugin) { ?>
					<tr>
						<td class="key"><label for="data[<?php echo $type;?>][<?php echo $type;?>_name]"><?php echo JText::_('HIKA_NAME');?></label></td>
						<td>
							<input type="text" name="data[<?php echo $type;?>][<?php echo $type;?>_name]" value="<?php echo @$this->element->{$type.'_name'}; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="data[<?php echo $type;?>][<?php echo $type;?>_published]"><?php echo JText::_('HIKA_PUBLISHED');?></label></td>
						<td>
							<?php echo JHTML::_('hikaselect.booleanlist', 'data['.$type.']['.$type.'_published]' , '', @$this->element->{$type.'_published'}); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="data[<?php echo $type;?>][<?php echo $type;?>_access]"><?php echo JText::_('ACCESS_LEVEL');?></label></td>
						<td><?php
							$plugin_access = 'all';
							if(isset($this->element->{$type.'_access'}))
								$plugin_access = $this->element->{$type.'_access'};
							echo $this->joomlaAcl->display('data['.$type.']['.$type.'_access]', $plugin_access, true, true);
						?></td>
					</tr>
<?php } else { ?>
					<tr>
						<td class="key"><label for="data[<?php echo $type;?>][<?php echo $type;?>_name]"><?php echo JText::_('HIKA_NAME');?></label></td>
						<td><?php echo $this->plugin_name; ?><input type="hidden" name="data[<?php echo $type;?>][<?php echo $type; ?>_published]" value="1"/>
						</td>
					</tr>
<?php } ?>
				</table>
			</fieldset>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
		</td>
		<td style="vertical-align:top">
<?php } else { ?>
	</div>
	<div class="span6">
<?php } ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('PLUGIN_PARAMETERS'); ?></legend>
<?php echo $this->content; ?>
			</fieldset>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
		</td>
	</tr>
</table>
<?php } else { ?>
	</div>
</div>
<?php } ?>
		<input type="hidden" name="data[<?php echo $type;?>][<?php echo $type;?>_id]" value="<?php $n = $type.'_id'; echo @$this->element->$n;?>"/>
		<input type="hidden" name="data[<?php echo $type;?>][<?php echo $type;?>_type]" value="<?php echo $this->name;?>"/>
		<input type="hidden" name="task" value="save"/>
<?php
	} else {
		echo $this->content;
	}
?>
		<input type="hidden" name="name" value="<?php echo $this->name;?>"/>
		<input type="hidden" name="ctrl" value="plugins" />
		<input type="hidden" name="plugin_type" value="<?php echo $this->plugin_type;?>" />
		<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
