<?php
/** 
 * Manage login/logout for social networks connect
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.plugin.plugin' );

class plgSystemJChatlogin extends JPlugin {
	/**
	 * Component params
	 * 
	 * @access private
	 * @var Object
	 */
	private $cParams;
	
	/**
	 * Check if the execution is valid
	 * 
	 * @access private
	 * return boolean
	 */
	private function checkIfValidExecution($cParams, $app) {
		// Ottenimento document
		$doc = JFactory::getDocument ();
		// Output JS APP nel Document
		if($doc->getType() !== 'html' || $app->input->getCmd ( 'tmpl' ) === 'component') {
			return false;
		}
		
		$user = JFactory::getUser();
		if(!$user->id && !$cParams->get('guestenabled', false)) {
			return false;
		}
		
		// Check access levels intersection to ensure that users has access usage permission for chat
		// Get users access levels based on user groups belonging
		$userAccessLevels = $user->getAuthorisedViewLevels();
		
		// Get chat access level from configuration, if set AKA param != array(0) go on with intersection
		$chatAccessLevels = $cParams->get('chat_accesslevels', array(0));
		if(is_array($chatAccessLevels) && !in_array(0, $chatAccessLevels, false)) {
			$intersectResult = array_intersect($userAccessLevels, $chatAccessLevels);
			$hasChatAccess = (bool)(count($intersectResult));
			// Return if user has no access
			if(!$hasChatAccess) {
				return false;
			}
		}
		
		// Check for IP multiple ranges exclusions
		if($cParams->get ( 'ipbanning', false)) {
			$ipAddressRegex = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/i';
			$clientIP = $_SERVER ['REMOTE_ADDR'];
			$clientIpDec = ( float ) sprintf ( "%u", ip2long ( $clientIP ) );
			$ipRanges = $cParams->get ( 'iprange_multiple', null);
			// Check if data are not null
			if($ipRanges) {
				// Try to load every range, one per row
				$explodeRows = explode(PHP_EOL, $ipRanges);
				if(!empty($explodeRows)) {
					foreach ($explodeRows as $singleRange) {
						// Try to detect single range
						$explodeRange = explode('-', $singleRange);
						if(!empty($explodeRange) && count($explodeRange) == 2) {
							$ipStart = trim($explodeRange[0]);
							$ipEnd = trim($explodeRange[1]);
							$validIpRangeStart = preg_match ( $ipAddressRegex, $ipStart );
							$validIpRangeEnd = preg_match ( $ipAddressRegex, $ipEnd );
							if ($validIpRangeStart && $validIpRangeEnd) {
								$lowerIpDec = ( float ) sprintf ( "%u", ip2long ( $ipStart ) );
								$upperIpDec = ( float ) sprintf ( "%u", ip2long ( $ipEnd ) );
								if (($clientIpDec >= $lowerIpDec) && ($clientIpDec <= $upperIpDec)) {
									return false;
								}
							}
						}
					}
				}
			}
		}
		
		// Check for hours activation
		$startHour = $cParams->get('start_at_hour', null);
		$stopHour = $cParams->get('stop_at_hour', null);
		if($startHour && $stopHour) {
			if(class_exists('DateTimeZone')) {
				$jTimeZone = JFactory::getConfig ()->get ( 'offset' );
				$dateObject = JFactory::getDate();
				$dateObject->setTimezone(new DateTimeZone($jTimeZone));
				$currentHour = $dateObject->format('G', true);
			} else {
				$currentHour = date('G');
			}
			if($currentHour < $startHour || $currentHour >= $stopHour) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * onAfterInitialise handler
	 *
	 * @access	public
	 * @return null
	 */
	public function onAfterRoute() {
		$app = JFactory::getApplication();
		if(!$app->getClientId() && $this->checkIfValidExecution($this->cParams, $app)) {
			// Load framework classes without autoloading
			require_once JPATH_ROOT . '/administrator/components/com_jchat/framework/helpers/users.php';
			
			// Manage partial language translations
			$jLang = JFactory::getLanguage();
			$jLang->load('com_jchat', JPATH_SITE . '/components/com_jchat', 'en-GB', true, true);
			if($jLang->getTag() != 'en-GB') {
				$jLang->load('com_jchat', JPATH_SITE, null, true, false);
				$jLang->load('com_jchat', JPATH_SITE . '/components/com_jchat', null, true, false);
			}
			
			// Check and include if Facebook social login is enabled
			if($this->cParams->get('fblogin_active', 0)) {
				// Inject the FB app id in the js domain
				$doc = JFactory::getDocument();
				$appId = $this->cParams->get('appId', '');
				$locale = JFactory::getLanguage ()->getTag();
				$sdkLangTag = str_replace("-", "_", $locale);
				$sdkVersion = $this->cParams->get ( 'sdkversion', '2.6' );
				
				$doc->addScriptDeclaration ( "var jchatAppId = '$appId';" );
				$doc->addScriptDeclaration ( "var jchatSdkVersion = 'v$sdkVersion';" );
				$doc->addScriptDeclaration ( "jQuery(function(){jQuery('<div id=\'fb-root\'></div>').appendTo('body')});" );

				switch ((int)$this->cParams->get ( 'sdkloadmode', '2' )) {
					// Override load mode
					case 2 :
						$doc->addScriptDeclaration ( "(function(d){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];js = d.createElement('script');js.id = id;js.async = true;js.src = '//connect.facebook.net/$sdkLangTag/sdk.js#xfbml=1&version=v$sdkVersion&appId=$appId';ref.parentNode.insertBefore(js, ref);}(document));" );
						break;
					
					// No override load mode
					case 1 :
						$doc->addScriptDeclaration ( "(function(d) {var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];if (d.getElementById(id)){return;}js = d.createElement('script');js.id = id;js.async = true;js.src = '//connect.facebook.net/$sdkLangTag/sdk.js#xfbml=1&version=v$sdkVersion&appId=$appId';ref.parentNode.insertBefore(js, ref);}(document));" );
						break;
						
					// Load nothing
					case 0 :
						break;
				}
				
				// Include the Facebook connector class
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/connector.php';
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/facebook.php';
				$fbConnector = new JChatLoginConnectorFacebook($this->cParams);
				$fbConnector->execute();
			}
			
			// Check and include if G+ social login is enabled
			if($this->cParams->get('gpluslogin_active', 0)) {
				// Include the Facebook connector class
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/connector.php';
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/google.php';
				$gplusConnector = new JChatLoginConnectorGoogle($this->cParams);
				$gplusConnector->execute();
			}
			
			// Check and include if Twitter social login is enabled
			// Check and include if G+ social login is enabled
			if($this->cParams->get('twitterlogin_active', 0)) {
				// Include the Facebook connector class
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/connector.php';
				require_once JPATH_ROOT . '/plugins/system/jchatlogin/connectors/twitter.php';
				$gplusConnector = new JChatLoginConnectorTwitter($this->cParams);
				$gplusConnector->execute();
			}
		}
	}
	
	/**
	 * Class Constructor
	 * 
	 * @access protected
	 * @param object $subject
	 *        	object to observe
	 * @param array $config
	 *        	An array that holds the plugin configuration
	 * @since 1.6
	 */
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		
		$component = JComponentHelper::getComponent('com_jchat');
		$this->cParams = $component->params;
	}
}
