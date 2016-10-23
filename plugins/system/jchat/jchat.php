<?php
/** 
 * App runner
 * @package JCHAT::plugins::system
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.filesystem.file' );

class plgSystemJChat extends JPlugin {	
	/**
	 * JS App Inject
	 *
	 * @access	private
	 * @param Object $cParams
	 * @return void
	 */
	private function injectApp ($cParams, $app) {
		// Ottenimento document
		$doc = JFactory::getDocument ();
		$option = $app->input->get('option', null);
		$viewName = $app->input->get('view', null);
		
		// Output JS APP nel Document
		if($doc->getType() !== 'html' || $app->input->getCmd ( 'tmpl' ) === 'component') {
			return false;
		}
		
		$user = JFactory::getUser();
		if(!$user->id && !$cParams->get('guestenabled', false)) {
			return;
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
				return;
			}
		}
		
		// Check for menu exclusion
		$menu = $app->getMenu()->getActive();
		if(is_object($menu)) {
			$menuItemid = $menu->id;
			$menuExcluded = $cParams->get('chat_exclusions');
			if(is_array($menuExcluded) && !in_array(0, $menuExcluded, false) && in_array($menuItemid, $menuExcluded)) {
				return;
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
				return;
			}
		}
		
		//load the translation
		require_once JPATH_BASE . '/administrator/components/com_jchat/framework/helpers/language.php';
		$base = JUri::base();
		
		// Manage partial language translations
		$jLang = JFactory::getLanguage();
		$jLang->load('com_jchat', JPATH_SITE . '/components/com_jchat', 'en-GB', true, true);
		if($jLang->getTag() != 'en-GB') {
			$jLang->load('com_jchat', JPATH_SITE, null, true, false);
			$jLang->load('com_jchat', JPATH_SITE . '/components/com_jchat', null, true, false);
		}
		
		$chatLanguage = JChatHelpersLanguage::getInstance();
		
		// Inject js translations
		$translations = array(	'chat',
								'privatechat',
								'nousers',
								'nousers_filter',
								'gooffline',
								'available',
								'busy',
								'statooffline',
								'statodonotdisturb',
								'reset_chatboxes',
								'minimize_chatboxes',
								'defaultstatus',
								'sent_file',
								'received_file',
								'sent_file_waiting',
								'sent_file_downloaded',
								'sent_file_downloaded_realtime',
								'sent_file_download',
								'error_deleted_file',
								'error_notfound_file',
								'groupchat_filter',
								'addfriend',
								'optionsbutton',
								'maximizebutton_maximized',
								'maximizebutton_minimized',
								'closesidebarbutton',
								'loginbutton',
								'logindesc',
								'already_logged',
								'spacer',  
								'scegliemoticons',
								'wall_msgs',
								'wall_msgs_refresh',
								'manage_avatars',
								'seconds',
								'minutes',
								'hours',
								'days',
								'years',
								'groupchat_request_sent',
								'groupchat_request_received',
								'groupchat_request_accepted',
								'groupchat_request_removed',
								'groupchat_request_received',
								'groupchat_request_accepted_owner',
								'groupchat_nousers',
								'groupchat_allusers',
								'audio_onoff',
								'public_audio_onoff',
								'vibrate_onoff',
								'notification_onoff',
								'wall_notification',
								'privatemsg_notification',
								'sentfile_notification',
								'trigger_emoticon',
								'trigger_fileupload',
								'trigger_export',
								'trigger_delete',
								'trigger_refresh',
								'trigger_skypesave',
								'trigger_skypedelete',
								'trigger_infoguest',
								'trigger_room',
								'trigger_history',
								'trigger_history_wall',
								'trigger_webrtc',
								'trigger_webrtc_ringing',
								'trigger_send',
								'trigger_geolocation',
								'search',
								'invite',
								'pending',
								'remove',
								'userprofile_link',
								'you',
								'me',
								'seen',
								'banning',
								'banneduser',
								'ban_moderator',
								'startskypecall',
								'startskypedownload',
								'insert_skypeid',
								'skypeidsaved',
								'skypeid_deleted',
								'roomname',
								'roomcount',
								'available_rooms',
								'chatroom_users',
								'chatroom_join',
								'chatroom_joined',
								'users_inchatroom',
								'noavailable_rooms',
								'chatroom',
								'addnew_chatroom',
								'chatroomform_name',
								'chatroomform_description',
								'chatroomform_accesslevel',
								'chatroomform_submit',
								'success_storing_chatroom',
								'success_deleting_chatroom',
								'insert_override_name',
								'trigger_override_name',
								'override_name_saved',
								'override_name_deleted',
								'select_period',
								'nomessages_available',
								'period_1d',
								'period_1w',
								'period_1m',
								'period_3m',
								'period_6m',
								'period_1y',
								'skype',
								'newmessage_tab',
								'start_call',
								'accept_call',
								'decline_call',
								'end_call',
								'call_starting',
								'call_started',
								'call_disconnected',
								'incoming_started',
								'connecting',
								'missing_local_stream',
								'closing_connection',
								'connection_closed',
								'connection_active',
								'error_creating_connection',
								'session_error',
								'connection_close_error',
								'connection_failed',
								'webrtc_videochat',
								'webcam_quality',
								'webrtc_bandwidth',
								'mediastream_error',
								'requires_https',
								'noanswer',
								'webrtc_nosupport',
								'chrome_webrtc_support',
								'firefox_webrtc_support',
								'opera_webrtc_support',
								'ie_webrtc_support',
								'safari_webrtc_support',
								'webrtc_caniuse',
								'nowebcam_detected',
								'grant_cam_access',
								'quality_cam',
								'onoffswitch',
								'endcall_before_close',
								'endcall_totoggle_popups',
								'hardware_unavailable',
								'webrtc_notification_ringing',
								'newvideocall_tab',
								'fallback_suggestion',
								'expired_msg',
								'lamform_name',
								'lamform_email',
								'lamform_message',
								'lamform_submit',
								'lamform_required',
								'success_send_lamessage',
								'error_send_lamessage',
								'sendus_aticket',
								'select_user_receiver',
								'trigger_messaging_emoticon',
								'trigger_messaging_fileupload',
								'trigger_messaging_export',
								'trigger_messaging_delete',
								'trigger_messaging_openbox',
								'open_privatemess',
								'agentbox_defaultmessage',
								'source_language',
								'target_language',
								'lang_switcher',
								'translate_arrow',
								'start_accept_call',
								'end_decline_call',
								'start_recording',
								'stop_recording',
								'pause_recording',
								'view_recording',
								'download_recording',
								'upload_recording',
								'send_recording',
								'upload_complete',
								'upload_error'
		);
		$chatLanguage->injectJsTranslations($translations, $doc);
				
		// Output JS APP nel Document
		$baseTemplate = $cParams->get('chat_template', 'default.css');
		switch ($baseTemplate) {
			case 'custom.css':
				JHtml::stylesheet('com_jchat/css/templates/default.css', array(), true, false, false, false);
			break;
				
			case 'default.css';
			default:
				$doc->addStyleSheet(JURI::root(true) . '/components/com_jchat/css/templates/default.css');
			break;
		}
		
		$directTemplates = array('default.css', 'custom.css');
		if(!in_array($baseTemplate, $directTemplates)) {
			$doc->addStyleSheet(JURI::root(true) . '/components/com_jchat/css/templates/' . $baseTemplate);
		}
		
		// Scripts loading
		$defer = $cParams->get('scripts_loading', null) == 'defer' ? true : false;
		$async = $cParams->get('scripts_loading', null) == 'async' ? true : false;

		if($cParams->get('includejquery', 1)) {
			JHtml::_('jquery.framework');
		}
		if($cParams->get('noconflict', 1)) {
			$doc->addScript(JURI::root(true) . '/components/com_jchat/js/jquery.noconflict.js');
		}
		$doc->addScriptDeclaration("var jchat_livesite='$base';");
		$doc->addScriptDeclaration("var jchat_excludeonmobile='" . $cParams->get('exclude_onmobile', 0) . "';");
		$doc->addScriptDeclaration("var jchat_guestenabled='" . $cParams->get('guestenabled', 1) . "';");
		$doc->addScriptDeclaration("var jchat_userid='" . $user->id . "';");
		$doc->addScriptDeclaration("var jchat_usersessionid='" . session_id() . "';");
		$doc->addScriptDeclaration("var jchat_notifications_time='" . $cParams->get('notifications_time', 10) . "';");
		$doc->addScriptDeclaration("var jchat_notifications_public_time='" . $cParams->get('notifications_public_time', 5) . "';");
		$doc->addScriptDeclaration("var jchat_wall_sendbutton=" . $cParams->get('show_send_button', 2) . ";");
		$doc->addScriptDeclaration("var jchat_sidebar_default_width_override='" . $cParams->get('sidebar_default_width_override', '') . "';");
		$doc->addScriptDeclaration("var jchat_privatemess_uri='" . @JRoute::_('index.php?option=com_jchat&view=messaging') . "';");

		// Manage by plugin append the chat target element based on rendering mode and related overridden styles
		$renderingMode = $cParams->get('rendering_mode', 'auto');
		$targetElement = $renderingMode == 'auto' ? 'body' : '#jchat_target';
		$doc->addScriptDeclaration("var jchatTargetElement='$targetElement';");
		
		$offlineMessage = str_replace(array( "\n",  "\r", "\t", PHP_EOL), ' ', JText::_($cParams->get('offline_message', JText::_('COM_JCHAT_DEFAULT_OFFLINE_MESSAGE')), true));
		$doc->addScriptDeclaration("var jchatOfflineMessage='$offlineMessage';");
		
		// Load and inject emoticons into the JS domain
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('linkurl') . ' AS ' . $db->quoteName('path') . ',' . $db->quoteName('keycode'));
		$query->from($db->quoteName('#__jchat_emoticons'));
		$query->where($db->quoteName('published') . '=1');
		$query->order($db->quoteName('ordering') . 'ASC');
		$emoticons = $db->setQuery($query)->loadObjectList();
		$doc->addScriptDeclaration("window.jchat_emoticons=" . json_encode($emoticons) . ";");
		
		// Add styles for module displacement
		if($renderingMode == 'module') {
			$doc->addStyleDeclaration('
				#jchat_base, #jchat_wall_popup, #jchat_userstab_popup {
						position: relative;
					}
				#jchat_base, #jchat_wall_popup, #jchat_userstab_popup {
						width: ' . ($cParams->get('sidebar_width', 250)) . 'px;
					}
				#jchat_userstab, #jchat_userstab.jchat_userstabclick {
						width: ' . ($cParams->get('sidebar_width', 250) - 2) . 'px;
					}
				#jchat_target {
						width: ' . $cParams->get('sidebar_width', 250) . 'px;
						height: ' . $cParams->get('sidebar_height', 600) . 'px;
					}
				#jchat_users_search {
						width: ' . $cParams->get('search_width', 100) . 'px;
					}
				#jchat_roomstooltip {
						width: ' . $cParams->get('chatroom_width', 400) . 'px;
					}
				#jchat_roomsdragger {
						width: ' . (int)($cParams->get('chatroom_width', 400) + 2) . 'px;
					}
				#jchat_users_search {
						padding: 2px 6px 0 16px;
					}
				#jchat_wall_popup.jchat_wall_minimized {
						top: 0;
					}
			');
		}
		
		if($fontsizeOverride = $cParams->get('fontsize_override', null)) {
			$doc->addStyleDeclaration("div.jchat_chatboxmessage,
									   div.jchat_textarea{font-size:$fontsizeOverride}");
		}
		
		if($fontsizeTitlesOverride = $cParams->get('fontsize_titles_override', null)) {
			$doc->addStyleDeclaration("span.jchat_publicchattitle, 
									   span.jchat_privatechattitle,
									   #jchat_userstab:not(.jchat_tabclick) #jchat_userstab_text,
									   span.jchat_lamform_title{font-size:$fontsizeTitlesOverride}");
			if((int)$fontsizeTitlesOverride >= 17 && $cParams->get('chat_template', 'default.css') != 'alternative.css') {
				$doc->addStyleDeclaration("#jchat_userstab:not(.jchat_tabclick) #jchat_userstab_text{margin-top:0}");
			}
			$doc->addStyleDeclaration("div.jchat_userlist span.jchat_userscontentname{font-size:" . ((int)$fontsizeTitlesOverride - 1) . "px}");
		}
		
		if(!$cParams->get('show_users_count', 1)) {
			$doc->addStyleDeclaration("span.jchat_userscount{display:none}");
		}
		
		// Add override color styles
		if($colorOverride = $cParams->get('chat_color_override', null)) {
			$doc->addStyleDeclaration("#jchat_userstab.jchat_tab,
									   div.jchat_userstabtitle,
									   #jchat_roomsdragger.jchat_tab,
									   div.jchat_tabpopup span.jchat_tab,
									   div#jchat_select_period,
									   div.jchat_tooltip_header,
									   div.jchat_roomstooltip div.jchat_roomright .jchat_roomjoin,
									   div.jchat_userstabsubtitle,
									   div.jchat_infoguest_title{background:$colorOverride!important;background-color:$colorOverride}
									   div.jchat_tabpopup span.jchat_tab,
									   #jchat_roomstooltip,
									   #jchat_confirm_delete,
									   #jchat_select_period,
									   div.jchat_emoticonstooltip div.jchat_tooltip_content,
									   div.jchat_fileuploadtooltip iframe,
									   div.jchat_messaging_fileuploadtooltip iframe,
									   div.jchat_historytooltip,
									   div.jchat_webrtctooltip,
									   div.jchat_messaging_delete_conversation_tooltip,
									   div.jchat_userslist_ctrls div.jchat_userslist_reply,
									   div.jchat_tooltip_header,
									   div.jchat_infoguesttooltip,
									   div.jchat_geolocationtooltip{border-color:$colorOverride!important}");
		}
		
		// Submit button color override
		if($submitButtonColorOverride = $cParams->get('submitlamform_color_override', null)) {
			$pathImages = JURI::root(true) . '/components/com_jchat/images/default/icon_ticket.png';
			$doc->addStyleDeclaration("div.jchat_submit_lam_form {border-color:$submitButtonColorOverride;background:$submitButtonColorOverride url($pathImages) no-repeat 6px 3px}
									   div.jchat_submit_chatroom_form {border-color:$submitButtonColorOverride;background:$submitButtonColorOverride}");
		}
		
		// Tooltip border color override
		if($tooltipBorderColorOverride = $cParams->get('tooltip_bordercolor_override', null)) {
			$doc->addStyleDeclaration("#jchat_default_suggestion_tooltip div.jchat_tooltip_content {border-color: $tooltipBorderColorOverride}");
			$doc->addStyleDeclaration("#jchat_default_suggestion_tooltip:not(.jchat_arm)::before {background-color: $tooltipBorderColorOverride}");
		}
		
		// Tooltip background color override
		if($tooltipBckcolorOverride = $cParams->get('tooltip_bckcolor_override', null)) {
			$doc->addStyleDeclaration("#jchat_default_suggestion_tooltip div.jchat_tooltip_content {background-color: $tooltipBckcolorOverride !important}");
		}
		
		// Add override height
		if($cParams->get('public_chat_height_override', null)) {
			$publicChatHeightOverride = (int)trim($cParams->get('public_chat_height_override'), '%');
			$doc->addStyleDeclaration("#jchat_wall_popup{height:$publicChatHeightOverride%}");
		}
		if($cParams->get('private_chat_height_override', null)) {
			$privateChatHeightOverride = (int)trim($cParams->get('private_chat_height_override'), '%');
			$doc->addStyleDeclaration("#jchat_userstab_popup{height:$privateChatHeightOverride%}");
			$doc->addStyleDeclaration("#jchat_wall_popup.jchat_wall_minimized{bottom:$privateChatHeightOverride%}");
		}
		
		// Add override top margin for public chat
		if($cParams->get('public_chat_top_override', null)) {
			$publicChatTopOverride = trim($cParams->get('public_chat_top_override'));
			if(stripos($publicChatTopOverride, 'px') === false && stripos($publicChatTopOverride, '%') === false) {
				$publicChatTopOverride .= 'px';
			}
			$doc->addStyleDeclaration("#jchat_wall_popup:not(.maximized):not(.jchat_wall_minimized ){top:$publicChatTopOverride}");
		}
		
		// Disable emoticons if needed
		if(!$cParams->get('emoticons_enabled', 1)) {
			$doc->addStyleDeclaration("*.jchat_trigger_emoticon,*.jchat_trigger_messaging_emoticon{display:none;}");
		}
		
		// Override emoticons width
		if($cParams->get('emoticons_original_size', 0)) {
			$doc->addStyleDeclaration("div.jchat_tabcontenttext span.jchat_chatboxmessagecontent img.jchat_emoticons," .
									  " #jchat_usersmessages span.jchat_chatboxmessagecontent img.jchat_emoticons{max-width:inherit!important;max-height:inherit!important;}" .
									  " span.jchat_chatboxmessagecontent{min-width:26px;}");
		}
		
		$doc->addScript(JURI::root(true) . '/components/com_jchat/js/utility.js', 'text/javascript', $defer, $async);
		$doc->addScript(JURI::root(true) . '/components/com_jchat/js/jstorage.min.js', 'text/javascript', $defer, $async);
		$doc->addScript(JURI::root(true) . '/components/com_jchat/sounds/soundmanager2.js', 'text/javascript', $defer, $async);
		$doc->addScript(JURI::root(true) . '/components/com_jchat/js/notifications.js', 'text/javascript', $defer, $async);
		if($option == 'com_jchat' && $viewName == 'conference') {} else {
			$doc->addScript(JURI::root(true) . '/components/com_jchat/js/webrtc.js', 'text/javascript', $defer, $async);
		}
		if($cParams->get('enable_recording', 0)) {
			$doc->addScript(JURI::root(true) . '/components/com_jchat/js/recorder.js', 'text/javascript', $defer, $async);
		}
		$doc->addScript(JURI::root(true) . '/components/com_jchat/js/main.js', 'text/javascript', $defer, $async);
		$doc->addScript(JURI::root(true) . '/components/com_jchat/js/emoticons.js', 'text/javascript', $defer, $async);
		
		// Check for Geolocation feature and related scripts
		if($cParams->get('geolocation_enabled', 0)) {
			$doc->addScript ( 'https://maps.google.com/maps/api/js?key=AIzaSyCHiHkF9krK7xfDsqX_37a9SjqKJ80wgYM' );
			$doc->addScript ( JURI::root ( true ) . '/components/com_jchat/js/gmap.js', 'text/javascript', $defer, $async );
		}
	}
	
	
	/**
	 * onAfterInitialise handler
	 *
	 * @access	public
	 * @return null
	 */
	public function onAfterInitialise() {
		$app = JFactory::getApplication(); 
		$component = JComponentHelper::getComponent('com_jchat');
		$cParams = $component->params;
		if(!$app->getClientId() && $cParams->get('includeevent', 'afterdispatch') == 'afterinitialize') {
			$this->injectApp($cParams, $app);
		}
		
		// Set the custom emoticons path for the admin com_media path folder, supporting J3.5+ too
		if($app->getClientId() && $app->input->get('option') == 'com_media' && ($app->input->get('author') == 'jchat' || $app->input->get('asset') == 'com_jchat')) {
			$params = JComponentHelper::getParams('com_media');
			$params->set('file_path', 'components/com_jchat/emoticons');
			$params->set('image_path', 'components/com_jchat/emoticons');
		}
	}

	/**
	 * onAfterInitialise handler
	 *
	 * @access	public
	 * @return null
	 */
	public function onAfterDispatch() {
		$app = JFactory::getApplication(); 
		$component = JComponentHelper::getComponent('com_jchat');
		$cParams = $component->params;
		if(!$app->getClientId() && $cParams->get('includeevent', 'afterdispatch') == 'afterdispatch') {
			$this->injectApp($cParams, $app);
		}
	}
	
	/* Manage the Joomla updater based on the user license
	 *
	* @access public
	* @return void
	*/
	public function onInstallerBeforePackageDownload(&$url, &$headers) {
		$uri 	= JUri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		$app = JFactory::getApplication();
		if ($uri->getHost() == 'storejextensions.org' && in_array('com_jchat.zip', $parts)) {
			// Init as false unless the license is valid
			$validUpdate = false;
				
			// Manage partial language translations
			$jLang = JFactory::getLanguage();
			$jLang->load('com_jchat', JPATH_BASE . '/components/com_jchat', 'en-GB', true, true);
			if($jLang->getTag() != 'en-GB') {
				$jLang->load('com_jchat', JPATH_BASE, null, true, false);
				$jLang->load('com_jchat', JPATH_BASE . '/components/com_jchat', null, true, false);
			}
				
			// Email license validation API call and &$url building construction override
			$cParams = JComponentHelper::getParams('com_jchat');
			$registrationEmail = $cParams->get('registration_email', null);
				
			// License
			if($registrationEmail) {
				$prodCode = 'jchatent';
				$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
	
				// Retrieve license informations from the remote REST API
				$apiResponse = null;
				$apiEndpoint = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet') . "/option,com_easycommerce/action,licenseCode/email,$registrationEmail/productcode,$prodCode";
				if (function_exists('curl_init')){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$apiResponse = curl_exec($ch);
					curl_close($ch);
				}
				$objectApiResponse = json_decode($apiResponse);
	
				if(!is_object($objectApiResponse)) {
					// Message user about error retrieving license informations
					$app->enqueueMessage(JText::_('COM_JCHAT_ERROR_RETRIEVING_LICENSE_INFO'));
				} else {
					if(!$objectApiResponse->success) {
						switch ($objectApiResponse->reason) {
							// Message user about the reason the license is not valid
							case 'nomatchingcode':
								$app->enqueueMessage(JText::_('COM_JCHAT_LICENSE_NOMATCHING'));
								break;
	
							case 'expired':
								// Message user about license expired on $objectApiResponse->expireon
								$app->enqueueMessage(JText::sprintf('COM_JCHAT_LICENSE_EXPIRED', $objectApiResponse->expireon));
								break;
						}
							
					}
						
					// Valid license found, builds the URL update link and message user about the license expiration validity
					if($objectApiResponse->success) {
						$url = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/XZY1305SQUOnifs3243564864kfunjx35tdrnty1386g.ugzy');
	
						$validUpdate = true;
						$app->enqueueMessage(JText::sprintf('COM_JCHAT_EXTENSION_UPDATED_SUCCESS', $objectApiResponse->expireon));
					}
				}
			} else {
				// Message user about missing email license code
				$app->enqueueMessage(JText::sprintf('COM_JCHAT_MISSING_REGISTRATION_EMAIL_ADDRESS', JFilterOutput::ampReplace('index.php?option=com_jchat&task=config.display#_licensepreferences')));
			}
				
			if(!$validUpdate) {
				$app->enqueueMessage(JText::_('COM_JCHAT_UPDATER_STANDARD_ADVISE'), 'notice');
			}
		}
	}
	
	/**
	 * Class Constructor 
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
	} 
}