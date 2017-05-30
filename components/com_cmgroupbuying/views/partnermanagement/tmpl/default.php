<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/common.php");

JFactory::getDocument()->addStyleSheet('components/com_cmgroupbuying/assets/css/style.css');

$app			= JFactory::getApplication();
$jinput			= $app->input;
$pageAddress	= CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
$permissions	= array(
	'view_deal_list'			=> $this->settings['partner_view_deal_list'],
	'submit_new_deal'			=> $this->settings['partner_submit_new_deal'],
	'check_coupon_status'		=> $this->settings['partner_check_coupon_status'],
	'change_coupon_status'		=> $this->settings['partner_change_coupon_status'],
	'view_coupon_list'			=> $this->settings['partner_view_coupon_list'],
	'view_buyer_info'			=> $this->settings['partner_view_buyer_info'],
	'view_commission_report'	=> $this->settings['partner_view_commission_report'],
	'edit_profile'				=> $this->settings['partner_edit_profile'],
	'view_free_coupon_list'		=> $this->settings['partner_view_free_coupon_list'],
	'submit_new_free_coupon'	=> $this->settings['partner_submit_new_free_coupon'],
);

// Default settings
$this->return		= base64_encode($pageAddress);
$this->pageAddress	= $pageAddress;
$this->permissions	= $permissions;
$this->menu			= CMGroupBuyingHelperPartnerManagement::buildMenu($this->permissions, $this->navigation);
$this->welcome		= $this->settings['partner_welcome'];
$this->footer		= $this->settings['partner_footer'];
?>
<div class=cm-management>
<?php
if($this->user->guest):
	echo $this->loadTemplate('login');
elseif(!$this->user->guest):
	if(empty($this->partner)):
		echo $this->loadTemplate('error');
	else:
?>
	<div class="cm-management-management">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a href="<?php echo $this->pageAddress; ?>" class="brand"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_PAGE_HEADER'); ?></a>
					<form class="form-inline" id="login-form" method="post" action="<?php echo JRoute::_('index.php', true); ?>">
						<p class="logout-button pull-right"><button class="btn btn-danger btn-small"><?php echo JText::_('JLOGOUT'); ?></button></p>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.logout" />
						<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
					<p class="navbar-text pull-right"><?php echo JText::sprintf('COM_CMGROUPBUYING_MANAGEMENT_LOGGED_IN_MESSAGE', '<span class="user_name">' . $this->user->username . '</span'); ?></p>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
					<div class="well sidebar-nav">
						<?php echo $this->menu; ?>
					</div>
				</div>
				<div class="span9">
					<?php if(JFactory::getDocument()->getBuffer('message')): ?>
					<?php echo JFactory::getDocument()->getBuffer('message'); ?>
					<?php else: ?>
					<div id="system-message-container">
						<div id="system-message"></div>
					</div>
					<?php endif; ?>
<?php
		switch($this->navigation)
		{
			case 'deal_list':
				if($this->permissions['view_deal_list'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$this->deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealsOfPartner($this->partner['id']);
					echo $this->loadTemplate('deal_list');
				}
				break;

			case 'deal_submission':
				if($this->permissions['submit_new_deal'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$this->form  = $this->get('Form');
					$this->item = $this->get('Item');
					$this->state = $this->get('State');

					$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.partnermanagement.data');

					// Get the deal's locations when editting deal
					// Get the deal's options when editting deal
					$locationsOfDeal = array();
					$optionsOfDeal = array();

					if(empty($this->item->id) || $this->item->id == 0)
					{
						$configuration = JModelLegacy::getInstance('Configuration',
								'CMGroupBuyingModel')->getConfiguration('coupon_background, coupon_elements');
						$this->form->setValue('coupon_path', null, $configuration['coupon_background']);
						$this->form->setValue('coupon_elements', null, $configuration['coupon_elements']);
					}
					else
					{
						// Check is the current partner is the owner of the deal
						if($this->item->partner_id != $this->partner['id'])
						{
							$message = JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_NOT_OWNDER');
							$type = 'error';
							$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
							$app->enqueueMessage($message, $type);
							$app->redirect($redirectUrl);
						}

						// Check if the deal was approved already
						if(CMGroupBuyingHelperDeal::generateDealStatus($this->item->id) != JText::_('COM_CMGROUPBUYING_DEAL_STATUS_PENDING'))
						{
							$message = JText::_('COM_CMGROUPBUYING_DEAL_SUBMISSION_ALREADY_APPROVED');
							$type = 'error';
							$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
							$app->enqueueMessage($message, $type);
							$app->redirect($redirectUrl);
						}
					}

					if($this->item->id != 0)
					{
						$optionsOfDeal = JModelLegacy::getInstance('DealOption','CMGroupBuyingModel')->getOptions($this->item->id);
					}
					else
					{

					for($i=1; $i<=10; $i++)
						{
							$option = array(
								"deal_id" => 0,
								"option_id" => $i,
								"name" => isset($data["option_name_" . $i]) ? $data["option_name_" . $i] : '',
								"original_price" => isset($data["option_original_price_" . $i]) ? $data["option_original_price_" . $i] : '',
								"price" => isset($data["option_price_" . $i]) ? $data["option_price_" . $i] : '',
							);

							$optionsOfDeal[$i] = $option;
						}
					}

					for($i = 1; $i<= 10; $i++)
					{
						if(!isset($optionsOfDeal[$i]['name']))
							$optionsOfDeal[$i]['name'] = '';

						if(!isset($optionsOfDeal[$i]['original_price']))
							$optionsOfDeal[$i]['original_price'] = '';

						if(!isset($optionsOfDeal[$i]['price']))
							$optionsOfDeal[$i]['price'] = '';
					}

					$this->optionsOfDeal = $optionsOfDeal;
					$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
					echo $this->loadTemplate('deal_submission');
				}
				break;

			case 'coupon_status':
				if($this->permissions['check_coupon_status'] == false):
					echo $this->loadTemplate('access_denied');
				else:
					echo $this->loadTemplate('coupon_status');
				endif;
				break;

			case 'coupon_list':
				if($this->permissions['view_coupon_list'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$dealIdFilter = $jinput->post->get('filter_deal', 0, 'integer');
					$statusFilter = $jinput->post->get('filter_status', -1, 'integer');

					JFactory::getApplication()->setUserState("cmgroupbuying.partner_deal_filter", $dealIdFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.partner_status_filter", $statusFilter);

					$this->deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(false, false, false, null, $this->partner['id'], null, true, "name ASC", false);
					$this->coupons = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getLimit($this->partner['id']);
					$this->pageNav = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getPagination($this->partner['id']);
					echo $this->loadTemplate('coupon_list');
				}
				break;

			case 'commission_report':
				if($this->permissions['view_commission_report'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$dealIdFilter = $jinput->post->get('filter_deal', 0, 'integer');
					$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealsOfPartner($this->partner['id']);
					$dealsFilter = $deals;
					JFactory::getApplication()->setUserState("cmgroupbuying.commission_deal_filter", $dealIdFilter);

					if($dealIdFilter > 0)
					{
						$deals = array();
						$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealIdFilter);

						if(!empty($deal))
							$deals[] = $deal;
					}

					$this->deals = $deals;
					$this->dealIdFilter = $dealIdFilter;
					$this->dealsFilter = $dealsFilter;

					$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
					echo $this->loadTemplate('commission_report');
				}
				break;

			case 'profile':
				if($this->permissions['edit_profile'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$model = JModelLegacy::getInstance('PartnerProfile', 'CMGroupBuyingModel');
					$this->form = $model->getForm();
					$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.partnerprofile.data');
					echo $this->loadTemplate('profile');
				}
				break;

			case 'free_coupon_list':
				if($this->permissions['view_free_coupon_list'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$this->coupons = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getFreeCouponsOfPartner($this->partner['id']);
					echo $this->loadTemplate('free_coupon_list');
				}
				break;

			case 'free_coupon_submission':
				if($this->permissions['submit_new_free_coupon'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$model = JModelLegacy::getInstance('FreeCouponSubmission','CMGroupBuyingModel');
					$this->form  = $model->getForm();
					$this->item = $model->getItem();
					$this->state = $model->getState();

					$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.freecouponsubmission.data');

					if(!empty($this->item->id) && $this->item->id > 0)
					{
						// Check is the current partner is the owner of the coupon
						if($this->item->partner_id != $this->partner['id'])
						{
							$message = JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_NOT_OWNDER');
							$type = 'error';
							$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
							$app->enqueueMessage($message, $type);
							$app->redirect($redirectUrl);
						}

						// Check if the deal was approved already
						if(CMGroupBuyingHelperFreeCoupon::generateFreeCouponStatus($this->item->id) != JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_PENDING'))
						{
							$message = JText::_('COM_CMGROUPBUYING_FREE_COUPON_SUBMISSION_ALREADY_APPROVED');
							$type = 'error';
							$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
							$app->enqueueMessage($message, $type);
							$app->redirect($redirectUrl);
						}
					}

					$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
					echo $this->loadTemplate('free_coupon_submission');
				}
				break;

			default:
				$stats = CMGroupBuyingHelperPartnerManagement::getPartnerStats($this->partner['id']);
				$this->numOfDeals = $stats['numOfDeals'];
				$this->numOfCoupons = $stats['numOfCoupons'];
				$this->earning = $stats['earning'];
				echo $this->loadTemplate('dashboard');
				break;
		}
?>
				</div>
			</div>
		</div>
		<hr>
		<footer>
			<div class="container-fluid">
				<div class="span12"><?php echo $this->footer; ?></div>
			</div>
		</footer>
	</div>
<?php
	endif;
endif;
?>
</div>
