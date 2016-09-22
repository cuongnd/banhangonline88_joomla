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
class PluginsViewPlugins extends hikaserialView {

	const ctrl = 'plugins';
	const name = 'PLUGINS';
	const icon = 'plugin';

	public function display($tpl = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaserial::config();
		$this->assignRef('config',$config);

		$toggleClass = hikaserial::get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);

		$manage = hikaserial::isAllowed($config->get('acl_plugins_manage','all'));
		$this->assignRef('manage', $manage);

		$type = $app->getUserStateFromRequest(HIKASERIAL_COMPONENT.'.plugin_type', 'plugin_type', 'generator');
		$group = 'hikaserial'; //.$type;
		if(!HIKASHOP_J16) {
			$query = 'SELECT * FROM ' . hikaserial::table('plugins', false).' WHERE `folder` = ' . $db->Quote($group) . ' ORDER BY published DESC, ordering ASC';
		} else {
			$query = 'SELECT extension_id as id, enabled as published, name, element FROM ' . hikaserial::table('extensions', false) . ' WHERE `folder` = ' . $db->Quote($group) . ' AND type=\'plugin\' ORDER BY enabled DESC, ordering ASC';
		}
		$db->setQuery($query);
		$plugins = $db->loadObjectList();


		$this->assignRef('plugins', $plugins);
		$this->assignRef('plugin_type', $type);

		$this->toolbar = array(
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl . '-listing'),
			'dashboard'
		);
	}

	public function form() {
		JHTML::_('behavior.modal');
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$config = hikaserial::config();
		$task = JRequest::getVar('task');

		$this->content = '';
		$this->plugin_name = JRequest::getCmd('name', '');
		if(empty($this->plugin_name)) {
			return false;
		}

		$plugin = hikaserial::import('hikaserial', $this->plugin_name);
		if(!$plugin || !method_exists($plugin, 'type')) {
			$app->enqueueMessage('Fail to load the plugin or the plugin is not an HikaSerial one', 'error');
			return false;
		}
		$this->plugin_type = $plugin->type();

		$query = '';
		if(in_array($this->plugin_type, array('generator','consumer','plugin'))){
			$query = 'SELECT * FROM ' . hikaserial::table($this->plugin_type).' WHERE ' . $this->plugin_type . '_type = '.$db->Quote($this->plugin_name);
			$query .= ' ORDER BY ' . $this->plugin_type . '_ordering ASC';
		}
		if(empty($query))
			return false;
		$db->setQuery($query);
		$elements = $db->loadObjectList($this->plugin_type.'_id');

		if(!empty($elements)){
			$params_name = $this->plugin_type.'_params';
			foreach($elements as $k => $el){
				if(!empty($el->$params_name)){
					$elements[$k]->$params_name = hikaserial::unserialize($el->$params_name);
				}
			}
		}

		$multiple_plugin = false;
		if(method_exists($plugin, 'isMultiple')) {
			$multiple_plugin = $plugin->isMultiple();
		}

		$function = 'onPluginConfiguration';
		$ctrl = '&plugin_type='.$this->plugin_type.'&task=edit&name='.$this->plugin_name;
		if($multiple_plugin === true) {
			$subtask = JRequest::getCmd('subtask','');
			$ctrl .= '&subtask='.$subtask;
			if(empty($subtask)) {
				$function = 'onPluginMultipleConfiguration';
			}
			$cid = hikaserial::getCID($this->plugin_type.'_id');
			if(isset($elements[$cid])){
				$this->assignRef('element', $elements[$cid]);
				$ctrl .= '&'.$this->plugin_type.'_id='.$cid;
			}
		} else {
			if(!empty($elements)) {
				$this->assignRef('element', reset($elements));
			}
		}
		$this->assignRef('elements', $elements);

		$setTitle = true;
		if(method_exists($plugin, $function)) {
			if(empty($plugin->title))
				$plugin->title = JText::_('HIKAS_PLUGIN').' '.$this->plugin_name;
			$plugin->ctrl_url = self::ctrl.$ctrl;

			ob_start();
			$plugin->$function($elements);
			$this->content = ob_get_clean();
			$this->data = $plugin->getProperties();
			$setTitle = false;
		}

		$this->assignRef('name', $this->plugin_name);
		$this->assignRef('plugin', $plugin);
		$this->assignRef('multiple_plugin', $multiple_plugin);
		$this->assignRef('content', $this->content);
		$this->assignRef('plugin_type', $this->plugin_type);

		$joomlaAcl = hikaserial::get('shop.type.joomla_acl');
		$this->assignRef('joomlaAcl', $joomlaAcl);

		if(empty($plugin->pluginView)) {
			$this->content .= $this->loadPluginTemplate(@$plugin->view, $this->plugin_type);
		}

		if($setTitle)
			hikaserial::setTitle(JText::_('HIKAS_PLUGIN').' '.$this->name, self::icon, self::ctrl.$ctrl);
		return true;
	}

	private function loadPluginTemplate($view = '', $type = '') {
		static $previousType = '';

		$app = JFactory::getApplication();

		$this->subview = '';
		if(!empty($view)) {
			$this->subview = '_' . $view;
		}

		if(isset($this->data['pluginConfig'])) {

			$paramsType = $type.'_params';
			if(empty($this->element))
				$this->element = new stdClass();
			if(empty($this->element->$paramsType))
				$this->element->$paramsType = new stdClass();

			$html = '<table class="table admintable" style="width:100%"><tbody>';
			$closeTag = true;
			foreach($this->data['pluginConfig'] as $key => $value){
				if(is_array($value[0])) {
					$a = array_shift($value[0]);
					$label = vsprintf(JText::_($a), $value[0]);
				} else {
					$label = JText::_($value[0]);
				}

				if($closeTag)
					$html .= '<tr><td class="key"><label for="data['.$type.']['.$paramsType.']['.$key.']">'.$label.'</label></td><td>';
				$closeTag = true;

				switch ($value[1]) {
					case 'input':
					case 'int':
					case 'float':
						$v = @$this->element->$paramsType->$key;
						if(empty($v) && !empty($value[2]))
							$v = $value[2];
						if($value[1] == 'int') $v = (int)$v;
						if($value[1] == 'float') $v = (float)hikaserial::toFloat($v);

						$html .= '<input type="text" name="data['.$type.']['.$paramsType.']['.$key.']" value="'.$v.'"/>';

						if(!empty($value[3]))
							$html .= $value[3];
						break;

					case 'textarea':
						$html .= '<textarea name="data['.$type.']['.$paramsType.']['.$key.']" rows="3">'.@$this->element->$paramsType->$key.'</textarea>';
						break;

					case 'big-textarea':
						$html .= '<textarea name="data['.$type.']['.$paramsType.']['.$key.']" rows="9" width="100%" style="width:100%;">'.@$this->element->$paramsType->$key.'</textarea>';
						break;

					case 'boolean':
						if(!isset($this->element->$paramsType->$key) && isset($value[2]))
							$this->element->$paramsType->$key = $value[2];
						if(!isset($this->element->$paramsType->$key))
							$this->element->$paramsType->$key = 0;
						$html .= JHTML::_('hikaselect.booleanlist', 'data['.$type.']['.$paramsType.']['.$key.']' , '', @$this->element->$paramsType->$key);
						break;

					case 'checkbox':
						$i = 0;
						foreach($value[2] as $listKey => $listData){
							$checked = '';
							if(!empty($this->element->$paramsType->$key)){
								if(in_array($listKey, $this->element->$paramsType->$key))
									$checked = 'checked="checked"';
							}
							$html .= '<input id="data_'.$type.'_'.$paramsType.'_'.$key.'_'.$i.'" name="data['.$type.']['.$paramsType.']['.$key.'][]" type="checkbox" value="'.$listKey.'" '.$checked.' /><label for="data_'.$type.'_'.$paramsType.'_'.$key.'_'.$i.'">'.$listData.'</label><br/>';
							$i++;
						}
						break;

					case 'list':
						$values = array();
						foreach($value[2] as $listKey => $listData){
							$values[] = JHTML::_('select.option', $listKey,JText::_($listData));
						}
						$html .= JHTML::_('select.genericlist', $values, 'data['.$type.']['.$paramsType.']['.$key.']' , 'class="inputbox" size="1"', 'value', 'text', @$this->element->$paramsType->$key );
						break;

					case 'group':
						$aclType = hikaserial::get('type.acl');
						$html .= $aclType->display('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key);
						break;

					case 'pack':
						$packType = hikaserial::get('type.pack');
						$html .= $packType->displaySingle('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key);
						break;

					case 'packs':
						$packType = hikaserial::get('type.pack');
						$html .= $packType->displayMultiple('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key);
						break;

					case 'product':
						$productDisplayType = hikaserial::get('shop.type.productdisplay');
						$html .= $productDisplayType->displaySingle('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key);
						break;

					case 'discount':
						$discountType = hikaserial::get('shop.type.discount');
						$html .= $discountType->displaySelector('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key, false, 'coupon');
						break;

					case 'period-value':
						$closeTag = false;
						$v = @$this->element->$paramsType->$key;
						if(empty($v))
							$v = '0';
						$html .= '<input type="text" name="data['.$type.']['.$paramsType.']['.$key.']" value="'.$v.'"/>';
						break;

					case 'period-type':
						$values = array(
							JHTML::_('select.option', 'day', JText::_('DAYS')),
							JHTML::_('select.option', 'month', JText::_('MONTHS')),
							JHTML::_('select.option', 'year', JText::_('YEARS'))
						);
						$html .= JHTML::_('select.genericlist', $values, 'data['.$type.']['.$paramsType.']['.$key.']', 'class="inputbox" size="1"', 'value', 'text', @$this->element->$paramsType->$key);
						break;

					case 'currency':
						$currenciesType = hikaserial::get('shop.type.currency');
						$html .= $currenciesType->display('data['.$type.']['.$paramsType.']['.$key.']', @$this->element->$paramsType->$key);
						break;

					case 'serial_test':
						if(empty($this->element->$paramsType->format)) {
							$html .= '<strong>'.@$this->data['serial_default_format'].'</strong><br/>';
						} else {
							$html .= '<strong>'.$this->element->$paramsType->format.'</strong><br/>';
						}
						$pack = null;
						$p_id = $type . '_id';
						if(isset($this->element->$p_id)) {
							$name = $this->plugin_name;

							$pack = new stdClass();
							$pack->$name = $this->element->$p_id;
							$order = null;
							$serials = array();
							$this->plugin->test = true;
							$this->plugin->generate($pack, $order, 5, $serials);
							if(is_array($serials)) {
								$s = reset($serials);
								if(is_string($s))
									$html .= implode('<br/>', $serials);
							}
						}
						break;

					case 'html':
						$html .= $value[2];
						break;
				}

				if($closeTag)
					$html .= '</td></tr>';
			}
			if(!$closeTag)
				$html .= '</td></tr>';
			$html .= '</tbody></table>';

			return $html;
		}

		$name = $this->plugin_name . '_configuration' . $this->subview . '.php';
		$path = JPATH_THEMES . DS . $app->getTemplate() . DS . 'hikaserial' . DS . $name;
		if(!file_exists($path)) {
			if(!HIKASHOP_J16) {
				$path = JPATH_PLUGINS . DS . 'hikaserial' . DS . $name;
			} else {
				$path = JPATH_PLUGINS . DS . 'hikaserial' . DS . $this->plugin_name . DS . $name;
			}
			if(!file_exists($path)) {
				return '';
			}
		}
		ob_start();
		require($path);
		return ob_get_clean();
	}

}
