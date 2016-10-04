<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CMGroupBuyingControllerCoupon extends JControllerLegacy
{
	public function generate()
	{
		JSession::checkToken('get') or die('Invalid Token');
		$jinput = JFactory::getApplication()->input;
		$code = $jinput->get('code', '');
		$size = $jinput->get('size', '');

		if(!empty($code) && !empty($size))
		{
			$couponCode = base64_decode($code);
			$qrSize = $size;
		}
		else
		{
			$couponCode = '';
			$qrSize = '';
		}

		if($couponCode != '' && is_numeric($qrSize))
		{
			header("Content-type: image/png");
			$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration('qr_code_generator');

			if ($configuration['qr_code_generator'] == 'php_qr_code')
			{
				// QR code version 2's module size is 25x25.
				// http://www.qrcode.com/en/about/version.html
				$pixelPerPoint = $qrSize / (25 + 8);
				require_once JPATH_SITE . '/components/com_cmgroupbuying/libraries/phpqrcode.php';
				QRcode::png($couponCode, false, QR_ECLEVEL_L, $pixelPerPoint, 6);
			}
			else
			{
				$url = 'https://chart.googleapis.com/chart?chid=' . md5(uniqid(rand(), true));
				$url .= '&cht=qr&chs=' . $qrSize . "x" . $qrSize . '&chl=' . $couponCode;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$contents = curl_exec($ch);
				curl_close($ch);
				echo $contents;
			}
		}

		jexit();
	}
}
?>