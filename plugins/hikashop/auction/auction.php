<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgHikashopAuction extends JPlugin {
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	protected function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKAAUCTION_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKAAUCTION_COMPONENT');
			}
		}
		return $init;
	}

	public function onBeforeCalculateProductPriceForQuantity(&$product) {
		if(empty($product->product_auction))
			return;
		if(!$this->init())
			return;
		$class = hikaauction::get('class.product');
		$class->beforePriceCalculate($product);
	}

	public function onProductFormDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		$pid = !empty($product->product_id) ? (int)$product->product_id : 0;
		$params->set('product_id', $pid);
		$params->set('product', $product);

		$js = '';
		$ret = hikaauction::getLayout('productauction', 'shop_form', $params, $js);
		if(!empty($ret))
			$html[] = $ret;
	}

	function onProductBlocksDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		$pid = !empty($product->product_id) ? (int)$product->product_id : 0;
		$params->set('product_id', $pid);
		$params->set('product', $product);

		$js = '';
		$ret = hikaauction::getLayout('productauction', 'show_block_auction_history', $params, $js);
		if(!empty($ret))
			$html[] = $ret;
	}

	public function onMarketAclPluginListing(&$categories) {
		$categories['product'][] = 'hikaauction';
	}

	public function onMarketProductBlocksDisplay(&$product, &$html) {
		if(!defined('HIKAMARKET_COMPONENT'))
			return;

		if(!$this->init())
			return;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0)) return;
		if(!hikamarket::acl('product/edit/plugin/hikaauction')) return;

		$params = new HikaParameter('');
		$pid = !empty($product->product_id) ? (int)$product->product_id : 0;
		$params->set('product_id', $pid);
		$params->set('product', $product);

		$js = '';
		$ret = hikaauction::getLayout('productauction', 'market_block', $params, $js);
		if(!empty($ret))
			$html[] = $ret;
	}

	public function onBeforeProductCreate(&$product, &$do) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() && !hikaauction::initMarket())
			return;

		$class = hikaauction::get('class.product');
		$class->checkForm($product, $do);
	}

	public function onBeforeProductUpdate(&$product, &$do) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			$component = JRequest::getCmd('option');
			if($component == 'com_hikashop' || !hikaauction::initMarket())
				return;
		}

		$class = hikaauction::get('class.product');
		$class->checkForm($product, $do);
	}

	public function onAfterProductCreate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() && !hikaauction::initMarket())
			return;

		$class = hikaauction::get('class.product');
		$class->saveForm($product);
	}

	public function onAfterProductUpdate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() && !hikaauction::initMarket())
			return;

		$class = hikaauction::get('class.product');
		$class->saveForm($product);
	}

	public function onAfterProductQuantityCheck(&$product, &$wantedQuantity, &$quantity, &$cartContent, &$cart_product_id_for_product, &$displayErrors) {
		if(!$this->init())
			return;

		if(empty($product->product_auction))
			return;

		$cartClass = hikaauction::get('class.cart');
		return $cartClass->onAfterProductQuantityCheck($product, $wantedQuantity, $quantity, $cartContent, $cart_product_id_for_product, $displayErrors);
	}

	public function onBeforeOrderCreate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikaauction::get('class.order');
		$class->beforeOrderCreate($order, $do);
	}

	public function onAfterOrderUpdate(&$order) {
		if(!$this->init())
			return;

		$class = hikaauction::get('class.order');
		$class->afterOrderUpdate($order);
	}

	public function onHkContentParamsDisplay($container, $control, $element, &$ret) {
		if(!$this->init())
			return;

		if($container != 'menu' && $container != 'module')
			return;

		if(empty($ret['products']))
			$ret['products'] = array();
		if(empty($ret['products']['auction_show_auctions'])) {
			$arr = array(
				JHTML::_('select.option', '0', JText::_('HKA_OPT_SHOWAUCTION_ALL')),
				JHTML::_('select.option', '1', JText::_('HKA_OPT_SHOWAUCTION_YES')),
				JHTML::_('select.option', '2', JText::_('HKA_OPT_SHOWAUCTION_NO')),
			);
			if(!isset($element->hikashop_params['auction_show_auctions']))
				$element->hikashop_params['auction_show_auctions'] = '0';
			$ret['products']['auction_show_auctions'] = array(
				'HKA_OPT_SHOWAUCTION',
				JHTML::_('hikaselect.genericlist', $arr, $control.'[auction_show_auctions]' , '', 'value', 'text', @$element->hikashop_params['auction_show_auctions'])
			);
		}
		if(empty($ret['products']['auction_expired_products'])) {
			$arr = array(
				JHTML::_('select.option', '0', JText::_('HKA_OPT_EXPIREDPRODUCTS_ALL')),
				JHTML::_('select.option', '1', JText::_('HKA_OPT_EXPIREDPRODUCTS_VALID')),
				JHTML::_('select.option', '2', JText::_('HKA_OPT_EXPIREDPRODUCTS_UNLIMITED_VALID')),
				JHTML::_('select.option', '3', JText::_('HKA_OPT_EXPIREDPRODUCTS_EXPIRED')),
			);
			if(!isset($element->hikashop_params['auction_expired_products']))
				$element->hikashop_params['auction_expired_products'] = '0';
			$ret['products']['auction_expired_products'] = array(
				'HKA_OPT_EXPIRED_PRODUCTS',
				JHTML::_('hikaselect.genericlist', $arr, $control.'[auction_expired_products]' , '', 'value', 'text', @$element->hikashop_params['auction_expired_products'])
			);
		}
	}

	public function onBeforeProductListingLoad(&$filters, &$order, &$view, &$select, &$select2, &$ON_a, &$ON_b, &$ON_c) {
		$app = JFactory::getApplication();
		if($app->isAdmin())
			return;

		$expired_products = (int)$view->params->get('auction_expired_products', 0);
		if($expired_products > 0) {
			switch($expired_products) {
				case 1:
					$filters[] = '(b.product_sale_end > 0 AND b.product_sale_end > '.time().')';
					break;
				case 2:
					$filters[] = '(b.product_sale_end = 0 OR b.product_sale_end > '.time().')';
					break;
				case 3:
					$filters[] = '(b.product_sale_end > 0 AND b.product_sale_end < '.time().')';
					break;
			}
		}

		$show_auctions = (int)$view->params->get('auction_show_auctions', 0);
		if($show_auctions == 0 || !$this->init())
			return;

		if($show_auctions == 1) {
			$filters[] = '(b.product_auction > 0)';
		}

		if($show_auctions == 2) {
			$filters[] = '(b.product_auction = 0)';
		}
	}

	public function onMailListing(&$files) {
		if(!$this->init())
			return;

		jimport('joomla.filesystem.folder');
		$emailFiles = JFolder::files(HIKAAUCTION_MEDIA.'mail'.DS, '^([-_A-Za-z]*)(\.html)?\.php$');
		if(!empty($emailFiles)) {
			foreach($emailFiles as $emailFile) {
				$file = str_replace(array('.html.php', '.php'), '', $emailFile);
				if(substr($file, -9) == '.modified')
					continue;
				$key = strtoupper($file);
				$files[] = array(
					'folder' => HIKAAUCTION_MEDIA.'mail'.DS,
					'name' => JText::_('AUCTION_MAIL_' . $key),
					'filename' => $file,
					'file' => 'auction.'.$file
				);
			}
		}
	}

	public function onBeforeMailPrepare(&$mail, &$mailer, &$do) {
		if(!$this->init())
			return;

		$class = hikaauction::get('class.mail');
		$class->beforeMailPrepare($mail, $mailer, $do);
	}

	public function onViewsListingFilter(&$views, $client) {
		if(!$this->init())
			return;

		switch($client){
			case 0:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKAAUCTION_NAME,
					'component' => HIKAAUCTION_COMPONENT,
					'view' => HIKAAUCTION_FRONT.'views'.DS
				);
				break;
			case 1:
				$views[] = array(
					'client_id' => 1,
					'name' => HIKAAUCTION_NAME,
					'component' => HIKAAUCTION_COMPONENT,
					'view' => HIKAAUCTION_BACK.'views'.DS
				);
				break;
			default:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKAAUCTION_NAME,
					'component' => HIKAAUCTION_COMPONENT,
					'view' => HIKAAUCTION_FRONT.'views'.DS
				);
				$views[] = array(
					'client_id' => 1,
					'name' => HIKAAUCTION_NAME,
					'component' => HIKAAUCTION_COMPONENT,
					'view' => HIKAAUCTION_BACK.'views'.DS
				);
				break;
		}
	}

	public function onHikashopBeforeDisplayView(&$viewObj) {
		$app = JFactory::getApplication();

		$viewName = $viewObj->getName();
		$layoutName = $viewObj->getLayout();

		if(isset($viewObj->rows) && $layoutName == 'listing' && $viewName == 'product'){
			if($app->isAdmin() || !$this->init())
				return;

			$config = hikaauction::config();
			$auctionClass = hikaauction::get('class.auction');

			if(!$config->get('show_auction_price_in_listing', 1))
				return;

			foreach($viewObj->rows as $product_row){
				if(isset($product_row->product_auction) && $product_row->product_auction > 0){
					$current_winner_id = $auctionClass->getUsersWithMaxBid($product_row->product_id);

					if(!$current_winner_id)
						continue;

					$starting_price_amount = $auctionClass->getAuctionStartingPrice($product_row->prices[0]);

					$current_price_amount = $auctionClass->getAuctionCurrentPrice($product_row->product_id, $starting_price_amount, $current_winner_id);

					$product_row->prices[0]->price_value = $current_price_amount;
					$product_row->prices[0]->price_value_with_tax = $current_price_amount;
				}
			}
		}

		if($viewName == 'menu') {
			if(!$app->isAdmin() || !$this->init())
				return;
			$class = hikaauction::get('class.menu');
			$class->processView($viewObj);
			return;
		}

		if($viewName == 'product') {
			$layout = $viewObj->getLayout();
			if($layout != 'show' || $app->isAdmin() || empty($viewObj->element->product_auction) || !$this->init())
				return;
			$class = hikaauction::get('class.product');
			$class->processView($viewObj);
			return;
		}
	}

	public function onHikashopCronTrigger(&$messages) {
		if(!$this->init())
			return;

		$config = hikaauction::config();
		$cronClass = null;

		$cron_checks_period = (int)$config->get('cron_checks_period', 7200); // 7200 = 2 hours
		$cron_checks_last = (int)$config->get('cron_checks_last', 0);

		if(empty($cron_checks_last) || ($cron_checks_last + $cron_checks_period) < time()) {
			$newConf = new stdClass();
			$newConf->cron_checks_last = time();
			$config->save($newConf);

			if(empty($cronClass))
				$cronClass = hikaauction::get('class.cron');
			$cronClass->doChecksTask($messages);
		}

		$cron_queue_period = (int)$config->get('cron_queue_period', 1200); // 1200 = 20 minutes
		$cron_queue_last = (int)$config->get('cron_queue_last', 0);

		if(empty($cron_queue_last) || ($cron_queue_last + $cron_queue_period) < time()) {
			$newConf = new stdClass();
			$newConf->cron_queue_last = time();
			$config->save($newConf);

			if(empty($cronClass))
				$cronClass = hikaauction::get('class.cron');
			$cronClass->doQueueTask($messages);
		}
	}
}
