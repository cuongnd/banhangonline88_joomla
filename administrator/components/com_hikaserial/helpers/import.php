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
class hikaserialImportHelper {

	private $db = null;
	private $config = null;
	private $shopConfig = null;
	private $serialClass = null;
	private $packClass = null;
	private $packs = null;
	private $columns = array();
	private $fields = array();

	private $quote = '"';
	private $listSeparators = array(';',',','|',"\t");
	private $perBatch = 50;

	public $pack_id = 0;
	public $charset = '';

	public $serialCounter = 0;
	public $packCounter = 0;
	public $productCounter = 0;

	public function __construct() {
		$this->db = JFactory::getDBO();

		$this->config = hikaserial::config();
		$this->shopConfig = hikaserial::config(false);
		$this->serialClass = hikaserial::get('class.serial');
		$this->packClass = hikaserial::get('class.pack');
	}

	public function importFromFile(&$importFile, $options = array()) {
		$app = JFactory::getApplication();
		if(empty($importFile['name'])) {
			$app->enqueueMessage(JText::_('BROWSE_FILE'),'notice');
			return false;
		}

		jimport('joomla.filesystem.file');
		$allowedFiles = array('csv','txt');

		$uploadFolder = rtrim(JPath::clean(html_entity_decode($this->config->get('uploadfolder'))), DS) . DS;
		if(strpos($uploadFolder, JPATH_ROOT) !== false) {
			$uploadFolder = str_replace(JPATH_ROOT, '', $uploadFolder);
		}
		$uploadFolder = ltrim($uploadFolder,'/');
		$uploadFolder = JPATH_ROOT.DS.$uploadFolder;
		$uploadPath = $uploadFolder;

		if(!is_dir($uploadPath)) {
			jimport('joomla.filesystem.folder');
			JFolder::create($uploadPath);
			$data = '<html><body bgcolor="#FFFFFF"></body></html>';
			JFile::write($uploadPath . 'index.html', $data);
		}

		if(!is_writable($uploadPath)) {
			@chmod($uploadPath,'0755');
			if(!is_writable($uploadPath)) {
				$app->enqueueMessage(JText::sprintf('WRITABLE_FOLDER', $uploadPath), 'notice');
			}
		}

		$attachment = new stdClass();
		$attachment->filename = strtolower(JFile::makeSafe($importFile['name']));
		$attachment->size = $importFile['size'];
		$attachment->extension = strtolower(substr($attachment->filename, strrpos($attachment->filename, '.') + 1));

		if(!in_array($attachment->extension, $allowedFiles)) {
			$app->enqueueMessage(JText::sprintf('ACCEPTED_TYPE', $attachment->extension, implode(',', $allowedFiles)), 'notice');
			return false;
		}

		if(!move_uploaded_file($importFile['tmp_name'], $uploadPath . $attachment->filename)) {
			if(!JFile::upload($importFile['tmp_name'], $uploadPath . $attachment->filename)) {
				$app->enqueueMessage(JText::sprintf( 'FAIL_UPLOAD', $importFile['tmp_name'], $uploadPath . $attachment->filename), 'error');
			}
		}

		hikaserial::increasePerf();

		$contentFile = file_get_contents($uploadPath . $attachment->filename);
		if(!$contentFile) {
			$app->enqueueMessage(JText::sprintf('FAIL_OPEN', $uploadPath . $attachment->filename), 'error');
			return false;
		};
		unlink($uploadPath . $attachment->filename);
		return $this->handleCsvContent($contentFile, $options);
	}

	public function handleCsvContent(&$contentFile, $options = array()) {
		$app = JFactory::getApplication();
		$encodingHelper = hikaserial::get('shop.helper.encoding');

		$db_quote = $this->quote . $this->quote;
		$contentFile = str_replace(array("\r\n","\r"), "\n", trim($contentFile) );

		$nlPos = strpos($contentFile, "\n");
		$header = substr($contentFile, 0, $nlPos);
		$contentFile = substr($contentFile, $nlPos+1);
		if(!$this->autoHeader($header)) {
			return;
		}

		$contentFile = str_replace($db_quote, '&quot;', $contentFile);
		$contentFile = str_replace($this->separator.'&quot;'.$this->separator, $this->separator.$this->separator, $contentFile);
		$contentFile = str_replace($this->separator.'&quot;'."\n", $this->separator."\n", $contentFile);
		$contentFile .= "\n";

		$opt = array(
			'add_status' => !in_array('serial_id', $this->columns) && !in_array('serial_status', $this->columns),
			'add_pack' => !in_array('serial_pack_id', $this->fields),
			'extradata' => ($this->fields[count($this->fields) - 1] == 'serial_extradata'),
		);

		$inquotes = false;
		$start = 0;

		$insert = 'REPLACE INTO ' . hikaserial::table('serial') . ' (' . implode(',', $this->fields);
		if($opt['add_status']) {
			$insert .= ',serial_status';
		}
		if($opt['add_pack']) {
			if(empty($this->pack_id)) {
				$app->enqueueMessage('PACK_IMPORT_ERROR_NO_PACK_SPECIFIED');
				return false;
			}
			$insert .= ',serial_pack_id';
		}
		$insert .= ') VALUES (';
		$serials = array();
		$productsAssociations = array('insert' => array(), 'search' => array());

		$duplicates = 0;
		$checkDuplicates = !empty($options['check_duplicates']) && !in_array('serial_id', $this->columns);

		$i = 0;
		$l = strlen($contentFile);
		while($i < $l) {

			$char = $contentFile[$i];
			if($char == $this->quote) {
				$inquotes = !$inquotes;
			}

			if(($char == $this->separator || $char == "\n") && !$inquotes) {
				$cell = substr($contentFile, $start, $i - $start);
				$cell = str_replace($this->quote, '', $cell);
				$cell = str_replace('&quot;', $this->quote, $cell);

				$data[] = $cell;

				$start = $i + 1;
				if($char == "\n") {
					if(count($data) == 0)
						continue;

					if(count($data) != count($this->columns)) {
						return false;
					}

					$serial_data = null;
					$serial = array();
					$association = array();
					$serial_extradata = array();

					foreach($this->columns as $k => $v) {
						if(substr($v, 0, 10) == 'extradata.') {
							if(!empty($data[$k]))
								$serial_extradata[substr($v, 10)] = $data[$k];
							continue;
						}

						switch($v) {
							case 'pack_name':
								if(!isset($this->packs[ $data[$k] ])) {
									$pack = new stdClass();
									$pack->pack_name = $data[$k];
									$pack->pack_data = 'sql';
									$pack->pack_generator = '';
									$pack->pack_published = '1';
									$pack->pack_params = new stdClass();
									$pack->pack_description = '';
									$ret = $this->packClass->save($pack);
									if($ret) {
										$this->packs[ $data[$k] ] = $ret;
										$this->packCounter++;
									} else {
										$this->packs[ $data[$k] ] = 0;
									}
								}
								$association[$v] = $data[$k];
								$serial[] = (int)$this->packs[ $data[$k] ];
								break;
							case 'pack_quantity':
							case 'pack_id':
							case 'product_id':
							case 'product_code':
								$association[$v] = $data[$k];
								break;
							case 'serial_data':
								$serial_data = $data[$k];
							default:
								$serial[] = $this->db->quote($data[$k]);
								break;
						}
					}

					if($opt['extradata']) {
						$serial[] = $this->db->quote(serialize($serial_extradata));
					}

					if( ( !empty($association['product_id']) || !empty($association['product_code']) ) && (!empty($association['pack_name']) || !empty($association['pack_id'])) ) {
						if(!isset($association['pack_quantity']) || ((int)$association['pack_quantity'] <= 0 && trim($data['pack_quantity']) != '0') )
							$association['pack_quantity'] = 1;

						if(!empty($association['pack_id']) && !empty($association['product_id'])) {
							$productsAssociations['insert'][] = array(
								'pack_id' => (int)$association['pack_id'],
								'product_id' => (int)$association['product_id'],
								'pack_quantity' => (int)$association['pack_quantity']
							);
						} else {
							$productsAssociations['search'][] = array(
								'pack_id' => (int)@$association['pack_id'],
								'pack_name' => @$association['pack_name'],
								'product_id' => (int)@$association['product_id'],
								'product_code' => @$association['product_code'],
								'pack_quantity' => (int)$association['pack_quantity']
							);
						}

						if(!empty($productsAssociations['search']) && count($productsAssociations['search']) >= $this->perBatch) {
							$this->searchPackAssociation($productsAssociations);
						}
						if(!empty($productsAssociations['insert']) && count($productsAssociations['insert']) >= $this->perBatch) {
							$this->productCounter = $this->processPackAssociation($productsAssociations['insert']);
						}

						$serial = array();
					}

					if(!empty($serial) && $serial_data !== null) {
						if($opt['add_status']) {
							$serial[] = "'free'";
						}
						if($opt['add_pack']) {
							$serial[] = $this->pack_id;
						}

						if($checkDuplicates && isset($serials[$serial_data])) {
							$app->enqueueMessage(JText::sprintf('&quot;%s&quot; duplicate serials found in the content', htmlentities($serial_data)));
						} else {
							if($checkDuplicates)
								$serials[$serial_data] = implode(',', $serial);
							else
								$serials[] = implode(',', $serial);

							if(count($serials) >= $this->perBatch ) {

								if($checkDuplicates) {
									$keys = array_keys($serials);
									foreach($keys as &$key) {
										$key = $this->db->Quote($key);
									}
									$query = 'SELECT serial_data FROM ' . hikaserial::table('serial') . ' WHERE serial_data IN ('.implode(',', $keys).')';
									$this->db->setQuery($query);
									if(!HIKASHOP_J25)
										$keys = $this->db->loadResultArray();
									else
										$keys = $this->db->loadColumn();

									if(!empty($keys)) {
										foreach($keys as $key) {
											unset($serials[ $key ]);
										}
										$duplicates += count($keys);
									}
								}

								if(!empty($serials)) {
									$this->db->setQuery($insert . implode('),(', $serials) . ')');
									$this->db->query();
									$this->serialCounter += count($serials);
								}
								$serials = array();
							}
						}
					}

					$data = array();
				}
			}
			$i++;
		}

		if($checkDuplicates && count($serials) > 0) {
			$keys = array_keys($serials);
			foreach($keys as &$key) {
				$key = $this->db->Quote($key);
			}
			$query = 'SELECT serial_data FROM ' . hikaserial::table('serial') . ' WHERE serial_data IN ('.implode(',', $keys).')';
			$this->db->setQuery($query);
			if(!HIKASHOP_J25)
				$keys = $this->db->loadResultArray();
			else
				$keys = $this->db->loadColumn();

			if(!empty($keys)) {
				foreach($keys as $key) {
					unset($serials[ $key ]);
				}
				$duplicates += count($keys);
			}
		}

		if(count($serials) > 0) {
			$this->db->setQuery($insert . implode('),(', $serials) . ')');
			$this->db->query();
			$this->serialCounter += count($serials);
		}

		if(count($productsAssociations['search']) > 0) {
			$this->searchPackAssociation($productsAssociations);
		}
		if(count($productsAssociations['insert']) > 0) {
			$this->productCounter = $this->processPackAssociation($productsAssociations['insert']);
		}

		$app->enqueueMessage( JText::sprintf('%d serials imported', $this->serialCounter)); // _('%d serials imported')
		if($this->packCounter > 0) {
			$app->enqueueMessage( JText::sprintf('%d packs imported', $this->packCounter)); // _('%d packs imported')
		}
		if($this->productCounter > 0) {
			$app->enqueueMessage( JText::sprintf('%d product associations imported', $this->productCounter)); // _('%d product associations imported')
		}

		if($duplicates > 0) {
			$app->enqueueMessage( JText::sprintf('%d duplicate serials found', $duplicates), 'error'); // _('%d duplicate serials found')
		}
	}

	private function searchPackAssociation(&$data) {
		$pack_search = array();
		$product_search = array();

		foreach($data['search'] as $d) {
			if(!empty($d['pack_name']))
				$pack_search[] = $this->db->Quote($d['pack_name']);
			if(!empty($d['product_code']))
				$product_search[] = $this->db->Quote($d['product_code']);
		}

		if(!empty($pack_search)) {
			$query = 'SELECT pack_id, pack_name FROM '.hikaserial::table('pack').' WHERE pack_name IN (' . implode(',', $pack_search) . ')';
			$this->db->setQuery($query);
			$packs = $this->db->loadObjectList('pack_name');

			foreach($data['search'] as $k => $d) {
				if(!empty($d['pack_name'])) {
					$n = $d['pack_name'];
					if(isset($packs[ $n ])) {
						$data['search'][$k]['pack_id'] = (int)$packs[ $n ]->pack_id;
						unset($data['search'][$k]['pack_name']);
					} else {
						unset($data['search'][$k]);
					}
				}
			}
			unset($packs);
		}
		if(!empty($product_search)) {
			$query = 'SELECT product_id, product_code FROM '.hikaserial::table('shop.product').' WHERE product_code IN (' . implode(',', $product_search) . ')';
			$this->db->setQuery($query);
			$products = $this->db->loadObjectList('product_code');

			foreach($data['search'] as $k => $d) {
				if(!empty($d['product_code'])) {
					$n = $d['product_code'];
					if(isset($products[ $n ])) {
						$data['search'][$k]['product_id'] = (int)$products[ $n ]->product_id;
						unset($data['search'][$k]['product_code']);
					} else {
						unset($data['search'][$k]);
					}
				}
			}

			unset($products);
		}

		foreach($data['search'] as $d) {
			$data['insert'][] = $d;
		}
		$data['search'] = array();
	}

	private function processPackAssociation(&$data) {
		$insertQuery = 'REPLACE INTO '.hikaserial::table('product_pack').' (product_id, pack_id, quantity) VALUES ';
		$doInsert = false;
		$deleteQuery = 'DELETE FROM '.hikaserial::table('product_pack').' WHERE ';
		$doDelete = false;

		foreach($data as $d) {
			if($d['pack_quantity'] == 0) {
				if($doDelete)
					$deleteQuery .= ' OR ';
				$deleteQuery .= '(product_id = '.$d['product_id'].' AND pack_id = '.$d['pack_id'].')';
				$doDelete = true;
			} else {
				if($doInsert)
					$insertQuery .= ',';
				$insertQuery .= '('.$d['product_id'].','.$d['pack_id'].','.$d['pack_quantity'].')';
				$doInsert = true;
			}
		}

		if($doDelete) {
			$this->db->setQuery($deleteQuery);
			$this->db->query();
		}
		if($doInsert) {
			$this->db->setQuery($insertQuery);
			$this->db->query();
		}

		return count($data);
	}

	private function autoHeader($header) {
		$header = str_replace("\xEF\xBB\xBF", '', $header);

		$this->separator = ',';
		foreach($this->listSeparators as $sep){
			if(strpos($header,$sep) !== false){
				$this->separator = $sep;
				break;
			}
		}

		$this->columns = explode($this->separator, $header);
		$extra_data = false;
		foreach($this->columns as &$column){
			if(function_exists('mb_strtolower')) {
				$column = mb_strtolower(trim($column,'" '));
			} else {
				$column = strtolower(trim($column,'" '));
			}

			if(substr($column, 0, 10) == 'extradata.') {
				$extra_data = true;
				continue;
			}

			switch($column) {
				case 'pack_name':
					$this->fields[] = 'serial_pack_id';
					break;
				case 'pack_quantity':
				case 'pack_id':
				case 'product_id':
				case 'product_code':
					break;
				default:
					$this->fields[] = $column;
					break;
			}
		}
		unset($column);

		if($extra_data) {
			$this->fields[] = 'serial_extradata';
		}

		if(in_array('pack_name', $this->columns)) {
			$query = 'SELECT pack_name, pack_id FROM ' . hikaserial::table('pack');
			$this->db->setQuery($query);
			$packs = $this->db->loadObjectList();
			$this->packs = array();
			foreach($packs as $pack) {
				if(!isset($this->packs[$pack->pack_name])) {
					$this->packs[$pack->pack_name] = $pack->pack_id;
				} else {
					$app = JFactory::getApplication();
					$app->enqueueMessage(JText::sprintf('SERIAL_IMPORT_DUPLICATE_PACK_NAME', $pack->pack_name), 'error');
					return false;
				}
			}
		}

		if(!in_array('serial_data', $this->columns)) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('SERIAL_IMPORT_INCORRECT_HEADER'), 'error');
			return false;
		}

		return true;
	}

	public function handleTextContent(&$contentFile, $options = array()) {

		$contentFile = str_replace(array("\r\n","\r"), "\n", trim($contentFile) );
		$data = explode("\n", $contentFile);

		if(empty($this->pack_id)) {
			return false;
		}

		$cpt = 0;
		$baseSql = 'REPLACE INTO ' . hikaserial::table('serial') . ' (serial_data, serial_pack_id, serial_status) VALUES ';
		$sql = $baseSql;
		foreach($data as $d) {
			if($cpt > 0)
				$sql .= ', ';

			$sql .= '(' . $this->db->quote($d) . ', ' . $this->pack_id . ', ' . $this->db->quote('free') . ')';

			if($cpt >= $this->perBatch ) {
				$this->db->setQuery($sql);
				$this->db->query();
				$sql = $baseSql;
				$this->serialCounter += $cpt;
				$cpt = 0;
			}
			$cpt++;
		}
		unset($data);

		if($cpt > 0 ) {
			$this->db->setQuery($sql);
			$this->db->query();
			$this->serialCounter += $cpt;
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::sprintf('%d serials imported', $this->serialCounter));

		if($this->packCounter > 0) {
			$app->enqueueMessage( JText::sprintf('%d packs imported', $this->packCounter));
		}

		return true;
	}
}
