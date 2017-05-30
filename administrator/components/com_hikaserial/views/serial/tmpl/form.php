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
<script type="text/javascript">
	window.hikashop.ready(function(){window.hikashop.dlTitle('adminForm');});
</script>
<form action="<?php echo hikaserial::completeLink('serial'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="hikashop_backend_tile_edition">
	<div class="hk-container-fluid">

	<div class="hkc-xl-8 hkc-lg-12 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('MAIN_INFORMATION');
		?></div>
		<dl class="hika_options">
			<dt class="hikaserial_serial_data"><label for="data_serial__serial_data"><?php echo JText::_('SERIAL_DATA'); ?></label></dt>
			<dd class="hikaserial_serial_data input_large">
<?php
	if(!isset($this->serial->custom_display) || $this->serial->custom_display === false ) {
?>
				<textarea rows="5" style="width:100%" id="data_serial__serial_data" name="data[serial][serial_data]"><?php echo @$this->serial->serial_data;?></textarea>
<?php
	} else if($this->serial->custom_display === true) {
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDisplaySerialForm', array(&$this->serial));
	} else {
		echo $this->serial->custom_display;
	}
?>
			</dd>

			<dt class="hikaserial_serial_pack"><label><?php echo JText::_('SERIAL_PACK'); ?></label></dt>
			<dd class="hikaserial_serial_pack"><?php
				echo $this->packType->display('data[serial][serial_pack_id]', @$this->serial->serial_pack_id);
			?></dd>

			<dt class="hikaserial_serial_status"><label for="data[serial][serial_status]"><?php echo JText::_('SERIAL_STATUS'); ?></label></dt>
			<dd class="hikaserial_serial_status"><?php
				echo $this->serialStatusType->display('data[serial][serial_status]', @$this->serial->serial_status);
			?></dd>
		</dl>
	</div></div>

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('ADDITIONAL_INFORMATION');
		?></div>
		<table class="admintable table" style="width:100%">
			<tr>
				<td class="key">
					<label for="data[serial][serial_assign_date]"><?php echo JText::_('ASSIGN_DATE'); ?></label>
				</td>
				<td id="hikaserial_serial_order_0"><?php
					if(!empty($this->serial->serial_assign_date)) {
						echo hikaserial::getDate($this->serial->serial_assign_date);
					} else {
						echo JText::_('HIKAS_NONE');
					}
				?></td>
				<td style="width:1%"></td>
			</tr>
			<tr>
				<td class="key">
					<label for="data[serial][serial_order_id]"><?php echo JText::_('ORDER_NUMBER'); ?></label>
				</td>
				<td id="hikaserial_serial_order_1"><?php
					if(!empty($this->serial->serial_order_id)) {
						if($this->manage_shop_order) {
							?><a href="<?php echo hikaserial::completeLink('shop.order&task=edit&cid[]='.$this->serial->serial_order_id); ?>"><?php
						}

						if(!empty($this->serial->order->order_number))
							echo $this->serial->order->order_number;
						else
							echo '<em>' . $this->serial->serial_order_id . '</em>';

						if($this->manage_shop_order) {
							?></a><?php
						}

						if(!empty($this->serial->orderproduct->order_product_name)) {
							echo ' - ' . $this->serial->orderproduct->order_product_name;
						}
					} else {
						echo JText::_('HIKAS_NONE');
					}
				?></td>
				<td style="width:1%;text-align:center;">
<?php if(!empty($this->serial->serial_order_id)) { ?>
					<a onclick="return hikaserial_unassign('order',this);" href="#"><img src="<?php echo HIKASERIAL_IMAGES;?>icon-16/delete.png" alt="<?php JText::_('UNASSIGN');?>"/></a>
<?php } ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="data[serial][serial_user_id]"><?php echo JText::_('HIKA_USER'); ?></label>
				</td>
				<td id="hikaserial_serial_user_0"><?php
					if(!empty($this->serial->serial_user_id)) {
						if($this->manage_shop_user) {
							?><a href="<?php echo hikaserial::completeLink('shop.user&task=edit&cid[]='.$this->serial->serial_user_id); ?>"><?php
						}
						if(!empty($this->serial->user->username))
							echo $this->serial->user->username;
						else
							echo '<em>' . $this->serial->serial_user_id . '</em>';
						if($this->manage_shop_user) {
							?></a><?php
						}
					} else {
						echo JText::_('HIKAS_NONE');
					}
				?></td>
				<td style="width:1%;text-align:center;">
					<div id="hikaserial_serial_user_del" style="<?php if(!empty($this->serial->serial_order_id) || empty($this->serial->serial_user_id)) { echo 'display:none;'; } ?>">
						<a onclick="return hikaserial_unassign('user',this);" href="#"><img src="<?php echo HIKASERIAL_IMAGES;?>icon-16/delete.png" alt="<?php JText::_('UNASSIGN');?>"/></a>
					</div>
					<div id="hikaserial_serial_user_add" style="<?php if(!empty($this->serial->serial_user_id)) { echo 'display:none;'; } ?>">
						<?php
							echo $this->popup->display(
								'<img src="'.HIKASERIAL_IMAGES.'icon-16/add.png" alt="'.JText::_('ASSIGN').'"/>',
								'ASSIGN',
								hikaserial::completeLink('users&task=select&single=true', true),
								'hikaserial_user_assign_serial',
								760, 480, 'onclick="return hikaserial_assignUser(this);"', '', 'link'
							);
						?>
					</div>
				</td>
			</tr>
		</table>
<script type="text/javascript">
function hikaserial_unassign(type, el){
	var d = document, e = null;
	switch(type){
		case 'order':
			e = d.getElementById('hikaserial_serial_order_0'); if(e){ e.innerHTML = '<?php echo str_replace("'","\\'",JText::_('HIKAS_NONE'));?>'; }
			e = d.getElementById('hikaserial_serial_order_1'); if(e){ e.innerHTML = '<?php echo str_replace("'","\\'",JText::_('HIKAS_NONE'));?>'; }
			e = d.getElementById('hikaserial_serial_assign_date'); if(e){ e.value = '0'; }
			e = d.getElementById('hikaserial_serial_order_id'); if(e){ e.value = '0'; }
			e = d.getElementById('hikaserial_serial_user_del'); if(e){ e.style.display = ''; }
			el.style.display = 'none';
			break;
		case 'user':
			e = d.getElementById('hikaserial_serial_user_0'); if(e){ e.innerHTML = '<?php echo str_replace("'","\\'",JText::_('HIKAS_NONE'));?>'; }
			e = d.getElementById('hikaserial_serial_user_id'); if(e){ e.value = '0'; }
			e = d.getElementById('hikaserial_serial_user_add'); if(e){ e.style.display = ''; }
			el.style.display = 'none';
			break;
	}
	return false;
}
function hikaserial_assignUser(el) {
	window.hikaserial.submitFct = function(user) {
		try {
			if( typeof(user) == "object" && user.id > 0 ) {
				var d = document, e = d.getElementById('hikaserial_serial_user_id');
				if(e){
					e.value = user.id;
				}
				e = d.getElementById('hikaserial_serial_user_0');
				e.innerHTML = user.username+' ('+user.id+')';
				e = d.getElementById('hikaserial_serial_user_del');
				if(e){ e.style.display = ''; }
				e = d.getElementById('hikaserial_serial_user_add');
				if(e){ e.style.display = 'none'; }
			}
		}catch(e){}
		hikaserial.closeBox();
	};
	hikaserial.openBox(el,null,(el.getAttribute('rel') == null));
	return false;
}
function hikaserial_createExtraData() {
	var d = document,
		extd_name = d.getElementById('hikaserial_extra_data_new_name');
	if(extd_name.value != '') {
		keyName = extd_name.value;
		extd_name.value = '';
		var exists = d.getElementById('data[serial][serial_extradata]['+keyName+']');
		if(!exists) {
			var htmlblocks = {name: keyName, key: 'data[serial][serial_extradata]['+keyName+']'};
			hikaserial.dupRow('hikaserial_extra_data_tpl', htmlblocks);
		}
	}
	return false;
}
function hikaserial_delete_extradata(el) {
	hikaserial.deleteRow(el);
	return false;
}
function hikaserial_handleinput(field, event) {
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
		return hikaserial_createExtraData();
	return true;
}
</script>
	</div></div>

	<div class="hkc-xl-clear"></div>

	<div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('SERIAL_EXTRA_DATA');
		?></div>
		<table class="admintable table" style="width:100%">
			<tr>
				<td class="key">
					<label><?php echo JText::_('ADD'); ?></label>
				</td>
				<td>
					<input type="hidden" value="1" name="hikaserial_extradata"/>
					<input type="text" id="hikaserial_extra_data_new_name" value="" onkeypress="return hikaserial_handleinput(this, event);"/>
					<a href="#add" onclick="return hikaserial_createExtraData();"><img style="vertical-align:middle;" src="<?php echo HIKASERIAL_IMAGES;?>icon-16/add.png" alt="<?php echo JText::_('ADD'); ?>"/></a>
				</td>
				<td style="width:2%"></td>
			</tr>
<?php
	$extradata = @$this->serial->serial_extradata;
	if(!empty($extradata)) {
		if(is_string($extradata)) $extradata = hikaserial::unserialize($extradata);
		if(empty($extradata)) $extradata = array();
		foreach($extradata as $data_key => $data_value) {
?>			<tr>
				<td class="key"><label for="data[serial][serial_extradata][<?php echo $data_key; ?>]"><?php echo $data_key; ?></label></td>
				<td>
					<textarea rows="3" style="width:100%" id="data[serial][serial_extradata][<?php echo $data_key; ?>]" name="data[serial][serial_extradata][<?php echo $data_key; ?>]"><?php
						echo $data_value;
					?></textarea>
				</td>
				<td>
					<a href="#remove" onclick="return hikaserial_delete_extradata(this);"><img src="<?php echo HIKASERIAL_IMAGES;?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a>
				</td>
			</tr>
<?php
		}
	}
?>			<tr id="hikaserial_extra_data_tpl" style="display:none;">
				<td class="key"><label for="{key}">{name}</label></td>
				<td><textarea rows="3" style="width:100%" id="{key}" name="{key}"></textarea></td>
				<td><a href="#remove" onclick="return hikaserial_delete_extradata(this);"><img src="<?php echo HIKASERIAL_IMAGES;?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
		</table>
	</div></div>

<?php if($this->config->get('save_history', 1)) { ?>
	<div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('HISTORY');
		?></div>
		<table class="adminlist table table-striped" cellpadding="1">
			<thead>
			<tr>
				<th class="title"><?php
					echo JText::_('HIKA_TYPE');
				?></th>
				<th class="title"><?php
					echo JText::_('ORDER_STATUS');
				?></th>
				<th class="title"><?php
					echo JText::_('HIKA_USER').' / '.JText::_('IP');
				?></th>
				<th class="title"><?php
					echo JText::_('DATE');
				?></th>
				<th class="title"><?php
					echo JText::_('INFORMATION');
				?></th>
			</tr>
			</thead>
			<tbody>
<?php
	if(!empty($this->serial->history)) {
		foreach($this->serial->history as $history ) {
?>
				<tr>
					<td><?php
						$val = preg_replace('#[^a-z0-9]#i','_',strtoupper($history->history_type));
						$trans = JText::_($val);
						if($val!=$trans){
							$history->history_type = $trans;
						}
						echo $history->history_type;
					?></td>
					<td><?php
						echo $this->serialStatusType->get($history->history_new_status);
					?></td>
					<td><?php
						if(!empty($history->history_user_id)){
							$class = hikaserial::get('shop.class.user');
							$user = $class->get($history->history_user_id);
							echo $user->username.' / ';
						}
						echo $history->history_ip;
					?></td>
					<td><?php echo hikaserial::getDate($history->history_created,'%Y-%m-%d %H:%M');?></td>
					<td><?php echo $history->history_data; ?></td>
				</tr>
<?php
		}
	}
?>
			</tbody>
		</table>
	</div></div>
<?php } ?>
	</div></div>
	<div class="clr"></div>

	<input type="hidden" name="data[serial][serial_assign_date]" id="hikaserial_serial_assign_date" value="<?php echo @$this->serial->serial_assign_date;?>"/>
	<input type="hidden" name="data[serial][serial_order_id]" id="hikaserial_serial_order_id" value="<?php echo @$this->serial->serial_order_id;?>"/>
	<input type="hidden" name="data[serial][serial_user_id]" id="hikaserial_serial_user_id" value="<?php echo @$this->serial->serial_user_id;?>"/>

	<input type="hidden" name="cid[]" value="<?php echo @$this->serial->serial_id; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
