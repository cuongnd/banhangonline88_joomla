<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT . '/helpers/common.php';

class CMGroupBuyingControllerOrder extends JControllerLegacy
{
	public function notify()
	{
		$app		= JFactory::getApplication();
		$jinput		= $app->input;
		$paymentId	= JFactory::getApplication()->input->get('gateway', '', 'word');
		$validation	= false;

		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('cmpayment');
		$results = JFactory::getApplication()->triggerEvent('onCMPaymentValidate');

		foreach ($results as $r)
		{
			if ($r['payment_id'] == $paymentId && $r['validation'] === true)
			{
				$validation	= true;
				$result		= $r;
			}
		}

		if ($validation)
		{
			if ($paymentId == 'dineromail')
			{
				foreach ($result['order_ids'] as $orderId)
				{
					$data = array(
						'order_id'			=> $orderId,
						'transaction_info'	=> $result['transaction_info']
					);
					$this->update($data);
				}
			}
			else
			{
				$this->update($result);
			}
		}

		// Default message and message type, they are overriden when transaction fails.
		$message= JText::_('COM_CMGROUPBUYING_SUCCESSFUL_TRANSACTION');
		$type	= '';

		$failureMessage = JText::_('COM_CMGROUPBUYING_FAILED_TRANSACTION');

		// Special stuff for eWAY payment gateway.
		if ($paymentId == 'eway')
		{
			$paymentCode = $jinput->post->get('AccessPaymentCode', '');

			if ($paymentCode != '')
			{
				$params		= $this->getPluginParams('eway');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == true)
				{
					$message= JText::_('COM_CMGROUPBUYING_EWAY_SUCCESSFUL_TRANSACTION');
					$type	= '';
				}
				else
				{
					$message= JText::sprintf('COM_CMGROUPBUYING_EWAY_FAILED_TRANSACTION', $result['transaction_info']['responseCode']);
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for 2CheckOut payment gateway.
		if ($paymentId == 'twocheckout')
		{
			$sid = $jinput->get('sid', '');

			if ($sid != '')
			{
				$params		= $this->getPluginParams('twocheckout');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for Cardcom payment gateway.
		if ($paymentId == 'cardcom')
		{
			$lowProfileCode = $jinput->get('lowprofilecode', '');

			if ($lowProfileCode != '')
			{
				$params		= $this->getPluginParams('cardcompay');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for VivaPayments payment gateway.
		if($paymentId == 'vivapayments')
		{
			$s = $jinput->get('s', '');

			if ($s != '')
			{
				$params		= $this->getPluginParams('vivapayments');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for PesaPal payment gateway.
		if ($paymentId == 'pesapal')
		{
			$trackingId			= $jinput->get('pesapal_transaction_tracking_id', '');
			$merchantReference	= $jinput->get('pesapal_merchant_reference', '');

			if ($trackingId != '' && $merchantReference != '')
			{
				$params		= $this->getPluginParams('pesapal');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for MercadoPago payment gateway.
		if ($paymentId == 'mercadopago')
		{
			$collectionId = $jinput->get('collection_id', '');

			if ($collectionId != '')
			{
				$params		= $this->getPluginParams('mercadopago');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for MOLPay payment gateway.
		if ($paymentId == 'molpay')
		{
			$tranId = $jinput->post->get('tranID', '');

			if ($tranId != '')
			{
				$params		= $this->getPluginParams('molpay');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for WebToPay payment gateway.
		if ($paymentId == 'webtopay' && isset($_GET['data']))
		{
			if ($validation == true)
			{
				echo 'OK';
			}
		}

		// Special stuff for iPay88 payment gateway.
		if ($paymentId == 'ipay88')
		{
			$status = $jinput->post->get('Status', '');

			if ($status != '')
			{
				$params		= $this->getPluginParams('ipay88');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		// Special stuff for iDEAL (TargetPay) payment gateway.
		if ($paymentId == 'ideal')
		{
			$trxid	= $jinput->get('trxid', '');
			$ec		= $jinput->get('ec', '');

			if ($trxid != '' && $ec != '')
			{
				$params		= $this->getPluginParams('ideal');
				$returnUrl	= $params->get('return_url', JURI::root());

				if ($validation == false)
				{
					$message= $failureMessage;
					$type	= 'error';
				}

				$this->setRedirect($returnUrl, $message, $type);
				$this->redirect();
			}
		}

		$app->close();
	}

	function getPluginParams($pluginId)
	{
		$plg	= JPluginHelper::getPlugin('cmpayment', $pluginId);
		$params	= new JRegistry;
		$params->loadString($plg->params);
		return $params;
	}

	function update($result)
	{
		$currentDateTime = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$orderId = $result['order_id'];
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
		$transactionInfo  = $result['transaction_info'];

		if(CMGroupBuyingHelperOrder::checkValidTransaction($order, $currentDateTime))
		{
			CMGroupBuyingHelperOrder::updatePaidOrder($order, $transactionInfo);
		}
		else
		{
			CMGroupBuyingHelperOrder::updateLatePaidOrder($order, $transactionInfo);
		}
	}
}
