<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgHikashopMarket extends JPlugin {
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	private function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKAMARKET_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKAMARKET_COMPONENT');
			}
		}
		return $init;
	}

	public function onProductFormDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		$params->set('product_id', 0);
		$params->set('product_type', '');
		$params->set('product_vendor_id', 0);

		if(!empty($product->product_vendor_id))
			$params->set('product_vendor_id', (int)$product->product_vendor_id);
		if(!empty($product->product_type))
			$params->set('product_type', $product->product_type);
		if(!empty($product->product_id))
			$params->set('product_id', (int)$product->product_id);

		$js = '';
		$ret = hikamarket::getLayout('productmarket', 'shop_form', $params, $js);
		if(!empty($ret)) {
			$html[] = array(
				'name' => 'hikamarket_vendor',
				'label' => 'HIKA_VENDOR',
				'content' => $ret
			);
		}
	}

	public function onProductBlocksDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		$params->set('product_id', 0);
		$params->set('product_type', '');

		if(!empty($product->product_type))
			$params->set('product_type', $product->product_type);
		if(!empty($product->product_id))
			$params->set('product_id', (int)$product->product_id);

		$js = '';
		$ret = hikamarket::getLayout('productmarket', 'shop_block', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onAfterProductCreate(&$product) {
		$app = JFactory::getApplication();
		if(!$app->isAdmin())
			return;
		if(!$this->init())
			return;
		$class = hikamarket::get('class.product');
		$class->backSaveForm($product);
	}

	public function onAfterProductUpdate(&$product) {
		$app = JFactory::getApplication();
		if(!$app->isAdmin())
			return;
		if(!$this->init())
			return;
		$class = hikamarket::get('class.product');
		$class->backSaveForm($product);
	}

	public function onBeforeProductCreate(&$product) {
		$this->onBeforeProductUpdate($product);
	}

	public function onBeforeProductUpdate(&$product) {
		if(!isset($product->product_vendor_params))
			return;
		if(!is_string($product->product_vendor_params)) {
			$product->product_vendor_params = serialize($product->product_vendor_params);
		} else {
			$formData = JRequest::getVar('data', array(), '', 'array');
			if(!empty($formData) && !empty($formData['product']['product_vendor_params'])) {
				$product->product_vendor_params = serialize($formData['product']['product_vendor_params']);
			} else {
				$product->product_vendor_params = '';
			}
		}
	}

	public function onAfterOrderProductsListingDisplay(&$order, $type) {
		if(!$this->init())
			return;

		if($type == 'order_back_show') {
			$params = new HikaParameter('');
			if(isset($order->order_id)) {
				$params->set('order_id', $order->order_id);
			} else {
				$params->set('order_id', $order->products[0]->order_id);
			}
			$js = '';
			echo hikamarket::getLayout('ordermarket', 'show_'.$type, $params, $js);
		}
	}

	public function onBeforeOrderCreate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->beforeCreate($order, $do);
	}

	public function onAfterOrderCreate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->afterCreate($order, $send_email);

		$app = JFactory::getApplication();
		$app->setUserState(HIKAMARKET_COMPONENT.'.checkout_terms', null);
	}

	public function onBeforeOrderProductsUpdate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->beforeProductsUpdate($order, $do);
	}

	public function onBeforeOrderUpdate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->beforeUpdate($order, $do);
	}

	public function onAfterOrderUpdate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->afterUpdate($order, $send_email);
	}

	public function onBeforeOrderDelete(&$elements, &$do) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->beforeDelete($elements, $do);
	}

	public function onAfterOrderDelete(&$elements) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.order');
		$class->afterDelete($elements);
	}

	public function onBeforeMailPrepare(&$mail, &$mailer, &$do) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.mail');
		$class->beforeMailPrepare($mail, $mailer, $do);
	}

	public function onHkProcessMailTemplate(&$mail, &$data, &$content, &$vars, &$texts, &$templates) {
		if(!$this->init())
			return;
		$class = hikamarket::get('class.mail');
		$class->processMailTemplate($mail, $data, $content, $vars, $texts, $templates);
	}

	public function onBeforeSendContactRequest(&$element, &$send) {
		$target = JRequest::getCmd('target', '');
		$vendor_id = JRequest::getInt('vendor_id', 0);
		if($target == 'vendor' && !empty($vendor_id)) {
			$element->target = $target;
			$element->vendor_id = $vendor_id;
			if(!$send) {
				if(!$this->init())
					return;

				$config = hikamarket::config();
				$send = $config->get('display_vendor_contact', 0);
			}
		}
	}

	public function onAfterUserCreate(&$user) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.vendor');
		$class->onAfterUserCreate($user);
	}

	public function onAfterUserUpdate(&$user) {
		if(!$this->init())
			return;

		$class = hikamarket::get('class.vendor');
		$class->onAfterUserUpdate($user);
	}

	public function onShippingWarehouseFilter(&$shipping_groups, &$order, &$rates) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		$class = hikamarket::get('class.shipping');
		$class->onShippingWarehouseFilter($shipping_groups, $order, $rates);
	}

	public function onPaymentDisplay(&$order, &$methods, &$usable_methods) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		$paymentClass = hikamarket::get('class.payment');
		$paymentClass->onPaymentDisplay($order, $methods, $usable_methods);
	}

	public function onHikaPluginConfiguration($type, &$plugin, &$element, &$extra_config, &$extra_blocks) {
		if(!in_array($type, array('payment', 'shipping')))
			return;
		if(!$this->init() || !hikamarket::level(1))
			return;

		$class = null;
		if($type == 'shipping')
			$class = hikamarket::get('class.shipping');
		if($type == 'payment')
			$class = hikamarket::get('class.payment');

		if(!empty($class))
			$class->onPluginConfiguration($plugin, $element, $extra_config, $extra_blocks);
	}

	public function onBeforeHikaPluginCreate($type, &$element, &$do) {
		if(!in_array($type, array('payment', 'shipping')))
			return;
		if(!$this->init() || !hikamarket::level(1))
			return;

		$class = null;
		if($type == 'shipping')
			$class = hikamarket::get('class.shipping');
		if($type == 'payment')
			$class = hikamarket::get('class.payment');

		if(!empty($class))
			$class->onBeforePluginSave($element, $do, true);
	}

	public function onBeforeHikaPluginUpdate($type, &$element, &$do) {
		if(!in_array($type, array('payment', 'shipping')))
			return;
		if(!$this->init() || !hikamarket::level(1))
			return;

		$class = null;
		if($type == 'shipping')
			$class = hikamarket::get('class.shipping');
		if($type == 'payment')
			$class = hikamarket::get('class.payment');

		if(!empty($class))
			$class->onBeforePluginSave($element, $do, false);
	}

	public function onViewsListingFilter(&$views, $client) {
		if(!$this->init())
			return;

		switch($client){
			case 0:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKAMARKET_NAME,
					'component' => HIKAMARKET_COMPONENT,
					'view' => HIKAMARKET_FRONT.'views'.DS
				);
				break;
			case 1:
				$views[] = array(
					'client_id' => 1,
					'name' => HIKAMARKET_NAME,
					'component' => HIKAMARKET_COMPONENT,
					'view' => HIKAMARKET_BACK.'views'.DS
				);
				break;
			default:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKAMARKET_NAME,
					'component' => HIKAMARKET_COMPONENT,
					'view' => HIKAMARKET_FRONT.'views'.DS
				);
				$views[] = array(
					'client_id' => 1,
					'name' => HIKAMARKET_NAME,
					'component' => HIKAMARKET_COMPONENT,
					'view' => HIKAMARKET_BACK.'views'.DS
				);
			break;
		}
	}

	public function onBeforeHikaPluginConfigurationListing($type, &$filters, &$order, &$searchMap, &$extrafilters, &$view) {
		if(!in_array($type, array('payment', 'shipping')) || !$this->init() || !hikamarket::level(1))
			return;

		if($type == 'payment') {
			$class = hikamarket::get('class.payment');
			return $class->onBeforeHikaPluginConfigurationListing($type, $filters, $order, $searchMap, $extrafilters, $view);
		}
	}

	public function onAfterHikaPluginConfigurationListing($type, &$rows, &$listing_columns, &$view) {
		if(!in_array($type, array('payment', 'shipping')) || !$this->init() || !hikamarket::level(1))
			return;

		if($type == 'payment') {
			$class = hikamarket::get('class.payment');
			return $class->onAfterHikaPluginConfigurationListing($type, $rows, $listing_columns, $view);
		}
	}

	public function onMailListing(&$files) {
		if(!$this->init())
			return;

		jimport('joomla.filesystem.folder');
		$emailFiles = JFolder::files(HIKAMARKET_MEDIA.'mail'.DS, '^([-_A-Za-z]*)(\.html)?\.php$');
		if(empty($emailFiles))
			return;
		foreach($emailFiles as $emailFile) {
			$file = str_replace(array('.html.php', '.php'), '', $emailFile);
			if(substr($file, -9) == '.modified')
				continue;
			$key = strtoupper($file);
			$files[] = array(
				'folder' => HIKAMARKET_MEDIA.'mail'.DS,
				'name' => JText::_('MARKET_' . $key),
				'filename' => $file,
				'file' => 'market.'.$file
			);
		}
	}

	public function onHikashopBeforeDisplayView(&$viewObj) {
		$app = JFactory::getApplication();

		if(!empty($viewObj->toolbar)) {
			if($app->isAdmin() && $this->init()) {
				$toolbarClass = hikamarket::get('class.toolbar');
				$toolbarClass->processView($viewObj);
			}
		}

		$viewName = $viewObj->getName();
		$views = array(
			'menu' => array('menu', 1),
			'product' => array('product', 0),
			'category' => array('category', 2),
			'order' => array('order', 0),
			'contact' => array('contact', 2)
		);
		if(isset($views[$viewName])) {
			if(!$this->init())
				return;
			if(($views[$viewName][1] == 1 && !$app->isAdmin()) || ($views[$viewName][1] == 2 && $app->isAdmin()))
				return;
			$processClass = hikamarket::get('class.'.$views[$viewName][0]);
			$processClass->processView($viewObj);
			return;
		}

		if($viewName == 'characteristic' && $app->isAdmin()) {
			$layout = $viewObj->getLayout();
			if(($layout != 'listing' && $layout != 'form') || !$this->init() || !hikamarket::level(1))
				return;

			if((!empty($viewObj->rows) || !empty($viewObj->element->values)) && empty($viewObj->pageInfo->filter->filter_vendor)) {
				$db = JFactory::getDBO();
				$vendors = array();
				if(isset($viewObj->rows))
					$rows =& $viewObj->rows;
				else
					$rows =& $viewObj->element->values;

				foreach($rows as $row) {
					$vendors[(int)$row->characteristic_vendor_id] = (int)$row->characteristic_vendor_id;
				}
				unset($vendors[0]);
				if(!empty($vendors)) {
					$query = 'SELECT a.* FROM ' . hikamarket::table('vendor').' AS a WHERE a.vendor_id IN ('.implode(',',$vendors).')';
					$db->setQuery($query);
					$vendors = $db->loadObjectList('vendor_id');
					foreach($rows as &$row) {
						if(!empty($vendors[(int)$row->characteristic_vendor_id])) {
							$v = $vendors[(int)$row->characteristic_vendor_id];
							$row->vendor_id = (int)$row->characteristic_vendor_id;
							$row->vendor_name = $v->vendor_name;
							$row->vendor_email = $v->vendor_email;
						} else {
							$row->vendor_id = 0;
							$row->vendor_name = '';
							$row->vendor_email = '';
						}
					}
					unset($row);
					$fieldName = new stdClass();
					$fieldName->name = JText::_('HIKA_VENDOR');
					$fieldName->value = 'vendor_name';
					$viewObj->extrafields['vendor_name'] = $fieldName;
				}
			}
		}
	}

	public function onBeforeOrderListing($paramBase, &$extrafilters, &$pageInfo, &$filters) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin())
			return;

		$pageInfo->filter->filter_vendor = (int)$app->getUserStateFromRequest($paramBase.".filter_vendor", 'filter_vendor', '', 'int');
		$extrafilters['filter_vendor'] =& $this;

		if(!empty($pageInfo->filter->filter_vendor)) {
			foreach($filters as $k => $filter) {
				if($filter == 'b.order_type=\'sale\'')
					unset($filters[$k]);
			}

			if($pageInfo->filter->filter_vendor > 0) {
				$filters[] = 'b.order_vendor_id = ' . (int)$pageInfo->filter->filter_vendor;
			}

			$pageInfo->filter->filter_vendorinvoice = (int)$app->getUserStateFromRequest($paramBase.".filter_vendorinvoice", 'filter_vendorinvoice', '', 'int');

			if(!$pageInfo->filter->filter_vendorinvoice) {
				$vendorOrderType = 'subsale';
			} else {
				$vendorOrderType = 'vendorpayment';
			}
			$filters[] = 'b.order_type=\''.$vendorOrderType.'\'';
		}
	}

	public function onAfterOrderListing(&$rows, &$extrafields, $pageInfo) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		if(!empty($rows) && !empty($pageInfo->filter->filter_vendor)) {
			$fieldTotal = new stdClass();
			$fieldTotal->name = JText::_('VENDOR_TOTAL');
			$fieldTotal->obj =& $this;
			$extrafields['vendor_total'] = $fieldTotal;

			$db = JFactory::getDBO();
			$vendors = array();
			foreach($rows as $row) {
				$vendors[$row->order_vendor_id] = $row->order_vendor_id;
			}
			$query = 'SELECT a.* FROM ' . hikamarket::table('vendor').' AS a WHERE a.vendor_id IN ('.implode(',',$vendors).')';
			$db->setQuery($query);
			$vendors = $db->loadObjectList('vendor_id');
			foreach($rows as &$row) {
				if(!empty($vendors[$row->order_vendor_id])) {
					$v = $vendors[$row->order_vendor_id];
					$row->vendor_name = $v->vendor_name;
					$row->vendor_email = $v->vendor_email;
				} else {
					$row->vendor_name = '';
					$row->vendor_email = '';
				}
			}
			$fieldName = new stdClass();
			$fieldName->name = JText::_('HIKA_VENDOR');
			$fieldName->value = 'vendor_name';
			$extrafields['vendor_name'] = $fieldName;
		}
	}

	public function onBeforeOrderExportQuery(&$filters, $paramBase) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		$app = JFactory::getApplication();
		$filter_vendor = (int)$app->getUserStateFromRequest($paramBase . '.filter_vendor', 'filter_vendor', '', 'int');
		if(!empty($filter_vendor)) {
			$filter_vendorinvoice = (int)$app->getUserStateFromRequest($paramBase . '.filter_vendorinvoice', 'filter_vendorinvoice', '', 'int');
			$vendorOrderType = (!$filter_vendorinvoice) ? 'subsale' : 'vendorpayment';
			$filters['order_type'] = 'hk_order.order_type = \''.$vendorOrderType.'\'';

			if($filter_vendor > 0) {
				$filters['order_vendor_id'] = 'hk_order.order_vendor_id = ' . (int)$filter_vendor;
			}
		}
	}

	public function onBeforeProductListingLoad(&$filters, &$order, &$view, &$select, &$select2, &$ON_a, &$ON_b, &$ON_c) {
		$app = JFactory::getApplication();
		if($app->isAdmin() || !$this->init() || !hikamarket::level(1))
			return;


		$ON_c .= ' LEFT JOIN ' . hikamarket::table('vendor').' AS hikam_vendor ON b.product_vendor_id = hikam_vendor.vendor_id ';
		$filters['active_vendor'] = '(hikam_vendor.vendor_published IS NULL OR hikam_vendor.vendor_published = 1)';

		if((int)$view->params->get('market_filter_same_vendor', '0') == 1) {
			$currentVendor = 0;
			$option = JRequest::getString('option','');
			$ctrl = JRequest::getString('ctrl','');
			$task = JRequest::getString('task','');
			if($option == HIKAMARKET_COMPONENT && $ctrl == 'vendor' && $task == 'show')
				$currentVendor = hikamarket::getCID('vendor_id');
			else if(isset($view->product->vendor))
				$currentVendor = (int)$view->product->vendor->vendor_id;
			if(!empty($currentVendor) && $currentVendor == 1) {
				$filters['current_vendor'] = '(hikam_vendor.vendor_id IS NULL OR hikam_vendor.vendor_id <= 1)';
			} else if(!empty($currentVendor)) {
				$filters['current_vendor'] = '(hikam_vendor.vendor_id = '.$currentVendor.')';
			}
		}

		foreach($filters as &$filter) {
			if(strpos($filter, 'b.product_vendor_id LIKE \'%') !== false) {
				$filter = str_replace('b.product_vendor_id LIKE', 'hikam_vendor.vendor_name LIKE', $filter);
			}
			unset($filter);
		}

		if($view->module) {
			$productClass = hikamarket::get('class.product');
			$productClass->processListing($filters, $order, $view, $select, $select2, $ON_a, $ON_b, $ON_c);
		}
	}

	public function onAfterProductCharacteristicsLoad(&$product, &$mainCharacteristics, &$characteristics) {
		$app = JFactory::getApplication();
		if(empty($mainCharacteristics[$product->product_id][0]) || $app->isAdmin() || !$this->init() || !hikamarket::level(1))
			return;

		$process = false;
		foreach($mainCharacteristics[$product->product_id][0] as $c) {
			if(empty($c->values)) {
				$process = true;
				break;
			}
		}

		if(!$process)
			return;

		$productClass = hikamarket::get('class.product');
		$productClass->loadVendorProductCharacteristics($product, $mainCharacteristics, $characteristics);
	}

	public function onBeforeCategoryListingLoad(&$filters, &$order, &$view, &$leftjoin) {
		$app = JFactory::getApplication();
		if($app->isAdmin() || !$this->init() || !hikamarket::level(1))
			return;

		if(isset($view->module) && $view->module) {
			$option = JRequest::getString('option','');
			$ctrl = JRequest::getString('ctrl','');
			$viewName = $view->getName();
			if($option == HIKAMARKET_COMPONENT && $ctrl == 'vendor' && $viewName == 'category') {
				$categoryClass = hikamarket::get('class.category');
				$categoryClass->processListing($filters, $order, $view, $leftjoin);
			}
		}
	}

	public function onBeforeCharacteristicListing($paramBase, &$extrafilters, &$pageInfo, &$filters) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		$app = JFactory::getApplication();
		$pageInfo->filter->filter_vendor = (int)$app->getUserStateFromRequest($paramBase.".filter_vendor", 'filter_vendor', '', 'int');
		$extrafilters['filter_vendor'] =& $this;

		if(!empty($pageInfo->filter->filter_vendor) && $pageInfo->filter->filter_vendor > 0) {
			$filters[] = 'a.characteristic_vendor_id = ' . (int)$pageInfo->filter->filter_vendor;
		}
	}

	public function onTableFieldsLoad(&$values) {
		if(!$this->init())
			return;
		$fieldClass = hikamarket::get('class.field');
		$fieldClass->tableFieldsLoad($values);
	}

	public function onFieldFileDownload(&$found, $name, $field_table, $field_namekey, $options) {
		if(substr($field_table, 0, 15) != 'plg.hikamarket.' && !in_array($field_table, array('order')))
			return;
		if(!$this->init())
			return;
		$fieldClass = hikamarket::get('class.field');
		$fieldClass->fieldFileDownload($found, $name, $field_table, $field_namekey, $options);
	}

	public function onCustomfieldEdit(&$field, &$view) {
		if(!$this->init())
			return;

		$fieldClass = hikamarket::get('class.field');
		$fieldClass->customfieldEdit($field, $view);
	}

	public function displayFilter($name, $filter) {
		$vendorFilterType = hikamarket::get('type.filter_vendor');
		if(isset($filter->filter_vendorinvoice))
			return $vendorFilterType->display('filter_vendor', @$filter->filter_vendor, 'filter_vendorinvoice', @$filter->filter_vendorinvoice);
		return $vendorFilterType->display('filter_vendor', @$filter->filter_vendor);
	}

	public function showField($container, $name, &$row) {
		$ret = '';
		switch($name) {
			case 'vendor_total':
				$ret .= $container->currencyHelper->format($row->order_vendor_price, $row->order_currency_id);
				break;
		}
		return $ret;
	}

	public function onAfterCartProductsLoad(&$cart) {
		if(!$this->init())
			return;
		$cartClass = hikamarket::get('class.cart');
		return $cartClass->onAfterCartProductsLoad($cart);
	}

	public function onAfterProductQuantityCheck(&$product, &$wantedQuantity, &$quantity, &$cartContent, &$cart_product_id_for_product, &$displayErrors) {
		if(!$this->init())
			return;
		$cartClass = hikamarket::get('class.cart');
		return $cartClass->onAfterProductQuantityCheck($product, $wantedQuantity, $quantity, $cartContent, $cart_product_id_for_product, $displayErrors);
	}

	public function onBeforeCouponCheck(&$coupon, &$total, &$zones, &$products, &$display_error, &$error_message, &$do) {
		if(empty($coupon->discount_target_vendor) || $coupon->discount_target_vendor == 1)
			return;
		if(!$this->init())
			return;
		$discountClass = hikamarket::get('class.discount');
		return $discountClass->beforeCouponCheck($coupon, $total, $zones, $products, $display_error, $error_message, $do);
	}

	public function onAfterCouponCheck(&$coupon, &$total, &$zones, &$products, &$display_error, &$error_message, &$do) {
		if(!$do)
			return;
		if(isset($coupon->discount_percent_amount)) {
			$coupon->discount_percent_amount_orig = $coupon->discount_percent_amount;
			$coupon->discount_coupon_nodoubling_orig = $coupon->discount_coupon_nodoubling;
		}

		if(empty($coupon->discount_target_vendor) || $coupon->discount_target_vendor == 1)
			return;
		if(!$this->init())
			return;
		if(empty($this->discountClass))
			$this->discountClass = hikamarket::get('class.discount');
		return $this->discountClass->afterCouponCheck($coupon, $total, $zones, $products, $display_error, $error_message, $do);
	}

	public function onSelectDiscount(&$product, &$discountsSelected, &$discounts, $zone_id, &$parent) {
		if(empty($discounts) || !$this->init())
			return;
		if(empty($this->discountClass))
			$this->discountClass = hikamarket::get('class.discount');
		return $this->discountClass->onSelectDiscount($product, $discountsSelected, $discounts, $zone_id, $parent);
	}

	public function onDiscountBlocksDisplay(&$discount, &$html) {
		if(!$this->init())
			return;
		$discountClass = hikamarket::get('class.discount');
		return $discountClass->discountBlocksDisplay($discount, $html);
	}

	public function onHkContentParamsDisplay($container, $control, $element, &$ret) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		if($container == 'menu' || $container == 'module') {
			if(empty($ret['products']))
				$ret['products'] = array();

			if(empty($ret['products']['market_show_sold_by'])) {
				$arr = array(
					JHTML::_('select.option', '-1', JText::_('HIKA_INHERIT')),
					JHTML::_('select.option', '1', JText::_('HIKASHOP_YES')),
					JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
				);
				if(!isset($element->hikashop_params['market_show_sold_by']))
					$element->hikashop_params['market_show_sold_by'] = '-1';
				$ret['products']['market_show_sold_by'] = array(
					'HIKAM_FRONT_SHOW_SOLD_BY',
					JHTML::_('hikaselect.radiolist', $arr, $control.'[market_show_sold_by]' , '', 'value', 'text', @$element->hikashop_params['market_show_sold_by'])
				);

				if(!isset($element->hikashop_params['market_filter_same_vendor']))
					$element->hikashop_params['market_filter_same_vendor'] = '0';
				$ret['products']['market_filter_same_vendor'] = array(
					'HIKAM_FRONT_FILTER_SAME_VENDOR',
					JHTML::_('hikaselect.booleanlist', $control.'[market_filter_same_vendor]', '', @$element->hikashop_params['market_filter_same_vendor'])
				);
			}

			if(empty($ret['categories']))
				$ret['categories'] = array();
			if(empty($ret['categories']['market_vendor_categories'])) {
				$arr = array(
					JHTML::_('select.option', '-1', JText::_('HIKA_INHERIT')),
					JHTML::_('select.option', '1', JText::_('HIKASHOP_YES')),
					JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
				);
				if(!isset($element->hikashop_params['market_vendor_categories']))
					$element->hikashop_params['market_vendor_categories'] = '-1';
				$ret['products']['market_vendor_categories'] = array(
					'HIKAM_VENDOR_CATEGORY_TO_VENDOR_PAGE',
					JHTML::_('hikaselect.radiolist', $arr, $control.'[market_vendor_categories]' , '', 'value', 'text', @$element->hikashop_params['market_vendor_categories'])
				);
			}
		}
	}

	public function onUploadControllerGet($controllerName, &$controller) {
		if(substr($controllerName, 0, 11) != 'plg.market.')
			return;
		if(!$this->init())
			return;
		$ctrl = substr($controllerName, 11);
		$controller = hikamarket::get('controller.' . $ctrl, array(), true);
	}

	public function onBeforeVoteUpdate(&$element, &$do, &$currentElement) {
		if(!isset($element->vote_type) || $element->vote_type != 'vendor')
			return;

		if(!$this->init() || !hikamarket::level(1))
			return;

		$vendorClass = hikamarket::get('class.vendor');
		$vendor = $vendorClass->get($element->vote_ref_id);
		if(!$vendor) {
			$do = false;
			return;
		}
		$currentElement->vote_type = 'vendor';
		$currentElement->vote_ref_id = (int)$element->vote_ref_id;
	}

	public function onAfterVoteUpdate(&$element, $return_data) {
		if(!isset($element->vote_type) || $element->vote_type != 'vendor')
			return;

		if(!$this->init() || !hikamarket::level(1))
			return;

		$vendor = new stdClass();
		$vendor->vendor_id = (int)$element->vote_ref_id;
		$vendor->vendor_average_score = (float)hikamarket::toFloat($return_data['average']);
		$vendor->vendor_total_vote = (int)$return_data['total'];

		$vendorClass = hikamarket::get('class.vendor');
		$ret = $vendorClass->save($vendor);
	}

	public function onBeforeVoteCreate(&$element, &$do, &$currentElement) {
		$this->onBeforeVoteUpdate($element, $do, $currentElement);
	}

	public function onAfterVoteCreate(&$element, $return_data) {
		$this->onAfterVoteUpdate($element, $return_data);
	}

	public function onBeforeVoteDelete(&$elements, &$do, &$currentElements) {
		if(!$this->init() || !hikamarket::level(1))
			return;

		JArrayHelper::toInteger($elements);
		if(empty($elements))
			return;

		$db = JFactory::getDBO();
		$query = 'SELECT vote.vote_id, vote.vote_type, vote.vote_ref_id, vendor.vendor_id, vendor.vendor_average_score as `average_score`, vendor.vendor_total_vote  as `total_vote` '.
			' FROM '.hikamarket::table('vendor').' as vendor '.
			' INNER JOIN '.hikamarket::table('shop.vote').' AS vote ON (vote.vote_ref_id = vendor.vendor_id AND vote.vote_type = '.$db->Quote('vendor').') '.
			' WHERE vote.vote_id IN ('.implode(',', $elements).')';
		$db->setQuery($query);
		$myElements = $db->loadObjectList('vote_id');
		foreach($myElements as $k => $el) {
			$currentElements[ (int)$k ] = $el;
		}
	}

	public function onAfterVoteDelete(&$element) {
		if(!isset($element->vote_type) || $element->vote_type != 'vendor')
			return;
		if(!$this->init() || !hikamarket::level(1))
			return;
		if(!isset($element->average_score) || !isset($element->total_vote))
			return;

		$vendor = new stdClass();
		$vendor->vendor_id = (int)$element->vote_ref_id;
		$vendor->vendor_average_score = $element->average_score;
		$vendor->vendor_total_vote = (int)$element->total_vote;

		if(!isset($this->vendorClass))
			$this->vendorClass = hikamarket::get('class.vendor');
		$this->vendorClass->save($vendor);
	}

	public function onCheckoutStepList(&$list) {
		if(!$this->init() || !hikamarket::level(1))
			return;
		$list['plg.market.terms'] = 'HikaMarket ' . JText::_('HIKASHOP_CHECKOUT_TERMS');
	}

	public function onCheckoutStepDisplay($layoutName, &$html, &$view) {
		if($layoutName != 'plg.market.terms' || !$this->init() || !hikamarket::level(1))
			return;
		$params = new stdClass();
		$params->view = $view;
		$js = null;
		$html .= hikamarket::getLayout('checkoutmarket', 'terms', $params, $js);
	}

	public function onBeforeCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
	}

	public function onAfterCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
		if($controllerName != 'plg.market.terms' || !$this->init() || !hikamarket::level(1))
			return;

		$checkoutClass = hikamarket::get('class.checkout');
		$checkoutClass->afterCheckoutStep($controllerName, $go_back, $original_go_back, $controller);
	}

	public function onHikashopBeforeCheckDB(&$createTable, &$custom_fields, &$structure, &$helper) {
		if(!$this->init())
			return;
		$helper->parseTableFile(HIKAMARKET_BACK.'_database'.DS.'install.sql', $createTable, $structure);
	}

	public function onHikaShopAfterCheckDB(&$ret) {
		if(!$this->init())
			return;
		$updateHelper = hikamarket::get('helper.update');
		$updateHelper->onAfterCheckDB($ret);
	}
}
