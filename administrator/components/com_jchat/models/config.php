<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('joomla.application.component.modelform');

/**
 * Config model responsibilities
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface IConfigModel {
	
	/**
	 * Ottiene i dati di configurazione da db params field record component
	 *
	 * @access public
	 * @return Object
	 */
	public function &getData();
	
	/**
	 * Effettua lo store dell'entity config
	 *
	 * @access public
	 * @return boolean
	 */
	public function storeEntity();
}

/**
 * Config model concrete implementation
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
class JChatModelConfig extends JModelForm implements IConfigModel {
	/**
	 * Variables in request array
	 *
	 * @access protected
	 * @var Object
	 */
	protected $requestArray;
	
	/**
	 * Variables in request array name
	 *
	 * @access protected
	 * @var Object
	 */
	protected $requestName;
	
	/**
	 * Clean the cache
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 * @return  void
	 * @since   11.1
	 */
	private function cleanComponentCache($group = null, $client_id = 0) {
		// Initialise variables;
		$conf = JFactory::getConfig();
		$dispatcher = JDispatcher::getInstance();
	
		$options = array(
				'defaultgroup' => ($group) ? $group : $this->app->input->get('option'),
				'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));
	
		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
	
		// Trigger the onContentCleanCache event.
		$dispatcher->trigger('onContentCleanCache', $options);
	}
	
	/**
	 * Ottiene i dati di configurazione da db params field record component
	 *
	 * @access public
	 * @return Object
	 */
	private function &getConfigData() { 
		$instance = JComponentHelper::getParams('com_jchat'); 
		return $instance;
	}
	
	/**
	 * Method to get a form object.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		jimport ( 'joomla.form.form' );
		JForm::addFormPath ( JPATH_ADMINISTRATOR . '/components/com_jchat' );
	
		// Get the form.
		$form = $this->loadForm ( 'com_jchat.component', 'config', array ('control' => 'params', 'load_data' => $loadData ), false, '/config' );
	
		if (empty ( $form )) {
			return false;
		}
	
		return $form;
	}
	
	/**
	 * Ottiene i dati di configurazione del componente
	 *
	 * @access public
	 * @return Object
	 */
	public function &getData() {
		return $this->getConfigData ();
	}
	/**
	 * Effettua lo store dell'entity config
	 *
	 * @access public
	 * @return boolean
	 */
	public function storeEntity() {
		$table = JTable::getInstance('extension');
		// Replace SEF images links
		$base = JUri::root(true) . '/';
		$protocols = '[a-zA-Z0-9]+:';
		$regex     = '#(src|href|poster)="(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
		
		try {
			// Found as installed extension
			if (!$extensionID = $table->find(array('element' => 'com_jchat'))) {
				throw new JChatException($table->getError (), 'error');
			} 
			
			$table->load($extensionID);

			// Translate posted jform array to params for ORM table binding
			$post = $this->app->input->post;
			
			if (!$table->bind ($post->getArray($this->requestArray[$this->requestName]))) { 
				throw new JChatException($table->getError (), 'error');
			}
			
			// Unserialize and replace offline_message param as RAW no filter
			$unserializedParams = json_decode($table->params);
			$unserializedParams->offline_message = $this->requestArray[$this->requestName]['params']['offline_message'];
			$unserializedParams->offline_message = preg_replace($regex, "$1=\"$base\$2\"", $unserializedParams->offline_message);
			$unserializedParams->suggestion_tooltip_text = $this->requestArray[$this->requestName]['params']['suggestion_tooltip_text'];
			$unserializedParams->suggestion_tooltip_text = preg_replace($regex, "$1=\"$base\$2\"", $unserializedParams->suggestion_tooltip_text);
			$unserializedParams->auto_open_agentbox_defaultmessage = $this->requestArray[$this->requestName]['params']['auto_open_agentbox_defaultmessage'];
			$unserializedParams->auto_open_agentbox_defaultmessage = preg_replace($regex, "$1=\"$base\$2\"", $unserializedParams->auto_open_agentbox_defaultmessage);
			
			$table->params = json_encode($unserializedParams);

			// pre-save checks
			if (!$table->check()) {
				throw new JChatException($table->getError (), 'error');
			}

			// save the changes
			if (!$table->store()) {
				throw new JChatException($table->getError (), 'error');
			}

		} catch (JChatException $e) {
			$this->setError($e);
			return false;
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->setError($jchatException);
			return false;
		}

		// Clean the cache.
		$this->cleanComponentCache('_system', 0);
		$this->cleanComponentCache('_system', 1);
		return true;
	}
	
	/**
	 * Class contructor
	 *
	 * @access public
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	
		// App reference
		$this->app = JFactory::getApplication();
		$this->requestArray = &$GLOBALS;
		$this->requestName = '_' . strtoupper('post');
	}
}