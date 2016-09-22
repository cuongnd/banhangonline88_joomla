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
class plgHikaserialSeriesgen extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = true;
	protected $populate = true;
	protected $doc_form = 'seriesgen-';
	protected $name = 'seriesgen';

	protected $pluginConfig = array(
		'min_value' => array('MIN_VALUE', 'input'),
		'max_value' => array('MAX_VALUE', 'input'),
		'format' => array('SERIAL_FORMAT', 'input', '', '<br/><em>sprintf format. See <a href="http://php.net/sprintf" target="_blank"">PHP documentation</a></em>'),
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		if(!isset($pack->seriesgen))
			return;

		parent::pluginParams($pack->seriesgen);

		$offset = 1;
		if(!empty($order->hikaserial->formData['start_from'])) {
			$offset = (int)$order->hikaserial->formData['start_from'];
		} else {
			$this->db->setQuery('SELECT MAX(a.serial_data * 1) FROM ' . hikaserial::table('serial') . ' AS a WHERE serial_pack_id = ' . $pack->pack_id);
			$offset = ((int)$this->db->loadResult()) + 1;
		}

		if(!empty($this->plugin_params->min_value) && $offset < (int)$this->plugin_params->min_value) {
			$offset = (int)$this->plugin_params->min_value;
		}

		$max = -1;
		if(!empty($this->plugin_params->max_value) && (int)$this->plugin_params->max_value > 0 && (int)$this->plugin_params->min_value < (int)$this->plugin_params->max_value) {
			$max = (int)$this->plugin_params->max_value;
		}

		if($max > 0 && $offset > $max)
			return false;

		for($q = 0; $q < $quantity; $q++) {
			$serials[] = $offset++;
			if($max > 0 && $offset > $max)
				break;
		}
		return true;
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	public function onDisplaySerials(&$data, $viewName) {
		if($viewName == 'back-serial-form')
			return;

		$n = 'plg.seriesgen';
		$l = strlen($n) + 1;
		$packs = array();
		foreach($data as &$serial) {
			if(substr($serial->pack_generator, 0, $l) != ($n . '-'))
				continue;
			$id = (int)substr($serial->pack_generator, $l);
			$packs[$id] = $id;
		}
		unset($serial);

		if(!empty($packs)) {
			$this->db->setQuery('SELECT * FROM '.hikaserial::table('generator').' WHERE generator_type = \'seriesgen\' AND generator_id IN ('.implode(',', $packs).')');
			$confs = $this->db->loadObjectList('generator_id');
			foreach($confs as &$conf) {
				if(!empty($conf->generator_params))
					$conf->generator_params = hikaserial::unserialize($conf->generator_params);
				unset($conf);
			}
		}

		foreach($data as &$serial) {
			if(substr($serial->pack_generator, 0, $l) != ($n . '-'))
				continue;
			$id = (int)substr($serial->pack_generator, $l);
			if(!isset($confs[$id]) || empty($confs[$id]->generator_params->format))
				continue;
			$format = $confs[$id]->generator_params->format;
			if(!isset($serial->serial_data_orig))
				$serial->serial_data_orig = $serial->serial_data;
			$serial->serial_data = sprintf($format, (int)$serial->serial_data);
		}
		unset($serial);

		return;
	}

	public function populateForm(&$pack) {
		$ret = '';

		$ret .= '<tr>
			<td class="key"><label>'.JText::_('START_FROM_NUMBER').'</label></td>
			<td><input type="text" name="data[start_from]" value=""/></td>
		</tr>';

		return $ret;
	}
}
