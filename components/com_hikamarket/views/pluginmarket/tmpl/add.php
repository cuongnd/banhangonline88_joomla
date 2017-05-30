<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('plugin&task=add&plugin_type='.$this->plugin_type); ?>" method="post" name="hikamarket_form" id="hikamarket_form">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="hikamarket_plugins_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="inputbox"/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_plugins_listing_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span7">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_plugins_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="inputbox"/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_plugins_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span5">
			<div class="expand-filters" style="width:auto;float:right">
<?php }

	if($this->plugin_type == 'payment') {
		$values = array(
			JHTML::_('select.option', 0, JText::_('HIKA_ALL'))
		);
		foreach($this->currencies as $currency) {
			$values[] = JHTML::_('select.option', (int)$currency->currency_id, $currency->currency_symbol.' '.$currency->currency_code);
		}
		echo JHTML::_('select.genericlist', $values, 'filter_currency', 'onchange="this.form.submit();"', 'value', 'text', @$this->pageInfo->filter->currency);
	}

	if($this->vendor->vendor_id <= 1) {
		$values = array(
			JHTML::_('select.option', -1, JText::_('HIKA_ALL')),
			JHTML::_('select.option', 1, JText::_('HIKA_PUBLISHED')),
			JHTML::_('select.option', 0, JText::_('HIKA_UNPUBLISHED'))
		);
		if(!isset($this->pageInfo->filter->publish))
			$this->pageInfo->filter->publish = -1;
		echo JHTML::_('select.genericlist', $values, 'filter_publish', 'onchange="this.form.submit();"', 'value', 'text', $this->pageInfo->filter->publish);
	}

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
</form>
<table class="adminlist table table-striped table-hover" cellpadding="1">
	<thead>
		<tr>
			<th class="title titlenum"><?php
				echo JText::_('HIKA_NUM');
			?></th>
			<th class="title"><?php
				echo JText::_('HIKA_NAME');
			?></th>
<?php
	if(!empty($this->currencies)) {
		foreach($this->currencies as $currency) {
?>			<th class="title"><?php
				echo @$currency->currency_code;
			?></th>
<?php
		}
	}

	if($this->vendor->vendor_id <= 1) {
?>
			<th class="title titletoggle"><?php
				echo JText::_('HIKA_ENABLED');
			?></th>
<?php } ?>
		</tr>
	</thead>
	<tbody>
<?php
$k = 0;

foreach($this->plugins as $i => &$row) {

?>
		<tr class="row<?php echo $k; ?>">
			<td align="center"><?php
				echo $i+1
			?></td>
			<td>
				<a href="<?php echo hikamarket::completeLink('plugin&task=edit&name='.$row->element.'&plugin_type='.$this->plugin_type.'&subtask=edit');?>"><?php
					$translation_key = 'PLG_HIKASHOP'.strtoupper($this->plugin_type).'_'.strtoupper($row->element);
					if($translation_key != JText::_($translation_key))
						echo JText::_($translation_key);
					else
						echo $row->name;
				?></a>
			</td>
<?php
	if(!empty($this->currencies)) {
		foreach($this->currencies as $currency) {
?>			<td align="center"><?php
				if(empty($row->accepted_currencies) || in_array($currency->currency_code, $row->accepted_currencies))
					echo $this->toggleClass->display(null, 1);
				else
					echo $this->toggleClass->display(null, 0);
			?></td>
<?php
		}
	}

	if($this->vendor->vendor_id <= 1) {
?>
			<td align="center"><?php
				echo $this->toggleClass->display('', $row->published);
			?></td>
		</tr>
<?php
	}

	$k = 1-$k;
}
?>
	</tbody>
</table>
