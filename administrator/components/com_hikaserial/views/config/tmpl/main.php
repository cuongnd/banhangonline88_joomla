<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="hk-container-fluid">
	<div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('MAIN');
		?></div>
		<dl class="hika_options large">

	<dt><?php echo JText::_('ASSIGNABLE_ORDER_STATUSES'); ?></dt>
	<dd><?php
		echo $this->orderstatusType->displayMultiple("config[assignable_order_statuses]", $this->config->get('assignable_order_statuses', 'confirmed,shipped'));
	?></dd>

	<dt><?php echo JText::_('ASSIGNED_SERIAL_STATUS'); ?></dt>
	<dd><?php
		echo $this->serial_status->display("config[assigned_serial_status]", $this->config->get('assigned_serial_status', 'assigned'));
	?></dd>

	<dt><?php echo JText::_('USED_SERIAL_STATUS'); ?></dt>
	<dd><?php
		echo $this->serial_status->display("config[used_serial_status]", $this->config->get('used_serial_status', 'used'));
	?></dd>

	<dt><?php echo JText::_('UNASSIGNED_SERIAL_STATUS'); ?></dt>
	<dd><?php
		echo $this->serial_status->display("config[unassigned_serial_status]", $this->config->get('unassigned_serial_status', 'unassigned'), true);
	?></dd>

	<dt><?php echo JText::_('REMOVE_DATA_ON_UNASSIGNED'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[unassigned_remove_data]", '', $this->config->get('unassigned_remove_data', false));
	?></dd>

	<dt><?php echo JText::_('DISPLAY_SERIAL_STATUSES'); ?></dt>
	<dd><?php
		echo $this->serial_status->displayMultiple("config[display_serial_statuses][]", explode(',', $this->config->get('display_serial_statuses', 'assigned,used')), 'namebox');
	?></dd>

	<dt><?php echo JText::_('USEABLE_SERIAL_STATUSES'); ?></dt>
	<dd><?php
		echo $this->serial_status->displayMultiple("config[useable_serial_statuses][]", explode(',', $this->config->get('useable_serial_statuses', 'free,reserved')), 'namebox');
	?></dd>

		</dl>
	</div></div>

	<div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block"><div>
		<div class="hikashop_tile_title"><?php
			echo JText::_('HIKA_ADVANCED_SETTINGS');
		?></div>
		<dl class="hika_options large">

	<dt><?php echo JText::_('LINK_PRODUCT_QUANTITY'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[link_product_quantity]", '', $this->config->get('link_product_quantity', false));
	?></dd>

	<dt><?php echo JText::_('FORBIDDEN_CONSUME_GUEST'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[forbidden_consume_guest]", '', $this->config->get('forbidden_consume_guest', true));
	?></dd>

	<dt><?php echo JText::_('CONSUME_DISPLAY_DETAILS'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[consume_display_details]", '', $this->config->get('consume_display_details', false));
	?></dd>

	<dt><?php echo JText::_('SERIAL_TRUNCATED_SIZE_IN_BACKEND'); ?></dt>
	<dd><input type="text" name="config[serial_display_size]" value="<?php echo $this->config->get('serial_display_size', 30);?>"/></dd>

	<dt><?php echo JText::_('USE_FAST_RANDOM'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[use_fast_random]", '', $this->config->get('use_fast_random', false));
	?></dd>

	<dt><?php echo JText::_('SAVE_HISTORY'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[save_history]", '', $this->config->get('save_history', false));
	?></dd>

	<dt><?php echo JText::_('USE_DELETED_SERIAL_STATUS'); ?></dt>
	<dd><?php
		echo JHTML::_('hikaselect.booleanlist', "config[use_deleted_serial_status]", '', $this->config->get('use_deleted_serial_status', false));
	?></dd>

		</dl>
	</div></div>

</div>
