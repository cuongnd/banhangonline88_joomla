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
<form action="<?php echo hikaserial::completeLink('pack'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="hikashop_backend_tile_edition">
	<div class="hk-container-fluid">

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('MAIN_INFORMATION');
		?></div>
		<dl class="hika_options">
			<dt class="hikaserial_pack_name"><label for="data[pack][pack_name]"><?php echo JText::_('HIKA_NAME'); ?></label></dt>
			<dd class="hikaserial_pack_name input_large">
				<input type="text" name="data[pack][pack_name]" value="<?php echo $this->escape(@$this->pack->pack_name); ?>" />
			</dd>

			<dt class="hikaserial_pack_data"><label for="data[pack][pack_data]"><?php echo JText::_('PACK_DATA'); ?></label></dt>
			<dd class="hikaserial_pack_data"><?php
				echo $this->packDataType->display('data[pack][pack_data]', @$this->pack->pack_data);
			?></dd>

			<dt class="hikaserial_pack_generator"><label for="data[pack][pack_generator]"><?php echo JText::_('PACK_GENERATOR'); ?></label></dt>
			<dd class="hikaserial_pack_generator"><?php
				echo $this->packGeneratorType->display('data[pack][pack_generator]', @$this->pack->pack_generator);
			?></dd>

			<dt class="hikaserial_pack_published"><label for="data[pack][pack_published]"><?php echo JText::_('HIKA_PUBLISHED'); ?></label></dt>
			<dd class="hikaserial_pack_published"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack][pack_published]', '', @$this->pack->pack_published);
			?></dd>

<?php if(!empty($this->hikamarket)) { ?>
			<dt class="hikaserial_pack_vendor"><label for="data[pack][pack_vendor_id]"><?php echo JText::_('HIKA_VENDOR'); ?></label></dt>
			<dd class="hikaserial_pack_vendor"><?php
				echo $this->nameboxMarketType->display(
					'data[pack][pack_vendor_id]',
					(int)@$this->pack->pack_vendor_id,
					hikamarketNameboxType::NAMEBOX_SINGLE,
					'vendor',
					array(
						'delete' => true,
						'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
					)
				);
			?></dd>
			<dt class="hikaserial_pack_manageaccess"><label for="data[pack][pack_manage_access]"><?php echo JText::_('ACCESS_LEVEL'); ?></label></dt>
			<dd class="hikaserial_pack_manageaccess"><?php
				if(empty($this->pack->pack_manage_access))
					$this->pack->pack_manage_access = 'all';
				echo $this->joomlaAclMarketType->display('data[pack][pack_manage_access]', $this->pack->pack_manage_access, true, true);
			?></dd>
<?php } ?>
		</dl>
	</div></div>

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('ADDITIONAL_INFORMATION');
		?></div>
		<dl class="hika_options">

			<dt class="hikaserial_pack_params_statusrefund"><label for="data[pack_params][status_for_refund]"><?php echo JText::_('SERIAL_STATUS_FOR_REFUND'); ?></label></dt>
			<dd class="hikaserial_pack_params_statusrefund"><?php
				echo $this->serialStatusType->display('data[pack_params][status_for_refund]', @$this->pack->pack_params->status_for_refund);
			?></dd>

			<dt class="hikaserial_pack_params_levelnotify"><label for="data[pack_params][stock_level_notify]"><?php echo JText::_('SERIAL_STOCK_LEVEL_NOTIFY'); ?></label></dt>
			<dd class="hikaserial_pack_params_levelnotify">
				<input type="text" name="data[pack_params][stock_level_notify]" value="<?php echo @$this->pack->pack_params->stock_level_notify;?>"/>
			</dd>

			<dt class="hikaserial_pack_params_unlimitedqty"><label for="data[pack_params][unlimited_quantity]"><?php echo JText::_('SERIAL_UNLIMITED_QUANTITY'); ?></label></dt>
			<dd class="hikaserial_pack_params_unlimitedqty"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][unlimited_quantity]', '',  @$this->pack->pack_params->unlimited_quantity);
			?></dd>

			<dt class="hikaserial_pack_params_consumer"><label for="data[pack_params][consumer]"><?php echo JText::_('HIKA_CONSUMER_PACK'); ?></label></dt>
			<dd class="hikaserial_pack_params_consumer"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][consumer]', '',  @$this->pack->pack_params->consumer);
			?></dd>

			<dt class="hikaserial_pack_params_consumerassign"><label for="data[pack_params][consume_user_assign]"><?php echo JText::_('HIKA_CONSUME_USER_ASSIGN'); ?></label></dt>
			<dd class="hikaserial_pack_params_consumerassign"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][consume_user_assign]', '',  @$this->pack->pack_params->consume_user_assign);
			?></dd>

			<dt class="hikaserial_pack_params_webservice"><label for="data[pack_params][webservice]"><?php echo JText::_('HIKA_WEBSERVICE_ACCESS'); ?></label></dt>
			<dd class="hikaserial_pack_params_webservice"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][webservice]', '',  @$this->pack->pack_params->webservice);
			?></dd>

			<dt class="hikaserial_pack_params_nouserassign"><label for="data[pack_params][no_user_assign]"><?php echo JText::_('SERIAL_NO_USER_ASSIGN'); ?></label></dt>
			<dd class="hikaserial_pack_params_nouserassign"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][no_user_assign]', '',  @$this->pack->pack_params->no_user_assign);
			?></dd>

			<dt class="hikaserial_pack_params_randompickup"><label for="data[pack_params][random_pickup]"><?php echo JText::_('SERIAL_RANDOM_PICKUP'); ?></label></dt>
			<dd class="hikaserial_pack_params_randompickup"><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[pack_params][random_pickup]', '',  @$this->pack->pack_params->random_pickup);
			?></dd>
		</dl>
	</div></div>

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('PACK_DESCRIPTION');
		?></div>
<?php
		$this->editor->content = @$this->pack->pack_description;
		$this->editor->name = 'pack_description';
		$ret = $this->editor->display();
		if($this->editor->editor == 'codemirror')
			echo str_replace(array('(function() {'."\n",'})()'."\n"),array('window.hikashop.ready(function(){', '});'), $ret);
		else
			echo $ret;
?>
		<div style="clear:both"></div>
	</div></div>

	<div class="hkc-xl-clear"></div>

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('HIKA_STATISTICS');
		?></div>
		<dl class="hika_options">
<?php
$statuses = $this->serialStatusType->getValues();
foreach($statuses as $key => $value) { ?>
			<dt class="hikaserial_stat_<?php echo $key; ?>"><?php
				if(isset($this->counters[$key])) {
					echo '<a href="' . hikaserial::completeLink('serial&filter_pack=' . @$this->pack->pack_id . '&filter_status=' . $key).'">'.$value.'</a>';
				} else {
					echo $value;
				}
			?></dt>
			<dd class="hikaserial_stat_<?php echo $key; ?>"><?php
				if(isset($this->counters[$key])) {
					echo $this->counters[$key];
				} else {
					echo '0';
				}
			?></dd>
<?php } ?>
			<dt class="hikaserial_stat_total">
				<a href="<?php echo hikaserial::completeLink('serial&filter_pack=' . @$this->pack->pack_id . '&filter_status=');?>"><?php echo JText::_('TOTAL_SERIALS'); ?></a>
			</dt>
			<dd class="hikaserial_stat_total"><?php
				if(isset($this->counters['total'])) {
					echo $this->counters['total'];
				} else {
					echo '0';
				}
			?></td>

<?php foreach($this->counters as $name => $value) {
	if($name == 'total' || isset($statuses[$name]))
		continue;
?>
			<dt><label><?php echo ucfirst($name); ?></label></dt>
			<dd><?php echo $value;?></dd>
<?php } ?>
		</dl>
	</div></div>

	<div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('PRODUCTS');
		?></div>
		<table class="adminlist table table-striped" style="width:100%">
			<thead>
				<tr>
					<th><?php echo JText::_('HIKA_NAME');?></th>
					<th><?php echo JText::_('PRODUCT_CODE');?></th>
					<th><?php echo JText::_('PRODUCT_QUANTITY');?></th>
					<th><?php echo JText::_('ID');?></th>
				</tr>
			</thead>
			<tbody>
<?php
if(!empty($this->products)){
	foreach($this->products as $key => $product) {
?>
				<tr>
					<td><a href="<?php echo hikaserial::completeLink('shop.product&task=edit&cid[]='.$product->product_id); ?>"><?php echo $product->product_name; ?></a></td>
					<td><?php echo $product->product_code; ?></td>
					<td align="center"><?php echo $product->quantity; ?></td>
					<td align="center"><?php echo $product->product_id; ?></td>
				<tr>
<?php
	}
} else {
?>
				<tr>
					<td colspan="4">
						<em><?php echo JText::_('PACK_NOT_LINKED_WITH_PRODUCT');?></em>
					</td>
				<tr>
<?php
}
?>
			</tbody>
		</table>
	</div></div>

	</div>
</div>

	<div class="clr"></div>
	<input type="hidden" name="cid[]" value="<?php echo @$this->pack->pack_id; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
