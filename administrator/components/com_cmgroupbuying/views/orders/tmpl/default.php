<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$configuration = $this->configuration;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$filterOrderStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_ORDER_STATUS'),
	"0"=>JText::_('COM_CMGROUPBUYING_ORDER_UNPAID_ORDER'),
	"1"=>JText::_('COM_CMGROUPBUYING_ORDER_PAID_ORDER'),
	"2"=>JText::_('COM_CMGROUPBUYING_ORDER_LATE_PAID_ORDER'),
	"4"=>JText::_('COM_CMGROUPBUYING_ORDER_REFUNDED_ORDER')
);
$filterOrderOption = array();

foreach($filterOrderStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterOrderOption, $option);
}

$filterPaymentOption = array();
$option = JHTML::_('select.option', '*', JText::_('COM_CMGROUPBUYING_ALL_PAYMENT'));
array_push($filterPaymentOption, $option);

foreach($this->payments as $payment)
{
	$option = JHTML::_('select.option', $payment['name'], $payment['name']);
	array_push($filterPaymentOption, $option);
}
?>
<script>
	function clear_filter()
	{
		document.id('filter_payment').value = '*';
		document.id('filter_status').value = '*';
		document.adminForm.submit();
	}
</script>
<div class="cmgroupbuying">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		<form action="index.php" method="get" name="fast_edit_form" id="fast_edit_form">
			<div id="filter-bar-1" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" id ="order_id" name="cid[]" value="" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID_LABEL'); ?>" />
				</div>
				<div class="btn-group hidden-phone">
					<button class="btn tip" type="button" onclick="document.fast_edit_form.submit()"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VIEW_INFO'); ?></button>
				</div>
				 <div class="clearfix"></div>
			</div>
			<input type="hidden" name="option" value="com_cmgroupbuying" />
			<input type="hidden" name="view" value="order" />
		</form>
		<form action="index.php?option=com_cmgroupbuying" method="post" name="adminForm" id="adminForm">
			<div id="filter-bar-2" class="btn-toolbar">
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterPaymentOption, 'filter_payment', null , 'value', 'text', $this->state->get('filter.payment'));?>
				</div>
				<div class="btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterOrderOption, 'filter_status', null , 'value', 'text', $this->state->get('filter.status'));?>
				</div>
				<div class="btn-group">
					<button class="btn tip" type="submit"><?php echo JText::_('COM_CMGROUPBUYING_FILTER'); ?></button>
					<button class="btn tip" type="button" onclick="clear_filter()"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clr"> </div>

			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_ID_LABEL', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JTEXT::_('COM_CMGROUPBUYING_BUYER'); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_ORDER_PAYMENT_LABEL'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_VALUE_LABEL', 'a.value', $listDirn, $listOrder); ?>
						</th>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_CREATED_DATE_LABEL', 'a.created_date', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_EXPIRED_DATE_LABEL', 'a.expired_date', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_PAID_DATE_LABEL', 'a.paid_date', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_STATUS_LABEL', 'a.status', $listDirn, $listOrder); ?>
						</th> 
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) : 
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<td align="center">
							<a href="index.php?option=com_cmgroupbuying&view=order&cid[]=<?php echo $item->id; ?>"><?php echo $item->id; ?></a>
						</td>
						<td>
							<?php
							if($item->buyer_id == 0)
							{
								echo JText::_('COM_CMGROUPBUYING_GUEST');
							}
							else
							{
								$buyer = JFactory::getUser($item->buyer_id);
								echo $buyer->username;
							}
							?>
						</td>
						<td align="center">
							<?php echo $item->payment_name; ?>
						</td>
						<td align="center">
							<?php echo CMGroupBuyingHelperDeal::displayDealPrice($item->value, true, $configuration); ?>
						</td>
						<td align="center">
							<?php echo $item->created_date; ?>
						</td>
						<td align="center">
							<?php echo $item->expired_date; ?>
						</td>
						<td align="center">
							<?php if($item->paid_date != '0000-00-00 00:00:00') echo $item->paid_date; ?>
						</td>
						<td align="center">
							<?php echo $filterOrderStatus[$item->status]; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="view" value="orders" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" /> 
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>