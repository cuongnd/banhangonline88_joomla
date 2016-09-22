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
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

$hikashopHelperFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
if(!file_exists($hikashopHelperFile)) {
	JError::raiseWarning(500, 'HikaShop not installed ( www.hikashop.com )');
	exit;
}
include_once($hikashopHelperFile);

$jversion = preg_replace('#[^0-9\.]#i','',JVERSION);
define('HIKAAUCTION_J16',version_compare($jversion,'1.6.0','>=') ? true : false);
define('HIKAAUCTION_J17',version_compare($jversion,'1.7.0','>=') ? true : false);
define('HIKAAUCTION_J25',version_compare($jversion,'2.5.0','>=') ? true : false);
define('HIKAAUCTION_J30',version_compare($jversion,'3.0.0','>=') ? true : false);

define('HIKAAUCTION_PHP5',version_compare(PHP_VERSION,'5.0.0', '>=') ? true : false);

define('HIKAAUCTION_RESPONSIVE',HIKASHOP_RESPONSIVE);
define('HIKAAUCTION_BACK_RESPONSIVE',HIKASHOP_BACK_RESPONSIVE);

if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
define('HIKAAUCTION_COMPONENT','com_hikaauction');
define('HIKAAUCTION_LIVE',rtrim(JURI::root(),'/').'/');
define('HIKAAUCTION_ROOT',rtrim(JPATH_ROOT,DS).DS);
define('HIKAAUCTION_FRONT',rtrim(JPATH_SITE,DS).DS.'components'.DS.HIKAAUCTION_COMPONENT.DS);
define('HIKAAUCTION_BACK',rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.HIKAAUCTION_COMPONENT.DS);
define('HIKAAUCTION_HELPER',HIKAAUCTION_BACK.'helpers'.DS);
define('HIKAAUCTION_BUTTON',HIKAAUCTION_BACK.'buttons');
define('HIKAAUCTION_CLASS',HIKAAUCTION_BACK.'classes'.DS);
define('HIKAAUCTION_INC',HIKAAUCTION_BACK.'inc'.DS);
define('HIKAAUCTION_VIEW',HIKAAUCTION_BACK.'views'.DS);
define('HIKAAUCTION_TYPE',HIKAAUCTION_BACK.'types'.DS);
define('HIKAAUCTION_MEDIA',HIKAAUCTION_ROOT.'media'.DS.HIKAAUCTION_COMPONENT.DS);
define('HIKAAUCTION_DBPREFIX','#__hikaauction_');

define('HIKAAUCTION_NAME','HikaAuction');
define('HIKAAUCTION_LNAME','hikaauction');
define('HIKAAUCTION_TEMPLATE',HIKAAUCTION_FRONT.'templates'.DS);
define('HIKAAUCTION_URL','https://www.hikashop.com/');
define('HIKAAUCTION_UPDATEURL',HIKAAUCTION_URL.'index.php?option=com_updateme&ctrl=update&task=');
define('HIKAAUCTION_HELPURL',HIKAAUCTION_URL.'index.php?option=com_updateme&ctrl=doc&component='.HIKAAUCTION_LNAME.'&page=');
define('HIKAAUCTION_REDIRECT',HIKAAUCTION_URL.'index.php?option=com_updateme&ctrl=redirect&page=');

$app = JFactory::getApplication();
if($app->isAdmin()) {
	define('HIKAAUCTION_CONTROLLER',HIKAAUCTION_BACK.'controllers'.DS);
	define('HIKAAUCTION_IMAGES','../media/'.HIKAAUCTION_COMPONENT.'/images/');
	define('HIKAAUCTION_CSS','../media/'.HIKAAUCTION_COMPONENT.'/css/');
	define('HIKAAUCTION_JS','../media/'.HIKAAUCTION_COMPONENT.'/js/');
	$css_type = 'backend';
} else {
	define('HIKAAUCTION_CONTROLLER',HIKAAUCTION_FRONT.'controllers'.DS);
	define('HIKAAUCTION_IMAGES',JURI::base(true).'/media/'.HIKAAUCTION_COMPONENT.'/images/');
	define('HIKAAUCTION_CSS',JURI::base(true).'/media/'.HIKAAUCTION_COMPONENT.'/css/');
	define('HIKAAUCTION_JS',JURI::base(true).'/media/'.HIKAAUCTION_COMPONENT.'/js/');
	$css_type = 'frontend';
}
$lang = JFactory::getLanguage();
$lang->load(HIKAAUCTION_COMPONENT,JPATH_SITE);

$compatPath = HIKAAUCTION_BACK . 'compat' . DS . 'compat';
if(file_exists($compatPath.substr(str_replace('.','',$jversion),0,2).'.php'))
	require($compatPath.substr(str_replace('.','',$jversion),0,2).'.php');
elseif(file_exists($compatPath.substr(str_replace('.','',$jversion),0,1).'.php'))
	require($compatPath.substr(str_replace('.','',$jversion),0,1).'.php');
else {
	JError::raiseError(500, 'Could not load the compatibily file for Joomla! '.JVERSION);
	exit;
}


class hikaauction {
	private static $configClass = null;
	private static $shopConfigClass = null;

	public static function get($name) {
		$namespace = 'hikaauction';
		if(substr($name,0,5) == 'shop.') {
			$namespace = 'hikashop';
			$name = substr($name,5);
		}
		list($group,$class) = explode('.',$name,2);
		if($group=='controller') {
			if($namespace == 'hikaauction')
				$className = $class . 'Auction' . ucfirst($group);
			else
				$className = $class . ucfirst($group);
		}else{
			$className = $namespace.ucfirst($class).ucfirst($group);
		}
		if(class_exists($className.'Override'))
			$className .= 'Override';
		if(!class_exists($className)) {
			$const = constant(strtoupper($namespace . '_' . $group));
			$app = JFactory::getApplication();
			$path = JPATH_THEMES.DS.$app->getTemplate().DS.'html'.DS.'com_'.$namespace.DS.'administrator'.DS;
			$constOverride = str_replace(HIKAAUCTION_BACK, $path, $const);

			jimport('joomla.filesystem.file');
			if(JFile::exists($constOverride . $class . '.override.php')) {
				include_once($constOverride . $class . '.override.php');
				$className .= 'Override';
			} elseif(JFile::exists($const . $class . '.php')) {
				include_once $const . $class . '.php';
			}
			if(!class_exists($className)) {
				return null;
			}
		}

		$args = func_get_args();
		array_shift($args);
		switch(count($args)){
			case 5: return new $className($args[0],$args[1],$args[2],$args[3],$args[4],$args[5]);
			case 5: return new $className($args[0],$args[1],$args[2],$args[3],$args[4]);
			case 4: return new $className($args[0],$args[1],$args[2],$args[3]);
			case 3: return new $className($args[0],$args[1],$args[2]);
			case 2: return new $className($args[0],$args[1]);
			case 1: return new $className($args[0]);
			case 0:
			default:
				return new $className();
		}
	}

	public static function &config($myself = true, $reload = false) {
		if(!$myself) {
			if(self::$shopConfigClass === null || $reload){
				self::$shopConfigClass = self::get('shop.class.config');
				if( self::$shopConfigClass === null ) die(HIKASHOP_NAME.' config not found');
				self::$shopConfigClass->load();
			}
			return self::$shopConfigClass;
		}
		if(self::$configClass === null || $reload) {
			self::$configClass = self::get('class.config');
			if( self::$configClass === null ) die(HIKAAUCTION_NAME.' config not found');
			self::$configClass->load();
		}
		return self::$configClass;
	}

	public static function level($level) {
		$config = self::config();
		return ($config->get($config->get('level'), 0) >= $level);
	}

	public static function completeLink($link, $popup = false, $redirect = false, $js = false) {
		$namespace = HIKAAUCTION_COMPONENT;
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
		if( $component === true || $component === 'hikaauction' ) {
			if(strpos($name,'.') !== false) {
				if(substr($name, 0, 5) == 'shop.') return '#__hikashop_' . substr($name, 5);
				if(substr($name, 0, 5) == 'auction.') return '#__hikaauction_' . substr($name, 5);
				if(substr($name, 0, 7) == 'serial.') return '#__hikaserial_' . substr($name, 7);
				if(substr($name, 0, 7) == 'market.') return '#__hikamarket_' . substr($name, 7);
				if(substr($name, 0, 7) == 'points.') return '#__hikapoints_' . substr($name, 7);
				if(substr($name, 0, 7) == 'joomla.') return '#__'.substr($name, 7);
				if(substr($name, 0, 8) == 'booking.') return '#__hikabooking_' . substr($name, 8);
			}
			return HIKAAUCTION_DBPREFIX . $name;
		}
		if($component === 'shop') return '#__hikashop_' . $name;
		if($component === 'auction') return '#__hikaauction_' . $name;
		if($component === 'serial') return '#__hikaserial_' . $name;
		if($component === 'market') return '#__hikamarket_' . $name;
		if($component === 'points') return '#__hikapoints_' . $name;
		if($component === 'booking') return '#__hikabooking_' . $name;
		return '#__'.$name;
	}

	public static function getLayout($controller, $layout, $params, &$js) {
		$app = JFactory::getApplication();
		$component = HIKAAUCTION_COMPONENT;
		$base_path = HIKAAUCTION_FRONT;
		if($app->isAdmin()) {
			$base_path = HIKAAUCTION_BACK;
		}
		if(substr($controller, 0, 5) == 'shop.') {
			$controller = substr($controller, 5);
			$component = HIKASHOP_COMPONENT;
			$base_path = HIKASHOP_FRONT;
			if($app->isAdmin())
				$base_path = HIKASHOP_BACK;
		}
		$base_path = rtrim($base_path, DS);
		$document = JFactory::getDocument();

		$ctrl = new hikaauctionBridgeController(array(
			'name' => $controller,
			'base_path' => $base_path
		));
		$viewType = $document->getType();

		$view = $ctrl->getView('', $viewType, '', array('base_path' => $base_path));
		$folder	= JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.$component.DS.$view->getName();
		$view->addTemplatePath($folder);
		$view->setLayout($layout);
		ob_start();
		$view->display(null, $params);
		$js = @$view->js;
		return ob_get_clean();
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
		$shopConfig = self::config(false);
		$menu_style = $shopConfig->get('menu_style', 'content_top');
		$html='<a href="'.self::completeLink($link).'">'.$name.'</a>';
		if($menu_style != 'content_top') {
			$html = self::getMenu($html);
		}
		JToolBarHelper::title($html, $picture.'.png');
		if(HIKAAUCTION_J25) {
			$doc = JFactory::getDocument();
			$app = JFactory::getApplication();
			$doc->setTitle($app->getCfg('sitename'). ' - ' .JText::_('JADMINISTRATION').' - '.$name);
		}
	}

	public static function footer() {
		$app = JFactory::getApplication();
		$config = self::config();
		$shopConfig = self::config(false);

		$description = $config->get('description_'.strtolower($config->get('level')),'Joomla!<sup style="font-size:6px">TM</sup> Auction System');
		$link = HIKAAUCTION_URL;
		$aff = $shopConfig->get('partner_id');
		if(!empty($aff)){
			$link.='?partner_id='.$aff;
		}
		$text = '<!-- HikaAuction Component powered by '.$link.' -->'."\r\n".'<!-- version '.$config->get('level').' : '.$config->get('version').' -->';
		if(!$shopConfig->get('show_footer',true))
			return $text;

		$text .= '<div class="hikaauction_footer" style="text-align:center" align="center"><a href="'.$link.'" target="_blank" title="'.HIKAAUCTION_NAME.' : '.strip_tags($description).'">'.HIKAAUCTION_NAME;
		if($app->isAdmin()) {
			$text .= ' '.$config->get('level').' '.$config->get('version');
		}
		$text .= ', '.$description.'</a></div>'."\r\n";
		return $text;
	}

	public static function cancelBtn($url = '') {
		$cancel_url = JRequest::getVar('cancel_redirect');
		if(!empty($cancel_url) || !empty($url)) {
			$toolbar = JToolBar::getInstance('toolbar');
			if(!empty($cancel_url))
				$toolbar->appendButton('Link', 'cancel', JText::_('CANCEL'), base64_decode($cancel_url) );
			else
				$toolbar->appendButton('Link', 'cancel', JText::_('CANCEL'), $url );
		} else {
			JToolBarHelper::cancel();
		}
	}

	public static function getFormToken() {
		if(HIKAAUCTION_J30) {
			return JSession::getFormToken();
		}
		return JUtility::getToken();
	}

	public static function cloning(&$object) {
		if(is_array($object)) {
			$ret = array();
			foreach($object as $k => $v) {
				$ret[$k] = self::cloning($v);
			}
			return $ret;
		}
		if(is_object($object)) {
			$ret = new stdClass();
			foreach(get_object_vars($object) as $k => $v) {
				$ret->$k = self::cloning($v);
			}
			return $ret;
		}
		return $object;
	}

	public static function headerNoCache() {
		if(!headers_sent()) {
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
			return true;
		}
		return false;
	}

	public static function loadJslib($name) {
		$ret = hikashop_loadJslib($name);
		if($ret)
			return true;

		static $myselfLibs = array();
		$doc = JFactory::getDocument();
		$name = strtolower($name);
		if(isset($myselfLibs[$name]))
			return $myselfLibs[$name];

		$ret = true;
		switch($name) {
			default:
				$ret = false;
				break;
		}

		$myselfLibs[$name] = $ret;
		return $ret;
	}

	public static function getCID($field = '', $int = true) {
		return hikashop_getCID($field, $int);
	}

	public static function loadUser($full = false, $reset = false) {
		return hikashop_loadUser($full, $reset);
	}

	public static function toFloat($val) {
		return hikashop_toFloat($val);
	}

	public static function isAllowed($allowedGroups, $id = null, $type = 'user') {
		return hikashop_isAllowed($allowedGroups, $id, $type);
	}

	public static function secureField($fieldName) {
		return hikashop_secureField($fieldName);
	}

	public static function import($type, $name, $dispatcher = null) {
		return hikashop_import($type, $name, $dispatcher);
	}

	public static function tooltip($desc, $title = '', $image = 'tooltip.png', $name = '', $href = '', $link = 1) {
		return hikashop_tooltip($desc, $title, $image, $name,$href, $link = 1);
	}

	public static function display($messages, $type = 'success', $return = false) {
		return hikashop_display($messages, $type, $return);
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

	public static function createDir($dir, $report = true) {
		return hikashop_createDir($dir, $report);
	}

	public static function absoluteUrl($text) {
		return hikashop_absoluteUrl($text);
	}

	public static function getCurrency() {
		return hikashop_getCurrency();
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


	public static function timeCounter($start, $end) {
		if($end < $start) {
			$tmp = $start;
			$start = $end;
			$end = $tmp;
		}

		$delay = ($end - $start) / (24*3600);

		$days = floor($delay);
		$rest = floor(($delay - $days) * 24);

		if($days == 0) {
			if($rest > 1)
				return JText::sprintf('HIKA_AUCTION_HOURS', $rest);
			return JText::sprintf('HIKA_AUCTION_HOUR', $rest);
		}

		if($rest > 0) {
			if($days > 1 && $rest > 1)
				return JText::sprintf('HIKA_AUCTION_DAYS_HOURS', $days, $rest);
			if($days > 1)
				return JText::sprintf('HIKA_AUCTION_DAYS_HOUR', $days, $rest);
			if($rest > 1)
				return JText::sprintf('HIKA_AUCTION_DAY_HOURS', $days, $rest);
			return JText::sprintf('HIKA_AUCTION_DAY_HOUR', $days, $rest);
		}
		if($days > 1)
			return JText::sprintf('HIKA_AUCTION_DAYS', $days);
		return JText::sprintf('HIKA_AUCTION_DAY', $days);
	}

	public static function timeArrayCounter($start, $end) {
		if($end < $start) {
			$tmp = $start;
			$start = $end;
			$end = $tmp;
		}

		$diff = ($end - $start);

		$days = floor($diff  / (24*3600));
		$diff -= ($days*24*3600);

		$hours = floor($diff / 3600);
		$diff -= ($hours*3600);

		$minutes = floor($diff / 60);
		$diff -= ($minutes*60);

		return array($days, $hours, $minutes, $diff);
	}

	public static function convertNumber($num) {
		$dotPos = strrpos($num, '.');
		$commaPos = strrpos($num, ',');
		$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
				((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

		if (!$sep) {
				return floatval(preg_replace("/[^0-9]/", "", $num));
		}

		return floatval(
				preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
				preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
		);
	}
}

class hikaauctionController extends hikaauctionBridgeController {

	protected $type = '';
	protected $publish_return_view = 'listing';
	protected $rights = array(
		'display' => array(),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array(), $skip = false) {
		if(!$skip) {
			parent::__construct($config);
			$this->registerDefaultTask('listing');
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

	public function remove() {
		$cids = JRequest::getVar('cid', array(), '', 'array');
		if(empty($this->type))
			return $this->listing();

		$class = hikaauction::get('class.'.$this->type);
		if(empty($class))
			return $this->listing();

		$num = $class->delete($cids);
		if($num) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', count($cids)), 'message');
		}
		return $this->listing();
	}

	protected function adminStore($token = false) {
		$app = JFactory::getApplication();
		if($token) {
			JRequest::checkToken() || die('Invalid Token');
		}
		if(empty($this->type))
			return false;
		$class = hikaauction::get('class.'.$this->type);
		if( $class === null )
			return false;
		$status = $class->saveForm();
		if($status) {
			$app->enqueueMessage(JText::_('HIKAAUCTION_SUCC_SAVED'), 'message');
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
		$config = hikaauction::config(false);
		$menu_style = $config->get('menu_style', 'content_top');
		if($menu_style == 'content_top') {
			$app = JFactory::getApplication();
			if($app->isAdmin() && JRequest::getString('tmpl') !== 'component') {
				echo hikaauction::getMenu();
			}
		}
		return parent::display($tpl, $params);
	}

	private function toggle($cid, $publish) {
		if(empty($cid)) {
			JError::raiseWarning(500, 'No items selected');
		}
		$cids = implode(',', $cid);
		$dbHelper = self::get('helper.database');
		$db = $dbHelper->get();
		$query = $dbHelper->getQuery(true);
		$query->update( hikaauction::table($this->type) )
			->set( key($this->toggle).' = '.(int)$publish )
			->where( reset($this->toggle).' IN ( '.$cids.' )' );
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

	public function authorize($task) {
		if($this->isIn($task, array('modify','delete'))) {
			if(JRequest::checkToken('request')) {
				return true;
			}
			return false;
		}
		return $this->isIn($task);
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

class hikaauctionView extends hikaauctionBridgeView {
	protected $triggerView = false;
	protected $toolbar = array();

	public function display($tpl = null) {
		if($this->triggerView) {
			JPluginHelper::importPlugin('hikaauction');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onHikaauctionDisplayView', array(&$this));
		}

		if(!empty($this->toolbar)) {
			$toolbarHelper = hikaauction::get('helper.toolbar');
			$toolbarHelper->process($this->toolbar);
		}

		parent::display($tpl);

		if($this->triggerView) {
			$dispatcher->trigger('onHikaauctionAfterDisplayView', array( &$this));
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
		if(JRequest::getVar('search') != $app->getUserState($this->paramBase.'.search') || JRequest::getVar('limit') != $app->getUserState($this->paramBase.'.list_limit')) {
			$app->setUserState($this->paramBase.'.limitstart',0);
			$pageInfo->limit->start = 0;
		} else {
			$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int' );
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

		$this->assignRef('pageInfo', $pageInfo);
		return $pageInfo;
	}

	protected function processFilters(&$query, $searchMap = array(), $orderingAccept = array()) {
		if(!empty($this->pageInfo->search)) {
			$searchVal = '\'%' . $query->escape(JString::strtolower($this->pageInfo->search), true) . '%\'';
			$query->where( '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')', 'AND' );
		}

		if(!empty($this->pageInfo->filter->order->value)) {
			$t = '';
			if(strpos($this->pageInfo->filter->order->value, '.') !== false)
				list($t,$v) = explode('.', $this->pageInfo->filter->order->value, 2);

			if(empty($orderingAccept) || in_array($t.'.', $orderingAccept) || in_array($this->pageInfo->filter->order->value, $orderingAccept))
				$query->order($this->pageInfo->filter->order->value.' '.$this->pageInfo->filter->order->dir);
		}
	}

	protected function getPagination($max = 500, $limit = 100) {
		if(empty($this->pageInfo))
			return false;

		if($this->pageInfo->limit->value == $max)
			$this->pageInfo->limit->value = $limit;

		if(HIKASHOP_J30) {
			$pagination = hikaauction::get('shop.helper.pagination', $this->pageInfo->elements->total, $this->pageInfo->limit->start, $this->pageInfo->limit->value);
		} else {
			jimport('joomla.html.pagination');
			$pagination = new JPagination($this->pageInfo->elements->total, $this->pageInfo->limit->start, $this->pageInfo->limit->value);
		}
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
			$obj = hikaauction::get($name);
			if(!empty($obj))
				$this->assignRef($key, $obj);
			unset($obj);
		}
	}
}

class hikaauctionNull {}

class hikaauctionClass extends JObject {
	protected $db = null;
	protected $dbHelper = null;
	protected $tables = array();
	protected $pkeys = array();
	protected $namekeys = array();
	protected $toggle = array();
	protected $deleteToggle = array();

	public function  __construct($config = array()) {
		$this->dbHelper = hikaauction::get('helper.database');
		$this->db = $this->dbHelper->get();
		return parent::__construct($config);
	}

	public function get($element, $default = null) {
		if(empty($element))
			return null;
		if(empty($this->tables))
			return null;
		$pkey = end($this->pkeys);
		$namekey = end($this->namekeys);
		$table = hikaauction::table(end($this->tables));
		if(!is_numeric($element) && !empty($namekey)) {
			$pkey = $namekey;
		}
		$query = $this->dbHelper->getQuery(true);
		$query->select('*')->from( $query->quoteName($table) )->where( $pkey.' = '.$query->Quote($element) );
		$this->db->setQuery($query, 0, 1);
		$ret = $this->db->loadObject();
		return $ret;
	}

	function getTable() {
		return hikashop_table(end($this->tables));
	}

	public function save(&$element) {
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
		$table = $this->getTable();
		if(!HIKAAUCTION_J16) {
			$obj = new JTable($table, $pkey, $this->db);
			$obj->setProperties($element);
		} else {
			$obj =& $element;
		}

		if(empty($element->$pkey)) {
			$this->db->setQuery($this->getInsert($table, $obj));
			$status = $this->db->query();
		} else {
			if(count( (array)$element ) > 1) {
				$status = $this->db->updateObject($table, $obj, $pkey, true);
			} else {
				$status = true;
			}
		}
		if($status)
			return empty($element->$pkey) ? $this->db->insertid() : $element->$pkey;
		return false;
	}

	private function getInsert($table, &$obj, $keyName = null) {
		$query = $this->dbHelper->getQuery(true);

		$query->insert( $query->quoteName($table) );
		$fields = array();
		$values = array();
		if(is_object($obj))
			$vars = get_object_vars($obj);
		else if(is_array($obj))
			$vars = $obj;
		else
			return $query;

		foreach($vars as $k => $v) {
			if(is_array($v) || is_object($v) || $v === null || $k[0] == '_' ) {
				continue;
			}
			$fields[] = $query->quoteName($k);
			if(is_a($v, 'hikaauctionNull')) {
				$values[] = 'NULL';
			} else {
				$values[] = $this->db->quote($v);
			}
		}
		$query->columns($fields)->values(implode(',', $values));
		return $query;
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
				$table = hikaauction::table(end($this->tables));

				$query = $this->dbHelper->getQuery(true);
				$query->select( ($isNumeric?end($this->pkeys):end($this->namekeys)) )->from( $query->quoteName($table) )->where( ($isNumeric?end($this->pkeys):end($this->namekeys)).' IN ( '.implode(',',$elements) );

				$this->db->setQuery($query);
				if(!HIKAAUCTION_J25) {
					$otherElements = $this->db->loadResultArray();
				} else {
					$otherElements = $this->db->loadColumn();
				}
				foreach($otherElements as $key => $val) {
					$otherElements[$key] = $query->Quote($val);
				}
				break;
			}
		}

		$tables = array();
		if(is_array($this->tables)) {
			foreach($this->tables as $i => $oneTable) {
				$tables[$i] = hikaauction::table($oneTable);
			}
		} else {
			$tables[0] = hikaauction::table($this->tables);
		}

		$result = true;
		foreach($tables as $i => $oneTable) {
			$column = $columns[$i];

			$query = $this->dbHelper->getQuery(true);
			$query->delete( $query->quoteName($oneTable) );
			if(empty($column)) {
				$query->where( ($isNumeric?$this->namekeys[$i]:$this->pkeys[$i]).' IN ('.implode(',',$otherElements).')' );
			} else {
				$query->where( $column.' IN ('.implode(',',$elements).')' );
			}
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

$hikaauctionConfig = hikaauction::config();
define('HIKAAUCTION_RESSOURCE_VERSION', str_replace('.', '', $hikaauctionConfig->get('version')));
$doc = JFactory::getDocument();
$doc->addScript(HIKAAUCTION_JS.'hikaauction.js?v='.HIKAAUCTION_RESSOURCE_VERSION);
$hikaauction_css = $hikaauctionConfig->get('css_'.$css_type,'default');
if(!empty($hikaauction_css)){
	$doc->addStyleSheet(HIKAAUCTION_CSS.$css_type.'_'.$hikaauction_css.'.css?v='.HIKAAUCTION_RESSOURCE_VERSION);
}
if(!$app->isAdmin()){
	$hikaauctionstyleCss = $hikaauctionConfig->get('css_style','');
	if(!empty($styleCssMarket)){
		$doc->addStyleSheet(HIKAAUCTION_CSS.'style_'.$hikaauctionstyleCss.'.css?v='.HIKAAUCTION_RESSOURCE_VERSION);
	}
} else {
	if(HIKAAUCTION_J30 && $_REQUEST['option'] == HIKAAUCTION_COMPONENT) {
		JHtml::_('formbehavior.chosen', 'select');
		$doc->addScriptDeclaration("\r\n".'window.Oby.ready(function(){setTimeout(function(){window.hikashop.noChzn();},100);});');
	}
}
