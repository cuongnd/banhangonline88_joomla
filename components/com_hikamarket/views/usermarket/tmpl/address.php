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
$show_url = 'user&task=address&subtask=show&cid='.$this->address->address_id.'&user_id='.$this->user_id;
$save_url = 'user&task=address&subtask=save&cid='.$this->address->address_id.'&user_id='.$this->user_id;
$update_url = 'user&task=address&subtask=edit&cid='.$this->address->address_id.'&user_id='.$this->user_id;
$delete_url = 'user&task=address&subtask=delete&cid='.$this->address->address_id.'&user_id='.$this->user_id;

if(hikamarket::acl('user/edit/address') && ($this->vendor->vendor_id == 0 || $this->vendor->vendor_id == 1)) {
	if(!isset($this->edit) || $this->edit !== true ) {
?>		<div class="hikam_edit">
			<a href="<?php echo hikamarket::completeLink($update_url, true);?>" onclick="return window.hikamarket.get(this,'hikamarket_user_addresses_edition');"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/edit.png" alt=""/><span><?php echo JText::_('HIKA_EDIT'); ?></span></a>
			<a href="<?php echo hikamarket::completeLink($delete_url, true);?>" onclick="return window.addressMgr.delete(this,<?php echo $this->address->address_id; ?>);"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt=""/><span><?php echo JText::_('HIKA_DELETE'); ?></span></a>
		</div>
<?php
	} else {
?>		<div class="hikam_edit">
			<a href="<?php echo hikamarket::completeLink($save_url, true);?>" onclick="return window.hikamarket.form(this,'hikamarket_user_addresses_edition');"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/save.png" alt=""/><span><?php echo JText::_('HIKA_SAVE'); ?></span></a>
			<a href="<?php echo hikamarket::completeLink($show_url, true);?>" onclick="return window.hikamarket.get(this,'hikamarket_user_addresses_edition');"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/cancel.png" alt=""/><span><?php echo JText::_('HIKA_CANCEL'); ?></span></a>
		</div>
<?php
	}
}

if(isset($this->edit) && $this->edit === true && hikamarket::acl('user/edit/address')) {
	foreach($this->fields['address'] as $fieldname => $field) {
?>
	<dl id="hikamarket_user_address_<?php echo $this->address->address_id; ?>_<?php echo $fieldname;?>" class="hikam_options">
		<dt class="hikamarket_user_address_<?php echo $fieldname;?>"><label><?php
			echo $this->fieldsClass->trans($field->field_realname);
			if($field->field_required && !empty($field->vendor_edit))
				echo ' <span class="field_required">*</span>';
		?></label></dt>
		<dd class="hikamarket_user_address_<?php echo $fieldname;?>"><?php
			if(!empty($field->vendor_edit)) {
				$onWhat = 'onchange';
				if($field->field_type == 'radio')
					$onWhat = 'onclick';

				$field->field_required = false;
				echo $this->fieldsClass->display(
						$field,
						@$this->address->$fieldname,
						'data[user_address]['.$fieldname.']',
						false,
						' ' . $onWhat . '="hikashopToggleFields(this.value,\''.$fieldname.'\',\'user_address\',0);"',
						false,
						$this->fields['address'],
						$this->address
				);
			} else {
				echo $this->fieldsClass->show($field, @$this->address->$fieldname);
			}
		?></dd>
	</dl>
<?php
	}
	echo '<input type="hidden" name="data[user_address][address_id]" value="'.@$this->address->address_id.'"/>';
	echo '<input type="hidden" name="data[user_address][address_user_id]" value="'.@$this->address->address_user_id.'"/>';
	echo JHTML::_( 'form.token' );
} else {
	if($this->config->get('address_show_details', 0)) {
		foreach($this->fields['address'] as $fieldname => $field) {
?>
	<dl class="hikam_options">
		<dt class="hikamarket_user_address_<?php echo $fieldname;?>"><label><?php echo $this->fieldsClass->trans($field->field_realname);?></label></dt>
		<dd class="hikamarket_user_address_<?php echo $fieldname;?>"><span><?php echo $this->fieldsClass->show($field, @$this->address->$fieldname);?></span></dd>
	</dl>
<?php
		}
	} else {
		echo $this->addressClass->maxiFormat($this->address, $this->fields['address'], true);
	}
}

if(JRequest::getVar('tmpl', '') == 'component') {
	$miniFormat = $this->addressClass->miniFormat($this->address, $this->fields['address']);
?>
<script type="text/javascript">
window.Oby.fireAjax('hikamarket_address_changed',{'edit':<?php echo $this->edit?'1':'0'; ?>,'cid':<?php echo $this->address->address_id; ?>,'miniFormat':'<?php echo str_replace('\'','\\\'', $miniFormat); ?>'<?php
	$previous_id = JRequest::getVar('previous_cid', null);
	if((!empty($previous_id) || $previous_id === 0) && is_int($previous_id))
		echo ',\'previous_cid\':' . $previous_id;
?>});
</script>
<?php
}
