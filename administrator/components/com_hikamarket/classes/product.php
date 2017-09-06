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
class hikamarketProductClass extends hikamarketClass {

	protected $tables = array('shop.product');
	protected $pkeys = array('product_id');

	protected $toggle = array('product_published' => 'product_id');
	protected $toggleAcl = array('product_published' => 'product/edit/published');
	protected $deleteToggle = array('shop.product' => 'product_id');

	public function frontSaveForm() {
		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$shopConfig = hikamarket::config(false);
		$product_id = hikamarket::getCID('product_id');
		$productClass = hikamarket::get('shop.class.product');
		$fieldsClass = hikamarket::get('shop.class.field');
		$vendorClass = hikamarket::get('class.vendor');
		$vendor = hikamarket::loadVendor(true, false);
		$vendor_id = $vendor->vendor_id;

		$formData = JRequest::getVar('data', array(), '', 'array');
		$formProduct = array();
		if(!empty($formData['product']))
			$formProduct = $formData['product'];

		$new = empty($product_id);
		$oldProduct = null;
		if(!$new) {
			$oldProduct = $productClass->get($product_id);
			$editAllVendors = ($vendor_id == 1 && hikamarket::acl('product/subvendor'));

			if(!in_array($oldProduct->product_type, array('main', 'variant', 'waiting_approval')))
				return false;

			if($oldProduct->product_type == 'variant') {
				$parent_product = $productClass->get($oldProduct->product_parent_id);
				if((int)$parent_product->product_vendor_id > 1)
					return false;
			}

			$productVendorId = (int)$oldProduct->product_vendor_id;
			if($productVendorId != $vendor_id && !$editAllVendors && ($productVendorId > 1 || $vendor_id > 1))
				return false;

		} else {
			if(!hikamarket::acl('product/add'))
				return false;

			if($vendorClass->checkProductLimitation($vendor) !== true)
				return false;

			$rootCategory = $vendorClass->getRootCategory($vendor_id);
			if(empty($rootCategory)) {
				$categoryClass = hikamarket::get('shop.class.category');
				$rootCategory = 'product';
				$categoryClass->getMainElement($rootCategory);
			}

			$oldProduct = new stdClass();
			$oldProduct->categories = array();
			if(!empty($formProduct['categories']))
				$oldProduct->categories = $formProduct['categories'];
			JArrayHelper::toInteger($oldProduct->categories);
			if(!count($oldProduct->categories) && !empty($rootCategory))
				$oldProduct->categories = array($rootCategory);
		}

		$product = $fieldsClass->getInput('product', $oldProduct, true, 'data', false, 'display:vendor_product_edit'); // 'all');
		if(empty($product))
			return false;

		$this->db->setQuery('SELECT field.* FROM '.hikamarket::table('shop.field').' as field WHERE field.field_table = '.$this->db->Quote('product').' ORDER BY field.`field_ordering` ASC');
		$all_fields = $this->db->loadObjectList('field_namekey');
		$edit_fields = hikamarket::acl('product/edit/customfields');
		foreach($all_fields as $fieldname => $field) {
			if(!$edit_fields || empty($field->field_published) || (strpos($field->field_display, ';vendor_product_edit=1') === false) ) {
				unset($product->$fieldname);
			}
		}

		$product->product_id = (int)$product_id;
		if($new) {
			$reset = true;
			if(!empty($product->product_type) && $product->product_type == 'variant' && !empty($product->product_parent_id)) {
				$query = 'SELECT c.characteristic_id, p.product_id, p.product_vendor_id, p.product_code FROM ' . hikamarket::table('shop.variant') . ' AS v '.
					' INNER JOIN ' . hikamarket::table('shop.characteristic') . ' AS c ON v.variant_characteristic_id = c.characteristic_id '.
					' INNER JOIN ' . hikamarket::table('shop.product') . ' AS p ON v.variant_product_id = p.product_id '.
					' WHERE c.characteristic_parent_id = 0 AND c.characteristic_alias = \'vendor\' AND v.variant_product_id = ' . (int)$product->product_parent_id.' '.
					' ORDER BY v.ordering ASC';
				$db = JFactory::getDBO();
				$db->setQuery($query);
				$parent_data = $db->loadObject();
				if(!empty($parent_data) && (int)$parent_data->product_vendor_id == 0) {
					$reset = false;
					if(empty($product->product_code))
						$product->product_code = $parent_data->product_code . '_v_' . $vendor_id;
				}
			}

			if($reset) {
				$product->product_type = 'main';
				unset($product->product_parent_id);
				if($config->get('product_approval', 0) && !hikamarket::acl('product/approve'))
					$product->product_type = 'waiting_approval';
			}
		} else {
			$product->product_type = $oldProduct->product_type;
			unset($product->product_parent_id);
		}

		if($new && !empty($vendor_id))
			$product->product_vendor_id = $vendor_id;

		if( !hikamarket::level(1) || ($vendor_id > 1 && isset($product->product_vendor_id) && $product->product_vendor_id != $vendor_id) || ($vendor_id == 1 && !hikamarket::acl('product/edit/vendor')) ) {
			unset($product->product_vendor_id);
		}

		if(!hikamarket::acl('product/edit/name')) { unset($product->product_name); }
		if(!hikamarket::acl('product/edit/code')) { unset($product->product_code); }
		if(!hikamarket::acl('product/edit/weight')) { unset($product->product_weight); }
		if(!hikamarket::acl('product/edit/volume')) { unset($product->product_volume); }
		if(!hikamarket::acl('product/edit/published')) { unset($product->product_published); }
		if(!hikamarket::acl('product/edit/manufacturer')) { unset($product->product_manufacturer_id); }
		if(!hikamarket::acl('product/edit/pagetitle')) { unset($product->product_page_title); }
		if(!hikamarket::acl('product/edit/url')) { unset($product->product_url); }
		if(!hikamarket::acl('product/edit/metadescription')) { unset($product->product_meta_description); }
		if(!hikamarket::acl('product/edit/keywords')) { unset($product->product_keywords); }
		if(!hikamarket::acl('product/edit/alias')) { unset($product->product_alias); }
		if(!hikamarket::acl('product/edit/acl')) { unset($product->product_access); }
		if(!hikamarket::acl('product/edit/msrp')) { unset($product->product_msrp); }
		if(!hikamarket::acl('product/edit/canonical')) { unset($product->product_canonical); }
		if(!hikamarket::acl('product/edit/warehouse')) { unset($product->product_warehouse_id); }
		if(!hikamarket::acl('product/edit/tax')) { unset($product->product_tax_id); }

		if(hikamarket::acl('product/edit/qtyperorder')) {
			if(isset($product->product_max_per_order))
				$product->product_max_per_order = (int)$product->product_max_per_order;
			if(isset($product->product_min_per_order))
				$product->product_min_per_order = (int)$product->product_min_per_order;
		} else {
			unset($product->product_max_per_order);
			unset($product->product_min_per_order);
		}

		unset($product->tags);
		if(hikamarket::acl('product/edit/tags')) {
			$tagsHelper = hikamarket::get('shop.helper.tags');
			if(!empty($tagsHelper) && $tagsHelper->isCompatible())
				$product->tags = empty($formData['tags']) ? array() : $formData['tags'];
		}

		$removeFields = array(
			'contact', 'delay_id', 'waitlist', 'display_quantity_field', 'price_percentage', 'group_after_purchase',
			'status', 'hit', 'created', 'modified', 'last_seen_date', 'sales', 'layout', 'average_score', 'total_vote',
		);
		foreach($removeFields as $rf) {
			$rf = 'product_'.$rf;
			unset($product->$rf);
		}

		if(hikamarket::acl('product/edit/description')) {
			$product->product_description = JRequest::getVar('product_description','','','string',JREQUEST_ALLOWRAW);
			if((int)$config->get('vendor_safe_product_description', 1)) {
				$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
				$product->product_description = $safeHtmlFilter->clean($product->product_description, 'string');
			}
		}

		if(hikamarket::acl('product/edit/category')) {
			$categoryClass = hikamarket::get('shop.class.category');
			$vendor_chroot = $vendorClass->getRootCategory($vendor);
			$rootCategory = $vendor_chroot;
			if(empty($rootCategory)) {
				$rootCategory = 'product';
				$categoryClass->getMainElement($rootCategory);
			}

			$product->categories = array();
			if(!empty($formProduct['categories']))
				$product->categories = $formProduct['categories'];
			JArrayHelper::toInteger($product->categories);
			if(empty($product->product_id) && empty($product->categories) && !empty($rootCategory)) {
				$product->categories = array($rootCategory);
			}

			if(!empty($vendor_chroot)) {
				$oldCategories = array();
				if(!$new) {
					$query = 'SELECT category_id FROM ' . hikamarket::table('shop.product_category') . ' WHERE product_id = ' . (int)$product->product_id . ' ORDER BY ordering';
					$this->db->setQuery($query);
					if(!HIKASHOP_J25)
						$oldCategories = $this->db->loadResultArray();
					else
						$oldCategories = $this->db->loadColumn();
				}
				$extra_categories = $vendorClass->getExtraCategories($vendor);

				$product->categories = $vendorClass->filterCategories($product->categories, $vendor_chroot, $oldCategories, $extra_categories);
			}

			if(empty($product->categories))
				$product->categories = array($rootCategory);

			if($vendor_id > 1) {
				$vendor_limitation = $config->vendorget($vendor, 'product_max_categories_per_product', 0);
				if($vendor_limitation > 0 && count($product->categories) > $vendor_limitation) {
					$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MAX_CATEGORIES_PER_PRODUCT_MSG', $vendor_limitation), 'error');
					return false;
				}
			}

		} else if($new) {
			$rootCategory = 0;
			$category_explorer = $config->get('show_category_explorer', 1);
			if($category_explorer)
				$rootCategory = $app->getUserState(HIKAMARKET_COMPONENT.'.product.listing_cid');
			if(empty($rootCategory) || !is_numeric($rootCategory)){
				$rootCategory = $vendorClass->getRootCategory($vendor_id);
				if(empty($rootCategory)) {
					$rootCategory = 'product';
					$categoryClass = hikamarket::get('shop.class.category');
					$categoryClass->getMainElement($rootCategory);
				}
			}

			if(!empty($rootCategory)) {
				$product->categories = array($rootCategory);
			}
		}

		if(hikamarket::acl('product/edit/related')) {
			$related = @$formProduct['related'];
			$product->related = array();
			if(!empty($related)) {
				$k = 0;
				foreach($related as $r) {
					$obj = new stdClass();
					$obj->product_related_id = (int)$r;
					$obj->product_related_ordering = $k++;
					$product->related[] = $obj;
				}
			}

			if($vendor_id > 1) {
				$vendor_limitation = $config->vendorget($vendor, 'product_max_related_per_product', 0);
				if($vendor_limitation > 0 && count($product->related) > $vendor_limitation) {
					$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MAX_RELATED_PER_PRODUCT_MSG', $vendor_limitation), 'error');
					return false;
				}
			}
		} else {
			unset($product->related);
		}

		if(hikamarket::acl('product/edit/options')) {
			$options = @$formProduct['options'];
			$product->options = array();
			if(!empty($options)) {
				$k = 0;
				foreach($options as $r) {
					$obj = new stdClass();
					$obj->product_related_id = (int)$r;
					$obj->product_related_ordering = $k++;
					$product->options[] = $obj;
				}
			}

			if($vendor_id > 1) {
				$vendor_limitation = $config->vendorget($vendor, 'product_max_options_per_product', 0);
				if($vendor_limitation > 0 && count($product->options) > $vendor_limitation) {
					$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MAX_OPTIONS_PER_PRODUCT_MSG', $vendor_limitation), 'error');
					return false;
				}
			}
		} else {
			unset($product->options);
		}

		if(hikamarket::acl('product/edit/price')) {
			$acls = array(
				'value' => hikamarket::acl('product/edit/price/value'),
				'tax' => hikamarket::acl('product/edit/price/tax'),
				'currency' => hikamarket::acl('product/edit/price/currency'),
				'quantity' => hikamarket::acl('product/edit/price/quantity'),
				'acl' => hikashop_level(2) && hikamarket::acl('product/edit/price/acl')
			);

			if(!empty($oldProduct) && !empty($oldProduct->product_id)) {
				$query = 'SELECT * FROM '.hikamarket::table('shop.price').' WHERE price_product_id = ' . (int)$oldProduct->product_id;
				$this->db->setQuery($query);
				$oldProduct->prices = $this->db->loadObjectList();
			}

			$priceData = JRequest::getVar('price', array(), '', 'array');
			$product->prices = array();
			foreach($priceData as $k => $value) {
				if((int)$k == 0 && $k !== 0 && $k !== '0')
					continue;

				$price_id = (int)@$value['price_id'];
				if(!empty($oldProduct) && !empty($price_id) && !empty($oldProduct->prices)) {
					foreach($oldProduct->prices as $p) {
						if($p->price_id == $price_id) {
							$product->prices[$k] = $p;
							break;
						}
					}
				}

				if(empty($product->prices[$k]))
					$product->prices[$k] = new stdClass();

				if(($acls['value'] || $acls['tax']) && isset($value['price_value']))
					$product->prices[$k]->price_value = hikamarket::toFloat($value['price_value']);
				if($acls['acl'] && isset($value['price_access']))
					$product->prices[$k]->price_access = preg_replace('#[^a-z0-9,]#i', '', $value['price_access']);

				if($acls['currency'] && isset($value['price_currency_id']))
					$product->prices[$k]->price_currency_id = (int)$value['price_currency_id'];
				if(empty($product->prices[$k]->price_currency_id))
					$product->prices[$k]->price_currency_id = $shopConfig->get('main_currency',1);

				if($acls['quantity'] && isset($value['price_min_quantity'])) {
					$product->prices[$k]->price_min_quantity = (int)$value['price_min_quantity'];
					if($product->prices[$k]->price_min_quantity == 1)
						$product->prices[$k]->price_min_quantity = 0;
				}
				if(empty($product->prices[$k]->price_min_quantity))
					$product->prices[$k]->price_min_quantity = 0;
			}

			if($vendor_id > 1) {
				$vendor_limitation = $config->vendorget($vendor, 'product_max_prices_per_product', 0);
				if($vendor_limitation > 0 && count($product->prices) > $vendor_limitation) {
					$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MAX_PRICES_PER_PRODUCT_MSG', $vendor_limitation), 'error');
					return false;
				}

				$limit_min_price = $config->vendorget($vendor, 'product_min_price', null);
				$limit_max_price = $config->vendorget($vendor, 'product_max_price', null);
				if(!empty($limit_min_price)) $limit_min_price = json_decode($limit_min_price, true);
				if(!empty($limit_max_price)) $limit_max_price = json_decode($limit_max_price, true);
				if(!empty($limit_min_price) || !empty($limit_max_price)) {
					foreach($product->prices as $p) {
						$min = (isset($limit_min_price[$p->price_currency_id]) && $p->price_value < $limit_min_price[$p->price_currency_id]);
						$max = (isset($limit_max_price[$p->price_currency_id]) && $p->price_value > $limit_max_price[$p->price_currency_id]);
						if(!$min && !$max)
							continue;
						$currencyClass = hikamarket::get('shop.class.currency');
						$currency = $currencyClass->get($p->price_currency_id);
						if($min) {
							$min_price = $limit_min_price[$p->price_currency_id] . ' ' . $currency->currency_code;
							$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MIN_PRICE_MSG', $min_price), 'error');
						} else {
							$max_price = $limit_max_price[$p->price_currency_id] . ' ' . $currency->currency_code;
							$app->enqueueMessage(JText::sprintf('VENDOR_PRODUCT_MAX_PRICE_MSG', $max_price), 'error');
						}
						return false;
					}
				}
			}
		} else {
			unset($product->prices);
		}

		unset($product->images);
		unset($product->imagesorder);
		if(hikamarket::acl('product/edit/images')) {
			$product->images = @$formProduct['product_images'];
			JArrayHelper::toInteger($product->images);

			$product->imagesorder = array();
			foreach($product->images as $k => $v) {
				$product->imagesorder[$v] = $k;
			}
		}
		unset($product->product_images);

		unset($product->files);
		if(hikamarket::acl('product/edit/files')) {
			$product->files = @$formProduct['product_files'];
			JArrayHelper::toInteger($product->files);
		}
		unset($product->product_files);

		if(hikamarket::acl('product/edit/saledates')) {
			if(!empty($product->product_sale_start)){
				$product->product_sale_start = hikamarket::getTime($product->product_sale_start);
			}
			if(!empty($product->product_sale_end)){
				$product->product_sale_end = hikamarket::getTime($product->product_sale_end);
			}
		} else {
			unset($product->product_sale_start);
			unset($product->product_sale_end);
		}

		if(!empty($product->product_code))
			$product->product_code = trim($product->product_code);

		unset($product->characteristics);
		unset($product->characteristic);
		if(hikamarket::acl('product/edit/characteristics') && !empty($formData['characteristics']) && is_array($formData['characteristics'])) {
			$characteristics = $formData['characteristics'];
			JArrayHelper::toInteger($characteristics);
			if($new) {
				$characteristics = $this->checkProductCharacteristics($characteristics, $vendor_id, true);
				if(!empty($characteristics))
					$product->characteristics = $characteristics;
			} else
				$product->characteristics = $characteristics;
		}

		if($new) {
			$template_id = 0;
			if(!empty($vendor->vendor_template_id)) {
				$template_id = $vendor->vendor_template_id;
			} else if((int)$config->get('default_template_id', 0) > 0) {
				$template_id = (int)$config->get('default_template_id', 0);
			}

			$template = null;
			if(!empty($template_id))
				$template = $this->getRaw($template_id, true);
			if(!empty($template)) {
				$exclude_template_fields = array(
					'product_type','product_code','product_alias','product_hit','product_created','product_modified',
					'product_last_seen_date','product_sales', 'product_average_score','product_total_vote'
				);
				foreach($template as $k => $v) {
					if(!in_array($k, $exclude_template_fields) && !isset($product->$k))
						$product->$k = $v;
				}
			}
		}

		if($shopConfig->get('alias_auto_fill', 1) && empty($product->product_alias) && !empty($product->product_name)) {
			$productClass->addAlias($product);
			if($shopConfig->get('sef_remove_id', 0) && (int)$product->alias > 0)
				$product->alias = $shopConfig->get('alias_prefix', 'p') . $product->alias;
			$product->product_alias = $product->alias;
			unset($product->alias);
		}
		$autoKeyMeta = $shopConfig->get('auto_keywords_and_metadescription_filling', 0);
		if($autoKeyMeta) {
			$seoHelper = hikamarket::get('shop.helper.seo');
			$seoHelper->autoFillKeywordMeta($product, 'product');
		}


		$status = $this->save($product);
		if($status) {
			if(hikamarket::acl('product/edit/category') || $new)
				$productClass->updateCategories($product, $status);
			if(hikamarket::acl('product/edit/price') || ($new && !empty($product->prices)))
				$productClass->updatePrices($product, $status);
			if(hikamarket::acl('product/edit/files') || ($new && !empty($product->files)))
				$productClass->updateFiles($product, $status, 'files');
			if(hikamarket::acl('product/edit/images') || ($new && !empty($product->images)))
				$productClass->updateFiles($product, $status, 'images', $product->imagesorder);
			if(hikamarket::acl('product/edit/related') || ($new && !empty($product->related)))
				$productClass->updateRelated($product, $status, 'related');
			if(hikamarket::acl('product/edit/options') || ($new && !empty($product->options)))
				$productClass->updateRelated($product, $status, 'options');

			if(hikamarket::acl('product/edit/characteristics') && !empty($product->characteristics)) {
				if($new) {
					$product->product_type = 'main';
					$productClass->updateCharacteristics($product, $status, 0);
				} else {
					$query = 'UPDATE ' . hikamarket::table('shop.variant') . ' SET ordering = CASE variant_characteristic_id ';
					foreach($product->characteristics as $key => $val) {
						$query .= ' WHEN ' . (int)$val . ' THEN ' . ($key + 1);
					}
					$query .= ' ELSE ordering END WHERE variant_characteristic_id IN ('.implode(',', $product->characteristics).') AND variant_product_id = ' . (int)$status;
					$this->db->setQuery($query);
					$this->db->query();
				}

				if(!empty($product->product_code) && !empty($oldProduct->product_code) && $product->product_code != $oldProduct->product_code) {
					if(HIKASHOP_J30)
						$product_code = "'" . $this->db->escape($oldProduct->product_code, true) . "%'";
					else
						$product_code = "'" . $this->db->getEscaped($oldProduct->product_code, true) . "%'";

					$query = 'UPDATE '.hikamarket::table('shop.product').
							' SET `product_code` = REPLACE(`product_code`,' . $this->db->Quote($oldProduct->product_code) . ',' . $this->db->Quote($product->product_code) . ')'.
							' WHERE `product_code` LIKE '.$product_code.' AND product_parent_id = '.(int)$product->product_id.' AND product_type = '.$this->db->Quote('variant');
					$this->db->setQuery($query);
					$this->db->query();
				}
			}


			if(hikamarket::acl('product/variant') && !empty($formData['variant']))
				$this->frontSaveVariantForm();

			$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'));

			if($vendor_id > 1 || $config->get('always_send_product_email', 0)) {
				$mailClass = hikamarket::get('class.mail');
				$infos = new stdClass;
				$infos->vendor = hikamarket::loadVendor(true);
				$infos->user = hikamarket::loadUser(true);
				$infos->product =& $product;
				if($new)
					$mail = $mailClass->load('product_creation', $infos);
				else
					$mail = $mailClass->load('product_modification', $infos);

				if(!empty($mail) && $mail->published) {
					if(!empty($mail->subject))
						$mail->subject = JText::sprintf($mail->subject, HIKASHOP_LIVE);
					else if($new)
						$mail->subject = JText::sprintf('EMAIL_MARKET_PRODUCT_CREATION', HIKASHOP_LIVE);
					else
						$mail->subject = JText::sprintf('EMAIL_MARKET_PRODUCT_UPDATE', HIKASHOP_LIVE);

					$mail->from_email = $shopConfig->get('from_email');
					$mail->from_name = $shopConfig->get('from_name');
					if(HIKASHOP_J30) {
						$mail->mailer->addReplyTo($mailClass->cleanEmail($infos->user->user_email), $infos->user->name);
					} else {
						$mail->mailer->addReplyTo(array($infos->user->user_email, $infos->user->name));
					}

					if(!empty($infos->email))
						$mail->dst_email = $infos->email;
					else
						$mail->dst_email = $shopConfig->get('from_email');

					if(!empty($infos->name))
						$mail->dst_name = $infos->name;
					else
						$mail->dst_name = $shopConfig->get('from_name');

					if(!empty($mail->dst_email))
						$mailClass->sendMail($mail);
				}
			}
		} else {
			JRequest::setVar('fail', $product);
			if(empty($product->product_id) && empty($product->product_code) && empty($product->product_name)) {
				$app->enqueueMessage(JText::_('SPECIFY_NAME_AND_CODE'), 'error');
			} else {
				$query = 'SELECT product_id FROM '.hikamarket::table('shop.product').' WHERE product_code  = '.$this->db->Quote($product->product_code) . ' AND NOT (product_id = ' . (int)(@$product->product_id) . ')';
				$this->db->setQuery($query, 0, 1);
				if($this->db->loadResult())
					$app->enqueueMessage(JText::_('DUPLICATE_PRODUCT'), 'error');
				else
					$app->enqueueMessage(JText::_('PRODUCT_SAVE_UNKNOWN_ERROR'), 'error');
			}
		}
		return $status;
	}

	public function frontSaveVariantForm() {
		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$shopConfig = hikamarket::config(false);
		$product_id = hikamarket::getCID('variant_id');
		$parent_product_id = JRequest::getInt('product_id', 0);
		$productClass = hikamarket::get('shop.class.product');
		$fieldsClass = hikamarket::get('shop.class.field');
		$vendorClass = hikamarket::get('class.vendor');
		$vendor = hikamarket::loadVendor(true, false);
		$vendor_id = $vendor->vendor_id;

		$formData = JRequest::getVar('data', array(), '', 'array');
		$formVariant = array();
		if(!empty($formData['variant'])){
			$formVariant = $formData['variant'];
		}
		if(!empty($formData['product'])) {
			$product_id = (int)$formVariant['product_id'];
		}

		if(!hikamarket::acl('product/variant'))
			return false;

		$new = false;
		$oldProduct = null;
		$productParent = null;
		if(empty($product_id))
			$new = true;
		if(!$new) {
			$oldProduct = $productClass->get($product_id);
			$editAllVendors = false;
			if($vendor_id == 1 && hikamarket::acl('product/subvendor'))
				$editAllVendors = true;

			if($oldProduct->product_type != 'variant')
				return false;
			if((int)$oldProduct->product_parent_id != $parent_product_id && $parent_product_id > 0)
				return false;
			$parent_product_id = (int)$oldProduct->product_parent_id;

			$productParent = $productClass->get($parent_product_id);

			$productVendorId = (int)$oldProduct->product_vendor_id;
			if($productVendorId == 0)
				$productVendorId = (int)$productParent->product_vendor_id;

			if($productVendorId != $vendor_id && !$editAllVendors && ($productVendorId > 1 || $vendor_id > 1))
				return false;
		} else {
			if(!hikamarket::acl('product/add'))
				return false;

			if(empty($parent_product_id))
				return false;

			$productParent = $productClass->get($parent_product_id);
			if($productParent->product_type != 'main')
				return false;

			$productVendorId = (int)$productParent->product_vendor_id;
			if($productVendorId != $vendor_id && !$editAllVendors && ($productVendorId > 1 || $vendor_id > 1))
				return false;


		}
		$product = $fieldsClass->getInput('variant', $oldProduct, true, 'data', false, 'display:vendor_product_edit');
		if(empty($product))
			return false;

		$this->db->setQuery('SELECT field.* FROM '.hikamarket::table('shop.field').' as field WHERE field.field_table = '.$this->db->Quote('product').' ORDER BY field.`field_ordering` ASC');
		$all_fields = $this->db->loadObjectList('field_namekey');
		$edit_fields = hikamarket::acl('product/variant/customfields');
		foreach($all_fields as $fieldname => $field) {
			if(!$edit_fields || empty($field->field_published) || (strpos($field->field_display, ';vendor_product_edit=1') === false) ) {
				unset($product->$fieldname);
			}
		}

		$product->product_id = $product_id;
		$product->product_type = 'variant';
		$product->product_parent_id = $parent_product_id; // TODO

		if( !hikamarket::level(1) || ($vendor_id > 1 && isset($product->product_vendor_id) && $product->product_vendor_id != $vendor_id) || ($vendor_id == 1 && !hikamarket::acl('product/edit/vendor')) ) {
			unset($product->product_vendor_id);
		}

		if(hikamarket::acl('product/variant/characteristics')) {
			$product->characteristics = array();
			unset($product->characteristic);

			$query = 'SELECT v.*, c.* FROM '.hikamarket::table('shop.variant').' AS v '.
				' INNER JOIN '.hikamarket::table('shop.characteristic').' as c ON v.variant_characteristic_id = c.characteristic_id '.
				' WHERE variant_product_id = ' . (int)$parent_product_id;
			$this->db->setQuery($query);
			$characteristics = $this->db->loadObjectList('characteristic_id');

			$characteristic_ids = array();
			foreach($characteristics as $characteristic) {
				if((int)$characteristic->characteristic_parent_id == 0)
					$characteristic_ids[(int)$characteristic->characteristic_id] = (int)$characteristic->characteristic_id;
				else
					$characteristics[(int)$characteristic->characteristic_parent_id]->default = (int)$characteristic->characteristic_id;
			}

			$query = 'SELECT c.* FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
				' WHERE c.characteristic_parent_id IN ('.implode(',', $characteristic_ids).')';
			$this->db->setQuery($query);
			$characteristics_values = $this->db->loadObjectList('characteristic_id');

			foreach($characteristics as $characteristic) {
				if((int)$characteristic->characteristic_parent_id == 0) {
					$i = (int)$characteristic->characteristic_id;
					$v = (int)@$formVariant['characteristic'][$i];

					if(isset($characteristics_values[$v]) && $characteristics_values[$v]->characteristic_parent_id = $i)
						$product->characteristics[$v] = $i;
					else
						$product->characteristics[$characteristic->default] = $i;
				}
			}
		} else {
			unset($product->characteristics);
			unset($product->characteristic);
		}

		if(!hikamarket::acl('product/variant/name')) { unset($product->product_name); }
		if(!hikamarket::acl('product/variant/code')) { unset($product->product_code); }
		if(!hikamarket::acl('product/variant/weight')) { unset($product->product_weight); }
		if(!hikamarket::acl('product/variant/volume')) { unset($product->product_volume); }
		if(!hikamarket::acl('product/variant/published')) { unset($product->product_published); }
		if(!hikamarket::acl('product/variant/acl')) { unset($product->product_access); }

		if(hikamarket::acl('product/variant/qtyperorder')) {
			if(isset($product->product_max_per_order))
				$product->product_max_per_order = (int)$product->product_max_per_order;
			if(isset($product->product_min_per_order))
				$product->product_min_per_order = (int)$product->product_min_per_order;
		} else {
			unset($product->product_max_per_order);
			unset($product->product_min_per_order);
		}

		$removeFields = array(
			'manufacturer_id', 'page_title', 'url', 'meta_description', 'keywords', 'alias', 'msrp', 'canonical',
			'contact', 'delay_id', 'tax_id', 'waitlist', 'display_quantity_field',
			'status', 'hit', 'created', 'modified', 'last_seen_date', 'sales', 'layout', 'average_score', 'total_vote',
			'warehouse_id', 'group_after_purchase',
		);
		foreach($removeFields as $rf) {
			$rf = 'product_'.$rf;
			unset($product->$rf);
		}

		unset($product->categories);
		unset($product->related);
		unset($product->options);

		if(hikamarket::acl('product/variant/description')) {
			$product->product_description = JRequest::getVar('product_variant_description','','','string',JREQUEST_ALLOWRAW);
			$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
			$product->product_description = $safeHtmlFilter->clean($product->product_description, 'string');
		}

		if(hikamarket::acl('product/variant/price')) {
			$acls = array(
				'value' => hikamarket::acl('product/variant/price/value'),
				'tax' => hikamarket::acl('product/variant/price/tax'),
				'currency' => hikamarket::acl('product/variant/price/currency'),
				'quantity' => hikamarket::acl('product/variant/price/quantity'),
				'acl' => hikashop_level(2) && hikamarket::acl('product/variant/price/acl')
			);

			if(!empty($oldProduct)) {
				$query = 'SELECT * FROM '.hikamarket::table('shop.price').' WHERE price_product_id = ' . (int)$oldProduct->product_id;
				$this->db->setQuery($query);
				$oldProduct->prices = $this->db->loadObjectList();
			}

			$priceData = JRequest::getVar('variantprice', array(), '', 'array');
			$product->prices = array();
			foreach($priceData as $k => $value) {
				if((int)$k == 0 && $k !== 0 && $k !== '0')
					continue;

				$price_id = (int)@$value['price_id'];
				if(!empty($oldProduct) && !empty($price_id) && !empty($oldProduct->prices)) {
					foreach($oldProduct->prices as $p) {
						if($p->price_id == $price_id) {
							$product->prices[$k] = $p;
							break;
						}
					}
				}

				if(empty($product->prices[$k]))
					$product->prices[$k] = new stdClass();

				if(($acls['value'] || $acls['tax']) && isset($value['price_value']))
					$product->prices[$k]->price_value = hikamarket::toFloat($value['price_value']);
				if($acls['acl'] && isset($value['price_access']))
					$product->prices[$k]->price_access = preg_replace('#[^a-z0-9,]#i', '', $value['price_access']);
				if($acls['currency'] && isset($value['price_currency_id']))
					$product->prices[$k]->price_currency_id = (int)$value['price_currency_id'];
				if(empty($product->prices[$k]->price_currency_id))
					$product->prices[$k]->price_currency_id = $shopConfig->get('main_currency',1);
				if($acls['quantity'] && isset($value['price_min_quantity'])) {
					$product->prices[$k]->price_min_quantity = (int)$value['price_min_quantity'];
					if($product->prices[$k]->price_min_quantity == 1)
						$product->prices[$k]->price_min_quantity = 0;
				}
				if(empty($product->prices[$k]->price_min_quantity))
					$product->prices[$k]->price_min_quantity = 0;
			}
		} else {
			unset($product->prices);
		}

		if(hikamarket::acl('product/variant/images')) {
			$product->images = @$formVariant['product_images'];
			JArrayHelper::toInteger($product->images);

			$product->imagesorder = array();
			foreach($product->images as $k => $v) {
				$product->imagesorder[$v] = $k;
			}
		} else {
			unset($product->imagesorder);
		}
		unset($product->product_images);

		if(hikamarket::acl('product/variant/files')) {
			$product->files = @$formVariant['product_files'];
			JArrayHelper::toInteger($product->files);
		} else {
			unset($product->files);
		}
		unset($product->product_files);

		if(hikamarket::acl('product/variant/saledates')) {
			if(!empty($product->product_sale_start)){
				$product->product_sale_start = hikamarket::getTime($product->product_sale_start);
			}
			if(!empty($product->product_sale_end)){
				$product->product_sale_end = hikamarket::getTime($product->product_sale_end);
			}
		} else {
			unset($product->product_sale_start);
			unset($product->product_sale_end);
		}

		if(!empty($product->product_code))
			$product->product_code = trim($product->product_code);

		$status = $this->save($product);
		if($status) {
			if(hikamarket::acl('product/variant/price'))
				$productClass->updatePrices($product, $status);
			if(hikamarket::acl('product/variant/files'))
				$productClass->updateFiles($product, $status, 'files');
			if(hikamarket::acl('product/variant/images'))
				$productClass->updateFiles($product, $status, 'images', $product->imagesorder);
			if(hikamarket::acl('product/variant/characteristics'))
				$productClass->updateCharacteristics($product, $status);
		} else {
			JRequest::setVar('fail', $product);
			if(empty($product->product_id) && empty($product->product_code) && empty($product->product_name)) {
				$app->enqueueMessage(JText::_('SPECIFY_NAME_AND_CODE'), 'error');
			} else {
				$query = 'SELECT product_id FROM '.hikamarket::table('shop.product').' WHERE product_code  = '.$this->db->Quote($product->product_code) . ' AND NOT (product_id = ' . (int)(@$product->product_id) . ')';
				$this->db->setQuery($query, 0, 1);
				if($this->db->loadResult()) {
					$app->enqueueMessage(JText::_('DUPLICATE_PRODUCT'), 'error');
				} else {
					$app->enqueueMessage(JText::_('PRODUCT_SAVE_UNKNOWN_ERROR'), 'error');
				}
			}
		}

		return $product_id;
	}

	public function addCharacteristic($product_id, $characteristic_id, $characteristic_value_id, $vendor_id = 0) {
		if((int)$product_id <= 0 || (int)$characteristic_id <= 0 || (int)$characteristic_value_id <= 0)
			return false;

		$product_characteristics = $this->getProductCharacteristics($product_id);

		if(in_array((int)$characteristic_id, $product_characteristics))
			return false;

		$new_characteristics = array_merge($product_characteristics, array((int)$characteristic_id));

		$query = 'SELECT c.characteristic_id, c.characteristic_value, c.characteristic_parent_id FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
			' WHERE (c.characteristic_parent_id = '.$characteristic_id.' AND c.characteristic_vendor_id IN (0, '.(int)$vendor_id.'))';
		if(!empty($product_characteristics))
			$query .= 'OR (c.characteristic_parent_id IN (' . implode(',', $product_characteristics) . '))';
		$this->db->setQuery($query);
		$characteristic_values = $this->db->loadObjectList('characteristic_id');

		if(!isset($characteristic_values[ (int)$characteristic_value_id ]) || (int)$characteristic_values[ (int)$characteristic_value_id ]->characteristic_parent_id != (int)$characteristic_id)
			return false;

		$query = 'SELECT c.characteristic_id, c.characteristic_parent_id FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
			' INNER JOIN ' . hikamarket::table('shop.variant') . ' AS v ON v.variant_characteristic_id = c.characteristic_id '.
			' WHERE c.characteristic_parent_id > 0 AND v.variant_product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$default_values = $this->db->loadObjectList('characteristic_parent_id');

		if(empty($default_values))
			$default_values = array();

		$e = new stdClass();
		$e->characteristic_id = (int)$characteristic_value_id;
		$e->characteristic_parent_id = (int)$characteristic_id;
		$default_values[ (int)$characteristic_id ] = $e;

		$query = 'SELECT product_code FROM ' . hikamarket::table('shop.product') . ' WHERE product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$product = $this->db->loadObject();

		$elem = new stdClass();
		$elem->product_type = 'main';
		$elem->product_id = $product_id;
		$elem->product_code = $product->product_code;
		$elem->oldCharacteristics = $product_characteristics;
		$elem->characteristics = array();
		$i = 1;
		foreach($new_characteristics as $c) {
			$e = new stdClass();
			$e->characteristic_id = (int)$c;
			$e->ordering = $i++;
			$e->default_id = $default_values[ (int)$c ]->characteristic_id;
			$e->values = array();

			$elem->characteristics[ (int)$c ] = $e;
		}

		foreach($characteristic_values as $k => $v) {
			if(!isset($elem->characteristics[ (int)$v->characteristic_parent_id ]))
				continue;
			$elem->characteristics[ (int)$v->characteristic_parent_id ]->values[ (int)$k ] = $v->characteristic_value;
		}

		$shopProductClass = hikamarket::get('shop.class.product');
		$ret = $shopProductClass->updateCharacteristics($elem, (int)$product_id, 0);

		if(!$ret)
			return false;
		return ($i - 1);
	}

	public function populateVariant($product_id, $characteristic_data) {
		if((int)$product_id <= 0)
			return false;

		if(empty($characteristic_data['variant_add']))
			return false;

		$product_characteristics = $this->getProductCharacteristics($product_id);

		foreach($characteristic_data['variant_add'] as $k => $v) {
			if(!in_array($k, $product_characteristics))
				return false;
		}

		if(count($characteristic_data['variant_add']) != count($product_characteristics))
			return false;

		$query = 'SELECT product_code FROM ' . hikamarket::table('shop.product') . ' WHERE product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$product = $this->db->loadObject();

		$elem = new stdClass();
		$elem->product_type = 'main';
		$elem->product_id = $product_id;
		$elem->product_code = $product->product_code;
		$elem->oldCharacteristics = $product_characteristics;
		$elem->characteristics = array();
		$i = 1;
		foreach($characteristic_data['variant_add'] as $k => $v) {
			$e = new stdClass();
			$e->characteristic_id = (int)$k;
			$e->default_id = null;
			$e->ordering = null;
			JArrayHelper::toInteger($v);
			$e->values = array_combine($v, $v);

			$elem->characteristics[ (int)$k ] = $e;
		}

		$shopProductClass = hikamarket::get('shop.class.product');
		$ret = $shopProductClass->updateCharacteristics($elem, (int)$product_id, 2);

		return $ret;
	}

	public function duplicateVariant($product_id, $cid, $data) {
		if((int)$product_id <= 0)
			return false;

		if(empty($cid) || empty($data['variant_duplicate']) || empty($data['variant_duplicate']['characteristic']) || empty($data['variant_duplicate']['variants']))
			return false;

		$product_characteristics = $this->getProductCharacteristics($product_id);

		$characteristic_id = (int)$data['variant_duplicate']['characteristic'];

		if(!in_array((int)$characteristic_id, $product_characteristics))
			return false;

		if(!in_array($characteristic_id, $product_characteristics))
			return false;

		$query = 'SELECT product_code FROM ' . hikamarket::table('shop.product') . ' WHERE product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$product = $this->db->loadObject();

		$elem = new stdClass();
		$elem->product_type = 'main';
		$elem->product_id = $product_id;
		$elem->product_code = $product->product_code;
		$elem->oldCharacteristics = $product_characteristics;
		$elem->duplicateVariants = $cid;
		$elem->characteristics = array();
		$i = 1;

		$e = new stdClass();
		$e->characteristic_id = (int)$characteristic_id;
		$e->default_id = null;
		$e->ordering = null;
		JArrayHelper::toInteger($data['variant_duplicate']['variants']);
		$e->values = array_combine($data['variant_duplicate']['variants'], $data['variant_duplicate']['variants']);

		$elem->characteristics[ $characteristic_id ] = $e;

		$shopProductClass = hikamarket::get('shop.class.product');
		return $shopProductClass->updateCharacteristics($elem, (int)$product_id, 2);
	}

	public function removeCharacteristic($product_id, $characteristic_id) {
		if((int)$product_id <= 0 || (int)$characteristic_id <= 0)
			return false;

		$product_characteristics = $this->getProductCharacteristics($product_id);

		if(!in_array((int)$characteristic_id, $product_characteristics))
			return false;

		$query = 'SELECT c.characteristic_id, c.characteristic_value, c.characteristic_parent_id FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
			' WHERE c.characteristic_parent_id IN (' . implode(',', $product_characteristics) . ')';
		$this->db->setQuery($query);
		$characteristic_values = $this->db->loadObjectList('characteristic_id');

		$query = 'SELECT c.characteristic_id, c.characteristic_parent_id FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
			' INNER JOIN ' . hikamarket::table('shop.variant') . ' AS v ON v.variant_characteristic_id = c.characteristic_id '.
			' WHERE c.characteristic_parent_id > 0 AND v.variant_product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$default_values = $this->db->loadObjectList('characteristic_parent_id');

		if(empty($default_values))
			$default_values = array();

		$query = 'SELECT product_code FROM ' . hikamarket::table('shop.product') . ' WHERE product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$product = $this->db->loadObject();

		$elem = new stdClass();
		$elem->product_type = 'main';
		$elem->product_id = $product_id;
		$elem->product_code = $product->product_code;
		$elem->oldCharacteristics = $product_characteristics;
		$elem->characteristics = array();
		$i = 1;
		foreach($product_characteristics as $c) {
			if($c == (int)$characteristic_id)
				continue;

			$e = new stdClass();
			$e->characteristic_id = (int)$c;
			$e->ordering = $i++;
			$e->default_id = $default_values[ (int)$c ]->characteristic_id;
			$e->values = array();

			$elem->characteristics[ (int)$c ] = $e;
		}
		foreach($characteristic_values as $k => $v) {
			if(!isset($elem->characteristics[ (int)$v->characteristic_parent_id ]))
				continue;
			$elem->characteristics[ (int)$v->characteristic_parent_id ]->values[ (int)$k ] = $v->characteristic_value;
		}

		$shopProductClass = hikamarket::get('shop.class.product');
		$ret = $shopProductClass->updateCharacteristics($elem, (int)$product_id, 1);

		if(!$ret)
			return false;
		return ($i - 1);
	}

	public function deleteVariants($product_id, $variant_ids) {
		if((int)$product_id <= 0)
			return false;
		if(empty($variant_ids))
			return false;

		JArrayHelper::toInteger($variant_ids);
		$query = 'SELECT p.product_id FROM ' . hikamarket::table('shop.product') . ' AS p '.
				' WHERE p.product_type = ' . $this->db->Quote('variant') . ' AND p.product_parent_id = ' . (int)$product_id.
				' AND p.product_id IN (' . implode(',', $variant_ids) . ')';
		$this->db->setQuery($query);
		if(!HIKASHOP_J25)
			$ids = $this->db->loadResultArray();
		else
			$ids = $this->db->loadColumn();

		if(empty($ids))
			return false;

		JArrayHelper::toInteger($ids);
		$shopProductClass = hikamarket::get('shop.class.product');
		$ret = $shopProductClass->delete($ids);
		return $ret;
	}

	private function getProductCharacteristics($product_id) {
		if((int)$product_id <= 0)
			return false;

		$query = 'SELECT c.characteristic_id FROM ' . hikamarket::table('shop.variant') . ' AS v '.
			' INNER JOIN ' . hikamarket::table('shop.characteristic') . ' AS c ON v.variant_characteristic_id = c.characteristic_id '.
			' WHERE c.characteristic_parent_id = 0 AND v.variant_product_id = ' . (int)$product_id.' '.
			' ORDER BY v.ordering ASC';
		$this->db->setQuery($query);
		if(!HIKASHOP_J25)
			$ret = $this->db->loadResultArray();
		else
			$ret = $this->db->loadColumn();

		if(empty($ret))
			$ret = array();
		else
			JArrayHelper::toInteger($ret);
		return $ret;
	}

	public function backSaveForm(&$product) {
		if(!isset($product->product_id))
			return;
		$product_id = (int)$product->product_id;

		$formData = JRequest::getVar('market', array(), '', 'array');
		if(empty($formData) || empty($formData['form']))
			return;

		$feeClass = hikamarket::get('class.fee');
		if($feeClass)
			$feeClass->saveProduct($product->product_id, $formData['product_fee']);
	}

	public function save(&$product) {
		$this->checkProductCode($product);

		JPluginHelper::importPlugin('hikamarket');
		$productClass = hikamarket::get('shop.class.product');
		$status = $productClass->save($product);
		return $status;
	}

	protected function checkProductCode(&$product) {
		$config = hikamarket::config();

		$vendor_id = hikamarket::loadVendor(false, false);
		$vendor_prefix = $config->get('prefix_product_code', null);
		if(!empty($vendor_prefix) && $vendor_id > 1) {
			$prefix = 'v' . $vendor_id . '_';
			if(is_string($vendor_prefix)) {
				$prefix = $vendor_prefix . $vendor_id . '_';
				if(strpos($vendor_prefix, '%d') !== false)
					$prefix = sprintf($vendor_prefix, $vendor_id);
			}
			if(substr($product->product_code, 0, strlen($prefix)) != $prefix)
				$product->product_code = $prefix . $product->product_code;
		}

		if(!$config->get('avoid_duplicate_product_code', 0))
			return;

		if(!empty($product->product_id)) {
			$futur_product_id = $product->product_id;
		} else {
			$query = 'SELECT MAX(`product_id`) FROM '.hikamarket::table('shop.product');
			$this->db->setQuery($query);
			$futur_product_id = (1 + (int)$this->db->loadResult());

			if(empty($product->product_code)) {
				$test = '';

				if(!empty($product->product_name)) {
					$search = explode(',','ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u');
					$replace = explode(',','c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u');
					$test = str_replace($search, $replace, $product->product_name);
					$test = preg_replace('#[^a-z0-9_-]#i', '', $test);
				}

				if(empty($test)) {
					$product->product_code = 'product_' . $futur_product_id;
				} else {
					$test = str_replace($search, $replace, $product->product_name);
					$product->product_code = preg_replace('#[^a-z0-9_-]#i', '_', $test);
				}
			}
		}

		if(!empty($product->product_code)) {
			if(empty($product->product_id)) {
				$query = 'SELECT COUNT(*) FROM '.hikamarket::table('shop.product').' WHERE product_code = '.$this->db->Quote($product->product_code);
				$this->db->setQuery($query);
				$isSame = ((int)$this->db->loadResult() > 0);
			} else {
				$query = 'SELECT COUNT(*) FROM '.hikamarket::table('shop.product').' WHERE product_code = '.$this->db->Quote($product->product_code).' AND product_id != '.(int)$product->product_id;
				$this->db->setQuery($query);
				$isSame = ((int)$this->db->loadResult() > 0);
			}

			if($isSame) {
				if(HIKASHOP_J30)
					$productCodeEscaped = "'" . $this->db->escape($product->product_code . '_', true) . "%'";
				else
					$productCodeEscaped = "'" . $this->db->getEscaped($product->product_code . '_', true) . "%'";
				$query = 'SELECT product_code FROM '.hikamarket::table('shop.product').' WHERE product_code LIKE '.$productCodeEscaped;
				if(!empty($product->product_id)) {
					$query .= ' AND product_id != '.(int)$product->product_id;
				}
				$query .= ' ORDER BY product_code DESC';
				$this->db->setQuery($query, 0, 1);

				$last_product_code = $this->db->loadResult();
				$suffix = substr($last_product_code, 0, strlen($product->product_code) + 1);
				if(!empty($suffix) && (int)$suffix > 0)
					$product->product_code .= '_' . ((int)$suffix + 1);
				else
					$product->product_code .= '_' . $futur_product_id;

				$warning_key = 'WARNING_DUPLICATE_PRODUCT_CODE_MODIFIED';
				$warning_message = JText::_($warning_key);
				if(!empty($warning_message) && $warning_message != $warning_key) {
					$app = JFactory::getApplication();
					$app->enqueueMessage($warning_message);
				}
			}
		}
	}

	public function setDefaultVariant($product_id, $variant_id) {
		if(!hikamarket::acl('product/variant'))
			return false;

		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$shopConfig = hikamarket::config(false);
		$vendor = hikamarket::loadVendor(true, false);
		$vendor_id = $vendor->vendor_id;

		if(!hikamarket::isVendorProduct($product_id))
			return false;

		$productClass = hikamarket::get('shop.class.product');
		$variant = $productClass->get((int)$variant_id);
		if((int)$variant->product_parent_id != $product_id)
			return false;

		$query = 'SELECT variant.*, characteristic.* FROM '.hikamarket::table('shop.variant').' as variant '.
				' LEFT JOIN '.hikamarket::table('shop.characteristic').' AS characteristic ON variant.variant_characteristic_id = characteristic.characteristic_id '.
				' WHERE variant.variant_product_id = '.(int)$product_id;
		$this->db->setQuery($query);
		$original_data = $this->db->loadObjectList('characteristic_id');

		$query = 'SELECT variant.*, characteristic.* FROM '.hikamarket::table('shop.variant').' as variant '.
				' LEFT JOIN '.hikamarket::table('shop.characteristic').' AS characteristic ON variant.variant_characteristic_id = characteristic.characteristic_id '.
				' WHERE variant.variant_product_id = '.(int)$variant_id;
		$this->db->setQuery($query);
		$variant_data = $this->db->loadObjectList();

		$values = array();
		foreach($variant_data as $v) {
			$values[ (int)$v->characteristic_parent_id ] = (int)$v->characteristic_parent_id;
			$values[ (int)$v->characteristic_id ] = (int)$v->characteristic_id;
		}
		unset($values[0]);
		unset($variant_data);

		$query = 'DELETE FROM '.hikamarket::table('shop.variant').' WHERE variant_product_id = '.(int)$product_id;
		$this->db->setQuery($query);
		$this->db->query();

		$query = 'INSERT INTO '.hikamarket::table('shop.variant').' (`variant_characteristic_id`,`variant_product_id`,`ordering`) VALUES ';
		foreach($values as $k => $value) {
			$ordering = '0';
			if(isset($original_data[$k]))
				$ordering = $original_data[$k]->ordering;
			$values[$k] = '('.$k.','.$product_id.','.$ordering.')';
		}
		unset($original_data);

		$this->db->setQuery($query . implode(',', $values) );
		$this->db->query();

		unset($values);
		unset($query);

		return true;
	}


	public function checkProductCharacteristics($characteristics, $vendor_id = 0, $complete_return = false) {
		$query = 'SELECT c.characteristic_id, c.characteristic_value, c.characteristic_parent_id '.
			' FROM ' . hikamarket::table('shop.characteristic') . ' AS c '.
			' WHERE c.characteristic_id IN (' . implode(',', $characteristics) . ') AND c.characteristic_vendor_id IN (0, '.(int)$vendor_id.')';
		$this->db->setQuery($query);
		$characteristics = $this->db->loadObjectList('characteristic_id');

		foreach($characteristics as $k => $c) {
			$c->characteristic_parent_id = (int)$c->characteristic_parent_id;
			if($c->characteristic_parent_id == 0)
				continue;

			if(isset($characteristics[$c->characteristic_parent_id]) && empty($characteristics[$c->characteristic_parent_id]->checked))
				$characteristics[$c->characteristic_parent_id]->checked = $k;
			else
				unset($characteristics[$k]);
		}

		foreach($characteristics as $k => $c) {
			if($c->characteristic_parent_id > 0)
				continue;
			if(empty($c->checked))
				unset($characteristics[$k]);
		}

		if(empty($characteristics))
			return false;

		if(!$complete_return)
			return array_keys($characteristics);

		$ret = array();
		$i = 1;
		foreach($characteristics as $c) {
			if($c->characteristic_parent_id > 0)
				continue;

			$e = new stdClass();
			$e->characteristic_id = (int)$c->characteristic_id;
			$e->ordering = $i++;
			$e->default_id = $c->checked;
			$e->values = array();

			$ret[ $e->characteristic_id ] = $e;
		}

		return $ret;
	}

	public function approve($product_id = 0) {
		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$productClass = hikamarket::get('shop.class.product');
		if(!$app->isAdmin()) {
			$vendor = hikamarket::loadVendor(true, false);
			$vendor_id = $vendor->vendor_id;
		} else
			$vendor_id = 1;

		$oldProduct = $productClass->get($product_id);
		if(empty($oldProduct))
			return false;

		if($oldProduct->product_type != 'waiting_approval' || (!$app->isAdmin() && !hikamarket::acl('product/approve')))
			return false;

		$productVendorId = (int)$oldProduct->product_vendor_id;
		$editAllVendors = ($vendor_id == 1 && hikamarket::acl('product/subvendor'));
		if(!$app->isAdmin() && $productVendorId != $vendor_id && !$editAllVendors && ($productVendorId > 1 || $vendor_id > 1))
			return false;

		$do = true;
		$updateProduct = new stdClass();
		$updateProduct->product_id = (int)$product_id;
		$updateProduct->product_type = 'main';

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikamarket');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeProductApproval', array(&$oldProduct, &$do));

		if(!$do)
			return false;

		$status = $productClass->save($updateProduct);

		if($status && (int)$oldProduct->product_vendor_id > 1) {
			$oldProduct->product_type = 'main';

			$mailClass = hikamarket::get('class.mail');
			$vendorClass = hikamarket::get('class.vendor');
			$shopConfig = hikamarket::config(false);

			$infos = new stdClass;
			$infos->vendor = $vendorClass->get($oldProduct->product_vendor_id);
			$infos->product =& $oldProduct;
			$mail = $mailClass->load('product_approval', $infos);

			if(!empty($mail) && $mail->published) {
				if(!empty($mail->subject))
					$mail->subject = JText::sprintf($mail->subject, HIKASHOP_LIVE);
				else
					$mail->subject = JText::sprintf('EMAIL_MARKET_PRODUCT_APPROVAL', HIKASHOP_LIVE);

				$mail->from_email = $shopConfig->get('from_email');
				$mail->from_name = $shopConfig->get('from_name');

				$mail->dst_email = $infos->vendor->vendor_email;
				$mail->dst_name = $infos->vendor->vendor_name;

				$mailClass->setVendorNotifyEmails($mail, $infos->vendor);
				if(!empty($mail->dst_email))
					$mailClass->sendMail($mail);
			}
		}

		return $status;
	}

	public function getProduct($product_id, $options) {
		$product_id = (int)$product_id;
		if($product_id <= 0)
			return false;

		$query = 'SELECT p.* FROM ' . hikamarket::table('shop.product') . ' AS p ' .
			' WHERE p.product_id = ' . (int)$product_id;
		$this->db->setQuery($query);
		$ret = $this->db->loadObject();

		if(empty($ret) || !in_array($ret->product_type, array('main', 'variant')))
			return false;

		if($ret->product_type == 'variant') {
			$ret->product_parent_id = (int)$ret->product_parent_id;
			if(empty($ret->product_parent_id))
				return false;

			$query = 'SELECT p.* FROM ' . hikamarket::table('shop.product') . ' AS p ' .
				' WHERE p.product_type = \'main\' AND p.product_id = ' . (int)$ret->product_parent_id;
			$this->db->setQuery($query);
		 	$parent = $this->db->loadObject();

			$variant_name = htmlentities( ((!preg_match('!!u', $ret->product_name)) ? utf8_encode($ret->product_name) : $ret->product_name), ENT_QUOTES, "UTF-8");
			foreach(get_object_vars($ret) as $k => $v) {
				if(!empty($ret->$k))
					continue;
				$ret->$k = $parent->$k;
			}

			if(empty($variant_name)) {
				$characteristic_separator = JText::_('HIKA_VARIANT_SEPARATOR');
				if($characteristic_separator == 'HIKA_VARIANT_SEPARATOR')
					$characteristic_separator = ': ';
				$variant_separator = JText::_('HIKA_VARIANTS_MIDDLE_SEPARATOR');
				if($variant_separator == 'HIKA_VARIANTS_MIDDLE_SEPARATOR')
					$variant_separator = ' ';

				$characteristics = $this->getCharacteristics($ret->product_parent_id, $ret->product_id);
				$product_name = htmlentities( ((!preg_match('!!u', $ret->product_name)) ? utf8_encode($ret->product_name) : $ret->product_name), ENT_QUOTES, "UTF-8");

				$variant_name = $product_name . $characteristic_separator;
				foreach($characteristics[ (int)$ret->product_parent_id ] as $k => $c) {
					if($k > 0)
						$variant_name .= $variant_separator;
					$variant_name .= $characteristics[ (int)$ret->product_id ][ (int)$c->characteristic_id ]->characteristic_value;
				}

				$ret->product_name = $variant_name;
			}
		}

		if(!empty($options['price'])) {
			$shopConfig = hikamarket::config(false);
			$currencyClass = hikamarket::get('shop.class.currency');

			$main_currency = (int)$shopConfig->get('main_currency', 1);
			$discount_before_tax = (int)$shopConfig->get('discount_before_tax', 0);

			$user_id = isset($options['price']['user']) ? $options['price']['user'] : 0;
			$currency_id = isset($options['price']['currency']) ? $options['price']['currency'] : null; //
			$zone_id = isset($options['price']['zone']) ? $options['price']['zone'] : null; //
			$qty = isset($options['price']['qty']) ? $options['price']['qty'] : 0;
			$rows = array($ret);
			$ids = array($product_id);
			if($ret->product_type == 'variant') {
				$ids[] = (int)$ret->product_parent_id;
				$parent = new stdClass();
				$parent->product_id = (int)$ret->product_parent_id;
				$parent->product_tax_id = (int)$ret->product_tax_id;
				$rows[] = $parent;
			}

			$currencyClass->getPrices($rows, $ids, $currency_id, $main_currency, $zone_id, $discount_before_tax, $user_id);
			if(empty($rows[0]->prices) && $ret->product_type == 'variant') {
				$rows[0]->prices = $rows[1]->prices;
			}
			$currencyClass->pricesSelection($rows[0]->prices, $qty);
		}

		return $ret;
	}

	protected function getCharacteristics($products, $variants) {
		if(!is_array($products))
			$products = array($products);
		if(!is_array($variants))
			$variants = array($variants);

		$ids = array_merge($products, $variants);

		$query = 'SELECT v.variant_product_id as product_id, c.* '.
				' FROM ' . hikamarket::table('shop.characteristic') . ' AS c ' .
				' INNER JOIN ' . hikamarket::table('shop.variant') . ' AS v ON v.variant_characteristic_id = c.characteristic_id ' .
				' WHERE v.variant_product_id IN ('.implode(',', $ids).') '.
				' ORDER BY v.ordering ASC, c.characteristic_ordering ASC';
		$this->db->setQuery($query);
		$characteristic_data = $this->db->loadObjectList();

		$characteristics = array();
		foreach($characteristic_data as $k => $v) {
			$p = (int)$v->product_id;
			if(!isset($characteristics[ $p ]))
				$characteristics[ $p ] = array();
			if(in_array($p, $products)) { // isset($product_ids[ $p ])) {
				if((int)$v->characteristic_parent_id > 0)
					continue;
				$characteristics[ $p ][] = $v;
			} else {
				$characteristics[ $p ][ (int)$v->characteristic_parent_id ] = $v;
			}
		}
		unset($characteristic_data);

		return $characteristics;
	}

	public function &getNameboxData(&$typeConfig, &$fullLoad, $mode, $value, $search, $options) {
		$ret = array(
			0 => array(),
			1 => array()
		);

		$app = JFactory::getApplication();

		$fullLoad = false;
		$tree_mode = (!isset($typeConfig['mode']) || $typeConfig['mode'] == 'tree');

		$displayFormat = !empty($options['displayFormat']) ? $options['displayFormat'] : @$typeConfig['displayFormat'];

		$depth = (int)@$options['depth'];
		$start = (int)@$options['start'];
		$limit = (int)@$options['limit'];
		$unfold = (int)@$options['unfold'];
		$load_variants = (int)@$options['variants'];
		$is_root = false;
		$set_no_selection = empty($typeConfig['options']['onlyNode']);

		if(empty($start) && !empty($options['root'])) {
			$start = (int)$options['root'];
			$is_root = true;
		}

		if($load_variants) {
			$typeConfig['options']['tree_url'] .= '&variants=1';
		}

		if($depth <= 0)
			$depth = 1;
		if($limit <= 0 && $tree_mode)
			$limit = 200;
		if($limit <= 0 && !$tree_mode)
			$limit = 20;
		if(!$tree_mode)
			$limit++;

		if(!empty($search)) {
			$searchStr = "'%" . ((HIKASHOP_J30) ? $this->db->escape($search, true) : $this->db->getEscaped($search, true) ) . "%'";
		}

		$vendorFilter = '';
		$category_filter = '';
		if(!$app->isAdmin()) {
			$vendor_id = hikamarket::loadVendor(false);
			if($vendor_id === null || $vendor_id === false)
				return $ret;

			if($vendor_id > 1) {
				if(empty($options['allvendors'])) {
					$vendorFilter = ' AND p.product_vendor_id = ' . (int)$vendor_id . ' ';
				} else {
					$vendorFilter = ' AND (p.product_vendor_id = ' . (int)$vendor_id . ' OR p.product_published = 1) ';
					$typeConfig['options']['tree_url'] .= '&allvendors=1';
				}

				$vendorClass = hikamarket::get('class.vendor');
				$rootCategory_id = $vendorClass->getRootCategory($vendor_id);
				if(!empty($rootCategory_id)) {
					$query = 'SELECT c.* FROM ' . hikamarket::table('shop.category') . ' AS c WHERE category_id = ' . $rootCategory_id;
					$this->db->setQuery($query);
					$rootCategory = $this->db->loadObject();

					$category_filter = ' AND (c.category_left >= ' . (int)$rootCategory->category_left . ' AND c.category_right <= ' . (int)$rootCategory->category_right . ') ';
				}
			}
		}

		if($tree_mode) {
			if(empty($search)) {
				$query = 'SELECT c.*, 0 as `base_depth`' .
					' FROM ' .  hikamarket::table('shop.category') . ' AS c ' .
					' WHERE c.category_type IN (\'product\',\'manufacturer\',\'vendor\',\'root\') AND c.category_depth >= 0 AND c.category_depth <= ' . $depth .
					$category_filter .
					' ORDER BY c.category_left ASC, c.category_name ASC';

				if($start > 0) {
					$query = 'SELECT c.*, basecat.category_depth as `base_depth`' .
						' FROM ' .  hikamarket::table('shop.category') . ' AS c ' .
						' INNER JOIN ' . hikamarket::table('shop.category') . ' AS basecat ON c.category_left >= basecat.category_left AND c.category_right <= basecat.category_right'.
						' WHERE basecat.category_id = ' . $start . ' AND c.category_type IN (\'product\',\'manufacturer\',\'vendor\',\'root\') AND c.category_depth >= basecat.category_depth AND c.category_depth <= (basecat.category_depth + ' . $depth . ')'.
						$category_filter .
						' ORDER BY c.category_left ASC, c.category_name ASC';
				}
			} else {
				$query = 'SELECT c.*, 0 as `base_depth` '.
					' FROM ' . hikashop_table('category') . ' AS c ' .
					(($start > 0) ? ' INNER JOIN ' .  hikamarket::table('shop.category') . ' AS b ON a.category_left >= b.category_left AND a.category_right <= b.category_right' : '') .
					' WHERE c.category_type IN (\'product\',\'manufacturer\',\'vendor\',\'root\') AND (( c.category_name LIKE ' . $searchStr .
					(($start > 0) ?  'AND b.category_id = ' . $start . ') OR ( c.category_id = ' . $start : '') . '))' .
					$category_filter .
					' ORDER BY c.category_left ASC, c.category_name ASC';
			}

			$this->db->setQuery($query);
			$category_elements = $this->db->loadObjectList('category_id');
			$categories = array();
			$base_depth = 0;
			$lookup_categories = array($start => $start);
		}

		if(!empty($category_elements) && empty($search)) {
			$base_depth = (int)@$category_elements[$start]->category_depth + $depth;

			foreach($category_elements as $k => $v) {
				if($k == $start && !$is_root)
					continue;

				$o = new stdClass();
				$o->status = 3;
				$o->name = JText::_($v->category_name);
				$o->value = $k;
				$o->data = array();
				if($set_no_selection)
					$o->noselection = 1;

				if($depth > 1 && $v->category_depth < $base_depth) {
					$lookup_categories[$k] = $k;
					$o->status = $unfold ? 2: 1;
				}

				if(empty($v->category_parent_id) || $k == $start) {
					$o->status = 5;
					$o->icon = 'world';
					$ret[0][] =& $o;
				} else if((int)$v->category_parent_id == 1 || (int)$v->category_parent_id == $start || !isset($categories[(int)$v->category_parent_id])) {
					$ret[0][] =& $o;
				} else {
					$categories[(int)$v->category_parent_id]->data[] =& $o;
				}
				$categories[$k] =& $o;
				unset($o);
			}
		}

		$product_elements = array();
		if(!empty($lookup_categories) && empty($search)) {
			$query = 'SELECT p.*, c.category_id FROM ' . hikamarket::table('shop.product') . ' AS p '.
				' INNER JOIN ' . hikamarket::table('shop.product_category') . ' AS pc ON p.product_id = pc.product_id '.
				' INNER JOIN ' .  hikamarket::table('shop.category') . ' AS c ON c.category_id = pc.category_id '.
				' WHERE pc.category_id IN (' . implode(',', $lookup_categories) . ') ' . $vendorFilter .
				' ORDER BY c.category_left ASC, c.category_name ASC, p.product_name ASC';
			$this->db->setQuery($query, 0, $limit);
			$product_elements = $this->db->loadObjectList();

		} else if(!empty($search)) {
			$query = 'SELECT p.*, c.category_id, c.category_right, c.category_left FROM ' . hikamarket::table('shop.product') . ' AS p '.
				' INNER JOIN ' . hikamarket::table('shop.product_category') . ' AS pc ON p.product_id = pc.product_id '.
				' INNER JOIN ' . hikamarket::table('shop.category') . ' AS c ON c.category_id = pc.category_id '.
				' WHERE (p.product_name LIKE '.$searchStr.' OR p.product_code LIKE '.$searchStr.') '. $vendorFilter .
				' ORDER BY p.product_name ASC';
			$this->db->setQuery($query, 0, $limit);
			$product_elements = $this->db->loadObjectList();

			$lookup_categories = array();
			foreach($category_elements as $c) {
				if(empty($lookup_categories[ (int)$c->category_id ]))
					$lookup_categories[ (int)$c->category_id ] = (int)$c->category_left . ' AND c.category_right >= ' . (int)$c->category_right;
			}
			foreach($product_elements as $p) {
				if(empty($lookup_categories[ (int)$p->category_id ]))
					$lookup_categories[ (int)$p->category_id ] = (int)$p->category_left . ' AND c.category_right >= ' . (int)$p->category_right;
				if(isset($category_elements[ (int)$p->category_id ]))
					$category_elements[ (int)$p->category_id ]->isproduct = true;
			}

			$base = '';
			if($start > 0)
				$base = '(c.category_left <= ' . (int)$category_elements[$start]->category_left . ' AND c.category_right >= ' . (int)$category_elements[$start]->category_right . ') AND ';
			if(isset($rootCategory))
				$base .= '(c.category_left <= ' . (int)$rootCategory->category_left . ' AND c.category_right >= ' . (int)$rootCategory->category_right . ') AND ';

			$query = 'SELECT c.* ' .
				' FROM ' . hikamarket::table('shop.category') . ' AS c ' .
				' WHERE ' . $base . '((c.category_left <= '.implode(') OR (c.category_left <= ', $lookup_categories) . '))';
			$this->db->setQuery($query);
			$category_tree = $this->db->loadObjectList('category_id');

			foreach($category_tree as $k => $v) {
				if($k == $start && !$is_root)
					continue;

				$o = new stdClass();
				$o->status = 2;
				$o->name = JText::_($v->category_name);
				$o->value = $k;
				$o->data = array();
				if($set_no_selection)
					$o->noselection = 1;

				if(isset($category_elements[$k]) && empty($category_elements[$k]->isproduct))
					$o->status = 3;

				if(empty($v->category_parent_id) || $k == $start) {
					$o->status = 5;
					$o->icon = 'world';
					$ret[0][] =& $o;
				} else if((int)$v->category_parent_id == 1 || (int)$v->category_parent_id == $start || !isset($categories[(int)$v->category_parent_id])) {
					$ret[0][] =& $o;
				} else {
					$categories[(int)$v->category_parent_id]->data[] =& $o;
				}
				$categories[$k] =& $o;
				unset($o);
			}
		} else {
			$product_types = array();
			$product_type = array('main');
			if(!empty($typeConfig['params']['product_type']))
				$product_type = $typeConfig['params']['product_type'];
			if(!empty($options['product_type']))
				$product_type = $options['product_type'];
			if(is_string($product_type))
				$product_type = explode(',', $product_type);

			foreach($product_type as &$type) {
				$type = trim($type);
				$product_types[] = $this->db->Quote($type);
			}
			unset($type);

			$query = 'SELECT p.* FROM ' . hikamarket::table('shop.product') . ' AS p '.
				' WHERE p.product_type IN (' . implode(',', $product_types) . ' ) '.
				' ORDER BY p.product_name ASC';
			$this->db->setQuery($query, 0, $limit);
			$product_elements = $this->db->loadObjectList('product_id');
		}

		if(!empty($product_elements) && $tree_mode) {
			$displayFormat_tags = null;
			if(!preg_match_all('#{([-_a-zA-Z0-9]+)}#U', $displayFormat, $displayFormat_tags))
				$displayFormat_tags = null;

			$product_ids = array();
			if($load_variants) {
				foreach($product_elements as $p) {
					$product_ids[(int)$p->product_id] = array();
				}
				$query = 'SELECT p.* FROM ' . hikamarket::table('shop.product') . ' AS p '.
						' WHERE p.product_type = ' . $this->db->Quote('variant') . ' AND p.product_parent_id IN ('.implode(',', array_keys($product_ids)).')'.
						' ORDER BY p.product_parent_id ASC, p.product_name ASC';
				$this->db->setQuery($query);
				$variants = $this->db->loadObjectList('product_id');
				foreach($variants as $v) {
					$product_ids[ (int)$v->product_parent_id ][] = (int)$v->product_id;
				}

				$characteristics = $this->getCharacteristics(array_keys($product_ids), array_keys($variants));
				$characteristic_separator = JText::_('HIKA_VARIANT_SEPARATOR');
				if($characteristic_separator == 'HIKA_VARIANT_SEPARATOR')
					$characteristic_separator = ': ';
				$variant_separator = JText::_('HIKA_VARIANTS_MIDDLE_SEPARATOR');
				if($variant_separator == 'HIKA_VARIANTS_MIDDLE_SEPARATOR')
					$variant_separator = ' ';
			}

			foreach($product_elements as $p) {
				$o = new stdClass();
				$o->status = 0;
				$o->value = (int)$p->product_id;

				if(!preg_match('!!u', $p->product_name))
					$product_name = htmlentities(utf8_encode($p->product_name), ENT_QUOTES, "UTF-8");
				else
					$product_name = htmlentities($p->product_name, ENT_QUOTES, "UTF-8");

				if(!empty($displayFormat) && !empty($displayFormat_tags)) {
					if($p->product_quantity == -1)
						$p->product_quantity = JText::_('UNLIMITED');
					$p->product_name = $product_name;
					$o->name = $displayFormat;

					foreach($displayFormat_tags[1] as $key) {
						$o->name = str_replace('{'.$key.'}', $p->$key, $o->name);
					}
				}
				if(empty($o->name)) {
					$o->name = $product_name;
					if(empty($o->name))
						$o->name = '['.$p->product_id.']';
				}

				if(!empty($product_ids[ (int)$p->product_id ])) {
					$o->status = 1;
					$o->icon = 'node';
					$o->data = array();

					foreach($product_ids[ (int)$p->product_id ] as $id) {
						$o2 = new stdClass();
						$o2->status = 0;

						$o2->value = (int)$id;
						$v = $variants[$id];

						$variant_name = htmlentities( ((!preg_match('!!u', $v->product_name)) ? utf8_encode($v->product_name) : $v->product_name), ENT_QUOTES, "UTF-8");
						if(empty($variant_name)) {
							$variant_name = $product_name . $characteristic_separator;
							foreach($characteristics[ (int)$p->product_id ] as $k => $c) {
								if($k > 0)
									$variant_name .= $variant_separator;
								$variant_name .= $characteristics[ (int)$v->product_id ][ (int)$c->characteristic_id ]->characteristic_value;
							}
						}
						if(!empty($displayFormat) && !empty($displayFormat_tags)) {
							if($v->product_quantity == -1)
								$v->product_quantity = JText::_('UNLIMITED');
							$v->product_name = $variant_name;
							$o2->name = $displayFormat;

							foreach($displayFormat_tags[1] as $key) {
								$o2->name = str_replace('{'.$key.'}', $v->$key, $o2->name);
							}
						}
						if(empty($o2->name)) {
							$o2->name = !empty($variant_name) ? $variant_name : $product_name;
							if(empty($o2->name))
								$o2->name = '['.$v->product_id.']';
						}

						$o->data[] =& $o2;

						unset($o2);
					}
				}

				if($p->category_id != $start && isset($categories[(int)$p->category_id]))
					$categories[(int)$p->category_id]->data[] =& $o;
				else
					$ret[0][] =& $o;
				unset($o);
			}
		} else if(!empty($product_elements)) {
			if(count($product_elements) < $limit) {
				$fullLoad = true;
			} else {
				array_pop($product_elements);
			}
			$ret[0] = $product_elements;
		}

		if(!empty($value)) {
			if(!is_array($value))
				$value = array($value);

			if(is_object(reset($value))) {
				$values = array();
				foreach($value as $v) {
					$values[] = (int)$v->product_id;
				}
				$value = $values;
			}

			$filter = array();
			foreach($value as $v) {
				$filter[] = (int)$v;
			}
			$query = 'SELECT p.* '.
					' FROM ' . hikamarket::table('shop.product') . ' AS p ' .
					' WHERE p.product_id IN ('.implode(',', $filter).') ' . $vendorFilter;
			$this->db->setQuery($query);
			$products = $this->db->loadObjectList('product_id');

			$variant_data = array(
				'product' => array(),
				'variant' => array()
			);
			if(!empty($products)) {
				foreach($products as $p) {
					if($p->product_type != 'variant' || !empty($p->product_name))
						continue;
					$variant_data['product'][ (int)$p->product_parent_id ] = (int)$p->product_parent_id;
					$variant_data['variant'][ (int)$p->product_id ] = (int)$p->product_id;
				}
			}

			if(!empty($variant_data['product'])) {
				$query = 'SELECT p.product_id, p.product_name FROM ' . hikamarket::table('shop.product') . ' AS p WHERE p.product_id IN (' . implode(',', $variant_data['product']) . ');';
				$this->db->setQuery($query);
				$parents = $this->db->loadObjectList('product_id');

				$characteristics = $this->getCharacteristics($variant_data['product'], $variant_data['variant']);

				$characteristic_separator = JText::_('HIKA_VARIANT_SEPARATOR');
				if($characteristic_separator == 'HIKA_VARIANT_SEPARATOR')
					$characteristic_separator = ': ';
				$variant_separator = JText::_('HIKA_VARIANTS_MIDDLE_SEPARATOR');
				if($variant_separator == 'HIKA_VARIANTS_MIDDLE_SEPARATOR')
					$variant_separator = ' ';

				foreach($products as &$p) {
					if($p->product_type != 'variant' || !empty($p->product_name))
						continue;

					$parent = isset($parents[ $p->product_parent_id ]) ? $parents[ $p->product_parent_id ] : $p;
					$product_name = htmlentities( ((!preg_match('!!u', $parent->product_name)) ? utf8_encode($parent->product_name) : $parent->product_name), ENT_QUOTES, "UTF-8");

					$variant_name = $product_name . $characteristic_separator;
					foreach($characteristics[ (int)$p->product_parent_id ] as $k => $c) {
						if($k > 0)
							$variant_name .= $variant_separator;
						$variant_name .= $characteristics[ (int)$p->product_id ][ (int)$c->characteristic_id ]->characteristic_value;
					}
					$p->product_name_orig = $p->product_name;
					$p->product_name = $variant_name;
				}
				unset($p);
			}

			if(!empty($products)) {
				$ret[1] = $products;
			}

			if($mode == hikamarketNameboxType::NAMEBOX_SINGLE)
				$ret[1] = reset($ret[1]);
		}

		return $ret;
	}

	public function toggleId($task, $value = null) {
		if($value !== null) {
			$app = JFactory::getApplication();
			if(!$app->isAdmin() && ((int)$value == 0 || empty($this->toggle[$task]) || ( empty($this->toggleAcl[$task]) && !hikamarket::acl('product/edit/'.$task) ) || ( !empty($this->toggleAcl[$task]) && !hikamarket::acl($this->toggleAcl[$task]) ) || !hikamarket::isVendorProduct((int)$value) ))
				return false;
		}
		if(!empty($this->toggle[$task]))
			return $this->toggle[$task];
		return false;
	}

	public function toggleDelete($value1 = '', $value2 = '') {
		$app = JFactory::getApplication();
		if(!$app->isAdmin() && ((int)$value1 == 0 || !hikamarket::acl('product/delete') || !hikamarket::isVendorProduct((int)$value1)))
			return false;
		if(empty($this->deleteToggle))
			return false;

		if((int)$value1 > 0 && empty($value2)) {
			$productClass = hikamarket::get('shop.class.product');
			$product_id = (int)$value1;
			$ret = $productClass->delete($product_id);
			return (!empty($ret) && $ret > 0);
		}
		return $this->deleteToggle;
	}

	public function processView(&$view) {
		$app = JFactory::getApplication();
		if($app->isAdmin())
			return $this->processViewBack($view);
		return $this->processViewFront($view);
	}

	public function processViewFront(&$view) {
		$doc=JFactory::getDocument();
		$doc_type=$doc->getType();
		$layout = $view->getLayout();
		if(!in_array($layout, array('show','show.android','listing.android','contact.android')))
			return;

		$currentVendorid = hikamarket::loadVendor(false);

		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$show_sold_by = $config->get('show_sold_by', 0);
		$show_sold_by_me = $config->get('show_sold_by_me', 0);
		$show_edit_btn = $config->get('show_edit_btn', 0);

		$editAllVendors = false;
		if($currentVendorid == 1 && hikamarket::acl('product/subvendor')) {
			$editAllVendors = true;
		}
		if($currentVendorid == 0 || !hikamarket::acl('product/edit')) {
			$show_edit_btn = false;
		}

		$url_itemid = $config->get('vendor_default_menu', 0);
		if(!empty($url_itemid))
			$url_itemid = '&Itemid=' . (int)$url_itemid;
		else
			$url_itemid = '';

		if($layout == 'show'||$layout == 'show.android') {

			if(!isset($view->element->product_vendor_id))
				return;

			$vendor_id = (int)$view->element->product_vendor_id;
			if(isset($view->element->main) && isset($view->element->main->product_vendor_id))
				$vendor_id = (int)$view->element->main->product_vendor_id;
			if($vendor_id <= 1)
				$vendor_id = 1;
			$query = 'SELECT * FROM '.hikamarket::table('vendor').' WHERE vendor_id = ' . $vendor_id;
			$this->db->setQuery($query);
			$vendor = $this->db->loadObject();
			$view->element->vendor =& $vendor;

			$vendor->alias = (empty($vendor->vendor_alias)) ? $vendor->vendor_name : $vendor->vendor_alias;
			$stringSafe = (method_exists($app, 'stringURLSafe'));
			if($stringSafe)
				$vendor->alias = $app->stringURLSafe(strip_tags($vendor->alias));
			else
				$vendor->alias = JFilterOutput::stringURLSafe(strip_tags($vendor->alias));

			if(!isset($view->element->extraData))
				$view->element->extraData = new stdClass();

			if(empty($view->element->extraData->topEnd))
				$view->element->extraData->topEnd = array();

			if(!empty($vendor->vendor_image)){
				$image_helper = hikashop_get('helper.image');
				$vendor->vendor_image = $image_helper->getThumbnail($vendor->vendor_image, array(500, 500), array('default' => true), true);
			}
			$slot = 'vendor';
			if($doc_type=="json"){
				if($show_sold_by && ($vendor->vendor_id > 1 || $show_sold_by_me)) {
					$view->element->extraData->topEnd["vendor"] =$vendor;
				}
			}else{
				if($show_sold_by && ($vendor->vendor_id > 1 || $show_sold_by_me)) {
					$vendorLink = '<a href="'.hikamarket::completeLink('vendor&task=show&cid=' . $vendor->vendor_id . '&name=' . $vendor->alias . $url_itemid).'">' . $vendor->vendor_name . '('.$vendor->vendor_address_telephone.')</a>';
					$view->element->extraData->topEnd[$slot] = '<span class="hikamarket_vendor">'.JText::sprintf('SOLD_BY_VENDOR', $vendorLink).'</span>';
				}
			}
			if($show_edit_btn) {
				if(empty($view->element->extraData->topBegin))
					$view->element->extraData->topBegin = array();

				$slot = 'vendor_edit';
				if($editAllVendors || $currentVendorid == $vendor->vendor_id) {
					$product_id = $view->element->product_id;
					if(isset($view->element->main->product_id))
						$product_id = $view->element->main->product_id;
					$vendorLink = '<a href="'.hikamarket::getProductEditionUrl($product_id).'"><img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" alt="'.JText::_('EDIT').'"/></a>';
					$tooltip_data = '';
					$tooltip_title = JText::_('HIKAM_EDIT_PRODUCT');
					if(!empty($tooltip_title) && $tooltip_title != 'HIKAM_EDIT_PRODUCT') {
						hikamarket::loadJslib('tooltip');
						$tooltip_data = ' data-toggle="hk-tooltip" data-title="' . htmlspecialchars($tooltip_title, ENT_COMPAT, 'UTF-8') . '"';
					}
					$view->element->extraData->topBegin[$slot] = '<span class="hikamarket_show_edit"'.$tooltip_data.'>'.$vendorLink.'</span>';
				} else if(isset($view->element->main) || isset($view->element->characteristics)) {
					$found = false;
					if(!empty($view->element->variants)) {
						foreach($view->element->variants as $variant) {
							if((int)$variant->product_id != (int)$view->element->main->product_id && $variant->product_vendor_id == $currentVendorid) {
								$vendorLink = '<a href="'.hikamarket::getProductEditionUrl($variant->product_id).'"><img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" alt="'.JText::_('EDIT').'"/></a>';
								$view->element->extraData->topBegin[$slot] = '<span class="hikamarket_show_edit">'.$vendorLink.'</span>';
								$found = true;
								break;
							}
						}
					}
					if(!$found) {
						$product_id = (int)$view->element->product_id;
						if(isset($view->element->main->product_id))
							$product_id = (int)$view->element->main->product_id;
						$characteristics = @$view->element->characteristics;
						if(isset($view->element->main->characteristics))
							$characteristics = $view->element->main->characteristics;
						foreach($characteristics as $characteristic) {
							if(empty($characteristic->characteristic_alias) || $characteristic->characteristic_alias != 'vendor')
								continue;
							$vendorLink = '<a href="'.hikamarket::getProductEditionUrl($product_id, 'duplicate=1').'"><img src="'.HIKAMARKET_IMAGES.'icon-16/plus.png" alt="'.JText::_('CREATE').'"/></a>';
							$view->element->extraData->topBegin[$slot] = '<span class="hikamarket_show_edit">'.$vendorLink.'</span>';
						}
					}
				}
			}
			return;
		}

		if($layout == 'listing') {
			$debug=JUtility::get_debug();
			$hide_sold_by = (isset($view->hikamarket->hide_sold_by) && $view->hikamarket->hide_sold_by);
			if(!$hide_sold_by) {
				$paramsOpt = $view->params->get('market_show_sold_by', '');
				if($paramsOpt === '0')
					$hide_sold_by = true;
			}
			$vendorsId = array();
			foreach($view->rows as $row) {
				if(isset($row->product_vendor_id)) {
					$id = (int)$row->product_vendor_id;
					if($id <= 1)
						$id = 1;
					$vendorsId[$id] = $id;
				}
			}
			if(!empty($vendorsId)) {
				$query = 'SELECT vendor.*,user2.user_cms_id AS admin_vendor_user_cms_id,GROUP_CONCAT(user.user_cms_id) AS list_user_cms_id FROM '.hikamarket::table('vendor').' AS vendor
				 LEFT JOIN '.hikashop::table('user').' AS user ON user.user_vendor_id=vendor.vendor_id
				 LEFT JOIN '.hikashop::table('user').' AS user2 ON user2.user_id=vendor.vendor_admin_id
				  WHERE vendor.vendor_id IN (' . implode(',', $vendorsId).')
				';
				$this->db->setQuery($query);
				$vendors = $this->db->loadObjectList('vendor_id');
				$stringSafe = (method_exists($app, 'stringURLSafe'));
				foreach($vendors as &$vendor) {
					$vendor->alias = (empty($vendor->vendor_alias)) ? $vendor->vendor_name : $vendor->vendor_alias;
					if($stringSafe)
						$vendor->alias = $app->stringURLSafe(strip_tags($vendor->alias));
					else
						$vendor->alias = JFilterOutput::stringURLSafe(strip_tags($vendor->alias));
				}
				unset($vendor);

				$tooltip_data = null;
				foreach($view->rows as &$row) {
					$singleInLine = true;
					if(isset($row->product_vendor_id)) {
						$id = (int)$row->product_vendor_id;
						if($id <= 1)
							$id = 1;
						$row->vendor = null;
						if(isset($vendors[$id]))
							$row->vendor =& $vendors[$id];
						if(!$hide_sold_by && !empty($vendors[$id]->vendor_name)) {
							if(!isset($row->extraData))
								$row->extraData = new stdClass();

							$slot = 'soldby';
							if(empty($row->extraData->afterProductName))
								$row->extraData->afterProductName = array($slot => '');
							elseif(empty($row->extraData->afterProductName[$slot]))
								$row->extraData->afterProductName[$slot] = '';

							if($show_sold_by && ($id > 1 || $show_sold_by_me)) {
								$vendorLink = '<a href="'.hikamarket::completeLink('vendor&task=show&cid=' . $vendors[$id]->vendor_id .'&name=' . $vendors[$id]->alias . $url_itemid).'">' . $vendors[$id]->vendor_name . '</a>';
								$row->extraData->afterProductName[$slot] .= '<span class="hikamarket_vendor">'.JText::sprintf('SOLD_BY_VENDOR', $vendorLink).'</span> <a data-admin_vendor_user_cms_id="'.$row->vendor->admin_vendor_user_cms_id.'" data-list_user_cms_id="'.$row->vendor->list_user_cms_id.'"  class="talking-to-suppliers" title="'.JText::_('HIKA_TALKING_TO_SUPPLIERS').'" href="javascript:void(0)">'.($debug?" ({$row->vendor->admin_vendor_user_cms_id},{$row->vendor->list_user_cms_id})":'').'<span class="glyphicon glyphicon-comment"></span></a>';
								$singleInLine = false;
							}
							ksort($row->extraData->afterProductName);
						}
					}

					if($show_edit_btn && ($editAllVendors || $currentVendorid == $row->product_vendor_id)) {
						$slot = 'vendor_edit';
						if(!isset($row->extraData))
							$row->extraData = new stdClass();
						if(empty($row->extraData->afterProductName))
							$row->extraData->afterProductName = array($slot => '');
						elseif(empty($row->extraData->afterProductName[$slot]))
							$row->extraData->afterProductName[$slot] = '';

						if($tooltip_data === null) {
							$tooltip_data = '';
							$tooltip_title = JText::_('HIKAM_EDIT_PRODUCT');
							if(!empty($tooltip_title) && $tooltip_title != 'HIKAM_EDIT_PRODUCT') {
								hikamarket::loadJslib('tooltip');
								$tooltip_data = ' data-toggle="hk-tooltip" data-title="' . htmlspecialchars($tooltip_title, ENT_COMPAT, 'UTF-8') . '"';
							}
						}

						$vendorLink = '<a href="'. hikamarket::getProductEditionUrl($row->product_id).'"><img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" alt="'.JText::_('EDIT').'"/></a>';
						if($singleInLine)
							$row->extraData->afterProductName[$slot] .= '<span class="hikamarket_list_edit hikamarket_list_single_edit"' . $tooltip_data . '>'.$vendorLink.'</span>';
						else
							$row->extraData->afterProductName[$slot] .= '<span class="hikamarket_list_edit"' . $tooltip_data . '>'.$vendorLink.'</span>';
						ksort($row->extraData->afterProductName);
					}
				}
				unset($row);
			}
		}

		if($layout == 'contact') {
			$target = JRequest::getCmd('target', '');
			$vendor_id = JRequest::getInt('vendor_id', 0);
			if($target == 'vendor' && !empty($vendor_id)) {
				if($vendor_id <= 1)
					$vendor_id = 1;
				$query = 'SELECT * FROM '.hikamarket::table('vendor').' WHERE vendor_id = ' . $vendor_id;
				$this->db->setQuery($query);
				$vendor = $this->db->loadObject();

				$view->title = '';
				$imageHelper = hikamarket::get('shop.helper.image');
				$img = $imageHelper->getThumbnail($vendor->vendor_image, array(50,50), array('default' => true), true);
				if($img->success) {
					$view->title .= '<img src="'.$img->url.'" alt="" style="vertical-align:middle"/> ';
				}
				$view->title .= $vendor->vendor_name;

				if(!isset($view->extra_data)) $view->extra_data = array();
				if(empty($view->extra_data['hidden'])) $view->extra_data['hidden'] = array();

				$view->extra_data['hidden']['target'] = $target;
				$view->extra_data['hidden']['vendor_id'] = $vendor_id;
			}
		}
	}

	public function processViewBack(&$view) {
		$layout = $view->getLayout();
		if(!in_array($layout, array('form')))
			return;

		if(empty($view->product->product_type) || $view->product->product_type != 'waiting_approval')
			return;

		$cancel_redirect = JRequest::getString('cancel_redirect', '');
		if(!empty($cancel_redirect))
			$cancel_redirect = '&cancel_redirect=' . urlencode($cancel_redirect);

		$add_tools = array(
			array(
				'name' => 'link',
				'icon' => 'save',
				'alt' => JText::_('HIKAM_APPROVE'),
				'url' => hikamarket::completeLink('product&task=approve&cid='.$view->product->product_id.'&'.hikamarket::getFormToken().'=1'.$cancel_redirect)
			)
		);
		$view->toolbar = array_merge($add_tools, $view->toolbar);
	}

	public function processListing(&$filters, &$order, &$view, &$select, &$select2, &$ON_a, &$ON_b, &$ON_c) {
		if(!$view->module)
			return;

		$option = JRequest::getString('option','');
		$ctrl = JRequest::getString('ctrl','');
		if($option == HIKAMARKET_COMPONENT && $ctrl == 'vendor') {
			$content_synchronize = $view->params->get('content_synchronize');
			$product_synchronize = (int)$view->params->get('product_synchronize', 0);
			$vendor_id = hikamarket::getCID('vendor_id');
			if($content_synchronize) {
				if($vendor_id == 1) {
					$filters[] = '(b.product_vendor_id = 1 OR b.product_vendor_id = 0)';
				} else {
					$filters[] = 'b.product_vendor_id = '.$vendor_id;
				}
				if(empty($view->hikamarket))
					$view->hikamarket = new stdClass();
				$view->hikamarket->hide_sold_by = true;
			}
		}
	}

	public function loadVendorProductCharacteristics(&$product, &$mainCharacteristics, &$characteristics) {
		$characteristic = null;
		$product->characteristics = @$mainCharacteristics[$product->product_id][0];
		foreach($product->characteristics as $k => $c) {
			if(empty($c->values) && $c->characteristic_alias == 'vendor') {
				$characteristic = $k;
				break;
			}
		}
		if(empty($characteristic) || empty($product->variants))
			return;

		$characteristic_id = (int)$product->characteristics[$characteristic]->characteristic_id;

		$vendor_products = array();
		foreach($product->variants as $v) {
			if((int)$v->product_vendor_id > 0)
				$vendor_products[ (int)$v->product_id ] = (int)$v->product_vendor_id;
		}
		if(empty($vendor_products))
			return;

		$query = 'SELECT vendor_id, vendor_name FROM ' . hikamarket::table('vendor') . ' WHERE vendor_id IN ('.implode(',', $vendor_products).') AND vendor_published = 1';
		$this->db->setQuery($query);
		$vendors = $this->db->loadObjectList('vendor_id');

		foreach($vendor_products as $p => $v) {
			$id = $characteristic_id . 'v' . $v;

			$c = new stdClass();
			$c->variant_characteristic_id = $id;
			$c->variant_product_id = $p;
			$c->ordering = 0;
			$c->characteristic_id = $id;
			$c->characteristic_ordering = 0;
			$c->characteristic_display_type = '';
			$c->characteristic_params = '';
			$c->characteristic_parent_id = $characteristic_id;
			$c->characteristic_value = $vendors[$v]->vendor_name;
			$c->characteristic_alias = $vendors[$v]->vendor_name;
			$c->characteristic_vendor_id = $v;

			if(!isset($mainCharacteristics[$product->product_id][$characteristic_id])) {
				$mainCharacteristics[$product->product_id][$characteristic_id] = array(
					$id => &$c
				);
			}

			$characteristics[] =& $c;
			unset($c);
		}
	}

	public function handleTranslation(&$product) {
		if(empty($product) || empty($product->product_id))
			return false;

		$data = array();
		$translationHelper = hikamarket::get('shop.helper.translation');
		$formData = JRequest::getVar('translation', array(), '', 'array', JREQUEST_ALLOWRAW);

		$languages = $translationHelper->loadLanguages();

		$fields_acl = array(
			'product_name' => 'name',
			'product_page_title' => 'pagetitle',
			'product_url' => 'url',
			'product_alias' => 'alias',
			'product_canonical' => 'canonical',
			'product_meta_description' => 'metadescription',
			'product_keywords' => 'keywords',
			'product_description' => 'description',
		);

		foreach($formData as $field => $trans) {
			if(!isset($fields_acl[$field]))
				continue;
			if(is_string($fields_acl[$field]))
				$fields_acl[$field] = hikamarket::acl($fields_acl[$field]);
			if(!$fields_acl[$field])
				continue;

			foreach($trans as $lg => $value) {
				$lg = (int)$lg;
				if($lg == 0 || !isset($languages[$lg]) || empty($value))
					continue;

				if(empty($data[$field]))
					$data[$field] = array();
				$data[$field][$lg] = $value;
			}
		}

		foreach($_POST as $name => $value) {
			if(!preg_match('#^translation_([a-z_]+)_([0-9]+)$#i', $name, $match))
				continue;

			$html_element = trim(JRequest::getVar($name, '', '', 'string', JREQUEST_ALLOWRAW));
			if(empty($html_element))
				continue;

			$lg = (int)$match[2];
			$field = hikashop_secureField($match[1]);
			$value = $html_element;

			if(!isset($fields_acl[$field]) || !isset($languages[$lg]))
				continue;
			if(is_string($fields_acl[$field]))
				$fields_acl[$field] = hikamarket::acl($fields_acl[$field]);
			if(!$fields_acl[$field])
				continue;

			if(empty($data[$field]))
				$data[$field] = array();
			$data[$field][$lg] = $value;
		}

		$shopConfig = hikamarket::config(false);
		$hikashopVersion = $shopConfig->get('version');
		$translationHelper = hikamarket::get('shop.helper.translation');

		if(version_compare($hikashopVersion, '2.5.1', '<'))
			$translationHelper->handleTranslations('product', $product->product_id, $product);
		else
			$translationHelper->handleTranslations('product', $product->product_id, $product, 'hikashop_', $data);
		return true;
	}
}
