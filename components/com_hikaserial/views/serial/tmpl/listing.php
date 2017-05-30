<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikaserial_serial_listing">
<form action="<?php echo hikaserial::completeLink('serial&task=listing'); ?>" method="post" name="adminForm" id="adminForm">
<fieldset>
	<div class="header hikashop_header_title"><h1><?php echo JText::_('HIKA_SERIALS'); ?></h1></div>
	<div class="toolbar hikashop_header_buttons" id="toolbar" style="float: right;">
		<table class="hikashop_no_border">
			<tr>
				<td>
					<a href="<?php echo hikashop_completeLink('user&task=cpanel'.$this->cpanel_itemid); ?>">
						<span class="icon-32-back" title="<?php echo JText::_('HIKA_BACK'); ?>">
						</span>
						<?php echo JText::_('HIKA_BACK'); ?>
					</a>
				</td>
			</tr>
		</table>
	</div>
</fieldset>

<table id="hikaserial_serials_listing_table" class="hikaserial_serials adminlist table table-bordered table-striped table-hover" style="width:100%" cellpadding="1">
	<thead>
		<tr>
			<th class="hikaserial_serials_num_title title titlenum" align="center"><?php
				echo JText::_( 'HIKA_NUM' );
			?></th>
			<th class="hikaserial_serial_data_title title" align="center"><?php
				echo JText::_('SERIAL_DATA');
			?></th>
			<th class="hikaserial_serial_ordernumber_title title" align="center"><?php
				echo JText::_('ORDER_NUMBER');
			?></th>
			<th class="hikaserial_serial_orderproduct_title title" align="center"><?php
				echo JText::_('PRODUCT');
			?></th>
			<th class="hikaserial_serial_date_title title" align="center"><?php
				echo JText::_('DATE');
			?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
				<?php echo $this->pagination->getResultsCounter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
<?php
	$sizelimit = $this->config->get('serial_display_size', 30);

	$i = 0;
	$k = 0;
	foreach($this->serials as $serial) {
?>
		<tr class="row<?php echo $k; ?>">
			<td class="hikaserial_serials_num_value"><?php
				echo $this->pagination->getRowOffset($i);
			?></td>
			<td class="hikaserial_serial_data_value"><?php
				echo $this->escape($serial->serial_data);
			?></td>
			<td class="hikaserial_serial_ordernumber_value">
				<a href="<?php echo hikashop_completeLink('order&task=show&cid='.(int)$serial->serial_order_id); ?>"><?php
					echo $serial->order_number;
				?></a>
			</td>
			<td class="hikaserial_serial_orderproduct_value"><?php
				if(isset($serial->order_product_name))
					echo $this->escape( strip_tags($serial->order_product_name) );
				else
					echo JText::_('HIKASERIAL_NO_PRODUCT');
			?></td>
			<td class="hikaserial_serial_date_value"><?php
				echo hikaserial::getDate($serial->order_created, '%Y-%m-%d %H:%M');
			?></td>
		</tr>
<?php
		$k = 1-$k;
		$i++;
	}
?>
	</tbody>
</table>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="cancel_id" value="<?php echo JRequest::getInt('cancel_id', 0); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<div class="clear_both"></div>
