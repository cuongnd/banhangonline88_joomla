<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><h1><?php echo JText::_('USERS'); ?></h1>
<div class="hikamarket_add_vendoruser">
<?php
	JHTML::_('behavior.tooltip');
	echo JHTML::tooltip(JText::_('HIKAM_INFO_ADD_USER'), JText::_('HIKAM_INFO_ADD_USER_TITLE'), '', '<img src="'.HIKAMARKET_IMAGES.'/icon-16/notice.png" style="vertical-align:middle" alt="?" />');
?>
	<input type="text" id="hikamarket_vendor_adduser" value=""/>
	<a href="#add_vendor_user" onclick="return window.vendorMgr.addVendorUser(this);"><button class="button hikashop_cart_input_button btn btn-primary" onclick="return false"><?php echo JText::_('HIKAM_ADD_VENDOR_USER'); ?></button></a>
	<div id="hikamarket_vendor_adduser_loading" class="toggle_onload" style="display:none;"></div>
</div>
<table id="hikamarket_vendor_users" class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
	<thead>
		<tr>
			<th class="hikamarket_user_id_title title"><?php
				echo JText::_('ID');
			?></th>
			<th class="hikamarket_user_name_title title"><?php
				echo JText::_('HIKA_NAME');
			?></th>
			<th class="hikamarket_user_email_title title"><?php
				echo JText::_('HIKA_EMAIL');
			?></th>
			<th class="hikamarket_user_acl_title title"><?php
				echo JText::_('HIKAM_ACL');
			?></th>
			<th class="hikamarket_user_icon_title title"><?php
				echo JText::_('HIKA_DELETE');
			?></th>
		</tr>
	</thead>
<?php
$user_id = hikamarket::loadUser(false);
echo '<input type="hidden" name="data[users][]" value="'.$user_id.'"/>';
$k = 0;
if(!empty($this->users)) {
	foreach($this->users as $user) {
		if($user->user_id == $user_id)
			continue;
?>
	<tr class="row<?php echo $k; ?>" id="vendor_users_<?php echo $user->id; ?>">
		<td align="center"><?php echo $user->user_id;?></td>
		<td><?php echo $user->name;?></td>
		<td><?php echo $user->email;?></td>
		<td align="center"><?php echo $this->marketaclType->displayButton('user['.$user->user_id.'][user_access]', @$user->user_vendor_access); ?></td>
		<td align="center">
			<a href="#" onclick="window.hikamarket.deleteRow(this); return false;"><img src="<?php echo HIKASHOP_IMAGES;?>delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a>
			<input type="hidden" name="data[users][]" value="<?php echo $user->user_id;?>"/>
		</td>
	</tr>
<?php
		$k = 1 - $k;
	}
}
?>
	<!-- Template line -->
	<tr id="hikamarket_users_tpl" class="row<?php echo $k; ?>" style="display:none;">
		<td align="center">{user_id}</td>
		<td>{name}</td>
		<td>{user_email}</td>
		<td align="center"><?php echo $this->marketaclType->displayButton('user[{user_id}][user_access]', 'all'); ?></td>
		<td align="center">
			<a href="#" onclick="window.hikamarket.deleteRow(this); return false;"><img src="<?php echo HIKASHOP_IMAGES;?>delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a>
			<input type="hidden" name="{input_name}" value="{user_id}"/>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
window.vendorMgr = {};
window.vendorMgr.addVendorUser = function(el) {
	var d = document, w = window, o = window.Oby, loading = d.getElementById('hikamarket_vendor_adduser_loading'), emailInput = d.getElementById('hikamarket_vendor_adduser');
	if(emailInput) {
		if(loading)
			loading.style.display = '';
		var url = '<?php echo hikamarket::completeLink('vendor&task=adduser&email={EMAIL}&'.hikamarket::getFormToken().'=1', true, false, true); ?>', email = emailInput.value;
		emailInput.value = '';
		o.xRequest(url.replace('{EMAIL}', email), null, function(xhr){
			if(xhr.responseText.substring(0,1) == '{') {
				var data = o.evalJSON(xhr.responseText);
				data['input_name'] = "data[users][]";
				window.hikamarket.dupRow('hikamarket_users_tpl', data, "vendor_users_" + data.user_id);
			}
			if(loading)
				loading.style.display = 'none';
		});
	}
	return false;
}
// -->
</script>
