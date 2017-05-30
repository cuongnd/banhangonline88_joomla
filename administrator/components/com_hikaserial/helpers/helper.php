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
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
$hikashopHelperFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
if(!file_exists($hikashopHelperFile)) {
	JError::raiseWarning(500, 'HikaShop not installed ( www.hikashop.com )');
	exit;
}
include_once($hikashopHelperFile);

define('HIKASERIAL_COMPONENT','com_hikaserial');
define('HIKASERIAL_LIVE',rtrim(JURI::root(),'/').'/');
define('HIKASERIAL_ROOT',rtrim(JPATH_ROOT,DS).DS);
define('HIKASERIAL_FRONT',rtrim(JPATH_SITE,DS).DS.'components'.DS.HIKASERIAL_COMPONENT.DS);
define('HIKASERIAL_BACK',rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.HIKASERIAL_COMPONENT.DS);
define('HIKASERIAL_HELPER',HIKASERIAL_BACK.'helpers'.DS);
define('HIKASERIAL_BUTTON',HIKASERIAL_BACK.'buttons');
define('HIKASERIAL_CLASS',HIKASERIAL_BACK.'classes'.DS);
define('HIKASERIAL_INC',HIKASERIAL_BACK.'inc'.DS);
define('HIKASERIAL_VIEW',HIKASERIAL_BACK.'views'.DS);
define('HIKASERIAL_TYPE',HIKASERIAL_BACK.'types'.DS);
define('HIKASERIAL_MEDIA',HIKASERIAL_ROOT.'media'.DS.HIKASERIAL_COMPONENT.DS);
define('HIKASERIAL_DBPREFIX','#__hikaserial_');

define('HIKASERIAL_NAME','HikaSerial');
define('HIKASERIAL_LNAME','hikaserial');
define('HIKASERIAL_TEMPLATE',HIKASERIAL_FRONT.'templates'.DS);
define('HIKASERIAL_URL','https://www.hikashop.com/');
define('HIKASERIAL_UPDATEURL',HIKASERIAL_URL.'index.php?option=com_updateme&ctrl=update&task=');
define('HIKASERIAL_HELPURL',HIKASERIAL_URL.'index.php?option=com_updateme&ctrl=doc&component='.HIKASERIAL_LNAME.'&page=');
define('HIKASERIAL_REDIRECT',HIKASERIAL_URL.'index.php?option=com_updateme&ctrl=redirect&page=');

$app = JFactory::getApplication();
if($app->isAdmin()) {
	define('HIKASERIAL_CONTROLLER',HIKASERIAL_BACK.'controllers'.DS);
	define('HIKASERIAL_IMAGES','../media/'.HIKASERIAL_COMPONENT.'/images/');
	define('HIKASERIAL_CSS','../media/'.HIKASERIAL_COMPONENT.'/css/');
	define('HIKASERIAL_JS','../media/'.HIKASERIAL_COMPONENT.'/js/');
	$css_type = 'backend';
} else {
	define('HIKASERIAL_CONTROLLER',HIKASERIAL_FRONT.'controllers'.DS);
	define('HIKASERIAL_IMAGES',JURI::base(true).'/media/'.HIKASERIAL_COMPONENT.'/images/');
	define('HIKASERIAL_CSS',JURI::base(true).'/media/'.HIKASERIAL_COMPONENT.'/css/');
	define('HIKASERIAL_JS',JURI::base(true).'/media/'.HIKASERIAL_COMPONENT.'/js/');
	$css_type = 'frontend';
}
$lang = JFactory::getLanguage();
$lang->load(HIKASERIAL_COMPONENT,JPATH_SITE);

class hikaserial {

	private static $configClass = null;
	private static $shopConfigClass = null;

	public static function get($name) {
		$namespace = 'hikaserial';
		if(substr($name,0,5)=='shop.'){
			$namespace = 'hikashop';
			$name=substr($name,5);
		}
		list($group,$class) = explode('.',$name,2);
		if($group=='controller') {
			$className = $class.ucfirst($group);
		}else{
			$className = $namespace.ucfirst($class).ucfirst($group);
		}
		if(class_exists($className.'Override'))
			$className .= 'Override';
		if(!class_exists($className)) {
			$app = JFactory::getApplication();
			$path = JPATH_THEMES.DS.$app->getTemplate().DS.'html'.DS.'com_'.$namespace.DS.'administrator'.DS;
			if($namespace == 'hikaserial')
				$override = str_replace(HIKASERIAL_BACK, $path, constant(strtoupper('HIKASERIAL_'.$group))).$class.'.override.php';
			else
				$override = str_replace(HIKASHOP_BACK, $path, constant(strtoupper('HIKASHOP_'.$group))).$class.'.override.php';

			if(JFile::exists($override)) {
				$originalFile = constant(strtoupper($namespace.'_'.$group)).$class.'.php';
				include_once($override);
				$className .= 'Override';
			} else {
				include_once(constant(strtoupper($namespace.'_'.$group)).$class.'.php');
			}
		}
		if(!class_exists($className)) return null;

		$args = func_get_args();
		array_shift($args);
		switch(count($args)){
			case 4:
				return new $className($args[0],$args[1],$args[2],$args[3]);
			case 3:
				return new $className($args[0],$args[1],$args[2]);
			case 2:
				return new $className($args[0],$args[1]);
			case 1:
				return new $className($args[0]);
			case 0:
			default:
				return new $className();
		}
	}

	public static function &config($serial = true, $reload = false) {
		if(!$serial) {
			if(self::$shopConfigClass === null)
				self::$shopConfigClass =& hikashop_config();
			if(self::$shopConfigClass === null || $reload || !is_object(self::$shopConfigClass) || self::$shopConfigClass->get('configClassInit', 0) == 0) {
				self::$shopConfigClass = self::get('shop.class.config');
				if( self::$shopConfigClass === null ) die(HIKASHOP_NAME.' config not found');
				self::$shopConfigClass->load();
			}
			return self::$shopConfigClass;
		}
		if(self::$configClass === null || $reload){
			self::$configClass = self::get('class.config');
			if( self::$configClass === null ) die(HIKASERIAL_NAME.' config not found');
			self::$configClass->load();
		}
		return self::$configClass;
	}

	public static function level($level) {
		$config = self::config();
		return ($config->get($config->get('level'),0) >= $level);
	}

	public static function import($type, $name, $dispatcher = null) {
		$type = preg_replace('#[^A-Z0-9_\.-]#i', '', $type);
		$name = preg_replace('#[^A-Z0-9_\.-]#i', '', $name);
		if(!HIKASHOP_J16) {
			$path = JPATH_PLUGINS.DS.$type.DS.$name.'.php';
		} else {
			$path = JPATH_PLUGINS.DS.$type.DS.$name.DS.$name.'.php';
		}
		$instance = false;
		if( file_exists($path) ) {
			require_once($path);
			$className = 'plg'.$type.$name;
			if(class_exists($className)) {
				if($dispatcher == null) {
					$dispatcher = JDispatcher::getInstance();
				}
				$instance = new $className($dispatcher, array('name' => $name, 'type' => $type));
			}
		}
		return $instance;
	}

	public static function completeLink($link, $popup = false, $redirect = false, $js = false) {
		$namespace = HIKASERIAL_COMPONENT;
		if(substr($link,0,5)=='shop.'){
			$namespace = HIKASHOP_COMPONENT;
			$link=substr($link,5);
		}
		if( $popup )
			$link .= '&tmpl=component';
		$ret = JRoute::_('index.php?option='.$namespace.'&ctrl='.$link, !$redirect);
		if($js) return str_replace('&amp;', '&', $ret);
		return $ret;
	}

	public static function table($name, $component = true) {
		if( $component === true || $component === 'serial' ) {
			if( substr($name, 0, 5) == 'shop.' ) {
				return HIKASHOP_DBPREFIX . substr($name, 5);
			}
			if( substr($name, 0, 7) == 'joomla.' ) {
				return '#__'.substr($name, 7);
			}
			return HIKASERIAL_DBPREFIX . $name;
		}
		if( $component === 'shop' ) {
			return HIKASHOP_DBPREFIX . $name;
		}
		return '#__'.$name;
	}

	public static function secureField($fieldName) {
		if (!is_string($fieldName) OR preg_match('|[^a-z0-9#_.-]|i',$fieldName) !== 0 ){
			 die('field "'.$fieldName .'" not secured');
		}
		return $fieldName;
	}

	public static function getLayout($controller, $layout, $params, &$js, $backend = false) {
		$app = JFactory::getApplication();
		$component = HIKASERIAL_COMPONENT;
		$base_path = HIKASERIAL_FRONT;
		if($app->isAdmin() || $backend) {
			$base_path = HIKASERIAL_BACK;
		}
		if( substr($controller, 0, 5) == 'shop.' ) {
			$controller = substr($controller, 5);
			$component = HIKASHOP_COMPONENT;
			$base_path = HIKASHOP_FRONT;
			if($app->isAdmin() || $backend) {
				$base_path = HIKASHOP_BACK;
			}
		}
		$base_path = rtrim($base_path, DS);
		$document = JFactory::getDocument();

		$ctrl = new hikaserialBridgeController(array(
			'name' => $controller,
			'base_path' => $base_path
		));
		$viewType = $document->getType();
		if(empty($viewType))
			$viewType = 'html';

		$view = $ctrl->getView('', $viewType, '', array('base_path' => $base_path));
		$folder	= $base_path.DS.'views'.DS.$view->getName().DS.'tmpl';
		$view->addTemplatePath($folder);
		$folder	= JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$component.DS.$view->getName();
		$view->addTemplatePath($folder);
		$old = $view->setLayout($layout);
		ob_start();
		$view->display(null, $params);
		$js = @$view->js;
		if(!empty($old))
			$view->setLayout($old);
		return ob_get_clean();
	}

	public static function getCID($field = '', $int = true) {
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$res = reset($cid);
		if(empty($res) && !empty($field))
			$res = JRequest::getCmd($field, 0);
		if($int)
			return intval($res);
		return $res;
	}

	public static function getMenu($title = '', $tpl = null) {
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$base_path = rtrim(HIKASHOP_BACK, DS);
		$controller = new HikaShopBridgeController(
			array(
				'base_path' => $base_path,
				'name' => 'menu'
			)
		);
		$viewType = $document->getType();
		$view = $controller->getView('', $viewType, '', array('base_path' => $base_path));
		$folder	= JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.HIKASHOP_COMPONENT.DS.$view->getName();
		$view->addTemplatePath($folder);
		$view->setLayout('default');
		ob_start();
		$view->display($tpl, $title);
		return ob_get_clean();
	}

	public static function setTitle($name, $picture, $link) {
		$shopConfig = hikaserial::config(false);
		$menu_style = $shopConfig->get('menu_style', 'title_bottom');
		if(HIKASHOP_J30) $menu_style = 'content_top';
		$html='<a href="'.hikaserial::completeLink($link).'">'.$name.'</a>';
		if($menu_style != 'content_top') {
			$html = hikaserial::getMenu($html);
		}
		JToolBarHelper::title($html, $picture.'.png');
		if(HIKASHOP_J25) {
			$doc = JFactory::getDocument();
			$app = JFactory::getApplication();
			$doc->setTitle($app->getCfg('sitename'). ' - ' .JText::_('JADMINISTRATION').' - '.$name);
		}
	}

	public static function footer() {
		$app = JFactory::getApplication();
		$config = hikaserial::config();
		$shopConfig = hikaserial::config(false);

		$description = $config->get('description_'.strtolower($config->get('level')),'Joomla!<sup>&reg;</sup> Ecommerce System');
		$link = HIKASERIAL_URL;
		$aff = $shopConfig->get('partner_id');
		if(!empty($aff)){
			$link.='?partner_id='.$aff;
		}
		$text = '<!-- HikaSerial Component powered by '.$link.' -->'."\r\n".'<!-- version '.$config->get('level').' : '.$config->get('version').' -->';
		if(!$shopConfig->get('show_footer',true))
			return $text;

		$text .= '<div class="hikaserial_footer" style="text-align:center" align="center"><a href="'.$link.'" target="_blank" title="'.HIKASERIAL_NAME.' : '.strip_tags($description).'">'.HIKASERIAL_NAME;
		if($app->isAdmin()) {
			$text .= ' '.$config->get('level').' '.$config->get('version');
		}
		$text .= ', '.$description.'</a></div>'."\r\n";
		return $text;
	}

	public static function tooltip($desc, $title = '', $image = 'tooltip.png', $name = '', $href = '', $link = 1) {
		return JHTML::_('tooltip',
			str_replace(array("'",'"','::'),array("&#039;","&quot;",':'),$desc),
			str_replace(array("'",'"','::'),array("&#039;","&quot;",':'),$title),
			$image,
			str_replace(array("'",'"','::'),array("&#039;","&quot;",':'),$name),
			$href,
			$link
		);
	}

	public static function cancelBtn($url = '') {
		$cancel_url = JRequest::getVar('cancel_redirect');
		if(!empty($cancel_url) || !empty($url)) {
			$toolbar = JToolBar::getInstance('toolbar');
			if(!empty($cancel_url))
				$toolbar->appendButton('Link', 'cancel', JText::_('HIKA_CANCEL'), base64_decode($cancel_url) );
			else
				$toolbar->appendButton('Link', 'cancel', JText::_('HIKA_CANCEL'), $url );
		} else {
			JToolBarHelper::cancel();
		}
	}

	public static function getFormToken() {
		if(version_compare(JVERSION,'3.0','>=')){
			return JSession::getFormToken();
		}
		return JUtility::getToken();
	}

	public static function loadJslib($name) {
		$ret = hikashop_loadJslib($name);
		if($ret)
			return true;

		static $serialLibs = array();
		$doc = JFactory::getDocument();
		$name = strtolower($name);
		if(isset($serialLibs[$name]))
			return $serialLibs[$name];

		$ret = true;
		switch($name) {
			default:
				$ret = false;
				break;
		}

		$serialLibs[$name] = $ret;
		return $ret;
	}

	public static function initModule() {
		static $done = false;
		if(!$done) {
			$fe = JRequest::getVar('hikaserial_front_end_main', 0);
			if(empty($fe)) {
				$done = true;
				$lang = JFactory::getLanguage();
				if(HIKASHOP_J25 && !method_exists($lang, 'publicLoadLanguage'))
					$lang = new hikaLanguage($lang);
				$override_path = JLanguage::getLanguagePath(JPATH_ROOT).DS.'overrides'.DS.$lang->getTag().'.override.ini';
				$lang->load(HIKASHOP_COMPONENT,JPATH_SITE);
				if(!HIKASHOP_J16 && file_exists($override_path))
					$lang->_load($override_path, 'override');
				elseif(HIKASHOP_J25)
					$lang->publicLoadLanguage($override_path, 'override');
			}
		}
		return true;
	}

	public static function initShop() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKASHOP_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKASHOP_COMPONENT');
			}
		}
		return $init;
	}

	public static function initMarket() {
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

	public static function initPoints() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKAPOINTS_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikapoints'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKAPOINTS_COMPONENT');
			}
		}
		return $init;
	}

	public static function loadUser($full = false, $reset = false) {
		return hikashop_loadUser($full, $reset);
	}

	public static function isAllowed($allowedGroups, $id = null, $type = 'user') {
		return hikashop_isAllowed($allowedGroups, $id, $type);
	}

	public static function display($messages, $type = 'success', $return = false) {
		return hikashop_display($messages, $type, $return);
	}

	public static function createDir($dir, $report = true) {
		return hikashop_createDir($dir, $report);
	}

	public static function search($searchString, $object, $exclude = '') {
		return hikashop_search($searchString, $object, $exclude);
	}

	public static function getDate($time = 0, $format = '%d %B %Y %H:%M') {
		return hikashop_getDate($time, $format);
	}

	public static function currentUrl($checkInRequest = '') {
		return hikashop_currentUrl($checkInRequest);
	}

	public static function increasePerf() {
		return hikashop_increasePerf();
	}

	public static function getIP() {
		return hikashop_getIP();
	}

	public static function toFloat($value) {
		return hikashop_toFloat($value);
	}

	public static function absoluteUrl($text) {
		return hikashop_absoluteUrl($text);
	}

	public static function disallowUrlRedirect($url) {
		return hikashop_disallowUrlRedirect($url);
	}

	public static function unserialize($data) {
		if(!is_string($data))
			return false;
		if(!preg_match_all('#[OC]:[0-9]+:"([-_a-zA-Z0-9]+)":[0-9]+:\{#iU', $data, $matches))
			return unserialize($data);
		if(!empty($matches[1])) {
			foreach($matches[1] as $m) {
				if($m != 'stdClass')
					return false;
			}
		}
		return unserialize($data);
	}
}

if(!HIKASHOP_J30){
	class hikaserialBridgeController extends JController {}
} else {
	class hikaserialBridgeController extends JControllerLegacy {}
}

class hikaserialController extends hikaserialBridgeController {

	protected $type = '';
	protected $publish_return_view = 'listing';
	protected $rights = array(
		'display' => array(),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);
	protected $default_task = 'listing';

	public function __construct($config = array(), $skip = false) {
		if(!$skip) {
			parent::__construct($config);
			$this->registerDefaultTask($this->default_task);
		}
	}

	public function listing() {
		JRequest::setVar('layout', 'listing');
		return $this->display();
	}

	public function show() {
		JRequest::setVar('layout', 'show');
		return $this->display();
	}

	public function edit() {
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout','form');
		return $this->display();
	}

	public function add() {
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout', 'form');
		return $this->display();
	}

	public function apply() {
		$status = $this->store();
		return $this->edit();
	}

	public function save() {
		$this->store();
		return $this->listing();
	}

	public function store() {
		return false;
	}

	protected function adminStore($token = false) {
		$app = JFactory::getApplication();
		if($token) {
			JRequest::checkToken() || die('Invalid Token');
		}
		if(empty($this->type))
			return false;
		$class = hikaserial::get('class.'.$this->type);
		if( $class === null )
			return false;
		$status = $class->saveForm();
		if($status) {
			$app->enqueueMessage(JText::_('HIKASERIAL_SUCC_SAVED'), 'message');
			JRequest::setVar('cid', $status);
			JRequest::setVar('fail', null);
		} else {
			$app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
			if(!empty($class->errors)) {
				foreach($class->errors as $err) {
					$app->enqueueMessage($err, 'error');
				}
			}
		}
		return $status;
	}

	public function publish() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		return $this->toggle($cid,1);
	}

	public function unpublish() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		return $this->toggle($cid,0);
	}

	public function display($tpl = null, $params = null) {
		if(HIKASHOP_J30) {
			$document = JFactory::getDocument();
			$view = $this->getView('', $document->getType(), '');
			if($view->getLayout() == 'default' && JRequest::getString('layout', '') != '')
				$view->setLayout(JRequest::getString('layout'));
		}

		$shopConfig = hikaserial::config(false);
		$menu_style = $shopConfig->get('menu_style', 'title_bottom');
		if(HIKASHOP_J30) $menu_type = 'content_top';
		if($menu_style == 'content_top') {
			$app = JFactory::getApplication();
			if($app->isAdmin() && JRequest::getString('tmpl') !== 'component') {
				echo hikaserial::getMenu();
			}
		}
		return parent::display($tpl, $params);
	}

	protected function toggle($cid, $publish) {
		if(empty($cid)) {
			JError::raiseWarning(500, 'No items selected');
		}
		$cids = implode(',', $cid);
		$db = JFactory::getDBO();
		$query = 'UPDATE '.hikaserial::table($this->type).' SET '.key($this->toggle).' = '.(int)$publish.' WHERE '.reset($this->toggle).' IN ( '.$cids.' )';
		$db->setQuery($query);
		if(!$db->query()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		$task = $this->publish_return_view;
		return $this->$task();
	}

	public function getModel($name = '', $prefix = '', $config = array()) {
		return false;
	}

	public function execute($task) {
		if(HIKASHOP_J30 && !$this->authorize($task)) {
			return JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}
		return parent::execute($task);
	}

	public function authorize($task) {
		if(empty($task) && !empty($this->default_task))
			return $task = $this->default_task;

		if($this->isIn($task, array('modify','delete'))) {
			if(JRequest::checkToken('request')) {
				return true;
			}
			return false;
		}
		if($this->isIn($task))
			return true;
		return false;
	}

	public function authorise($task) {
		return $this->authorize($task);
	}

	private function isIn($task, $lists = array('*')) {
		if(!is_array($lists)) {
			$lists = array($lists);
		}
		foreach($lists as $list) {
			if($list == '*') {
				foreach($this->rights as $rights) {
					if(!empty($rights) && in_array($task, $rights)) {
						return true;
					}
				}
			} else {
				if(!empty($this->rights[$list]) && in_array($task, $this->rights[$list])) {
					return true;
				}
			}
		}
		return false;
	}
}

if(version_compare(JVERSION,'3.0','<')){
	class hikaserialBridgeView extends JView {}
} else {
	class hikaserialBridgeView extends JViewLegacy {}
}

class hikaserialView extends hikaserialBridgeView {
	protected $triggerView = false;
	protected $toolbar = array();

	public function display($tpl = null) {
		if($this->triggerView) {
			JPluginHelper::importPlugin('hikashop');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onHikaserialBeforeDisplayView', array(&$this));
		}

		if(!empty($this->toolbar)) {
			$toolbarHelper = hikaserial::get('helper.toolbar');
			$toolbarHelper->process($this->toolbar);
		}

		parent::display($tpl);

		if($this->triggerView) {
			$dispatcher->trigger('onHikaserialAfterDisplayView', array( &$this));
		}
	}

	protected function &getPageInfo($default = '', $dir = 'asc', $filters = array()) {
		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->search = $app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string');

		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.'.filter_order', 'filter_order', $default, 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.'.filter_order_Dir', 'filter_order_Dir',	$dir, 'word');

		$pageInfo->limit = new stdClass();
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		if((JRequest::getVar('search', null) != null && JRequest::getVar('search', null) != $app->getUserState($this->paramBase.'.search')) || JRequest::getVar('limit', $pageInfo->limit->value) != $app->getUserState($this->paramBase.'.list_limit')) {
			$app->setUserState($this->paramBase.'.limitstart',0);
			$pageInfo->limit->start = 0;
		} else {
			$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		}

		if(!empty($filters)) {
			$reset = false;
			foreach($filters as $k => $v) {
				$type = 'string';
				if(is_int($v)) $type = 'int';

				if(!$reset) $oldValue = $app->getUserState($this->paramBase.'.filter_'.$k, $v);
				$newValue = $app->getUserStateFromRequest($this->paramBase.'.filter_'.$k, 'filter_'.$k, $v, $type);
				$reset = $reset || ($oldValue != $newValue);
				$pageInfo->filter->$k = $newValue;
			}
			if($reset) {
				$app->setUserState($this->paramBase.'.limitstart',0);
				$pageInfo->limit->start = 0;
			}
		}

		$pageInfo->search = JString::strtolower($app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string'));
		$pageInfo->search = trim($pageInfo->search);

		$this->assignRef('pageInfo', $pageInfo);
		return $pageInfo;
	}

	protected function getPageInfoTotal($query, $countValue = '*') {
		if(empty($this->pageInfo))
			return false;

		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		$db->setQuery('SELECT COUNT('.$countValue.') '.$query);
		if(empty($this->pageInfo->elements))
			$this->pageInfo->elements = new stdClass();
		$this->pageInfo->elements->total = (int)$db->loadResult();
		if((int)$this->pageInfo->limit->start >= $this->pageInfo->elements->total) {
			$this->pageInfo->limit->start = 0;
			$app->setUserState($this->paramBase.'.limitstart', 0);
		}
	}

	protected function processFilters(&$filters, &$order, $searchMap = array(), $orderingAccept = array()) {
		if(!empty($this->pageInfo->search)) {
			$db = JFactory::getDBO();
			if(!HIKASHOP_J30) {
				$searchVal = '\'%' . $db->getEscaped(JString::strtolower($this->pageInfo->search), true) . '%\'';
			} else {
				$searchVal = '\'%' . $db->escape(JString::strtolower($this->pageInfo->search), true) . '%\'';
			}
			if(is_array($filters))
				$filters[] = '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')';
		}
		if(!empty($filters)) {
			if(is_array($filters))
				$filters = ' WHERE ('. implode(') AND (', $filters) . ')';
		} else {
			$filters = '';
		}

		if(!empty($this->pageInfo->filter->order->value)) {
			$t = '';
			if(strpos($this->pageInfo->filter->order->value, '.') !== false)
				list($t,$v) = explode('.', $this->pageInfo->filter->order->value, 2);

			$dir = strtolower($this->pageInfo->filter->order->dir);
			if(!in_array($dir, array('asc', 'desc')))
				$this->pageInfo->filter->order->dir = 'ASC';

			if(empty($orderingAccept) || in_array($t.'.', $orderingAccept) || in_array($this->pageInfo->filter->order->value, $orderingAccept))
				$order = ' ORDER BY '.$this->pageInfo->filter->order->value.' '.strtoupper($this->pageInfo->filter->order->dir);
		}
	}

	protected function getPagination($max = 500, $limit = 100) {
		if(empty($this->pageInfo))
			return false;

		if($this->pageInfo->limit->value == $max)
			$this->pageInfo->limit->value = $limit;

			$pagination = hikashop_get('helper.pagination', $this->pageInfo->elements->total, $this->pageInfo->limit->start, $this->pageInfo->limit->value);
		$this->assignRef('pagination', $pagination);
		return $pagination;
	}

	protected function getOrdering($value = '', $doOrdering = true) {
		$this->assignRef('doOrdering', $doOrdering);

		$ordering = new stdClass();
		$ordering->ordering = false;

		if($doOrdering) {
			$ordering->ordering = false;
			$ordering->orderUp = 'orderup';
			$ordering->orderDown = 'orderdown';
			$ordering->reverse = false;
			if(!empty($this->pageInfo) && $this->pageInfo->filter->order->value == $value) {
				$ordering->ordering = true;
				if($this->pageInfo->filter->order->dir == 'desc') {
					$ordering->orderUp = 'orderdown';
					$ordering->orderDown = 'orderup';
					$ordering->reverse = true;
				}
			}
		}
		$this->assignRef('ordering', $ordering);
		return $ordering;
	}

	protected function loadRef($refs) {
		foreach($refs as $key => $name) {
			$obj = hikaserial::get($name);
			if(!empty($obj))
				$this->assignRef($key, $obj);
			unset($obj);
		}
	}
}

class hikaserialClass extends JObject {
	protected $db;
	protected $tables = array();
	protected $pkeys = array();
	protected $namekeys = array();
	protected $toggle = array();
	protected $deleteToggle = array();

	public function  __construct($config = array()){
		$this->db = JFactory::getDBO();
		return parent::__construct($config);
	}

	public function get($element, $default = null) {
		if(empty($element))
			return null;
		if(empty($this->tables))
			return null;
		$pkey = end($this->pkeys);
		$namekey = end($this->namekeys);
		$table = hikaserial::table(end($this->tables));
		if(!is_numeric($element) && !empty($namekey)) {
			$pkey = $namekey;
		}
		$query = 'SELECT * FROM '.$table.' WHERE '.$pkey.' = '.$this->db->Quote($element).' LIMIT 1';
		$this->db->setQuery($query);
		$ret = $this->db->loadObject();
		return $ret;
	}

	public function save(&$element) {
		if(empty($this->tables))
			return false;
		$pkey = end($this->pkeys);
		if(empty($pkey)) {
			$pkey = end($this->namekey);
		} elseif(empty($element->$pkey)) {
			$t = end($this->namekeys);
			if(!empty($t)) {
				if(!empty($element->$t)) {
					$pkey = $t;
				} else {
					$element->$t = $this->getNamekey($element);
					if($element->$t === false)
						return false;
				}
			}
		}
		$table = hikaserial::table(end($this->tables));
		if(!HIKASHOP_J16) {
			$obj = new JTable($table, $pkey, $this->db);
			$obj->setProperties($element);
		} else {
			$obj =& $element;
		}
		if(empty($element->$pkey)) {
			$this->db->setQuery($this->getInsert($table, $obj));
			$status = $this->db->query();
		} else {
			if( count( (array)$element ) > 1 ) {
				$status = $this->db->updateObject($table, $obj, $pkey);
			} else {
				$status = true;
			}
		}
		if($status)
			return empty($element->$pkey) ? $this->db->insertid() : $element->$pkey;
		return false;
	}

	private function getInsert($table, &$obj, $keyName = null) {
		if(!HIKASHOP_J25) {
			$sql = 'INSERT IGNORE INTO '.$this->db->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		} else {
			$sql = 'INSERT IGNORE INTO '.$this->db->quoteName($table).' ( %s ) VALUES ( %s ) ';
		}
		$fields = array();
		foreach (get_object_vars($obj) as $k => $v) {
			if(is_array($v) || is_object($v) || $v === null || $k[0] == '_' ) {
				continue;
			}
			if(!HIKASHOP_J25) {
				$fields[] = $this->db->nameQuote($k);
				$values[] = $this->db->isQuoted($k) ? $this->db->Quote($v) : (int)$v;
			} else {
				$fields[] = $this->db->quoteName($k);
				$values[] = $this->db->Quote($v);
			}
		}
		return sprintf($sql, implode(',', $fields), implode(',', $values));
	}

	public function delete($elements) {
		if(empty($this->tables))
			return false;
		if(empty($elements))
			return false;
		if(!is_array($elements))
			$elements = array($elements);

		$isNumeric = is_numeric(reset($elements));
		foreach($elements as $key => $val) {
			$elements[$key] = $this->db->Quote($val);
		}

		$columns = $isNumeric ? $this->pkeys : $this->namekeys;

		if(empty($columns) || empty($elements))
			return false;

		$otherElements = array();
		$otherColumn = '';
		foreach($columns as $i => $column) {
			if(empty($column)) {
				$query = 'SELECT '.($isNumeric?end($this->pkeys):end($this->namekeys)).' FROM '.$this->getTable().' WHERE '.($isNumeric?end($this->pkeys):end($this->namekeys)).' IN ( '.implode(',',$elements).');';
				$this->db->setQuery($query);
				if(!HIKASHOP_J25) {
					$otherElements = $this->db->loadResultArray();
				} else {
					$otherElements = $this->db->loadColumn();
				}
				foreach($otherElements as $key => $val){
					$otherElements[$key] = $this->db->Quote($val);
				}
				break;
			}
		}

		$result = true;
		$tables = array();
		if(empty($this->tables)) {
			$tables[0] = $this->getTable();
		} else {
			foreach($this->tables as $i => $oneTable){
				$tables[$i] = hikaserial::table($oneTable);
			}
		}
		foreach($tables as $i => $oneTable) {
			$column = $columns[$i];
			if(empty($column)) {
				$whereIn = ' WHERE '.($isNumeric?$this->namekeys[$i]:$this->pkeys[$i]).' IN ('.implode(',',$otherElements).')';
			} else {
				$whereIn = ' WHERE '.$column.' IN ('.implode(',',$elements).')';
			}
			$query = 'DELETE FROM '.$oneTable.$whereIn;
			$this->db->setQuery($query);
			$result = $this->db->query() && $result;
		}
		return $result;
	}

	public function toggleId($task) {
		if( !empty($this->toggle[$task]))
			return $this->toggle[$task];
		return false;
	}

	public function toggleDelete() {
		if( !empty($this->deleteToggle))
			return $this->deleteToggle;
		return false;
	}
}

class hikaserialPlugin extends JPlugin {
	protected $db;
	protected $multiple = false;
	protected $type = 'generator';
	protected $populate = false;
	protected $plugin_params = null;
	protected $doc_form = '';
	protected $doc_listing = '';

	public function __construct(&$subject, $config) {
		$this->db = JFactory::getDBO();
		parent::__construct($subject, $config);
	}

	public function pluginParams($id = 0, $name = '') {
		$this->plugin_params = null;
		if($id > 0) {
			$this->db->setQuery('SELECT '.$this->type.'_params FROM '.hikaserial::table($this->type).' WHERE '.$this->type.'_id = ' . (int)$id);
			$this->db->query();
			$data = $this->db->loadResult();
			if(!empty($data)) {
				$this->plugin_params = hikaserial::unserialize($data);
				return true;
			}
		} else if(!empty($name)) {
			$this->db->setQuery('SELECT '.$this->type.'_params FROM '.hikaserial::table($this->type).' WHERE '.$this->type.'_type = ' . $this->db->Quote($name));
			$this->db->query();
			$data = $this->db->loadResult();
			if(!empty($data)) {
				$this->plugin_params = hikaserial::unserialize($data);
				return true;
			}
		}
		return false;
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		return;
	}

	public function type() {
		return $this->type;
	}

	public function canPopulate() {
		return ($this->type == 'generator' && $this->populate == true);
	}

	public function getGenerateLimit() {
		if($this->type != 'generator')
			return false;
		return -1;
	}

	public function populateForm(&$pack) {
		return '';
	}

	public function onDisplaySerials(&$data, $viewName) {
		return;
	}

	public function isMultiple() {
		return $this->multiple;
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	public function listPlugins($name, &$values, $full = true, $acl = null){
		if(!in_array($this->type, array('generator','consumer','plugin')))
			return;

		if(!$this->multiple) {
			$values['plg.'.$name] = ($this->name == $name && !empty($this->fullname)) ? $this->fullname : $name;
			return;
		}

		$filters = array(
			'type' => $this->type.'_type = ' . $this->db->Quote($name),
			'publish' => $this->type.'_published = 1 '
		);
		if($acl !== null && is_array($acl)) {
			$filters['acl'] = '('.$this->type.'_access = \'all\')';

			if(!empty($acl)) {
				foreach($acl as &$a) {
					$a = '%,'.(int)$a.',%';
				}
				unset($a);
				$filters['acl'] .= ' OR ('.$this->type.'_access LIKE \'' . implode('\') OR ('.$this->type.'_access LIKE \'', $acl). '\')';
			}
		}

		$query = 'SELECT '.$this->type.'_id as id, '.$this->type.'_name as name '.
				' FROM '.hikaserial::table($this->type).
				' WHERE ('.implode(') AND (', $filters).') '.
				' ORDER BY '.$this->type.'_ordering';
		$this->db->setQuery($query);
		$plugins = $this->db->loadObjectList();
		if($full) {
			foreach($plugins as $plugin) {
				$values['plg.'.$name.'-'.$plugin->id] = $name.' - '.$plugin->name;
			}
		} else {
			foreach($plugins as $plugin) {
				$values[] = $plugin->id;
			}
		}
	}

	public function onPluginConfiguration(&$elements) {
		$this->plugins =& $elements;
		$this->pluginName = JRequest::getCmd('name', $this->type);
		$this->pluginView = '';

		$plugin_id = JRequest::getInt('plugin_id',0);
		if($plugin_id == 0) {
			$plugin_id = JRequest::getInt($this->type.'_id', 0);
		}

		$toolbar = JToolBar::getInstance('toolbar');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		if($plugin_id == 0) {
			$toolbar->appendButton('Link', 'cancel', JText::_('HIKA_CANCEL'), hikaserial::completeLink('plugins') );
		} else {
			$toolbar->appendButton('Link', 'cancel', JText::_('HIKA_CANCEL'), hikaserial::completeLink('plugins&plugin_type='.$this->type.'&task=edit&name='.$this->pluginName) );
		}
		JToolBarHelper::divider();
		$toolbar->appendButton('Pophelp','plugins-'.$this->doc_form.'form');

		if(empty($this->title)) {
			$this->title = JText::_('HIKASERIAL_PLUGIN_METHOD');
		}

		if(empty($this->ctrl_url)) {
			if($plugin_id == 0) {
				$this->ctrl_url = 'plugins&plugin_type='.$this->type.'&task=edit&name='.$this->pluginName.'&subtask=edit';
			} else {
				$this->ctrl_url ='plugins&plugin_type='.$this->type.'&task=edit&name='.$this->pluginName.'&subtask=edit&plugin_id='.$plugin_id;
			}
		}

		hikaserial::setTitle($this->title, 'plugin', $this->ctrl_url);
	}

	public function onPluginMultipleConfiguration(&$elements) {
		if(!$this->multiple)
			return;

		$app = JFactory::getApplication();
		$this->plugins =& $elements;
		$this->pluginName = JRequest::getCmd('name', $this->type);
		$this->pluginView = 'sublisting';
		$this->subtask = JRequest::getCmd('subtask','');
		$this->task = JRequest::getVar('task');

		if(empty($this->title)) { $this->title = JText::_('HIKASERIAL_PLUGIN_METHOD'); }

		$listing_header = $this->configurationHead();
		if(!empty($listing_header)) {
			$this->listing_header = $listing_header;
		}

		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		$bar->appendButton('Standard', 'copy', JText::_('HIKA_COPY'), 'copy', true, false);
		$bar->appendButton('Link', 'new', JText::_('HIKA_NEW'), hikaserial::completeLink('plugins&plugin_type='.$this->type.'&task=edit&name='.$this->pluginName.'&subtask=edit'));
		JToolBarHelper::cancel();
		JToolBarHelper::divider();
		$bar->appendButton('Pophelp', 'plugins-'.$this->doc_listing.'sublisting');

		if(empty($this->ctrl_url))
			$this->ctrl_url = 'plugins&plugin_type='.$this->type.'&task=edit&name='.$this->pluginName;

		hikaserial::setTitle($this->title, 'plugin', $this->ctrl_url);
		$this->toggleClass = hikaserial::get('helper.toggle');
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination(count($this->plugins), 0, false);
		$this->order = new stdClass();
		$this->order->ordering = true;
		$this->order->orderUp = 'orderup';
		$this->order->orderDown = 'orderdown';
		$this->order->reverse = false;
		$app->setUserState(HIKASERIAL_COMPONENT.'.generator_plugin_type', $this->pluginName);
	}


	public function onPackGeneratorTypeDisplay(&$values) {
		if(empty($this->type) || $this->type != 'generator' || empty($this->name))
			return;

		$app = JFactory::getApplication();
		if($app->isAdmin() || !hikaserial::initMarket())
			return $this->listPlugins($this->name, $values);

		$vendor = hikamarket::loadVendor(true);
		if((int)$vendor->vendor_id === 1)
			return $this->listPlugins($this->name, $values, true);

		$acl = array();
		if(strpos($vendor->vendor_access, '@') === false)
			return $this->listPlugins($this->name, $values, true, $acl);

		$accesses = explode(',', $vendor->vendor_access);
		foreach($accesses as $ax) {
			if(substr($ax,0,1) != '@')
				continue;
			$ax_id = (int)substr($ax,1);
			if($ax_id > 0)
				$acl[] = $ax_id;
		}

		return $this->listPlugins($this->name, $values, true, $acl);
	}
}

if($app->isAdmin()) {
	$doc = JFactory::getDocument();
	$doc->addScript(HIKASERIAL_JS.'hikaserial.js');
	$serialConfig = hikaserial::config();
	$css = $serialConfig->get('css_'.$css_type,'default');
	if(!empty($css)){
		$doc->addStyleSheet(HIKASERIAL_CSS.$css_type.'_'.$css.'.css');
	}
	if(HIKASHOP_J30 && $_REQUEST['option'] == HIKASERIAL_COMPONENT) {
		JHtml::_('formbehavior.chosen', 'select');
		$doc->addScriptDeclaration("\r\n".'window.Oby.ready(function(){setTimeout(function(){window.hikaserial.noChzn();},100);});');
	}
} else {
	$doc = JFactory::getDocument();
	$doc->addScript(HIKASERIAL_JS.'hikaserial.js');
	if(HIKASHOP_RESPONSIVE)
		$doc->addScriptDeclaration("\r\n".'window.Oby.ready(function(){setTimeout(function(){window.hikaserial.noChzn();},100);});');
}
