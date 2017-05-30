<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('user&task=listing'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="hikamarket_user_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_user_listing_search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_user_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_user_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
			<div class="expand-filters" style="width:auto;float:right">
<?php }

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
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="hikamarket_user_name_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_USER_NAME'), 'juser.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_user_login_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_USERNAME'), 'juser.username', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_user_email_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_EMAIL'), 'hkuser.user_email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
<?php
if(!empty($this->fields)) {
	foreach($this->fields as $field) {
?>
				<th class="hikamarket_user_<?php echo $field->field_namekey; ?>_title title"><?php
					echo JHTML::_('grid.sort', $this->fieldsClass->trans($field->field_realname), 'hkuser.'.$field->field_namekey, $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value );
				?></th>
<?php
	}
}
?>
				<th class="hikamarket_user_id_title title">
					<?php echo JHTML::_('grid.sort', JText::_( 'ID' ), 'hkuser.user_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value ); ?>
				</th>
			</tr>
		</thead>
<?php if(!isset($this->embbed)) {
	$columns = 4 + count($this->fields);
?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $columns; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
<?php } ?>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach($this->rows as $user) {
	$rowId = 'market_user_'.$user->user_id;
	if($this->manage)
		$url = hikamarket::completeLink('user&task=show&cid='.$user->user_id);
?>
			<tr class="row<?php echo $k; ?>" id="<?php echo $rowId; ?>">
				<td class="hikamarket_user_name_value"><?php
					if(!empty($url))
						echo '<a href="'.$url.'"><img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" alt="'.JText::_('EDIT').'" style="vertical-align:middle;margin-right:5px;"/>';
					if(!empty($user->name))
						echo $user->name;
					else
						echo '<em>'.JText::_('HIKAM_GUEST_USER').'</em>';
					if(!empty($url))
						echo '</a>';
				?></td>
				<td class="hikamarket_user_login_value"><?php
					if(!empty($user->username))
						echo $user->username;
					else
						echo '-';
				?></td>
				<td class="hikamarket_user_email_value"><?php echo @$user->user_email; ?></td>
<?php
if(!empty($this->fields)) {
	foreach($this->fields as $field) {
		$namekey = $field->field_namekey;
?>
				<td class="hikamarket_user_<?php echo $namekey; ?>_value"><?php
					echo $this->fieldsClass->show($field, $user->$namekey);
				?></td>
<?php
	}
}
?>
				<td class="hikamarket_user_id_value"><?php echo $user->user_id; ?></td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
