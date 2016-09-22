<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaauction::completeLink('auctions'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(HIKAAUCTION_BACK_RESPONSIVE) { ?>
	<div class="row-fluid">
		<div class="span6">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" onchange="this.form.submit();" />
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span6">
<?php } else { ?>
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" onchange="this.form.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } ?>
				<!-- Filters -->
<?php
	$values = array(
		'all' => JHTML::_('select.option', 0, JText::_('HKA_ALL')),
		'current' => JHTML::_('select.option', 1, JText::_('HKA_AUCTIONTYPE_CURRENT')),
		'past' => JHTML::_('select.option', 2, JText::_('HKA_AUCTIONTYPE_PAST')),
		'futur' => JHTML::_('select.option', 3, JText::_('HKA_AUCTIONTYPE_FUTUR')),
	);
	echo JHTML::_('select.genericlist', $values, 'filter_auction_type', ' onchange="document.adminForm.submit();"', 'value', 'text', @$this->pageInfo->filter->auction_type);
?>
<?php if(HIKAAUCTION_BACK_RESPONSIVE) { ?>
		</div>
	</div>
<?php } else { ?>
			</td>
		</tr>
	</table>
<?php } ?>
	<table class="adminlist table table-striped table-hover">
		<thead>
			<tr>
				<th class="hikapoints_auctions_num_title title titlenum"><?php
					echo JText::_('HKA_NUM');
				?></th>
				<th class="hikaauctions_auctions_select_title title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikaauction.checkAll(this);" />
				</th>
				<th class="hikaauctions_auctions_productname_title title"><?php
					echo JHTML::_('grid.sort', JText::_('PRODUCT_NAME'), 'product.product_name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaauctions_auctions_status_title titletoggle"><?php
					echo JText::_('HKA_AUCTION_STATUS');
				?></th>
				<th class="hikaauctions_auctions_validation_title titletoggle"><?php
					echo JHTML::_('grid.sort', JText::_('HKA_AUCTION_VALIDATION'), 'product.product_auction', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaauctions_auctions_sales_title titletoggle"><?php
					echo JText::_('HKA_AUCTION_SALES');
				?></th>
				<th class="hikaauctions_auctions_end_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HKA_AUCTION_END'), 'product.product_sale_end', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaauctions_auctions_published_title titletoggle"><?php
					echo JHTML::_('grid.sort', JText::_('HKA_PUBLISHED'), 'product.product_published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikaauctions_auctions_id_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HKA_ID'), 'product.product_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$now = time();

$k = 0;
$i = 0;
foreach($this->rows as &$row) {
	$auction_status = '';
	$auction_validation = '';
	if($row->product_auction == 1) {
		if($now > (int)$row->product_sale_start) {
			$auction_status = 'progress-current';
		}

		if(!empty($row->prices)) {
			foreach($row->prices as $p) {
				if((int)$p->price_min_quantity > 1 && (int)$p->price_min_quantity < (int)@$row->auction_quantity) {
					$auction_validation = 'progress-finish';
				}
			}
		}
	} else {
		$auction_status = 'progress-finish';
		$auction_validation = 'progress-error';
		if($row->product_auction == 2)
			$auction_validation = 'progress-finish';
	}
?>
			<tr class="row<?php echo $k;?>">
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
				<td align="center"><?php
					echo JHTML::_('grid.id', $i, $row->product_id);
				?></td>
				<td>
					<a href="<?php echo hikaauction::completeLink('shop.product&task=edit&cid='.$row->product_id); ?>"><?php
						echo $this->escape($row->product_name);
					?></a><br/><em><?php echo $this->escape($row->product_code);?></em>
				</td>
				<td align="center">
					<span class="auction-progress-icon <?php echo $auction_status; ?>"></span>
				</td>
				<td align="center">
					<span class="auction-progress-icon <?php echo $auction_validation; ?>"></span>
				</td>
				<td><?php
					echo (int)@$row->auction_quantity;
				?></td>
				<td><?php
					echo hikashop_getDate($row->product_sale_end);
					if($row->product_sale_end > $now)
						echo '<br/>' . JText::sprintf('HIKA_AUCTION_END_IN_X', hikaauction::timeCounter($now, $row->product_sale_end));
				?></td>
				<td align="center"><?php
					echo $this->toggleHelper->display('', $row->product_published);
				?></td>
				<td width="1%" align="center"><?php
					echo $row->product_id;
				?></td>
			</tr>
<?php
	$k = 1 - $k;
	$i++;
}
unset($row);
?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo HIKAAUCTION_COMPONENT; ?>" />
	<input type="hidden" name="task" value="<?php echo @$this->task; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
