/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

var isSoundEnabled = true; // Change this to false to disable sound notifications

var processingSomething = false;

var specificDepartment = null;
var specificRouteId = null;
var specificOperators = null;

var chatName = null;
var refreshInterval = 2000; // in milliseconds

var operatorIsTyping = 0;
var clientIsTyping = 0;
var typingTimeoutTimer = null;

var chatSessionActive = 0;
var chatSessionMdate = 0;

var JLiveChat = {
    websiteRoot: '',
    hostedModeURI: false,
    debugging: false,
    currentURI: null,
    chatWindowOpen: false,
    chatWindowMode: null,
    serverURI: 'index.php?option=com_jlivechat&view=popup&tmpl=component',
    proactiveRecheckInterval: 7000, // In miliseconds, 7 seconds default
    memberId: null,
    chatSessionId: null,
    popupWindowObj: null,
    iframeWindowObj: null,
    autopopupId: 'jlivechat_autopopup',
    autopopupVisible: false,
    autoPopupHTML: null,
    autopopupObj: null,
    iframeId: 'livechat_iframe',
    iframeOpen: false,
    closedPermanently: false,
    callbackFunc: null,
    alreadyInitialized: false,
    xhrDataType: 'json',
    visitorSentLastMessage: true, // DO NOT CHANGE THIS
    popupUri: null,
    
    initialize: function() {
	if(!JLiveChat.alreadyInitialized) {
	    JLiveChat.alreadyInitialized=true;
	    JLiveChat.xhrDataType = (JLiveChat.hostedModeURI != false ? 'jsonp' : 'json');
	    JLiveChat.currentURI = String(document.location.href);
	    JLiveChat.loadJS();
	}
    },

    loadJS: function() {
	var requiredJSLibs = [];
	
	if(typeof(window.jQuery) == 'undefined') {
	    // jQuery hasn't been loaded yet, load it
	    requiredJSLibs.push(JLiveChat.websiteRoot+'/components/com_jlivechat/js/jquery-1.7.2.min.js');
	}
	
	if(requiredJSLibs.length > 0) {
	    LazyLoad.loadOnce(requiredJSLibs, JLiveChat.jsLoaded);
	} else {
	    JLiveChat.jsLoaded();
	}
    },

    jsLoaded: function() {
	jQuery.noConflict();
	
	if(JLiveChat.callbackFunc) {
	    JLiveChat.callbackFunc();
	    JLiveChat.callbackFunc=null;
	}
	
	if(JLiveChat.currentURI.indexOf("popup_mode") > -1) {
	    // We are currently in the popup window
	    setPopupWindowStyles();
	    
	    var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;

	    if(!isInIFrame) {
		attachCloseEvent();
	    }
	} else {
	    // This page is not the popup window
	    JLiveChat.checkProactive();
	    
	    setTimeout('JLiveChat.checkAutopopup();', 2000);
	}
    },

    checkAutopopup: function() {
	if(JLiveChat.currentURI.indexOf('popup_mode') > -1 || JLiveChat.currentURI.indexOf("tmpl=component") > -1 || JLiveChat.chatWindowOpen) {
	    return false;
	}

	var params = {
	    no_html: '1',
	    do_not_log: 'true',
	    tmpl: 'component',
	    task: 'check_autopopup'
	};
	
	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false,
	    success: function(data) {
		if(parseInt(data.show_popup) > 0) {
		    // Autopopup enabled
		    JLiveChat.autoPopupHTML=data.display_html;
		    
		    var displayInSeconds = (parseInt(data.display_html_in_seconds)-1)*1000; // Convert to miliseconds

		    var showPopup = true;

		    if(data.display_html_on_uris.length > 0) {
			// On show autopopup on defined urls
			showPopup = false;
			
			var pageUri = null;
			
			jQuery.each(data.display_html_on_uris, function (index, item) {
			    pageUri = String(item);

			    if(JLiveChat.currentURI.indexOf(pageUri) > -1) {
				// Show on this page
				showPopup = true;
			    }
			});
		    }

		    if(showPopup) {
			setTimeout('JLiveChat.showAutoPopup();', displayInSeconds);
		    }
		}
	    }
	});

	return true;
    },

    showAutoPopup: function () {
	var body = jQuery(document.body);

	JLiveChat.autopopupObj = jQuery('<div />', {'id': JLiveChat.autopopupId});
	
	JLiveChat.autopopupObj.html(JLiveChat.autoPopupHTML);

	body.append(JLiveChat.autopopupObj);
	
	JLiveChat.animateInAutopopup();

	var isMobile = (/iphone|ipad|ipod|android|blackberry/i.test(navigator.userAgent.toLowerCase()));

	if (isMobile) {
	    if (!navigator.userAgent.match(/(iPad|iPhone|iPod).*OS [5-9]_/i) && !navigator.userAgent.match(/Android [3-9]/i) && !navigator.userAgent.match(/BlackBerry[0-9\w\s\.]*\/[7-9]/i))
	    {
	       // This is iOS 4 or lower, NOT iOS 5 or Android 3.0 and lower, and Blackberry 7 and lower
	       var updatePositionFunc = function() {
		    var autoPopupHeight = parseInt(JLiveChat.autopopupObj.css('height'));

		    var newPosition = window.pageYOffset + window.innerHeight - autoPopupHeight;

		    JLiveChat.autopopupObj.css('top', String(newPosition)+"px");
		};

	       jQuery(document).bind('scroll', updatePositionFunc);
	       
	       updatePositionFunc();
	    }
	}
    },

    animateInAutopopup: function () {
	JLiveChat.autopopupObj.animate({
	    marginBottom: 0
	}, 650);
	
	JLiveChat.autopopupVisible=true;
    },

    animateOutAutopopup: function () {
	if(!JLiveChat.autopopupVisible) {
	    return false;
	}

	JLiveChat.autopopupObj.animate({
	    marginBottom: '-90px'
	}, 'slow');
	
	JLiveChat.autopopupVisible=false;
	
	return true;
    },

    closeAutopopup: function (closeDelay) {
	if(!closeDelay) {
	    closeDelay = 1500;
	}

	setTimeout('JLiveChat.animateOutAutopopup();', closeDelay);
    },

    checkProactive: function() {
	if(document.location.protocol == 'https:' || JLiveChat.currentURI.indexOf("popup_mode") > -1 || JLiveChat.currentURI.indexOf("tmpl=component") > -1) {
	    // Proactive Chat is disabled for this URL
	    return false;
	}

	var params = {
	    no_html: '1',
	    do_not_log: 'true',
	    tmpl: 'component',
	    task: 'check_proactive'
	};
	
	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false, 
	    success: function(data) {
		if(data) {
		    if(data.proactive && !JLiveChat.chatWindowOpen) {
			// There is a pending proactive chat for this visitor
			JLiveChat.memberId = parseInt(data.proactive.member_id);
			JLiveChat.chatSessionId = parseInt(data.proactive.chat_session_id);

			JLiveChat.openLiveChatWindow(null, 'iframe');
		    } else if(parseInt(data.proactive_setting) == 1 && !JLiveChat.chatWindowOpen) {
			// No pending proactive chat requests for this visitor, recheck?
			setTimeout("JLiveChat.checkProactive();", JLiveChat.proactiveRecheckInterval);
		    }
		} else {
		    setTimeout("JLiveChat.checkProactive();", JLiveChat.proactiveRecheckInterval);
		}
	    }
	});

	return true;
    },
    
    describe: function(obj) {
	if (obj==null) {
	    return null;
	}
	switch(typeof(obj)) {
	    case 'object': {
		var message = "";
		for (key in obj) {
		    message += ", [" + key + "]: [" + obj[key] + "]";
		}
		if (message.length > 0) {
		    message = message.substring(2); // chomp initial ', '
		}
		return message;
	    }
	    default:
		return "" + obj;
	}
    },

    debug: function(message) {
	if(JLiveChat.debugging) {
	    alert("AjaxJS Message:\n\n" + message);
	}
    },

    error: function(message) {
	if(JLiveChat.debugging) {
	    alert("AjaxJS ERROR:\n\n" + message);
	}
    },

    trim: function(str) {
	return str.replace(/(^\s+|\s+$)/g,'');
    },

    strip: function(str) {
	return str.replace(/\s+/, "");
    },

    urlencode: function(str) {
	if(!str) {
	    return str;
	}
	
	var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
	var ret = str.toString();

	var replacer = function(search, replace, str) {
	    var tmp_arr = [];
	    tmp_arr = str.split(search);
	    return tmp_arr.join(replace);
	};

	// The histogram is identical to the one in urldecode.
	histogram['!']   = '%21';
	histogram['%20'] = '+';

	// Begin with encodeURIComponent, which most resembles PHP's encoding functions
	ret = encodeURIComponent(ret);

	for (search in histogram) {
	    replace = histogram[search];
	    ret = replacer(search, replace, ret) // Custom replace. No regexing
	}

	// Uppercase for full PHP compatibility
	return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
	    return "%"+m2.toUpperCase();
	});

	return ret;
    },

    timestamp: function() {
	return Number(new Date());
    },

    openLiveChatWindow: function(popupUri, popupMode) {
	if(JLiveChat.iframeOpen && JLiveChat.chatWindowMode != 'iframe') {
	    JLiveChat.closeIFramePopup();
	}
	
	JLiveChat.chatWindowOpen = true;
	JLiveChat.chatWindowMode = popupMode;

	if(!popupUri) {
	    popupUri = JLiveChat.websiteRoot+'/'+JLiveChat.serverURI+'&popup_mode='+popupMode;
	}
	
	JLiveChat.popupUri = popupUri;
	
	if(popupMode == 'iframe') {
	    // Open iframe window
	    if(JLiveChat.iframeOpen) {
		return false;
	    }

	    JLiveChat.iframeOpen=true;

	    JLiveChat.closeAutopopup(1);

	    var popW = 504;
	    var popH = 404;

	    if(window.webkit) {
		// Safari popup window should be about 10px more
		popW = 514;
		popH = 414;
	    }

	    var body = document.getElementsByTagName('body').item(0);

	    if(!JLiveChat.iframeWindowObj) {
		JLiveChat.iframeWindowObj = document.createElement('iframe');
		JLiveChat.iframeWindowObj.src = popupUri;
		JLiveChat.iframeWindowObj.id = JLiveChat.iframeId;
		JLiveChat.iframeWindowObj.name = JLiveChat.iframeId;
		JLiveChat.iframeWindowObj.width=popW;
		JLiveChat.iframeWindowObj.height=popH;
		JLiveChat.iframeWindowObj.frameborder=0;
		JLiveChat.iframeWindowObj.scrolling='no';
		JLiveChat.iframeWindowObj.allowautotransparency=true;
		JLiveChat.iframeWindowObj.style.overflow='hidden';
		JLiveChat.iframeWindowObj.style.display='block';
		JLiveChat.iframeWindowObj.style.zIndex=10000;
		JLiveChat.iframeWindowObj.style.bottom=0;
		JLiveChat.iframeWindowObj.style.right=0;
		JLiveChat.iframeWindowObj.style.border='none';

		body.appendChild(JLiveChat.iframeWindowObj);
	    }

	    if(!jQuery(JLiveChat.iframeWindowObj).is(':visible')) {
		// Iframe has been closed
		JLiveChat.closedPermanently = false;

		JLiveChat.iframeWindowObj = document.createElement('iframe');
		JLiveChat.iframeWindowObj.src = popupUri;
		JLiveChat.iframeWindowObj.id = JLiveChat.iframeId;
		JLiveChat.iframeWindowObj.name = JLiveChat.iframeId;
		JLiveChat.iframeWindowObj.width=popW;
		JLiveChat.iframeWindowObj.height=popH;
		JLiveChat.iframeWindowObj.frameborder=0;
		JLiveChat.iframeWindowObj.scrolling='no';
		JLiveChat.iframeWindowObj.allowautotransparency=true;
		JLiveChat.iframeWindowObj.style.overflow='hidden';
		JLiveChat.iframeWindowObj.style.display='block';
		JLiveChat.iframeWindowObj.style.zIndex=10000;
		JLiveChat.iframeWindowObj.style.bottom=0;
		JLiveChat.iframeWindowObj.style.right=0;
		JLiveChat.iframeWindowObj.style.border='none';

		body.appendChild(JLiveChat.iframeWindowObj);
	    }

	    JLiveChat.showIFramePopup();
	    
	    setTimeout('JLiveChat.monitorIFramePopup();', 1000);
	} else {
	    // Open popup window
	    if(JLiveChat.iframeOpen) {
		JLiveChat.closeIFramePopup();
	    }
	    
	    JLiveChat.closeAutopopup(null);
	    
	    var screenWidth = 760, screenHeight = 420; // DO NOT CHANGE THESE DEFAULT VALUES

	    if(screen.availWidth && screen.availHeight) {
		screenWidth = screen.availWidth;
		screenHeight = screen.availHeight;
	    }

	    var livechatPopupWidth = 500;
	    var livechatPopupHeight = 400;

	    if(window.webkit) {
		// Safari popup window should be about 10px more
		livechatPopupWidth = 502;
		livechatPopupHeight = 402;
	    }

	    var leftPos = (screenWidth-livechatPopupWidth)/2, topPos = (screenHeight-livechatPopupHeight)/2;

	    JLiveChat.popupWindowObj = window.open(popupUri,'LiveChatWindow','menubar=0,scrollbars=0,status=1,resizable=0,location=0,toolbar=0,height='+livechatPopupHeight+',width='+livechatPopupWidth+',left='+leftPos+',top='+topPos);

	    if(window.focus && JLiveChat.popupWindowObj) {
		JLiveChat.popupWindowObj.focus();
	    }
	}

	return true;
    },

    closeIFramePopup: function () {
	if(JLiveChat.iframeWindowObj) {
	    if(JLiveChat.iframeOpen || !JLiveChat.closedPermanently) {
		JLiveChat.iframeOpen=false;
		JLiveChat.closedPermanently=true;
		
		if (!jQuery(JLiveChat.iframeWindowObj).hasClass('jlc-hide')) {
		    jQuery(JLiveChat.iframeWindowObj).addClass('jlc-hide');
		}
	    }
	}

	return true;
    },

    showIFramePopup: function () {
	jQuery(JLiveChat.iframeWindowObj).removeClass('jlc-popup-minimized');
    },

    hideIFramePopup: function () {
	if (!jQuery(JLiveChat.iframeWindowObj).hasClass('jlc-popup-minimized')) {
	    jQuery(JLiveChat.iframeWindowObj).addClass('jlc-popup-minimized');
	}
    },
    
    monitorIFramePopup: function () {
	if(!JLiveChat.iframeOpen || JLiveChat.closedPermanently || JLiveChat.currentURI.indexOf("iframe") > -1) {
	    return false;
	}
	
	var params = {
	    task: 'monitor_iframe_popup',
	    no_html: '1',
	    do_not_log: 'true',
	    tmpl: 'component'
	};

	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false,
	    success: function(data) {
		if(data.result == 'close_window') {
		    // The iframe window is attempting to close, close it
		    JLiveChat.closeIFramePopup();
		} else if(data.result == 'minimize_window') {
		    // The iframe window is attempting to minimize, minimize it
		    JLiveChat.hideIFramePopup();
		} else if(data.result == 'restore_window') {
		    // The iframe window is attempting to restore, restore it
		    JLiveChat.showIFramePopup();
		}
		
		setTimeout('JLiveChat.monitorIFramePopup();', 1000);
	    },
	    
	    error: function(theXHR, theTextStatus, theErrorThrown) {
		// Something went wrong, try again in a few seconds
		setTimeout('JLiveChat.monitorIFramePopup();', 1000);
	    }
	});
    },
    
    minimizeWnd: function () {
	var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;
	
	if(!isInIFrame) {
	    return false;
	}

	var params = {
	    task: 'minimize_iframe_popup',
	    no_html: '1',
	    tmpl: 'component'
	};

	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    data: params,
	    cache: false
	});
	
	jQuery('#minimize-window-button').attr('onclick', 'JLiveChat.restoreIFramePopup();');
    },

    restoreIFramePopup: function () {
	var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;
	
	if(!isInIFrame) {
	    return false;
	}
	
	// We are in iframe more
	var params = {
	    task: 'restore_iframe_popup',
	    no_html: '1',
	    tmpl: 'component'
	};

	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    data: params,
	    cache: false
	});
	
	jQuery('#minimize-window-button').attr('onclick', 'JLiveChat.minimizeWnd();');
    },

    closeWnd: function() {
	var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;

	if(!isInIFrame) {
	    window.close();
	} else {
	    // We are in iframe mode
	    var params = {
		task: 'close_iframe_popup',
		no_html: '1',
		tmpl: 'component'
	    };

	    jQuery.ajax({
		type: 'GET',
		url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
		data: params,
		cache: false, 
		success: function(data) {
		    // End Chat Session
		    JLiveChat.endSession();
		}
	    });
	}
    },
    
    endSession: function () {
	if(chatSessionActive == 1) {
	    chatSessionActive = 0;
	    
	    var params = {
		task: 'end_session',
		no_html: '1',
		do_not_log: 'true',
		tmpl: 'component'
	    };

	    jQuery.ajax({
		type: 'GET',
		url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
		data: params,
		cache: false,
		async: false
	    });
	}
    },

    startChatSession: function() {
	if(processingSomething == false)
	{
	    processingSomething = true;

	    var connectingLayer = jQuery('#connecting_layer');
	    var errorLayer = jQuery('#error_layer');
	    var chatNameInput = jQuery('#chat_name');

	    connectingLayer.css('display', 'inline-block');
	    errorLayer.css('display', 'none');
	    
	    var params = {
		task: 'start_session',
		name: chatNameInput.attr('value')
	    };
	    
	    if(specificOperators) {
		params.operators = specificOperators;
	    } else if(specificRouteId) {
		params.routeid = specificRouteId;
	    } else if(specificDepartment) {
		params.department = specificDepartment;
	    }

	    jQuery.ajax({
		type: 'POST',
		url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
		dataType: JLiveChat.xhrDataType,
		data: params,
		cache: false,
		success: function(data) {
		    if(data.success == 0) {
			errorLayer.text(data.error);

			connectingLayer.css('display', 'none');
			errorLayer.css('display', 'inline-block');

			processingSomething = false;
		    } else {
			//	Waiting for Operator Response
			chatName = String(chatNameInput.attr('value'));

			connectingLayer.css('display', 'inline-block');
			errorLayer.css('display', 'none');

			JLiveChat.monitorPendingChatSession();
		    }
		}
	    });
	}

	return false;
    },

    monitorPendingChatSession: function() {
	var params = {
	    task: 'check_session'
	};
	
	var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;

	if(isInIFrame) {
	    params.popup_mode='iframe';
	} else {
	    params.popup_mode='popup';
	}

	if(specificOperators) {
	    params.operators = specificOperators;
	} else if(specificRouteId) {
	    params.routeid = specificRouteId;
	} else if(specificDepartment) {
	    params.department = specificDepartment;
	}

	jQuery.ajax({
	    type: 'POST',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false,
	    success: function(data) {
		var chatActive = parseInt(data.chat_active);
		var chatAccepted = parseInt(data.chat_accepted);

		if(chatActive == 0)
		{
		    // Chat is offline
		    JLiveChat.setOffline();
		}
		else if(chatActive == 1 && chatAccepted == 1)
		{
		    // Chat is accepted and active
		    JLiveChat.setSessionActive();
		}
		else
		{
		    // Recheck
		    setTimeout('JLiveChat.monitorPendingChatSession();', refreshInterval);
		}
	    }
	});
    },

    setOffline: function() {
	processingSomething = false;

	var preChatContainer = jQuery('#jlc_prechat_container');
	var offlineContainer = jQuery('#jlc_offline_container');
	var livechatToolbar = jQuery('#jlc_toolbar_container span');

	preChatContainer.css('display', 'none');
	livechatToolbar.css('display', 'none');
	offlineContainer.css('display', 'block');
    },

    setSessionActive: function() {
	var inChatWrapper = jQuery('#jlc_inchat_container');
	var preChatWrapper = jQuery('#jlc_prechat_container');

	JLiveChat.initTxtInput();

	preChatWrapper.css('display', 'none');
	
	inChatWrapper.css('display', 'block');

	// Increase toolbar height
	jQuery('#livechat_container .livechat_toolbar').addClass('livechat_toolbar_active');
	
	// Show sound icons if sound is enabled
	if(isSoundEnabled) {
	    jQuery('#jlc_toolbar_container span').css('display', 'block');
	}

	jQuery(window).focus();

	jQuery('#msg-input').focus();

	JLiveChat.refreshSession();
    },

    leaveMessage: function () {
	if(processingSomething == false)
	{
	    processingSomething = true;
	    
	    var params = {
		task: 'leave_message',
		message_name: jQuery('#message_name').val()
	    };

	    if(jQuery('#message_phone')) {
		params.message_phone = jQuery('#message_phone').val();
	    }

	    params.message_email = jQuery('#message_email').val();
	    params.message_txt = jQuery('#message_txt').val();

	    if(specificOperators) {
		params.operators = specificOperators;
	    } else if(specificRouteId) {
		params.routeid = specificRouteId;
	    } else if(specificDepartment) {
		params.department = specificDepartment;
	    }

	    jQuery.ajax({
		type: 'POST',
		url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
		dataType: JLiveChat.xhrDataType,
		data: params,
		cache: false,
		success: function(data) {
		    var success = parseInt(data.success);

		    var errorMsg = '';

		    jQuery.each(data.errors, function (index, item) {
			errorMsg += String(item);
			errorMsg += "\n";
		    });

		    alert(errorMsg);

		    if(success == 1) {
			// Message sent
			JLiveChat.closeWnd();
		    } else {
			processingSomething = false;
		    }
		}
	    });
	}

	return false;
    },

    sendMsg: function(msg) {
	clientIsTyping = 0;
	JLiveChat.visitorSentLastMessage=true;

	var resetInput = false;

	if(!msg) {
	    resetInput = true;

	    msg = String(jQuery('#msg-input').val());
	}

	// Clear Box
	if(resetInput) {
	    var resetFunc = function() {
		jQuery('#msg-input').val('');
	    };

	    setTimeout(resetFunc, 500);
	}

	var params = {
	    task: 'send_message',
	    m: msg
	};

	jQuery.ajax({
	    type: 'POST',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false,
	    success: function() {
		
	    },
	    error: function(theXHR, theTextStatus, theErrorThrown) {
		alert('Your last message was not sent! Please try again.');

		jQuery('#msg-input').val(msg);
	    }
	});

	return false;
    },

    initTxtInput: function() {
	jQuery('#msg-input').bind('keydown', function (event) {
	    if(event.keyCode == 13) {
		//  user pressed enter on the keyboard
		JLiveChat.sendMsg(null);
	    } else {
		clientIsTyping = 1;
		
		if(typingTimeoutTimer) {
		    clearTimeout(typingTimeoutTimer);
		}

		var typingTimeoutFunc = function() {
		    clientIsTyping = 0;
		};

		typingTimeoutTimer = setTimeout(typingTimeoutFunc, 900);
	    }
	});
    },

    refreshSession: function () {
	var params = {
	    task: 'refresh_session',
	    client_is_typing: parseInt(clientIsTyping)
	};
	
	var isInIFrame = (JLiveChat.currentURI.indexOf("iframe") > -1) ? true : false;

	if(isInIFrame) {
	    params.popup_mode='iframe';
	} else {
	    params.popup_mode='popup';
	}

	jQuery.ajax({
	    type: 'GET',
	    url: JLiveChat.websiteRoot+'/'+JLiveChat.serverURI,
	    dataType: JLiveChat.xhrDataType,
	    data: params,
	    cache: false,
	    success: function(data) {
		if(data) {
		    if(data.is_typing.length > 0) {
			// Operator is typing
			jQuery('#status-display').css('display', 'block');
		    } else {
			// Operator is not typing
			jQuery('#status-display').css('display', 'none');
		    }

		    chatSessionActive = parseInt(data.is_active);

		    if(chatSessionMdate != parseInt(data.chat_mdate))
		    {
			chatSessionMdate = parseInt(data.chat_mdate);

			jQuery('#session-content-display-inner').html(data.chat_content);
			
			if(isSoundEnabled && !JLiveChat.visitorSentLastMessage) {
			    JLiveChat.playNewChatSoundNotification();
			}

			// Reset visitor sent last message flag
			JLiveChat.visitorSentLastMessage = false;

			// Make popup window flash in the task bar
			if(clientIsTyping == 0) {
			    jQuery(window).focus();

			    jQuery('#msg-input').focus();
			}

			// Keep scroll position at the bottom
			jQuery('#session-content-display').animate({
			    scrollTop: jQuery('#session-content-display-inner').height()
			}, 1200);
		    }

		    if(chatSessionActive == 1) {
			setTimeout('JLiveChat.refreshSession();', refreshInterval);
		    } else {
			//	Chat session is not active anymore
			jQuery('#status-display').css('display', 'none');
			jQuery('#msg-input').attr('disabled', true);
		    }
		}
	    },
	    
	    error: function(theXHR, theTextStatus, theErrorThrown) {
		try {
		    if(chatSessionActive == 1) {
			setTimeout('JLiveChat.refreshSession();', refreshInterval);
		    } else {
			//	Chat session is not active anymore
			jQuery('#status-display').css('display', 'none');
			jQuery('#msg-input').attr('disabled', true);
		    }
		} catch(err) {
		    // Ignore error
		}
	    }
	});
    },

    toggleSoundNotifications: function() {
	var muteIcon = jQuery('#mute_icon');

	if(muteIcon) {
	    if(isSoundEnabled) {
		// Sound is already enabled, so mute it
		isSoundEnabled = false;
		
		muteIcon.removeClass('unmute_sound_icon');
		muteIcon.addClass('mute_sound_icon');
	    } else {
		// Sound is muted, so enable it
		isSoundEnabled = true;

		muteIcon.removeClass('mute_sound_icon');
		muteIcon.addClass('unmute_sound_icon');
	    }
	}
    },

    playNewChatSoundNotification: function() {
	try {
	    var soundObject = soundManager.getSoundById('newMessageSound');

	    soundObject.play();
	} catch(e) {
	    // Do nothing
	}
    }
};

// This object has been deprecated, but exists for legacy code purposes
var AutoPopupChecker = {
    close: function (closeDelay) {
	JLiveChat.closeAutopopup(closeDelay);
    }
};

// Open livechat window function wrapper
function requestLiveChat(popupUri, mode) {
    JLiveChat.openLiveChatWindow(popupUri, mode);
}

function prepYUI() {
    var bodyObj = document.getElementsByTagName('body').item(0);

    if(bodyObj) {
	bodyObj.className='yui-skin-sam';
    }
}

function closeWnd() {
    return JLiveChat.closeWnd();
}

function sendMsg(msg) {
    return JLiveChat.sendMsg(msg);
}

function sendMsgAndClearEvent(eventObject) {
    return JLiveChat.sendMsg();
}

function leaveMessage() {
    return JLiveChat.leaveMessage();
}

function startChatSession() {
    return JLiveChat.startChatSession();
}

function setSessionActive() {
    return JLiveChat.setSessionActive();
}

function setOffline() {
    return JLiveChat.setOffline();
}

function minimizeWnd() {
    return JLiveChat.minimizeWnd();
}

function attachCloseEvent() {
    jQuery(window).bind('beforeunload', function() {
	JLiveChat.endSession();
    });

    jQuery(window).bind('unload', function() {
	JLiveChat.endSession();
    });
}

function setPopupWindowStyles() {
    if(jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6) {
	jQuery('#msg-input').css('width', '479px');
    } else {
	jQuery('#msg-input').css('width', '99.5%');
    }

    jQuery('#msg-input').css('height', '40px');
    jQuery('#msg-input').css('padding', '0px');
    jQuery('#msg-input').css('margin', '0px');
    jQuery('#msg-input').css('border', '1px solid #383C3F');
    jQuery('#msg-input').css('float', 'left');
    jQuery('#msg-input').css('font-size', '13px');
    jQuery('#msg-input').css('font-family', 'Arial, helvetica, sans');

    // Set cross browser compatible styles
    if(jQuery.browser.msie) {
	jQuery('#jlc_prechat_container input[type=text]').css('width', '470px');
	jQuery('#jlc_prechat_container input[type=text]').css('border', '1px solid #888888');
	jQuery('#jlc_prechat_container input[type=text]').css('font-size', '15px');
	jQuery('#jlc_prechat_container input[type=text]').css('padding', '4px 0 4px 0');
	jQuery('#jlc_prechat_container input[type=text]').css('margin', '2px 0 0 0');

	jQuery('#jlc_offline_container input[type=text]').css('border', '1px solid #888888');
	jQuery('#jlc_offline_container input[type=text]').css('font-size', '15px');
	jQuery('#jlc_offline_container input[type=text]').css('padding', '3px 0 3px 3px');
	jQuery('#jlc_offline_container input[type=text]').css('margin', '2px 0 0 0');
	jQuery('#jlc_offline_container textarea').css('padding', '1px');
	jQuery('#jlc_offline_container textarea').css('width', '98.9%');
	jQuery('#jlc_offline_container textarea').css('height', '40px');

	setTimeout('checkYUIMenus()', 10);
    }

    // Init start chat session form elements
    if(jQuery('#chat_name')) {
	jQuery('#chat_name').bind('keydown', function (event) {
	    if(event.keyCode == 13) {
		//  user pressed enter on the keyboard
		startChatSession();
	    }
	});
    }

    // Init Leave Message Form Elements
    if(jQuery('#message_name')) {
	jQuery('#message_name').bind('keydown', function (event) {
	    if(event.keyCode == 13) {
		//  user pressed enter on the keyboard
		leaveMessage();
	    }
	});
    }

    if(jQuery('#message_email')) {
	jQuery('#message_email').bind('keydown', function (event) {
	    if(event.keyCode == 13) {
		//  user pressed enter on the keyboard
		leaveMessage();
	    }
	});
    }
    
    if(jQuery('#message_phone')) {
	jQuery('#message_phone').bind('keydown', function (event) {
	    if(event.keyCode == 13) {
		//  user pressed enter on the keyboard
		leaveMessage();
	    }
	});
    }
}

function checkYUIMenus() {
    JLiveChat.initialize();
    
    jQuery('#route-filter-menubutton').css('width', '470px');
    jQuery('#route-filter-menubutton button').css('width', '470px');
    jQuery('#yui-gen0').css('width', '470px');

    if(jQuery.browser.msie && parseInt(jQuery.browser.version) <= 6) {
	jQuery('#route-filter-container').css('margin-left', '-2px');
    }

    if(jQuery.browser.msie && parseInt(jQuery.browser.version) == 7) {
	jQuery('#route-filter-container').css('margin-left', '-1px');
    }
}

var onRouteDepartmentSelectedMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    specificDepartment = oMenuItem.value;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onRouteOperatorSelectedMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    specificOperators = oMenuItem.value;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};
