<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

JHtml::_('behavior.multiselect');
JHTML::_('behavior.modal');

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

$filterStatus = array(
	"*"=>JText::_('COM_CMGROUPBUYING_ALL_COUPON_STATUS'),
	"0"=>JText::_('COM_CMGROUPBUYING_COUPON_UNPAID_COUPON'),
	"1"=>JText::_('COM_CMGROUPBUYING_COUPON_WAITING_COUPON'),
	"2"=>JText::_('COM_CMGROUPBUYING_COUPON_EXCHANGED_COUPON'),
);
$filterStatusOption = array();

foreach($filterStatus as $key=>$value)
{
	$option = JHTML::_('select.option', $key, $value);
	array_push($filterStatusOption, $option);
}
?>
<script>
	function clear_filter()
	{
		document.id('filter_buyer').value = '';
		document.id('filter_deal').value = '';
		document.id('filter_partner').value = '';
		document.id('filter_status').value = '*';
		document.adminForm.submit();
	}

	window.addEvent('domready', function() {
			SqueezeBox.initialize({});
			SqueezeBox.assign($$('a.modal-buyer'), {
				parse: 'rel'
			});
			SqueezeBox.assign($$('a.modal-deal'), {
				parse: 'rel'
			});
			SqueezeBox.assign($$('a.modal-partner'), {
				parse: 'rel'
			});
		});

	function jSelectUser_filter_buyer(id, title) {
		var old_id = document.getElementById("filter_buyer").value;
		if (old_id != id) {
			document.getElementById("filter_buyer").value = id;
			document.getElementById("filter_buyer_name").value = title;
		}
		SqueezeBox.close();
	}

	function jSelectDeal(id, title) {
		var old_id = document.getElementById("filter_deal").value;
		if (old_id != id) {
			document.getElementById("filter_deal").value = id;
			document.getElementById("filter_deal_name").value = title;
		}
		SqueezeBox.close();
	}

	function jSelectPartner(id, title) {
		var old_id = document.getElementById("filter_partner").value;
		if (old_id != id) {
			document.getElementById("filter_partner").value = id;
			document.getElementById("filter_partner_name").value = title;
		}
		SqueezeBox.close();
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
			<div id="filter-search" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" name="cid[]" value="" placeholder="<?php echo JText::_('COM_CMGROUPBUYING_COUPON_CODE_LABEL'); ?>" />
				</div>
				<div class="btn-group">
					<button class="btn tip" onclick="document.fast_edit_form.submit()"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VIEW_INFO'); ?></button> 
				</div>
			</div>
			<input type="hidden" name="option" value="com_cmgroupbuying" />
			<input type="hidden" name="view" value="coupon" />
		</form>
		<form action="index.php?option=com_cmgroupbuying" method="post" name="adminForm" id="adminForm">
			<div id="filter-buyer" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" id="filter_buyer_name" value="<?php if($this->state->get('filter.buyer') != '') echo JFactory::getUser($this->state->get('filter.buyer'))->name; ?>" disabled="disabled">
					<input type="hidden" id="filter_buyer" name="filter_buyer" value="<?php echo $this->state->get('filter.buyer'); ?>">
				</div>
				<div class="btn-group">
					<a class="modal-buyer btn" title="<?php echo JText::_('COM_CMGROUPBUYING_SELECT_BUYER'); ?>" href="index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=filter_buyer" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_('COM_CMGROUPBUYING_SELECT_BUYER'); ?></a>
				</div>
			</div>
			<div id="filter-deal" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" id="filter_deal_name" value="<?php if($this->state->get('filter.deal') != '') echo $this->deals[$this->state->get('filter.deal')]['name']; ?>" disabled="disabled">
					<input type="hidden" id="filter_deal" name="filter_deal" value="<?php echo $this->state->get('filter.deal'); ?>">
				</div>
				<div class="btn-group">
					<a class="modal-deal btn" title="<?php echo JText::_('COM_CMGROUPBUYING_SELECT_DEAL'); ?>" href="index.php?option=com_cmgroupbuying&amp;view=deals&amp;layout=modal&amp;tmpl=component&amp;field=filter_deal" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_('COM_CMGROUPBUYING_SELECT_DEAL'); ?></a>
				</div>
			</div>
			<div id="filter-partner" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input type="text" id="filter_partner_name" value="<?php if($this->state->get('filter.partner') != '') echo JFactory::getUser($this->partners[$this->state->get('filter.partner')]['user_id'])->name; ?>" disabled="disabled">
					<input type="hidden" id="filter_partner" name="filter_partner" value="<?php echo $this->state->get('filter.partner'); ?>">
				</div>
				<div class="btn-group">
					<a class="modal-partner btn" title="<?php echo JText::_('COM_CMGROUPBUYING_SELECT_PARTNER'); ?>" href="index.php?option=com_cmgroupbuying&amp;view=partners&amp;layout=modal&amp;tmpl=component&amp;field=filter_partner" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_('COM_CMGROUPBUYING_SELECT_PARTNER'); ?></a>
				</div>
			</div>
			<div id="filter-status" class="btn-toolbar">
				<div class="filter-bar btn-group pull-left">
					<?php echo JHTML::_('select.genericList', $filterStatusOption, 'filter_status', null , 'value', 'text', $this->state->get('filter.status'));?>
				</div>
				<div class="btn-group">
					<button class="btn tip" type="submit"><?php echo JText::_('COM_CMGROUPBUYING_FILTER'); ?></button>
					<button class="btn tip" type="button" onclick="clear_filter()"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
			</div>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_COUPON_CODE_LABEL', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_ID_LABEL', 'a.order_id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_BUYER'); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_DEAL_FIELD_PARTNER_ID_LABEL'); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_ORDER_DEAL_NAME_LABEL'); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_CMGROUPBUYING_ORDER_STATUS_LABEL', 'a.coupon_status', $listDirn, $listOrder); ?>
						</th> 
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				foreach ($this->items as $i => $item) : 
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->coupon_code); ?>
						<td align="center">
							<a href="index.php?option=com_cmgroupbuying&view=coupon&cid[]=<?php echo $item->coupon_code; ?>"><?php echo $item->coupon_code; ?></a>
						</td>
						<td align="center">
							<?php
								echo $item->order_id;
							?>
						</td>
						<td>
							<?php
							if($item->user_id == 0)
							{
								echo JText::_('COM_CMGROUPBUYING_GUEST');
							}
							else
							{
								$buyer = JFactory::getUser($item->user_id);
								echo $buyer->username;
							}
							?>
						</td>
						<td align="center">
							<?php
							if(isset($this->partners[$item->partner_id]['name']))
								echo $this->partners[$item->partner_id]['name'];
							?>
						</td>
						<td align="center">
							<?php
							if(isset($this->deals[$item->deal_id]['name']))
								echo $this->deals[$item->deal_id]['name']; ?>
						</td>
						<td align="center">
							<?php
							if($item->coupon_status == 0)
							{
								echo JText::_('COM_CMGROUPBUYING_COUPON_UNPAID_COUPON');
							}
							elseif($item->coupon_status == 1)
							{
								echo JText::_('COM_CMGROUPBUYING_COUPON_WAITING_COUPON');
							}
							elseif($item->coupon_status == 2)
							{
								echo JText::_('COM_CMGROUPBUYING_COUPON_EXCHANGED_COUPON');
							}
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="view" value="coupons" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>