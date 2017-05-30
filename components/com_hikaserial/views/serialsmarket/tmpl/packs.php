<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('serials&task=packs'); ?>" method="post" id="adminForm" name="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="hikamarket_serials_packs_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_serials_packs_listing_search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_serials_packs_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_serials_packs_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
			<div class="expand-filters" style="width:auto;float:right">
<?php }
					if(!empty($this->vendorType))
						echo $this->vendorType->display('filter_vendors', @$this->pageInfo->filter->vendors);
if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else {?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php } ?>
<?php
if(!empty($this->packs)) {
	$acls = array(
		'pack/edit' => hikamarket::acl('plugins/hikaserial/pack/edit'),
		'pack/publish' => hikamarket::acl('plugins/hikaserial/pack/edit/publish'),
		'product/edit' => hikamarket::acl('product/edit'),
		'serial/import' => hikamarket::acl('plugins/hikaserial/serial/import'),
	);
	foreach($this->packs as $pack) {
		$publish_icon = ($pack->pack_published ? '' : 'un') . 'publish.png';
		$publish_text = ($pack->pack_published ? 'HIKA_PUBLISHED' : 'HIKA_UNPUBLISHED');

		$pack->edit = $acls['pack/edit'] && ($this->vendor->vendor_id <= 1 || (int)$pack->pack_vendor_id == $this->vendor->vendor_id)
?>
	<table class="table table-bordered<?php echo HIKASHOP_RESPONSIVE ? '' : ' hikaserial_pack_listing_table'; ?>" style="width:100%">
		<tr>
			<td colspan="2" class="hikaserial_pack_header">
				<span style="float:right">
<?php
		if($acls['serial/import'] && $pack->edit) {
?>
				<a class="btn btn-default" href="<?php echo hikamarket::completeLink('serials&task=import&pack_id='.(int)$pack->pack_id); ?>"><img src="<?php echo HIKASERIAL_IMAGES; ?>icon-16/import.png" alt="" style="vertical-align:top;margin:0 3px 0 0;padding:0px;"/><?php echo JText::_('IMPORT'); ?></a>
<?php
		}
?>
					<img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/<?php echo $publish_icon; ?>" data-toggle="hk-tooltip" data-title="<?php echo JText::_($publish_text, true); ?>" alt=""/>
				</span>
				<h4><?php
					if($pack->edit)
						echo '<a href="'.hikamarket::completeLink('serials&task=pack&cid='.$pack->pack_id).'">';

					if(!empty($pack->pack_name))
						echo $pack->pack_name;
					else
						echo '<em>'.JText::_('HIKASERIAL_NO_NAME').'</em>';

					if($pack->edit)
						echo '<img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" style="vertical-align:middle;padding-left:3px;margin:0px;" alt=""/></a>';
				?></h4>
<?php
		if(!empty($pack->pack_description)) {
?>
				<div class="hikaserial_pack_description"><?php
					echo JHTML::_('content.prepare',preg_replace('#<hr *id="system-readmore" */>#i', '', $pack->pack_description));
				?></div>
<?php
		}
?>
			</td>
		</tr>
		<tr>
			<td style="width:50%">
<?php
		if(!empty($pack->products)) {
			$stats = hikamarket::acl('plugins/hikaserial/serials/listing');
			$export = hikamarket::acl('plugins/hikaserial/serials/export');
?>
	<table class="table table-bordered table-striped" style="margin:0;padding:0;width:100%">
		<thead>
			<tr>
				<th><?php echo JText::_('PRODUCT_NAME'); ?></th>
				<th><?php echo JText::_('PRODUCT_QUANTITY'); ?></th>
<?php if($stats || $export) { ?>
				<th></th>
<?php } ?>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($pack->products as $product) {
?>
			<tr>
				<td><?php
					if($acls['product/edit'])
						echo '<a href="'.hikamarket::completeLink('product&task=edit&cid='.$product->product_id).'">';
					echo $this->escape($product->product_name);
					if($acls['product/edit'])
						echo '</a>';
				?></td>
				<td><?php echo (int)$product->quantity; ?></td>
<?php
				if($stats || $export) {
					$stats_link = hikamarket::completeLink('serials&task='.($stats ? 'stats':'export').'&pack_id='.(int)$pack->pack_id.'&product_id='.$product->product_id);
?>
				<td style="text-align:center">
					<a href="<?php echo $stats_link; ?>"><img src="<?php echo HIKASERIAL_IMAGES; ?>icon-16/report.png" alt="&raquo;" style="margin:0;padding0;"/></a>
				</td>
<?php
				}
?>
			</tr>
<?php
			}
?>
		</tbody>
	</table>
<?php
		} else {
?>
	<div class="hikaserial_empty_msg"><?php
		echo JText::_('NOTICE_NO_PRODUCTS');
	?></div>
<?php
		}
?>
			</td>
			<td style="width:50%">
<?php
		if(!empty($pack->serials)) {
?>
	<table class="table table-bordered table-striped" style="margin:0;padding:0;width:100%">
		<thead>
			<tr>
				<th><?php echo JText::_('SERIAL_STATUS'); ?></th>
				<th><?php echo JText::_('PRODUCT_QUANTITY'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td><?php echo JText::_('TOTAL_SERIALS'); ?></td>
				<td><?php echo (int)$pack->total_serials; ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
			foreach($pack->serials as $serial) {
?>
			<tr>
				<td><?php echo $this->escape($serial->serial_status); ?></td>
				<td><?php echo (int)$serial->count; ?></td>
			</tr>
<?php
			}
?>
		</tbody>
	</table>
<?php
		} else {
?>
	<div class="hikaserial_empty_msg"><?php
		echo JText::_('NOTICE_NO_SERIALS');
	?></div>
<?php
		}
?>
			</td>
		</tr>
	</table>


<?php
	}
?>
	<div class="hikamarket_pagination"><?php
		echo $this->pagination->getListFooter();
		echo $this->pagination->getResultsCounter();
	?></div>
<?php
} else {
?>
	<div class="hikaserial_empty_msg"><?php
		echo JText::_('NOTICE_NO_PACKS');
	?></div>
<?php
}
?>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="packs" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
