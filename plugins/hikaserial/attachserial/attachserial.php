<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgHikaserialAttachserial extends hikaserialPlugin {

	protected $type = 'plugin';
	protected $multiple = true;
	protected $doc_form = 'attachserial-';

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	protected function loadPluginParams() {
		if(isset($this->params))
			return;

		$pluginsClass = hikaserial::get('class.plugins');
		$plugin = $pluginsClass->getByName('hikaserial', 'attachserial');

		if(version_compare(JVERSION, '2.5', '<')) {
			jimport('joomla.html.parameter');
			$this->params = new JParameter(@$plugin->params);
		} else {
			$this->params = new JRegistry(@$plugin->params);
		}
	}

	public function configurationHead() {
		return array(
			'email' => array(
				'title' => JText::_('ATTACH_IN_EMAILS'),
				'cell' => 'align="center" width="10%"'
			),
			'download' => array(
				'title' => JText::_('ATTACH_AS_DOWNLOAD'),
				'cell' => 'align="center" width="10%"'
			),
			'packs' => array(
				'title' => JText::_('SERIAL_PACKS'),
				'cell' => 'width="30%"'
			)
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		if(empty($this->toggleHelper))
			$this->toggleHelper = hikaserial::get('helper.toggle');

		switch($id) {
			case 'email':
				$v = !empty($conf->plugin_params->attach_email) ? 1 : 0;
				return $this->toggleHelper->display('', $v);
			case 'download':
				$v = !empty($conf->plugin_params->attach_download) ? 1 : 0;
				return $this->toggleHelper->display('', $v);
			case 'packs':
				if(empty($this->packs)) {
					$db = JFactory::getDBO();
					$db->setQuery('SELECT * FROM '.hikaserial::table('pack'));
					$this->packs = $db->loadObjectList('pack_id');
				}
				$ret = array();
				if(!empty($conf->plugin_params->packs)) {
					if(!is_array($conf->plugin_params->packs))
						$conf->plugin_params->packs = array($conf->plugin_params->packs);
					foreach($conf->plugin_params->packs as $p) {
						if(isset($this->packs[(int)$p]))
							$ret[] = '<a href="'.hikaserial::completeLink('pack&task=edit&cid='.(int)$p).'">'.$this->packs[(int)$p]->pack_name.'</a>';
					}
				}
				return implode(', ', $ret);
		}
		return null;
	}

	public function onPluginConfigurationSave(&$element) {
	}

	public function onBeforeSerialMailSend(&$mail, &$mailer, &$serials, $order) {

		$query = 'SELECT plugin_id, plugin_name, plugin_params FROM '.hikaserial::table('plugin').' WHERE plugin_type = '.$this->db->Quote('attachserial').' AND plugin_published = 1';
		$this->db->setQuery($query);
		$this->db->query();
		$plugins = $this->db->loadObjectList();
		if(empty($plugins)) {
			return;
		}

		$orderClass = hikaserial::get('shop.class.order');
		$fullOrder = $orderClass->loadFullOrder($order->order_id, true, false);

		foreach($plugins as $plugin) {
			$params = hikaserial::unserialize($plugin->plugin_params);

			if(empty($params) || empty($params->attach_email) || empty($params->packs))
				continue;

			$order_products = array();
			$products = array();

			$params->order = $order;

			foreach($serials as $k => $serial) {
				if(is_string($params->packs) && strpos(','.$params->packs.',', ','.$serial->serial_pack_id.',') === false)
					continue;
				if(is_array($params->packs) && !in_array($serial->serial_pack_id, $params->packs))
					continue;

				if(!empty($serial->serial_order_product_id)) {
					if(isset($order_products[$serial->serial_order_product_id])) {
						$serial->position = ($order_products[$serial->serial_order_product_id] + 1);
						$order_products[$serial->serial_order_product_id]++;
					} else {
						$serial->position = 1;
						$order_products[$serial->serial_order_product_id] = 1;
					}
				}

				$data = array(
					0 => array(
						'text' => $serial->serial_data,
						'format' => @$params->serial_text_format,
						'format_ex' => @$params->serial_text_format_ex,
						'x' => @$params->serial_text_x,
						'y' => @$params->serial_text_y,
						'w' => @$params->serial_text_w,
						'h' => @$params->serial_text_h,
						'size' => @$params->serial_text_size,
						'color' => @$params->serial_text_color,
						'font' => @$params->serial_text_font
					)
				);
				if(!empty($params->texts))
					$this->loadData($data, $params->texts, $serial, $fullOrder);

				$ret = $this->generateImage($params->image_path, null, $data, $params);
				if(!empty($ret)) {

					if(!isset($this->params))
						$this->loadPluginParams();
					$format = $this->params->get('image_name_format', '');
					$virtual_file = (int)$this->params->get('virtual_file', 1);
					$use_mime = (int)$this->params->get('use_mime', 1);

					$extension = strtolower(substr($params->image_path, strrpos($params->image_path, '.') + 1));
					if(empty($format)) {
						$filename = 'image'.uniqid().'.'.$extension;
					} else if(strpos($format, '{data}' === false) && strpos($format, '{id}' === false)) {
						$filename = $format.uniqid().'.'.$extension;
					} else {
						$filename = str_replace(
								array('{data}', '{id}'),
								array($serial->serial_data, uniqid()),
								$format
							) . '.' . $extension;
					}
					$mimeType = $this->getMime($extension);

					if($virtual_file) {
						if($use_mime)
							$mailer->AddStringAttachment($ret, $filename, 'base64', $mimeType);
						else
							$mailer->AddStringAttachment($ret, $filename, 'base64');
					} else {
						$tmpFilename = HIKASERIAL_MEDIA.'images'.DS.'safe'.DS.'tmp_attachserial_'.$plugin->plugin_id.'_'.$k.'.'.$extension;
						$writeRet = JFile::write($tmpFilename, $ret);
						$mailer->addAttachment($tmpFilename, $filename);
					}
				}
			}
		}
	}

	public function onBeforeSerialDownloadFile(&$filename, &$do, &$file, &$serials) {
		$startStr = 'hikaserial:attachserial:';
		$l = strlen($startStr);
		if(substr($filename, 1, $l) != $startStr) {
			return;
		}
		$params = substr($filename, $l+1);
		if(substr($params, 0, 1) == ':')
			$params = substr($params, 1);
		$id = 0;
		if((int)$params > 0) {
			$id = (int)$params;
			$this->pluginParams($id);
		} else {
			$query = 'SELECT plugin_params FROM '.hikaserial::table('plugin').' WHERE plugin_type = '.$this->db->Quote('attachserial').' AND plugin_published = 1 AND plugin_name = ' . $this->db->Quote($params);
			$this->db->setQuery($query);
			$this->db->query();
			$data = $this->db->loadResult();
			$this->plugin_params = null;
			if(!empty($data)) {
				$this->plugin_params = hikaserial::unserialize($data);
			}
		}

		if(empty($this->plugin_params) || empty($this->plugin_params->attach_download)) {
			$do = false;
			return;
		}

		$serial = reset($serials);
		if(!empty($file->file_pos)) {
			if(count($serials) >= $file->file_pos) {
				$serial = $serials[$file->file_pos - 1];
				$serial->position = $file->file_pos;
			} else {
				$do = false;
				return;
			}
		}

		$orderClass = hikaserial::get('shop.class.order');
		$order = $orderClass->loadFullOrder($file->order_id, true, false);

		$this->plugin_params->order = $order;

		$data = array(
			0 => array(
				'text' => $serial->serial_data,
				'format' => @$this->plugin_params->serial_text_format,
				'format_ex' => @$this->plugin_params->serial_text_format_ex,
				'x' => @$this->plugin_params->serial_text_x,
				'y' => @$this->plugin_params->serial_text_y,
				'w' => @$this->plugin_params->serial_text_w,
				'h' => @$this->plugin_params->serial_text_h,
				'size' => @$this->plugin_params->serial_text_size,
				'color' => @$this->plugin_params->serial_text_color,
				'font' => @$this->plugin_params->serial_text_font
			)
		);
		if(!empty($this->plugin_params->texts))
			$this->loadData($data, $this->plugin_params->texts, $serial, $order);

		$ret = $this->generateImage($this->plugin_params->image_path, null, $data, $this->plugin_params);

		if(!empty($ret)) {
			$extension = strtolower(substr($this->plugin_params->image_path, strrpos($this->plugin_params->image_path, '.') + 1));

			if(!isset($this->params))
				$this->loadPluginParams();
			$this->sendHeader($extension, $serial);

			echo $ret;
			exit;
		}
		$do = false;
	}

	private function loadData(&$data, $texts, $serial, $order) {
		if(empty($texts))
			return;

		$this->cache = array();

		foreach($texts as $text) {
			$content = null;
			$t = $text['type'];
			if(substr($t, -1) == '.')
				$t .= @$text['type_ex'];
			list($table, $field) = explode('.', $t, 2);

			if($table == 'dyntext' || $table == 'translation') {
				if($table == 'translation')
					$field = JText::_($field);

				$content = $field;
				if(preg_match_all('#\{([_a-z0-9]+\.[._a-z0-9]+)\}#i', $field, $matches)) {
					foreach($matches[1] as $match) {
						list($t,$f) = explode('.', $match, 2);
						$c = $this->retrieveData($t, $f, $serial, $order);
						if($c === null && empty($serial))
							$content = '';
						$content = str_replace('{'.$match.'}', $c, $content);
					}
				}
			} else {
				$content = $this->retrieveData($table, $field, $serial, $order);
			}

			if(!empty($content)) {
				$data[] = array(
					'text' => $content,
					'format' => @$text['format'],
					'format_ex' => @$text['format_ex'],
					'x' => @$text['x'],
					'y' => @$text['y'],
					'w' => @$text['w'],
					'h' => @$text['h'],
					'size' => @$text['size'],
					'color' => @$text['color'],
					'font' => @$text['font']
				);
			}
		}

		unset($this->cache);
	}

	private function retrieveData($table, $field, &$serial, $order) {
		$field = trim($field);
		$table = trim($table);
		switch($table) {
			case 'serial':
				if(substr($field, 0, 10) == 'extradata.') {
					$field = substr($field, 10);
					if(is_string($serial->serial_extradata))
						$serial->serial_extradata = hikaserial::unserialize($serial->serial_extradata);
					if(!empty($serial->serial_extradata->$field))
						return $serial->serial_extradata->$field;
					if(!empty($serial->serial_extradata[$field]))
						return $serial->serial_extradata[$field];
				} else if(!empty($serial->$field))
					return $serial->$field;
				break;
			case '_':
				return JText::_($field);
				break;
			case 'rawtext':
				return $field;
				break;
			case 'order':
				if(!empty($order->$field))
					return $order->$field;
				break;
			case 'customer':
				if(!empty($order->customer->$field))
					return $order->customer->$field;
				break;
			case 'order_product':
				if(empty($serial)) return null;
				$opid = 0; $pid = (int)$serial->product_id;
				if(!empty($serial->serial_order_product_id)) {
					$opid = (int)$serial->serial_order_product_id;
				}
				foreach($order->products as $product) {
					if(($opid > 0 && $product->order_product_id == $opid) || ($opid == 0 && $product->product_id == $pid)) {
						if(!empty($product->$field)) {
							if($field == 'order_product_name')
								return strip_tags($product->$field);
							return $product->$field;
						}
						return null;
					}
				}
				break;
			case 'product_price':
			case 'full_product_price':
				if(empty($serial)) return null;
				$opid = 0; $pid = (int)$serial->product_id;
				if(!empty($serial->serial_order_product_id)) {
					$opid = (int)$serial->serial_order_product_id;
				}
				$price = null;
				foreach($order->products as $product) {
					if(($opid > 0 && $product->order_product_id == $opid) || ($opid == 0 && $product->product_id == $pid)) {
						if($price === null) $price = 0.0;
						$price += $product->order_product_price;
						if($field == 'incvat') {
							$price += $product->order_product_tax;
						}

						if($table == 'product_price')
							return $price;
					}
					if($table == 'full_product_price' && $opid > 0 && $product->order_product_option_parent_id == $opid) {
						if($price === null) $price = 0.0;
						$price += $product->order_product_price;
						if($field == 'incvat') {
							$price += $product->order_product_tax;
						}
					}
				}
				if($price != null)
					return $price;
				break;
			case 'product':
				if($field == 'image' || substr($field, 0, 6) == 'image.') {
					if(!isset($this->cache['product_image']))
						$this->cache['product_image'] = array();
					if(!isset($this->cache['product_image'][$serial->product_id])) {
						$db = JFactory::getDBO();
						$query = 'SELECT * FROM '.hikaserial::table('shop.file').' as f '.
							' LEFT JOIN '.hikaserial::table('shop.product').' as p ON (f.file_ref_id = '.(int)$serial->product_id.' OR f.file_ref_id = p.product_parent_id) '.
							' WHERE file_type = \'product\' AND p.product_id = '.(int)$serial->product_id.
							' ORDER BY product_parent_id DESC, file_ordering ASC, file_id ASC';
						$db->setQuery($query);
						$this->cache['product_image'][$serial->product_id] = $db->loadObjectList();
					}
					$images = $this->cache['product_image'][$serial->product_id];
					if($field == 'image' || $field == 'image.0') {
						$main_image = reset($images);
						if(!empty($main_image))
							return $main_image->file_path;
					} else {
						list($field, $pos) = explode('.', $field, 2);
						$pos = (int)$pos;
						foreach($images as $k => $v) {
							if($k == $pos)
								return $v->file_path;
						}
					}
				} else {
					if(empty($this->productClass))
						$this->productClass = hikaserial::get('shop.class.product');
					if(!isset($this->cache['product']))
						$this->cache['product'] = array();
					if(!isset($this->cache['product'][$serial->product_id]))
						$this->cache['product'][$serial->product_id] = $this->productClass->get($serial->product_id);
					$product = $this->cache['product'][$serial->product_id];
					if(!empty($product) && !empty($product->$field))
						return $product->$field;
				}
				break;
			case 'shipping':
				if(!empty($order->shipping_address->$field))
					return $order->shipping_address->$field;
				break;
			case 'billing':
				if(!empty($order->billing_address->$field))
					return $order->billing_address->$field;
				break;
			case 'option_product':
				if(empty($serial)) return null;
				if(strpos($field, '.') === false) return null;
				list($pos, $field) = explode('.', $field, 2);
				$pos = (int)$pos + 1;

				if(empty($this->productClass))
					$this->productClass = hikaserial::get('shop.class.product');
				if(!isset($this->cache['product']))
					$this->cache['product'] = array();
				if(!isset($this->cache['product_image']))
					$this->cache['product_image'] = array();

				$opid = 0; $pid = (int)$serial->product_id;
				if(!empty($serial->serial_order_product_id)) {
					$opid = (int)$serial->serial_order_product_id;
				}
				if(empty($opid)) {
					foreach($order->products as $product) {
						if($product->product_id == $pid) {
							$opid = (int)$product->order_product_id;
							break;
						}
					}
				}
				if($opid <= 0)
					return null;
				$i = 0;
				foreach($order->products as $product) {
					if($product->order_product_option_parent_id != $opid)
						continue;
					$i++;
					if($i != $pos)
						continue;

					if($field == 'image' || substr($field, 0, 6) == 'image.') {
						if(!isset($this->cache['product_image'][$product->product_id])) {
							$db = JFactory::getDBO();
							$db->setQuery('SELECT * FROM '.hikaserial::table('shop.file').' as f LEFT JOIN '.hikaserial::table('shop.product').' as p ON (f.file_ref_id = '.(int)$product->product_id.' OR f.file_ref_id = p.product_parent_id) WHERE file_type = \'product\' AND p.product_id = '.(int)$product->product_id.' ORDER BY product_parent_id DESC, file_ordering ASC, file_id ASC');
							$this->cache['product_image'][$product->product_id] = $db->loadObjectList();
						}
						$images = $this->cache['product_image'][$product->product_id];
						if($field == 'image' || $field == 'image.0') {
							$main_image = reset($images);
							if(!empty($main_image))
								return $main_image->file_path;
							return null;
						}
						list($field, $pos) = explode('.', $field, 2);
						$pos = (int)$pos;
						foreach($images as $k => $v) {
							if($k == $pos)
								return $v->file_path;
						}
						return null;
					}

					if(!isset($this->cache['product'][$product->product_id]))
						$this->cache['product'][$product->product_id] = $this->productClass->get($product->product_id);
					$full_product = $this->cache['product'][$product->product_id];
					if(!empty($full_product) && !empty($full_product->$field))
						return $full_product->$field;
					return null;
				}
				break;
			case 'vendor_product':
				if(empty($serial)) return null;
				if(!isset($this->cache['product_vendor']))
					$this->cache['product_vendor'] = array();

				if(!defined('HIKAMARKET_COMPONENT')) {
					$marketHelper = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php';
					if(!file_exists($marketHelper))
						return null;
					include_once($marketHelper);
					if(!defined('HIKAMARKET_COMPONENT'))
						return null;
				}

				if(!isset($this->cache['product_vendor'][$serial->product_id])) {
					$query = 'SELECT * FROM '.hikamarket::table('vendor').' AS v INNER JOIN '.hikamarket::table('shop.product').' AS p ON p.product_vendor_id = v.vendor_id WHERE p.product_id = '.$serial->product_id;
					$db = JFactory::getDBO();
					$db->setQuery($query);
					$this->cache['product_vendor'][$serial->product_id] = $db->loadObject();
				}
				$vendor = $this->cache['product_vendor'][$serial->product_id];
				if(!empty($vendor) && !empty($vendor->$field))
					return $vendor->$field;

				break;
			case 'option_order':
				if(empty($serial)) return null;
				if(strpos($field, '.') === false) return null;
				list($pos, $field) = explode('.', $field, 2);
				$pos = (int)$pos + 1;
				$opid = 0; $pid = (int)$serial->product_id;
				if(!empty($serial->serial_order_product_id)) {
					$opid = (int)$serial->serial_order_product_id;
				}
				if(empty($opid)) {
					foreach($order->products as $product) {
						if($product->product_id != $pid)
							continue;

						$opid = $product->order_product_id;
						break;
					}
				}
				if($opid <= 0)
					return null;

				$i = 0;
				foreach($order->products as $product) {
					if($opid > 0 && $product->order_product_option_parent_id == $opid)
						$i++;

					if($i != $pos)
						continue;

					if(empty($product->$field))
						return null;
					if($field == 'order_product_name')
						return strip_tags($product->$field);
					return $product->$field;
				}
				break;
			case 'category':
			case 'manufacturer':
				if(empty($serial))
					return null;

				$pos = 0;
				if(strpos($field, '.') !== false)
					list($pos, $field) = explode('.', $field, 2);
				$pos = (int)$pos + 1;

				if(!isset($this->cache['category']))
					$this->cache['category'] = array();
				if(!isset($this->cache['category_image']))
					$this->cache['category_image'] = array();

				$db = JFactory::getDBO();
				$cache_key = (($table == 'category') ? 'c_' : 'm_') . (int)$pos . '_' . (int)$serial->product_id;

				if(!isset($this->cache['category'][$cache_key])) {
					if($table == 'category') {
						$query = 'SELECT category.* FROM ' . hikaserial::table('shop.category') . ' AS category ' .
							' INNER JOIN ' . hikaserial::table('shop.product_category') . ' AS product_category ON product_category.category_id = category.category_id ' .
							' WHERE product_category.product_id = ' . (int)$serial->product_id . ' AND category.category_published = 1 AND category.category_type = ' . $db->Quote('product') .
							' ORDER BY ordering ASC, product_category_id ASC';
					} else {
						$query = 'SELECT category.* FROM ' . hikaserial::table('shop.category') . ' AS category ' .
							' INNER JOIN ' . hikaserial::table('shop.product') . ' AS product ON category.category_id = product.product_manufacturer_id ' .
							' WHERE product.product_id = ' . (int)$serial->product_id . ' AND category.category_published = 1 AND category.category_type = ' . $db->Quote('manufacturer');
					}
					$db->setQuery($query, 0, 1);
					$this->cache['category'][$cache_key] = $db->loadObject();
				}

				if($field != 'image' && substr($field, 0, 6) != 'image.') {
					$category = $this->cache['category'][$cache_key];
					if(!empty($category) && !empty($category->$field))
						return $category->$field;
					return null;
				}

				if(!isset($this->cache['category_image'][$cache_key])) {
					$category = $this->cache['category'][$cache_key];
					if(empty($category))
						return null;

					$query = 'SELECT * FROM '.hikaserial::table('shop.file').' as f '.
						' WHERE f.file_type = \'category\' AND f.file_ref_id = '.(int)$category->category_id.
						' ORDER BY file_ordering ASC, file_id ASC';
					$db->setQuery($query, 0, 1);
					$this->cache['category_image'][$cache_key] = $db->loadObject();
				}

				$image = $this->cache['category_image'][$cache_key];
				if(empty($image))
					return null;
				return $image->file_path;
				break;
			case 'entry':
				if(empty($order->entries)) {
					$query='SELECT * FROM '.hikaserial::table('shop.entry').' WHERE order_id='.$order->order_id;
					$db = JFactory::getDBO();
					$db->setQuery($query);
					$order->entries = $db->loadObjectList();
				}
				if(empty($order->entries))
					return null;

				$opid = 0;
				$pid = (int)$serial->product_id;
				if(!empty($serial->serial_order_product_id)) {
					$opid = (int)$serial->serial_order_product_id;
				}
				$cpt_product = 0;
				$target_product = 0;
				foreach($order->products as $product) {
					if($product->product_id == $pid)
						$cpt_product += $product->order_product_quantity;
					if($opid > 0 && $opid == $product->order_product_id)
						$target_product = $cpt_product;
				}
				if($target_product <= 0)
					return null;

				$fieldClass = hikaserial::get('shop.class.field');
				$data = new stdClass();
				$entryFields = $fieldClass->getFields('', $data, 'entry');
				$accept_values = array();
				foreach($entryFields as $entryField) {
					if($entryField->field_options['product_id'] == $pid) {
						$accept_values[$entryField->field_namekey] = $entryField->field_options['product_value'];
					}
				}
				$cpt = 0;
				if(empty($serial->position))
					$serial->position = 1;
				foreach($order->entries as $entry) {
					foreach($accept_values as $key => $value) {
						if($entry->$key != $value && (!is_array($value) || !in_array($entry->$key, $value)))
							continue;

						$cpt++;
						if(($cpt + $serial->position - 1) == $target_product) {
							return $entry->$field;
						}
					}
				}
				break;
		}
		return null;
	}

	private function generateImage($file_path, $dest_path, $data, $params) {
		if(!function_exists('gd_info'))
			return false;
		$gd = gd_info();
		if( !isset($gd["GD Version"]))
			return false;

		$realfilepath = HIKASERIAL_ROOT . $file_path;
		if(!JFile::exists($realfilepath)) {
			$realfilepath = null;
		}
		if(empty($realfilepath)) {
			$shopConfig = hikaserial::config(false);
			$uploadFolder = ltrim(JPath::clean(html_entity_decode($shopConfig->get('uploadfolder'))),DS);
			$realfilepath = JPATH_ROOT.DS.rtrim($uploadFolder,DS).DS.$file_path;
			if(!JFile::exists($realfilepath)) {
				$realfilepath = null;
			}
		}
		if(empty($realfilepath)) {
			$realfilepath = $file_path;
			if(!JFile::exists($file_path)) {
				$realfilepath = null;
			}
		}

		if(empty($realfilepath))
			return false;
		$file_path = $realfilepath;

		$currencyClass = null;
		$extension = strtolower(substr($file_path, strrpos($file_path, '.') + 1));

		$file_path = JPath::clean($file_path);
		$img = $this->initializeImage($file_path, $extension);
		if(!$img)
			return false;

		if(in_array($extension, array('gif', 'png'))) {
			imagesavealpha($img,true);
			imagealphablending($img, false);
		}

		$font = @$params->default_font;
		if(!empty($font) && substr($font, 0, 1) != '.' && substr($font, -4) == '.ttf')
			$font = HIKASERIAL_MEDIA . 'ttf/' . $font;
		else
			$font = '';

		if(empty($font) || !file_exists($font))
			$font = HIKASERIAL_MEDIA . 'ttf/opensans-regular.ttf';

		$blackcolor = imagecolorallocatealpha($img, 0, 0, 0, 0);
		foreach($data as $d) {
			if(!is_array($d))
				$d = array('text' => $d);

			if(empty($d['text']))
				continue;

			if(!empty($d['format']) && $d['format'] != 'raw') {
				$format = $d['format'];
				if(substr($format, -1) == '.')
					$format .= @$d['format_ex'];
				list($format, $format_ex) = explode('.', $format, 2);

				switch($format) {
					case 'date':
						$d['text'] = hikaserial::getDate( (int)$d['text'], $format_ex );
						break;
					case 'price':
						if(empty($currencyClass))
							$currencyClass = hikaserial::get('shop.class.currency');
						$d['text'] = $currencyClass->format(hikaserial::toFloat($d['text']), $params->order->order_currency_id);
						break;
					case 'qrcode':
						$d['text'] = $this->generateImageQrCode($img, $d, $format_ex);
						break;
					case 'barcode':
						$d['text'] = $this->generateImageBarCode($img, $d, $format_ex);
						break;
					case 'image':
						$d['text'] = $this->includeImage($img, $d);
						break;
					default:
						$d['text'] = null;
						break;
				}
			}

			$localfont = $font;
			if(!empty($d['font']) && substr($d['font'], 0, 1) != '.' && substr($d['font'], -4) == '.ttf') {
				$localfont = HIKASERIAL_MEDIA . 'ttf/' . $d['font'];
				if(empty($localfont) || !file_exists($localfont))
					$localfont = $font;
			}

			if(!empty($d['text'])) {
				if(!empty($d['color']) && ((substr($d['color'],0,1) == '#' && strlen(trim($d['color'])) == 7) || strlen(trim($d['color'])) == 6)) {
					$rgb = str_split(ltrim($d['color'], '#'), 2);
					$textcolor = imagecolorallocatealpha($img, hexdec($rgb[0]), hexdec($rgb[1]), hexdec($rgb[2]), 0);
				} else {
					$textcolor = $blackcolor;
				}

				if(empty($d['size'])) $d['size'] = 12;
				if(empty($d['x'])) $d['x'] = 0;
				if(empty($d['y'])) $d['y'] = 0;
				if(empty($d['w'])) $d['w'] = 0;
				if(empty($d['h'])) $d['h'] = 0;
				$d['y'] += $d['size'];

				imagealphablending($img, true);
				$this->displayTextBox($img, (int)$d['size'], (int)$d['x'], (int)$d['y'], (int)$d['w'], (int)$d['h'], $textcolor, $localfont, $d['text']);
				imagealphablending($img, false);
			}
		}

		if(empty($dest_path)) {
			ob_start();
			$dest_path = null;
		}

		$imgData = false;
		switch($extension) {
			case 'gif':
				$imgData = imagegif($img, $dest_path);
				break;
			case 'jpg':
			case 'jpeg':
				$imgData = imagejpeg($img, $dest_path, 90);
				break;
			case 'png':
				$imgData = imagepng($img, $dest_path, 9);
				break;
		}
		imagedestroy($img);

		if(empty($dest_path)) {
			if($imgData)
				$imgData = ob_get_clean();
			else
				ob_end_clean();
		}
		return $imgData;
	}

	private function displayTextBox($image, $size, $x, $y, $w, $h, $color, $font, $text) {
		$text = str_replace(array("\r\n", "\r"), "\n", $text);
		if($w <= 0) {
			$text = str_replace(array("\n\n\n\n\n\n", "\n\n\n\n\n", "\n\n\n\n", "\n\n\n", "\n\n", "\n"), ' ', $text);
			imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
			return;
		}

		$isUTF8 = preg_match('//u', $text);

		$lines = explode("\n", $text);
		$spaceBlock = imagettfbbox($size, 0, $font, ' ');
		$spaceWidth = $spaceBlock[2] - $spaceBlock[0];
		$lineBlock = imagettfbbox($size, 0, $font, 'agplkhtqWPLITH');
		$spaceHeight = $lineBlock[1] - $lineBlock[7];
		$dx = $x; $dy = $y;
		$mw = $x + $w;

		foreach($lines as $line) {
			if(empty($line) || trim($line) == '') {
				$dx = $x;
				$dy += $spaceHeight;
				if($h > 0 && ($dy - $y > $h))
					break;
				continue;
			}

			$fbbox = imagettfbbox($size, 0, $font, $line);
			$width = $fbbox[2] - $fbbox[0];
			$ly = $fbbox[1] - $fbbox[7];

			if($width < $w) {
				imagettftext($image, $size, 0, $dx, $dy, $color, $font, $line);

				$dx = $x;
				$dy += $ly;
				if($h > 0 && ($dy - $y > $h))
					break;

				continue;
			}

			if($isUTF8 && function_exists('mb_split'))
				$words = mb_split('\s', $line);
			else
				$words = explode(' ', $line);

			foreach($words as $word) {
				$wbbox = imagettfbbox($size, 0, $font, $word);
				if($wbbox[2] - $wbbox[0] + $dx < $mw) {
					imagettftext($image, $size, 0, $dx, $dy, $color, $font, $word);
					$dx += $wbbox[2] - $wbbox[0]  + $spaceWidth;
				} elseif($wbbox[2] - $wbbox[0] < $w) {
					$dx = $x;
					$dy += $ly;

					if($h > 0 && ($dy - $y > $h))
						break;

					imagettftext($image, $size, 0, $dx, $dy, $color, $font, $word);
					$dx += $wbbox[2] - $wbbox[0] + $spaceWidth;
				} else {
					if($isUTF8)
						$letters = preg_split('//u', $word, null, PREG_SPLIT_NO_EMPTY);
					else
						$letters = str_split($word, 1);

					$currentWord = '';
					foreach($letters as $letter) {
						$lbbox = imagettfbbox($size, 0, $font, $currentWord . $letter);
						if($lbbox[2] - $lbbox[0] + $dx < $mw) {
							$currentWord .= $letter;
						} else {
							imagettftext($image, $size, 0, $dx, $dy, $color, $font, $currentWord);
							$currentWord = $letter;
							$dx = $x;
							$dy += $ly;

							if($h > 0 && ($dy - $y > $h))
								break;
						}
					}
					if(!empty($currentWord)) {
						imagettftext($image, $size, 0, $dx, $dy, $color, $font, $currentWord);
						$dx += $lbbox[2] - $lbbox[0] + $spaceWidth;
					}
				}
			}

			$dx = $x;
			$dy += $ly;

			if($h > 0 && ($dy - $y > $h))
				break;
		}
	}

	private function generateImageQrCode($img, $d, $format_ex = null) {
		$level = 'l';
		$pixelPerPoint = 3;
		$outerFrame = 0;
		if(!empty($d['size']))
			$pixelPerPoint = $d['size'];

		if(!empty($format_ex))
			$format_ex = strtolower($format_ex);
		if(!empty($format_ex) && in_array($format_ex, array('l','m','q','h')))
			$level = $format_ex;

		$QRCode = hikaserial::get('inc.qrcode', $level, $pixelPerPoint, $outerFrame, $d['color']);
		$qrcode_image = $QRCode->getImage($d['text']);
		if(empty($qrcode_image))
			return null;

		$imgH = imagesy($qrcode_image);
		$imgW = imagesx($qrcode_image);

		imagesavealpha($img, false);
		imagealphablending($img, true);

		imagecopyresized($img, $qrcode_image, $d['x'], $d['y'], 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
		imagedestroy($qrcode_image);

		imagealphablending($img, false);
		imagesavealpha($img, true);

		return null;
	}

	private function generateImageBarCode($img, $d, $format_ex = null) {
		$format = 'ean13';
		$width = 1;
		$showText = true;
		if(empty($d['h']))
			$d['h'] = 40;
		if(empty($d['size']))
			$d['size'] = 1;

		if(!empty($format_ex))
			$format_ex = strtolower($format_ex);
		if(!empty($format_ex) && in_array($format_ex, array('code39','int25','ean13','upca','upce','code128','ean8','postnet')))
			$format = $format_ex;

		$BarCode = hikaserial::get('inc.barcode', $format, $d['h'], $d['size'], $showText);
		$barcode_image = $BarCode->getImage($d['text']);
		if(empty($barcode_image))
			return null;

		$imgH = imagesy($barcode_image);
		$imgW = imagesx($barcode_image);

		imagesavealpha($img, false);
		imagealphablending($img, true);

		imagecopyresized($img, $barcode_image, $d['x'], $d['y'], 0, 0, $imgW, $imgH, $imgW, $imgH);
		imagedestroy($barcode_image);

		imagealphablending($img, false);
		imagesavealpha($img, true);

		return null;
	}

	private function includeImage($img, $d) {
		jimport('joomla.filesystem.file');
		$shopConfig = hikaserial::config(false);
		$filename = $d['text'];

		$uploadFolder = ltrim(JPath::clean(html_entity_decode($shopConfig->get('uploadfolder'))),DS);
		$uploadFolder = rtrim($uploadFolder,DS).DS;
		if((!preg_match('#^([A-Z]:)?/.*#', $uploadFolder)) && ($uploadFolder[0] != '/' || !is_dir($uploadFolder)))
			$uploadFolder = rtrim(JPath::clean(HIKASHOP_ROOT.DS.trim($uploadFolder, DS.' ').DS), DS.' ') . DS;
		$clean_filename = JPath::clean(realpath($uploadFolder.$filename));

		$uploadSecureFolder = ltrim(JPath::clean(html_entity_decode($shopConfig->get('uploadsecurefolder'))),DS);
		$uploadSecureFolder = rtrim($uploadFolder,DS).DS;
		if((!preg_match('#^([A-Z]:)?/.*#', $uploadSecureFolder)) && ($uploadSecureFolder[0] != '/' || !is_dir($uploadSecureFolder)))
			$uploadSecureFolder = rtrim(JPath::clean(HIKASHOP_ROOT.DS.trim($uploadSecureFolder, DS.' ').DS), DS.' ') . DS;
		$clean_secure_filename = JPath::clean(realpath($uploadSecureFolder.$filename));

		$file_path = '';
		if(JFile::exists($clean_filename)) {
			$file_path = $clean_filename;
		} elseif(JFile::exists($clean_secure_filename)) {
			$file_path = $clean_secure_filename;
		} elseif(JFile::exists(HIKASHOP_MEDIA.'images'.DS.$filename)) {
			$file_path = HIKASHOP_MEDIA.'images'.DS.$filename;
		} elseif(JFile::exists(HIKASHOP_ROOT.'images'.DS.$filename)) {
			$file_path = HIKASHOP_ROOT.'images'.DS.$filename;
		}

		if(empty($file_path))
			return null;

		$extension = strtolower(substr($file_path, strrpos($file_path, '.') + 1));
		$file_path = JPath::clean($file_path);
		$include_image = $this->initializeImage($file_path, $extension);

		$imgW = imagesx($include_image);
		$imgH = imagesy($include_image);

		$finalW = $imgW;
		$finalH = $imgH;
		if(!empty($d['w']) || !empty($d['h'])) {
			if(empty($d['w'])) $d['w'] = 0;
			if(empty($d['h'])) $d['h'] = 0;
			$scale = $this->scaleImage($imgW, $imgH, (int)$d['w'], (int)$d['h']);
			if(!empty($scale)) {
				$finalW = $scale[0];
				$finalH = $scale[1];
			}
		}

		imagesavealpha($img, false);
		imagealphablending($img, true);

		imagecopyresized($img, $include_image, $d['x'], $d['y'], 0, 0, $finalW, $finalH, $imgW, $imgH);
		imagedestroy($include_image);

		imagealphablending($img, false);
		imagesavealpha($img, true);

		return null;
	}

	private function initializeImage($file_path, $extension) {
		switch($extension) {
			case 'gif':
				return ImageCreateFromGIF($file_path);
			case 'jpg':
			case 'jpeg':
				return ImageCreateFromJPEG($file_path);
			case 'png':
				return ImageCreateFromPNG($file_path);
		}
		return false;
	}

	private function scaleImage($x, $y, $cx, $cy, $scaleMode = 'inside') {
		if(empty($cx)) $cx = 999999;
		if(empty($cy)) $cy = 999999;
		if ($x >= $cx || $y >= $cy) {
			$rx = 0; $ry = 0;
			if ($x>0) $rx = $cx / $x;
			if ($y>0) $ry = $cy / $y;

			if($scaleMode == 'outside') {
				$r = ($rx > $ry) ? $rx : $ry;
			} else {
				$r = ($rx > $ry) ? $ry : $rx;
			}
			$x = intval($x * $r);
			$y = intval($y * $r);
			return array($x,$y);
		}
		return false;
	}

	private function getMime($extension) {
		switch($extension) {
			case 'gif':
				return 'image/gif';
			case 'jpg':
			case 'jpeg':
				return 'image/jpeg';
			case 'png':
				return 'image/png';
		}
		return null;
	}

	private function sendHeader($extension, $serial = '') {
		if($this->params->get('force_download', 0)) {
			$filename = $this->params->get('force_download_filename', '');
			if(empty($filename))
				$filename = $serial;
			if(empty($filename))
				$filename = 'serial';

			jimport('joomla.filesystem.file');
			$filename = strtolower(JFile::makeSafe(trim($filename)));

			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $filename . '.' . $extension . '"');

			return true;
		}

		$mime = $this->getMime($extension);
		if(!empty($mime)) {
			header('Content-Type: '.$mime);
			return true;
		}
		return false;
	}
}
