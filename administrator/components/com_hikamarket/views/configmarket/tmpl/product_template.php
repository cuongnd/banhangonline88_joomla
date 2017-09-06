<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset class="adminform">
	<legend><?php echo JText::_('PRODUCT_TEMPLATES'); ?></legend>
	<div style="float:right">
		<a class="btn" href="<?php echo hikamarket::completeLink('product&task=new_template&'.hikamarket::getFormToken().'=1');?>">
			<img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/add.png" style="vertical-align:middle;margin:0px;"/> <?php echo JText::_('ADD');?>
		</a>
	</div>
	<table class="adminlist table table-striped table-hover" style="cell-spacing:1px">
		<thead>
			<tr>
				<th class="title titlenum"><?php
					echo JText::_('HIKA_NUM');
				?></th>
				<th class="title titlenum"><?php
					echo JText::_('HIKA_EDIT');
				?></th>
				<th class="title"><?php
					echo JText::_('HIKA_NAME');
				?></th>
				<th class="title" style="width:100px;"><?php
					echo JText::_('HIKAM_TEMPLATE_USED_BY');
				?></th>
				<th class="title titlenum"><?php
					echo JText::_('HIKA_DELETE');
				?></th>
			</tr>
		</thead>
		<tbody>
<?php
	$config_default_template = (int)$this->config->get('default_template_id', 0);

	$k = 0;
	if(!empty($this->product_templates)) {
		foreach($this->product_templates as $i => $product_template) {
?>
			<tr class="row<?php echo $k; ?>">
				<td style="text-align:center"><?php
					echo $product_template->product_id;
				?></td>
				<td style="text-align:center">
					<a href="<?php echo hikamarket::completeLink('shop.product&task=edit&cid='.$product_template->product_id); ?>"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/edit.png" alt="<?php echo JText::_('HIKA_EDIT');?>"/></a>
				</td>
				<td>
					<a href="<?php echo hikamarket::completeLink('shop.product&task=edit&cid='.$product_template->product_id); ?>"><?php
						if(!empty($product_template->product_name))
							echo $product_template->product_name;
						else
							echo '<em>' . JText::_('PRODUCT_NO_NAME') . '</em>';
					?></a>
					[ <?php echo $product_template->product_code; ?> ]
				</td>
				<td style="text-align:center"><?php
					if((int)$product_template->vendor_count > 0)
						echo JText::sprintf('HIKAM_USED_BY_X_VENDORS', $product_template->vendor_count);
					else if((int)$product_template->product_id == $config_default_template)
						echo JText::_('HIKAM_DEFAULT_TEMPLATE');
					else
						echo '-';
				?></td>
				<td style="text-align:center">
					<a onclick="if(!confirm('<?php echo str_replace("'","\'", JText::_('ASK_DELETE_PRODUCT_TEMPLATE')); ?>')) { return false; }" href="<?php echo hikamarket::completeLink('shop.product&task=remove&cid='.$product_template->product_id.'&'.hikamarket::getFormToken().'=1'); ?>"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('HIKA_DELETE');?>"/></a>
				</td>
			</tr>
<?php
			$k = 1-$k;
		}
	}
?>
		</tbody>
	</table>
</fieldset>
