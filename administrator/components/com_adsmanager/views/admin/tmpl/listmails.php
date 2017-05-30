<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

JHtml::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
  <div style="float:right">
  <?php if(version_compare(JVERSION, '3.0', 'ge')) {
	echo $this->pagination->getLimitBox();
	} ?>
  </div>
  <div style="clear:both"></div>
</div>
<table class="adminlist table table-striped" id="itemsList">
<thead>
<tr>
<?php if (version_compare(JVERSION,'2.5.0','>=')) { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
    <?php } else { ?>
    <th width="3%" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->list); ?>);" />
    <?php } ?>
<th width="2%" class="hidden-phone"><?php echo JText::_('Id') ?></th>
<th width="30%"><?php echo JText::_('ADSMANAGER_TH_SUBJECT'); ?></th>
<th width="40%"><?php echo JText::_('ADSMANAGER_TH_BODY');?></th>
<th><?php echo JText::_('ADSMANAGER_TH_RECIPIENT');?></th>
<th><?php echo JText::_('ADSMANAGER_TH_STATUT');?></th>
<th><?php echo JText::_('ADSMANAGER_TH_ACTION');?></th>
</tr>
</thead>
<tbody>
<?php
$num = 0;
$orders = array();
foreach ($this->list as $key => $row) {
	 ?>
	 <tr class="row<?php echo ($num & 1); ?>" item-id="<?php echo $row->id;?>" >
	 <td class="hidden-phone"><input type="checkbox" id="cb<?php echo $num;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>

	<td class="hidden-phone"><?php echo $row->id; ?></td>
	<td>
		<a href="<?php echo "index.php?option=com_adsmanager&c=mails&task=edit&id=".$row->id ?>"><?php echo $row->subject ?></a>
	</td>
	<td><?php echo $row->body ?></td>
    <td align='center'><?php echo $row->recipient; ?></td>
    <td align='center'>
        <?php
            switch($row->statut) {
                case 0: echo '<span style="color: red">'.JText::_('ADSMANAGER_MAIL_STATUT_NSENT').'</span>';
                    break;
                case 1: echo '<span style="color: green">'.JText::_('ADSMANAGER_MAIL_STATUT_SENT').'</span>';
                    break;
                default : echo 'Error';
                    break;
            }
        ?>
    </td>
	<td align='center'>
        <?php if(!$row->statut): ?>
            <a href="<?php echo "index.php?option=com_adsmanager&c=mails&task=send&id=".$row->id ?>">
                <?php echo JText::_('ADSMANAGER_SEND_MAIL'); ?>
            </a>
        <?php endif; ?>
    </td>
	</tr>
	<?php
	$num++;
}
?>
</tbody>
<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>

<input type="hidden" name="filter_order" id="filter_order" value="id" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="asc" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="mails" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form> 