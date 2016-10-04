<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>
<div class="cmgroupbuying">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		<form action="<?php echo JRoute::_('index.php?option=com_cmgroupbuying'); ?>" method="post" name="adminForm" id="adminForm">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_NAME_LABEL'); ?>
						</th>
						<th>
							<?php echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_SUBJECT_LABEL'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($this->items as $i => $item) :
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&task=mailtemplate.edit&id='.(int) $item->id); ?>">
							<?php
							switch($item->name)
							{
								case 'pay_buyer':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_PAY_BUYER');
									break;
								case 'pay_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_PAY_PARTNER');
									break;
								case 'coupon_for_buyer':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_COUPON_FOR_BUYER');
									break;
								case 'coupon_for_friend':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_COUPON_FOR_FRIEND');
									break;
								case 'void_buyer':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_VOID_BUYER');
									break;
								case 'void_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_VOID_PARTNER');
									break;
								case 'late_pay_buyer':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_LATE_BUYER');
									break;
								case 'late_pay_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_LATE_PARTNER');
									break;
								case 'tip_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_TIP_PARTNER');
									break;
								case 'cash_buyer':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_CASH_BUYER');
									break;
								case 'approve_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_APPROVE_PARTNER');
									break;
								case 'pending_admin':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_PENDING_ADMIN');
									break;
								case 'approve_coupon_partner':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_APPROVE_COUPON_PARTNER');
									break;
								case 'pending_coupon_admin':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_PENDING_COUPON_ADMIN');
									break;
								case 'cash_admin':
									echo JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_CASH_ADMIN');
									break;
							}
							?>
							</a>
						</td>
						<td>
							<?php echo $item->subject; ?>
						</td>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div>
				<input type="hidden" name="view" value="mailtemplates" />
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>