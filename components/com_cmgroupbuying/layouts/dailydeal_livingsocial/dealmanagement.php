<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
$count = 1;
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<?php if(empty($this->deals)): ?>
<?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_NO_DEAL_FOUND_MESSAGE'); ?>
<?php else: ?>
<div class="align_right">
	<a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=dealsubmission'); ?>"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_NEW_DEAL'); ?></a>
</div>
<table class="cmtable">
	<tr>
		<th><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_ID'); ?></th>
		<th><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_NAME'); ?></th>
		<th><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_STATUS'); ?></th>
		<th><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_BOUGHT'); ?></th>
		<th><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_DEAL_ACTION'); ?></th>
	</tr>
<?php foreach($this->deals as $deal): ?>
<?php
$status = CMGroupBuyingHelperDeal::generateDealStatus($deal['id']);

if($count % 2 == 0):
	echo '<tr class="even">';
else:
	echo '<tr>';
endif;

$count++;
?>
		<td class="align_center"><?php echo $deal['id']; ?></td>
		<td><?php echo $deal['name']; ?></td>
		<td class="align_center"><?php echo $status; ?></td>
		<td class="align_center"><?php echo CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']); ?></td>
		<?php if($status == JText::_('COM_CMGROUPBUYING_DEAL_STATUS_PENDING')): ?>
		<td class="align_center"><a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=dealsubmission&id=' . $deal['id']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_EDIT_DEAL'); ?></a></td>
		<?php else: ?>
		<td class="align_center"><a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']); ?>"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MANAGEMENT_VIEW_DEAL'); ?></a></td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>