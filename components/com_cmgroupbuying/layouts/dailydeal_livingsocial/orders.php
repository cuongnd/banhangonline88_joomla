<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$orders = $this->orders;
$configuration = $this->configuration;
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?> 
<div class="page_title">
	<p><?php echo $this->pageTitle; ?></p>
</div>
<?php
if(empty($orders)):
	echo JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
else:
?>
<form action="<?php echo JRoute::_('index.php', false); ?>" method="post">
	<input type="hidden" name="option" id="option" value="com_cmgroupbuying"/>
	<input type="hidden" name="view" value="orders" />
	<table class="cmtable">
		<tr>
			<th><?php echo JText::_('COM_CMGROUPBUYING_ORDER_ID'); ?></th>
			<th><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE'); ?></th>
			<th><?php echo JText::_('COM_CMGROUPBUYING_ORDER_STATUS'); ?></th>
		</tr>
		<?php
		$count = 1;

		foreach($orders as $order):
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=order&id=' . $order['id']);

			if($count % 2 == 0):
				echo '<tr class="even">';
			else:
				echo '<tr>';
			endif;

			$count++;
		?>
			<td class="align_center"><a href="<?php echo $link; ?>"><?php echo $order['id']; ?></a></td>
			<td class="align_center"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($order['value'], true, $configuration); ?></td>
			<td class="align_center">
				<?php
				switch($order['status'])
				{
					case 0:
						echo JText::_('COM_CMGROUPBUYING_ORDER_UNPAID_ORDER');
						break;
					case 1:
						echo JText::_('COM_CMGROUPBUYING_ORDER_PAID_ORDER');
						break;
					case 2:
						echo JText::_('COM_CMGROUPBUYING_ORDER_LATE_PAID_ORDER');
						break;
					case 3:
						echo JText::_('COM_CMGROUPBUYING_ORDER_DELIVERED_ORDER');
						break;
					case 4:
						echo JText::_('COM_CMGROUPBUYING_ORDER_REFUNDED_ORDER');
						break;
				}
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<div class="cmgroupbuying_pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
</form>
<?php endif; ?>