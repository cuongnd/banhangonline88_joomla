<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('plugin&plugin_type'.$this->type.'&task=listing'); ?>" method="post" id="adminForm" name="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td style="width:100%;">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="hikamarket_plugin_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_plugin_listing_search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span7">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_plugin_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_plugin_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span5">
			<div class="expand-filters" style="width:auto;float:right">
<?php }

if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php } ?>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="hikamarket_order_num_title title titlenum"><?php
					echo JText::_('HIKA_NUM');
				?></th>
				<th class="title"><?php
					echo JText::_('HIKA_NAME');
				?></th>
<?php
	$cols = 4;
	if(!empty($this->listing_columns)) {
		foreach($this->listing_columns as $key => $column) {
			$cols++;
?>				<th class="title"><?php echo JText::_($column['name']);?></th>
<?php
		}
	}
?>
				<th class="title"><?php echo JText::_('HIKA_TYPE');?></th>
				<th class="title" style="width:1%;"><?php echo JText::_('HIKA_ACTIONS');?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$p_id = $this->type.'_id';
$p_name = $this->type.'_name';
$p_order = $this->type.'_ordering';
$p_published = $this->type.'_published';
$p_type = $this->type.'_type';

$publish_type = 'plugin';
if(in_array($this->type, array('payment', 'shipping')))
	$publish_type = $this->type;

$k = 0;
$i = 0;
foreach($this->plugins as $plugin) {
	$id = 'market_plugin_' . $this->type.'_' . $plugin->$p_id;
	$published_id = $this->type.'_published-' . $plugin->$p_id;
?>
			<tr class="row<?php echo $k;?>" id="<?php echo $id;?>">
				<td align="center"><?php
					echo $i+1;
				?></td>
				<td>
					<a href="<?php echo hikamarket::completeLink('plugin&plugin_type='.$this->type.'&task=edit&name='. $plugin->$p_type .'&cid='.$plugin->$p_id.$this->url_itemid);?>"><?php
						echo $plugin->$p_name;
						if(empty($plugin->$p_name))
							echo '<em>' . JText::_('NO_NAME') . '</em>';
					?></a>
				</td>
<?php
		if(!empty($this->listing_columns)) {
			foreach($this->listing_columns as $key => $column) {
?>				<td><?php
					if(isset($column['col'])) {
						$col = $column['col'];
						echo @$plugin->$col;
					}
				?></td>
<?php
		}
	}
?>
				<td><?php
					if(!empty($currentPlugin))
						echo $currentPlugin->name;
					else
						echo $plugin->$p_type;
				?></td>
				<td align="center"><?php
	if($this->plugin_action_publish) {
		echo $this->toggleClass->toggle($published_id, (int)$plugin->$p_published, $publish_type);
	} else {
		echo $this->toggleClass->display('', (int)$plugin->$p_published);
	}

	if($this->plugin_action_delete) {
		echo $this->toggleClass->delete($id, (int)$plugin->$p_id . '-' . $plugin->$p_type, $publish_type, true);
	}
				?></td>
			</tr>
<?php
	$k = 1-$k;
	$i++;
}
?>
		</tbody>
	</table>
</form>
