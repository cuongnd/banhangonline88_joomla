/**
 * Eventstats class, manage stats edit view activities
 * 
 * @package JCHAT::WEBRTC::components::com_jchat
 * @subpackage js
 * @author Joomla! Extensions Store
 * @copyright (C)2014 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//'use strict';
(function($) {
	var Webrtc = function(options) {
		/**
		 * This bind object
		 * 
		 * @access private
		 * @var Object
		 */
		var bindContext = this;
		
		/**
		 * Main container tooltip for this peer user video chat session
		 * 
		 * @access private
		 * @var Object jQuery
		 */
		var $tooltipContainer;
		
		/**
		 * Store the full support state for WebRTC API
		 * 
		 * @access private
		 * @var Boolean
		 */
		var supportFullWebRTC;
		
		/**
		 * Ringing effect time interval
		 * 
		 * @access private
		 * @var Object
		 */
		var ringingInterval;
		
		/**
		 * Bandwidth timeout
		 * 
		 * @access private
		 * @var Object
		 */
		var bandwidthInterval;
		
		/**
		 * Duration interval resource
		 * 
		 * @access private
		 * @var Object
		 */
		var durationInterval;
		
		/**
		 * Bandwidth session bytes sent
		 * 
		 *  @access private
		 *  @var Float
		 */
		var bandwidthSentBytes = 0;
		
		/**
		 * Log status messages on debug enabled
		 * 
		 * @access private
		 * @var Boolean
		 */
		var debugLogging = options.debugEnabled;
		
		/**
		 * Log status messages on debug enabled
		 * 
		 * @access private
		 * @var Boolean
		 */
		var jsonLiveSite = options.jsonLiveSite;

		/**
		 * RTCPeerConnection object
		 * 
		 * @access private
		 * @var RTCPeerConnection
		 */
		var peerConnection = null;

		/**
		 * Peer candidates array
		 * 
		 * @access private
		 * @var Array
		 */
		var peerCandidates = new Array();
		
		/**
		 * Local peer stream retrieved to send to remote peer
		 * 
		 * @access private
		 * @var Stream
		 */
		var localGotStream;

		/**
		 * Candidates counter
		 * 
		 * @access private
		 * @var int
		 */
		var candidatesCounter = 0;

		/**
		 * STUN and TURN servers configurations ICE Framework requires them for
		 * NAT traversal, if devices are behind a NAT router TURN server is used
		 * as a fallback to relay video and audio stream
		 * 
		 * @access private
		 * @var Object
		 */
		var servers = {};
		
		/**
		 * Start call timeout, avoid to have ringing forever waiting answer
		 * 
		 * access private
		 * @var object
		 */
		var startCallTimeout = null;
		
		/**
		 * End call timeout, avoid to have infinite calls if other user close browser or connection is lost
		 * 
		 * access private
		 * @var object
		 */
		var endCallTimeout = null;
		
		/**
		 * The RTC Messages timeout
		 * 
		 * access private
		 * @var object
		 */
		var RTCMessagesTimeout = null;
		
		/**
		 * The other peer identifier, needs to be passed server side to store target informations
		 * 
		 * @access private
		 * @var String
		 */
		var remotePeer = null;
		
		/**
		 * Received sdp message, can be offer or answer
		 * 
		 * @access private
		 * @var Object
		 */
		var receivedSdpMessage = null;
		
		/**
		 * Received ICE candidates, can be offer candidates or answer candidates
		 * 
		 * @access private
		 * @var Object
		 */
		var receivedICECandidates = null;

		/**
		 * The call status, based on both peer connection
		 * 
		 * @access private
		 * @var Boolean
		 */
		var callStatus = false;
		
		/**
		 * Promise of signaling channel resolved
		 * 
		 * @access private
		 * @var Boolean
		 */
		var promiseResolved = false;
		
		/**
		 * Local stream audio context for the gain mic level
		 * 
		 * @access private
		 * @var Object
		 */
		var streamAudioContext = null;
		
		/**
		 * Gain filter for mic using web audio API
		 * 
		 * @access private
		 * @var Object
		 */
		var gainFilterNode = null;
		
		/**
		 * State of local video webcam
		 * 
		 * @access private
		 * @var Boolean
		 */
		var thispeerVideoCamState = 1;
		
		/**
		 * State of remote video webcam
		 * 
		 * @access private
		 * @var Boolean
		 */
		var otherpeerVideoCamState = 1;
		
		/**
		 * Detected connection speed
		 * 
		 * @access private
		 * @var Number
		 */
		var connectionSpeed = 0;
		
		/**
		 * Connection speed related to webcam media constraints
		 * 
		 * @access private
		 * @var Object
		 */
		var connectionSpeedConstraints = {
			'Highest' : {minValue : 0, maxValue : 0.5},
			'High' : {minValue : 0.5, maxValue : 1},
			'Average' : {minValue : 1, maxValue : 1.5},
			'Low' : {minValue : 1.5, maxValue : 3},
			'Lowest' : {minValue : 3, maxValue : 999}
		};
		
		/**
		 * Webcam media constraints
		 * 
		 * @access private
		 * @var Object
		 */
		var webCamConstraints = {
			'Auto' : true,
			'Highest' : {
			    mandatory: {
		    		minWidth: 1280,
		    		minHeight: 720
			    },
			    optional: [
	               { frameRate: 60 },
	               { facingMode: "user" }
	             ]
			},
			'High' : {
			    mandatory: {
		    		minWidth: 1024,
		    		minHeight: 720
			    },
			    optional: [
	               { frameRate: 60 },
	               { facingMode: "user" }
	             ]
			},
			'Average' : {
			    mandatory: {
			    	maxWidth: 640,
			    	maxHeight: 480
			    },
			    optional: [
	               { frameRate: 30 },
	               { facingMode: "user" }
	             ]
			},
			'Low' : {
			    mandatory: {
			    	maxWidth: 320,
			    	maxHeight: 240
			    },
			    optional: [
	               { frameRate: 30 },
	               { facingMode: "user" }
	             ]
			},
			'Lowest' : {
			    mandatory: {
			    	maxWidth: 160,
			    	maxHeight: 120
			    },
			    optional: [
	               { frameRate: 15 },
	               { facingMode: "user" }
	             ]
			}
		};
		
		/**
		 * Webcam new media constraints based on new specifications
		 * 
		 * @access private
		 * @var Object
		 */
		var webCamNewConstraints = {
			'Auto' : true,
			'Highest' : {
				video : {
				    width: { min: 1024, ideal: 1280, max: 1920 },
				    height: { min: 640, ideal: 720, max: 1080 }
				},
	    		frameRate: 60,
	            facingMode: "user"
			},
			'High' : {
				video : {
				    width: { min: 960, ideal: 1024, max: 1280 },
				    height: { min: 640, ideal: 720, max: 960 }
				},
	    		frameRate: 60,
	            facingMode: "user"
			},
			'Average' : {
				video : {
				    width: { min: 320, ideal: 640, max: 960 },
				    height: { min: 240, ideal: 480, max: 640 }
				},
	    		frameRate: 30,
	            facingMode: "user"
			},
			'Low' : {
				video : {
				    width: 320,
				    height: 240
				},
	    		frameRate: 30,
	            facingMode: "user"
			},
			'Lowest' : {
				video : {
				    width: 160,
				    height: 120
				},
	    		frameRate: 15,
	            facingMode: "user"
			}
		};
		
		/**
		 * Webcam media constraints dropdown
		 * 
		 * @access private
		 * @var String
		 */
		var webCamConstraintsSelect = $('<select id="jchat_webrtc_camquality"></select>');

		/**
		 * Webcam media constraints dropdown value
		 * 
		 * @access private
		 * @var String
		 */
		var webCamQuality = true;
		
		/**
		 * HTMLVideo element
		 * 
		 * @access public
		 * @var HTMLVideoElement
		 */
		this.localVideo = null;

		/**
		 * RTCPeerConnection main object
		 * 
		 * @access public
		 * @var Object
		 */
		this.remoteVideo = null;

		/**
		 * Caller role
		 * 
		 * @access public
		 * @var Object
		 */
		this.caller = false;
		
		/**
		 * Callee role
		 * 
		 * @access public
		 * @var Object
		 */
		this.callee = false;
		
		/**
		 * RTCPeerConnection main object
		 * 
		 * @access public
		 * @var Object
		 */
		window.RTCPeerConnection = window.RTCPeerConnection || window.webkitRTCPeerConnection || window.mozRTCPeerConnection;

		/**
		 * GetUserMedia main object
		 * 
		 * @access public
		 * @var Object
		 */
		// If everything defined do nothing (Chrome 46+ with web experimental activated or FF)
		if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {} else {
			// If mediaDevices completely not defined go on and define a new Object (Opera)
			if(!navigator.mediaDevices) {
				navigator.mediaDevices = new Object();
			}
			// Add the getUserMedia property (Chrome without web experimental active and Opera)
			navigator.mediaDevices.getUserMedia = ((navigator.getUserMedia || navigator.mozGetUserMedia || navigator.webkitGetUserMedia) ? 
				function(constraints) {
					return new $.Deferred(function(defer){
							(navigator.getUserMedia ||
							 navigator.mozGetUserMedia ||
							 navigator.webkitGetUserMedia).call(navigator, constraints, defer.resolve, defer.reject);
					});
				} : null);
		};

		/**
		 * Register user events for interface controls
		 * 
		 * @access private
		 * @return Void
		 */
		var registerEvents = function() {
			// Start call handler
			$('#jchat_start_accept_call', $tooltipContainer).on('click.jchatwebrtc', {scope: this}, function(jqEvent){
				// Check if button is in disabled state
				if($(this).hasClass('jchat_disabled')) {
					return false;
				}
				
				// Check callee state and post data to server using signaling channel
				if(!jqEvent.data.scope.callee) {
					// Set caller true, this peer is starting a new call caller = true
					jqEvent.data.scope.caller = true;
					
					// Play sound
					JChatNotifications.playStartWebrtcCall();
					
					// Start new call
					startCall();
				} else {
					// Stop ringing sound
					resetSounds();
					
					// Play accept sound
					JChatNotifications.playAcceptWebrtcCall();
					
					// Accept incoming call, this peer is the callee
					acceptCall.call(jqEvent.data.scope);
				}
			});
			
			// End call handler
			$('#jchat_end_call', $tooltipContainer).on('click.jchatwebrtc', {scope: this}, function(jqEvent, details){
				// Check if button is in disabled state
				if($(this).hasClass('jchat_disabled')) {
					return false;
				}
				
				// Reset state of roles
				jqEvent.data.scope.caller = false;
				jqEvent.data.scope.callee = false;
				promiseResolved = false;
				
				// End the call and flush the signaling channel
				endCall(details);
			});
			
			// Volume control range slider
			$('#jchat_webrtc_volume input', $tooltipContainer).on('mousemove.jchatwebrtc', function(jqEvent){
				// Calculate decimal value for video html element
				var volume = $(this).val() / 100;
				
				// Set the video element volume value
				$('#jchat_remotevideo').get(0).volume = volume;
				
				// Store the local storage value
				$.jStorage.set('jchat_webrtc_volume', volume);
			});
			
			// Mic control range slider
			$('#jchat_webrtc_mic input', $tooltipContainer).on('mousemove.jchatwebrtc', function(jqEvent){
				// Calculate decimal value for video html element
				var volume = $(this).val() / 100;

				// Set the mic volume value
				// Check first of all if WebAudio API are supported
			    if (window.AudioContext) {
			    	gainFilterNode.gain.value = volume;
			    }
			    
			    // Firefox mic interruptor fallback with feature detection
			    if(localGotStream && navigator.mozGetUserMedia) {
			    	localGotStream.getAudioTracks()[0].enabled = !!volume;
			    }
				
				// Store the local storage value
				$.jStorage.set('jchat_webrtc_mic', volume);
			});
			
			// Video webcam switcher on/off
			$('#jchat_webrtc_video', $tooltipContainer).on('change.jchatwebrtc', function(jqEvent){
				// Store the local storage value
				var state = $(this).prop('checked');
				// Set class property
				thispeerVideoCamState = state ? 1 : 0;
				$.jStorage.set('jchat_webrtc_videocam', thispeerVideoCamState);
				
				// Now use extended signaling channel to update the state of videocam
				var updateSessionPromise = $.Deferred(function(defer) {
					$.ajax({
						type : "POST",
						url : jsonLiveSite,
						dataType : 'json',
						context : this,
						data : {task : 'webrtc.updateEntity', 'videocam' : thispeerVideoCamState}
					}).done(function(response, textStatus, jqXHR) {
						if(!response.storing.status) {
							// Error found
							defer.reject(response.storing.exception_message, textStatus);
							return false;
						}
						
						// Check response all went well
						defer.resolve();
					}).fail(function(jqXHR, textStatus, errorThrown) {
						// Error found
						var genericStatus = textStatus[0].toUpperCase() + textStatus.slice(1);
						defer.reject('-' + genericStatus + '- ' + errorThrown);
					});
				}).promise();

				updateSessionPromise.then(function(responseData) {
					// Disable local video and show poster, based on chat config param
					if(options.hideWebcamWhenDisabled) {
						if(!thispeerVideoCamState) {
							// Check if the parameter require to hide all
							if(options.hideWebcamWhenDisabled == 2) {
								$('#jchat_wrapper_localvideo').hide();
							} else {
								$('#jchat_localvideo').after('<div id="jchat_localvideo_placeholder"></div>');
								// Resize the placeholder
								$('#jchat_localvideo_placeholder').height($('#jchat_localvideo').height());
							}
							$('#jchat_localvideo').hide();
						} else {
							// Check if the parameter require to hide all
							if(options.hideWebcamWhenDisabled == 2) {
								$('#jchat_wrapper_localvideo').show();
							} else {
								$('#jchat_localvideo').next('#jchat_localvideo_placeholder').remove();
							}
							$('#jchat_localvideo').show();
						}
					}
					
					// Disable the tab led
					$('#jchat_wrapper_localvideo').toggleClass('active');
					
					// Log status
					if (debugLogging) {
						console.log('Updated session videocam on server');
					}
				}, function(errorText, error) {
					// Log status
					if (debugLogging) {
						console.log('Error updating session videocam on server: ' + errorText);
					}
				});
			});

			// Webcam resolution change event
			$('#jchat_webrtc_camquality').on('change', function(jqEvent){
				$.jStorage.set('jchat_webrtc_videocam_quality', $(this).val());
				$.jStorage.set('jchat_remotepeer_webrtc_tooltip', remotePeer);
				
				// Lose focus
				$(this).blur();
				
				// Restart media stream refreshing page
				window.location.reload();
			});
		};
		
		/**
		 * Create a new peerConnection object, valid and needed in both cases caller or callee
		 * 
		 * @access private
		 * @return Void
		 */
		var createPeerConnection = function() {
			// Now create a new peer connection object
			try {
				// New peer connection
				peerConnection = new RTCPeerConnection(servers);
				
				// Record ICE candidates callback
				peerConnection.onicecandidate = gotIceCandidate;
				// Ensure to post always ice candidates if the user agent doesn't throw the complete event
				checkIceCandidatesSent();
				
				// Add the local media stream, it has still the local audio track to send mic to remote peer
				peerConnection.addStream(localGotStream);
				
				// Regsiter callback when remote stream is ready
				peerConnection.onaddstream = function(event) {
					gotRemoteStream(event);
					// Show stats procedure if active
					if(options.showWebRTCStats) {
						getStats(peerConnection);
					}
				};
				
				// Monitor connection state estabilished
				peerConnection.oniceconnectionstatechange = function() {
					// Check if valid peerConnection object
					if(peerConnection) {
						// Log status
						if (debugLogging) {
							console.log('Connection: ' + peerConnection.iceConnectionState);
						}
						
						// Connection completed, show call started message
						if(peerConnection.iceConnectionState == 'completed') {
							showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_call_started, 'jchat_info_closer');
						}
						
						// Connection completed, show call started message
						if(peerConnection.iceConnectionState == 'connected') {
							showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_connection_active, 'jchat_info_closer');
							
							// Connection connected from both perspective caller/callee, start the duration timer
							durationTimer(1);
						}
						
						// Connection disconnected, something went wrong on net or other peer closed browser?
						if(peerConnection.iceConnectionState == 'disconnected') {
							// Log status
							if (debugLogging) {
								console.log(peerConnection.iceConnectionState);
							}
							// Clear current timeout if any
							if(typeof endCallTimeout == 'number') {
								clearTimeout(endCallTimeout);
							}
							endCallTimeout = setTimeout(function(){
								// Check again if peerConnection still exists, a user could have hanged up!
								if(peerConnection) {
									if(peerConnection.iceConnectionState == 'disconnected') {
										showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_call_disconnected, 'jchat_info_closer');
										// End call at this stage
										$('#jchat_end_call', $tooltipContainer).trigger('click', [jchat_call_disconnected]);
									}
								}
							}, options.endCallTimeout);
						}
						
						// Connection failed, ICE candidates are probably not suitable for remote peer to peer
						if(peerConnection.iceConnectionState == 'failed') {
							showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_connection_failed, 'jchat_info_closer');
						}
					}
				};
			} catch(exception) {
				// Stop ringing sounds, both caller or callee
				resetSounds();
				// Reset buttons
				resetButtons();
				
				showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_error_creating_connection, 'jchat_exceptions_closer');

				// Log status
				if (debugLogging) {
					console.log(exception.message);
				}
				return;
			}
			
			// If peerConnection object is still not created return immediately, something went wrong
			if(!peerConnection) {
				// Stop ringing sounds, both caller or callee
				resetSounds();
				// Reset buttons
				resetButtons();
				
				showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_error_creating_connection, 'jchat_exceptions_closer');
				return;
			}
		};
		
		/**
		 * Start a new call
		 * 
		 * @access private
		 * @return Void
		 */
		var startCall = function() {
			// Check if a valid local stream has got
			if(!localGotStream) {
				showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_missing_local_stream, 'jchat_exceptions_closer');
				return;
			}
			
			// Create a new peerConnection object and initialize it
			createPeerConnection();
			
			// Ensure object is valid
			if(!peerConnection) {
				return;
			}
			
			// Start a new call process, register callback that is involved during sdp creation and signaling channel send store
			peerConnection.createOffer(gotSdpDescription, onSignalingError);
			
			// Show calling user interface
			showRTCMessages('jchat_infouser_webrtc', 'jchat_async_loader', 'jchat_tooltip_innermsg', jchat_connecting);
			
			// Set buttons and interval for the caller
			bindContext.setCallerRingingButton();
			
			// Set a timeout for the started call if no answer by other peer
			startCallTimeout = setTimeout(function(){
				// End call at this stage
				$('#jchat_end_call', $tooltipContainer).trigger('click', [jchat_noanswer]);
			}, options.startCallTimeout);
			
			// Disable the cam quality dropdown, no changes during calls
			$('#jchat_webrtc_camquality').attr('disabled', true);
			
			// Log status
			if (debugLogging) {
				console.log('New call started');
			}
		};
		
		/**
		 * Accept an incoming call
		 * 
		 * @access private
		 * @return Void
		 */
		var acceptCall = function() {
			// Create a new peerConnection object and initialize it if not already exists
			if(!peerConnection) {
				createPeerConnection();
			}
			
			// Check if accept call is by callee, in this case set and create an sdp answer
			if (this.callee) { // SDP Offer
				// Correct sdp desc received
        		if(receivedSdpMessage) {
        			peerConnection.setRemoteDescription(new RTCSessionDescription(JSON.parse(receivedSdpMessage)), function(){
        				// Log status
        				if (debugLogging) {
        					console.log('Set remote description by callee');
        				}
        			}, function(error){
        				// Log status
        				if (debugLogging) {
        					console.log('Error during set remote description:' + error);
        				}
        			});
        		}
        		// Now create the sdp answer
        		peerConnection.createAnswer(gotSdpDescription, onSignalingError);
        		
        		// Ensure call/end button are reset
        		resetButtons(true);
        		// Invert status after call started
				$('#jchat_start_accept_call', $tooltipContainer).addClass('jchat_disabled');
				$('#jchat_end_call', $tooltipContainer).removeClass('jchat_disabled');
				
				// Show user notice
				showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_call_starting, 'jchat_info_closer');
				
				// Log status
				if (debugLogging) {
					console.log('Exchanged SDP/ICE and call started between 2 peer');
				}
        	} else if (this.caller) { // SDP Answer
        		if(receivedSdpMessage) {
        			peerConnection.setRemoteDescription(new RTCSessionDescription(JSON.parse(receivedSdpMessage)), function(){
        				// Log status
        				if (debugLogging) {
        					console.log('Set remote description by caller');
        				}
        			}, function(error){
        				// Log status
        				if (debugLogging) {
        					console.log('Error during set remote description:' + error);
        				}
        			});
        		}
        	} 

			// In both cases ice candidates have to be set, caller or callee
        	if(receivedICECandidates) {
        		var candidates = JSON.parse(receivedICECandidates);
        		$.each(candidates, function(index, candidate){
        			var rtcCandidate = new RTCIceCandidate(candidate);
        			try{
        				peerConnection.addIceCandidate(rtcCandidate);
        			} catch(exception) {
        				// Log status
        				if (debugLogging) {
        					console.log(exception.message);
        				}
        			}
        		});
        	}
		};
		
		/**
		 * End the current call
		 * 
		 * @access private
		 * @param String details
		 * @return Void
		 */
		var endCall = function(details) {
			// Get the end call reason desc
			var closedDetails = details ? details : jchat_connection_closed;
			
			// Close the peer connection
			try {
				if(peerConnection) {
					// Show calling user interface
					showRTCMessages('jchat_infouser_webrtc', 'jchat_async_loader', 'jchat_tooltip_innermsg', jchat_closing_connection);
				}
				
				// Close if opened
				peerConnection.close();
				peerConnection = null;
				peerCandidates = new Array();
				candidatesCounter = 0;
			} catch(exception) {
				// Log status
				if (debugLogging) {
					console.log(exception.message);
				}
				
				// Log status
				if (debugLogging) {
					console.log('Call/connection ended or refused');
				}
			}
			
			// Delete session on server
			var deleteSessionPromise = $.Deferred(function(defer) {
				$.ajax({
					type : "POST",
					url : jsonLiveSite,
					dataType : 'json',
					context : this,
					data : {task : 'webrtc.deleteEntity', ids : remotePeer}
				}).done(function(response, textStatus, jqXHR) {
					if(!response.storing.status) {
						// Error found
						defer.reject(response.storing.exception_message, textStatus);
						return false;
					}
					
					// Check response all went well
					defer.resolve();
				}).fail(function(jqXHR, textStatus, errorThrown) {
					// Error found
					var genericStatus = textStatus[0].toUpperCase() + textStatus.slice(1);
					defer.reject('-' + genericStatus + '- ' + errorThrown);
				});
			}).promise();

			deleteSessionPromise.then(function(responseData) {
				// Show user notice
				showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', closedDetails, 'jchat_info_closer');
				
				// Stop the duration timer here
				durationTimer(0);
				
				// Log status
				if (debugLogging) {
					console.log('Call ended, connection closed and session deleted on server');
				}
			}, function(errorText, error) {
				// Show user notice
				showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_connection_close_error, 'jchat_exceptions_closer');
				
				// Log status
				if (debugLogging) {
					console.log('Error deleting session on server: ' + errorText);
				}
			}).always(function(){
				// Stop ringing sounds, both caller or callee
				resetSounds();
				
				// Play sound
				JChatNotifications.playEndWebrtcCall();
				
				// Stop button ringing
				resetButtons();
				
				// Reset video placeholder
				resetVideo();
				
				// Reset the bandwidth meter
				resetBandWidth();
			});
		};
		
		/**
		 * Manage the duration timer start, stop and rendering
		 * 
		 * @access private
		 * @param Integer state
		 * @return Void
		 */
		var durationTimer = function(state) {
			// Switch state
			if(state == 1) {
				$('#jchat_webrtc_bandwidth').after('<div id="jchat_webrtc_duration"><span></span></div>');
				$('#jchat_webrtc_duration').hide();
				var startTime = Date.now();
				durationInterval = setInterval(function(){
					var currentTime = Date.now();
					var differenceDate = new Date(currentTime - startTime);
					var elapsed = differenceDate.toUTCString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");;
					$('#jchat_webrtc_duration > span').text(elapsed);
					$('#jchat_webrtc_duration').show();
				}, 1000);
			} else {
				$('#jchat_webrtc_duration').remove();
				if(typeof(durationInterval) !== 'undefined') {
					clearInterval(durationInterval);
				}
			}
		};
		
		/**
		 * Local video and mic have been retrieved and authorized, now show on
		 * local video source
		 * 
		 * @access private
		 * @return Void
		 */
		var gotVideoStream = function(stream) {
			// Clone stream with no mic output MediaStreamTrack
			var localPeerVideoElementStream;
			
			// Assign local stream used by peerConnection to class scope property
			localGotStream = stream;
			
			// Manage the mic control gain using WebAudio API if the interface implements add/remove tracks
			if(localGotStream.addTrack && !navigator.mozGetUserMedia) {
				gainMicControl();
			}
			
			// Start VU Meter controls
			if(options.showWebRTCVUMeter) {
				micVUMeter();
			}
			
			// Allow call start
			$('#jchat_start_accept_call', $tooltipContainer).removeClass('jchat_disabled');
			
			// Retrieve the native local video HTMLVideoElement rendered inside popup
			bindContext.localVideo = $('#jchat_localvideo').get(0);
			
			// Associate the local video element with the retrieved localGotStream
			// Set volume to zero to avoid microphone feedback
			localPeerVideoElementStream = localGotStream;
			bindContext.localVideo.volume = 0;
			
			// Change the mic volume to use it as interruptor if add tracks to media stream is not supported but only enabled property
			if (navigator.mozGetUserMedia) {
				$('#jchat_webrtc_mic input').attr('step', 100);
				// Always active by default for all values if Firefox is detected
				if($.jStorage.get('jchat_webrtc_mic') == null) {
					$('#jchat_webrtc_mic input').val(100);
				}
			}

			// Now assign
			if (window.URL) {
				bindContext.localVideo.src = URL.createObjectURL(localPeerVideoElementStream);
			} else {
				bindContext.localVideo.src = localPeerVideoElementStream;
			}

			// Auto size placeholder height based on HTMLVideoElement height
			$(bindContext.localVideo).on('canplay', function(jqEvent){
				setTimeout(function(){
					var visibleWebcamVideoHeight = $('#jchat_localvideo:visible').height();
					if(visibleWebcamVideoHeight) {
						$('#jchat_remotevideo_placeholder').height(visibleWebcamVideoHeight);
					}
				}, 500);
			});
			
			// Ensure that the local video stream callback is not called by a Firefox instance without webcam AKA no video track in the stream
			if(!!navigator.mozGetUserMedia && !localGotStream.getVideoTracks()[0]) {
				// Show the not supported poster
				$('#jchat_localvideo').attr('poster', jchat_livesite + 'components/com_jchat/images/default/placeholder.png');
				// Turn off webcam
				$('#jchat_webrtc_video').prop('checked', false).off('.jchatwebrtc').next().attr('for', null);
				// Show user notice
				showRTCMessages('jchat_top_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_nowebcam_detected);
			}
			
			// If Chrome/Webkit ensure that the video track is not ended
			setTimeout(function() {
				var haslocalStreamVideoTrack = !!localGotStream.getVideoTracks()[0];
				if(!navigator.mozGetUserMedia && haslocalStreamVideoTrack) {
					// Check if the video stream is valid, otherwise hardware is busy or not available
					if(localGotStream.getVideoTracks()[0].readyState === 'ended') {
						// Show the not supported poster
						$('#jchat_localvideo').attr('poster', jchat_livesite + 'components/com_jchat/images/default/placeholder.png');
						// Show user notice
						showRTCMessages('jchat_top_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_hardware_unavailable);
						// Log status
						if (debugLogging) {
							console.log('Video track state:' + localGotStream.getVideoTracks()[0].readyState);
						}
					}
				}
			}, 500);
			
			// Now remove the tooltip to grant cam/mic access
			$('.jchat_infouser_webrtc.top').fadeOut(500);
			
			// Activate the tab led
			if(thispeerVideoCamState) {
				$('#jchat_wrapper_localvideo').toggleClass('active');
			}
			
			// Log status
			if (debugLogging) {
				console.log('Local video stream started, video track state');
			}
		};
		
		// Handler to be called as soon as the remote stream becomes available
		function gotRemoteStream(event) {
			bindContext.remoteVideo = $('#jchat_remotevideo').get(0);
			
			// Check if other peer webcam is enabled and a video track is found on the MediaStream object
			if(otherpeerVideoCamState && event.stream.getVideoTracks()[0]) {
				$('#jchat_remotevideo_placeholder').hide();
				$('#jchat_remotevideo').show();
			}
			
			// Associate the remote video element with the retrieved stream
			if (window.URL) {
				// Chrome
				bindContext.remoteVideo.src = window.URL.createObjectURL(event.stream);
			} else {
				// Firefox
				bindContext.remoteVideo.src = event.stream;
			}
			
			// Activate the tab led
			$('#jchat_wrapper_remotevideo').toggleClass('active');
			
			// Log status
			if (debugLogging) {
				console.log('Remote video stream got');
			}
		};
		
		/**
		 * The SDP description has been retrieved, now set on local peer connection object
		 * and send to the remote peer using the signaling channel
		 * 
		 * @access private
		 * @return Void
		 */
		var gotSdpDescription = function(sdp) {
			// Caller or callee?
			var SDPType = bindContext.caller ? 'offer' : 'answer';
			
			// Set the local sdp description for the local peer
			peerConnection.setLocalDescription(sdp, function(){
					// Log status
					if (debugLogging) {
						console.log('SDP ' + SDPType + ' correctly generated');
					}
				}, function (){
					// Log status
					if (debugLogging) {
						console.log('SDP ' + SDPType + ' error');
					}
			});
			
			// Log status
			if (debugLogging) {
				console.log('SDP ' + SDPType + ' created');
			}
			
			// Now use the signaling channel to send the sdp description to remote peer
			signalingChannel('sdp', sdp);
		};
		
		
		/**
		 * The ICE candidate has been retrieved, now set on local peer connection object
		 * and send to the remote peer using the signaling channel when completed all candidates
		 * 
		 * @access private
		 * @return Void
		 */
		var gotIceCandidate = function(event) {
			// Check if a new candidate is available, otherwise candidates are finished, so send using signaling channel
			if(event.candidate) {
				if (event.candidate) {
					peerCandidates[candidatesCounter] = event.candidate;
				}
				candidatesCounter++;
				
				// Still gathering candidates
				peerConnection.iceCandidatesSent = false;
				
				// Log status
				if (debugLogging) {
					console.log('Event candidate found: ' + event.candidate.candidate);
				}
			} else if(!event.candidate || peerConnection.iceGatheringState === 'complete') {
				// Normal send if not already sent by the polyfill
				if(!peerConnection.iceCandidatesSent) {
					signalingChannel('icecandidate', peerCandidates);
					peerConnection.iceCandidatesSent = true;
					
					// Log status
					if (debugLogging) {
						console.log('icecandidates sent successfully using standard complete event');
					}
				}
			}
		};
		
		/**
		 * Polyfill for the browser error not reaching the 'completed' ice candidates state or not trigger null final candidate
		 * 
		 * @access private
		 * @return Void
		 */
		var checkIceCandidatesSent = function() {
			setTimeout(function(){
				if(peerConnection) {
					if(!peerConnection.iceCandidatesSent) {
						signalingChannel('icecandidate', peerCandidates);
						peerConnection.iceCandidatesSent = true;

						// Log status
						if (debugLogging) {
							console.log('icecandidates sent successfully using polyfill');
						}
					}
				}
			}, 1000);
		};
		
		/**
		 * Implement the client server signaling channel storing the description messages and ICE candidates
		 * 
		 * @access private
		 * @return Void
		 */
		var signalingChannel = function(dataType, data) {
			// Ajax post params
			var postData = {
				task : 'webrtc.saveEntity',
				peer2 : remotePeer,
				caller : (bindContext.caller ? 1 : 0),
				videocam : thispeerVideoCamState
			};
			
			// Check the data type to send
			switch(dataType) {
				case 'sdp':
					$.extend(postData, {'sdp' : JSON.stringify(data)});
					break;
					
				case 'icecandidate':
					$.extend(postData, {'icecandidate' : JSON.stringify(data)});
					break;
			}
			
			// Signaling channel between peers
			var signalingChannelPromise = $.Deferred(function(defer) {
				$.ajax({
					type : "POST",
					url : jsonLiveSite,
					dataType : 'json',
					context : this,
					data : postData
				}).done(function(response, textStatus, jqXHR) {
					if(!response.storing.status) {
						// Error found
						defer.reject(response.storing.exception_message, textStatus, response.storing);
						return false;
					}
					
					// Check response all went well
					defer.resolve(response.storing);
				}).fail(function(jqXHR, textStatus, errorThrown) {
					// Error found
					var genericStatus = textStatus[0].toUpperCase() + textStatus.slice(1);
					defer.reject('-' + genericStatus + '- ' + errorThrown, null, {});
				});
			}).promise();

			signalingChannelPromise.then(function(responseData) {
				// Do stuff
				promiseResolved = true;
				
				// Log status
				if (debugLogging) {
					console.log(dataType + ' sent succesfully using signaling channel');
				}
			}, function(errorText, error, exception) {
				// Close if opened
				if(peerConnection) {
					// Show user exception, only the first!
					if(exception.usermessage) {
						showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', errorText, 'jchat_exceptions_closer');
					} else {
						showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_error_creating_connection, 'jchat_exceptions_closer');
					}
					
					peerConnection.close();
					peerConnection = null;
					peerCandidates = new Array();
					candidatesCounter = 0;
				}
				
				// Reset caller
				bindContext.caller = false;
				bindContext.callee = false;
				promiseResolved = false;
				
				// Stop ringing sounds, both caller or callee
				resetSounds();
				// Reset buttons
				resetButtons();
				// Reset video
				resetVideo();
				// Reset bandwidth
				resetBandWidth();
				
				// Log status
				if (debugLogging) {
					console.log(dataType + ' error using signaling channel: ' + errorText);
				}
			});
		};

		/**
		 * Initialize my local video when popup opens up
		 * 
		 * @access public
		 * @param HTMLElement
		 *            container
		 * @param String
		 *            otherPeerIdentifier
		 * @param String
		 *            otherPeerName
		 * @param String
		 *            thisPeerName
		 * @return Boolean
		 */
		this.initializeVideo = function($container, otherPeerIdentifier, otherPeerName, thisPeerName) {
			// Ensure getUserMedia and peer connectionobject are available
			if(!supportFullWebRTC) {
				// Show the fallback if no support for WebRTC is detected
				showFallback($container);
				return false;
			}
			
			// Store the remote peer identifier and tooltip container
			remotePeer = otherPeerIdentifier;
			$tooltipContainer = $container;
			
			// Build interface for the peer-to-peer video chat and calling system
			$container.append('<span id="jchat_wrapper_localvideo"><span class="jchat_video_tab">' + thisPeerName + '</span>' +
								'<video id="jchat_localvideo" autoplay="autoplay"></video>' +
							  '</span>');
			$container.append('<span id="jchat_wrapper_remotevideo"><span class="jchat_video_tab">' + otherPeerName + '</span>' +
								'<video id="jchat_remotevideo" autoplay="autoplay"></video>' +
							  '</span>');
			
			// Hide by default remote video and show placeholder
			$('#jchat_remotevideo').hide();
			$container.append('<div id="jchat_remotevideo_placeholder"></div>');
			
			// Append call controls
			$container.append('<div class="jchat_webrtc_clearer"/>');
			$container.append('<div id="jchat_start_accept_call"><span class="jchat_webrtc_icons jchat_call_icon"/><span class="text">' + jchat_start_call + '</span></div>');
			$container.append('<div id="jchat_end_call"><span class="jchat_webrtc_icons jchat_end_icon"/><span class="text">'+ jchat_end_call + '</span></div>');
			
			// Prompt to user to access cam/mic to start call button
			showRTCMessages('jchat_infouser_webrtc top', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_grant_cam_access);
			
			// Append bandwidth meter
			if(options.showWebRTCStats) {
				$container.append('<div id="jchat_webrtc_bandwidth"><span>' + jchat_webrtc_bandwidth + '0.0 kbits/s</span></div>');
			}
			
			// Append volume controls
			var controlAudioVolume = options.defaultAudioVolume * 100;
			var controlMicVolume = options.defaultMicVolume * 100;
			$container.append('<div id="jchat_webrtc_volume"><input class="jchat_slider" type="range" min="0" max="100" step="10" value="' + controlAudioVolume + '"/></div>');
			$container.append('<div id="jchat_webrtc_mic"><input class="jchat_slider" type="range" min="0" max="100" step="10" value="' + controlMicVolume + '"/></div>');
			
			$container.append('<canvas id="mic_vumeter"></canvas>');
			
			// Append switcher audio/video controls
			var getSwitcherCode = function(id) {
				return '<div class="jchat_onoffswitch ' + id + '">' +
							'<input type="checkbox" name="jchat_onoffswitch" class="jchat_onoffswitch-checkbox" id="' + id + '" checked>' +
						    '<label class="jchat_onoffswitch-label" for="' + id + '">' +
						        '<span class="jchat_onoffswitch-inner"></span>' +
						        '<span class="jchat_onoffswitch-switch"></span>' +
						    '</label>' +
						'</div>';
			}
			$container.append(getSwitcherCode('jchat_webrtc_video'));
			
			// Append the resolutions select
			$container.append(webCamConstraintsSelect);
			$('#jchat_webrtc_camquality').wrap($('<div id="jchat_quality_cam" class="jchat_quality_cam"></div>'));
			$('#jchat_quality_cam').prepend('<span>' + jchat_webcam_quality + '</span>');

			// Check if a stored volume for audio is available
			if($.jStorage.get('jchat_webrtc_volume', false)) {
				var currentVideoVolume = $.jStorage.get('jchat_webrtc_volume');
				
				// Set the video element volume value
				$('#jchat_remotevideo').get(0).volume = currentVideoVolume;
				
				// Set the range control value
				$('#jchat_webrtc_volume input', $container).val(currentVideoVolume * 100);
			} else {
				$('#jchat_remotevideo').get(0).volume = options.defaultAudioVolume;
			}
			
			// Check if a stored volume for audio is available
			if($.jStorage.get('jchat_webrtc_mic') !== null) {
				var currentMicVolume = $.jStorage.get('jchat_webrtc_mic');
				
				// Set the mic gain volume value
				if (window.AudioContext) {
			    	gainFilterNode.gain.value = currentMicVolume;
			    }
				
				// Set the range control value
				$('#jchat_webrtc_mic input', $container).val(currentMicVolume * 100);
			} else {
				if (window.AudioContext) {
			    	gainFilterNode.gain.value = options.defaultMicVolume;
			    }
			}
			
			// Check if videocam is available
			$('#jchat_webrtc_video', $container).prop('checked', !!thispeerVideoCamState);
			if(!thispeerVideoCamState && options.hideWebcamWhenDisabled) {
				// Check if the parameter require to hide all
				if(options.hideWebcamWhenDisabled == 2) {
					$('#jchat_wrapper_localvideo').hide();
				} else {
					$('#jchat_localvideo').after('<div id="jchat_localvideo_placeholder"></div>');
				}
				$('#jchat_localvideo').hide();
			}
			
			$('#jchat_start_accept_call, #jchat_end_call', $tooltipContainer).addClass('jchat_disabled');
			
			// Register user interface events
			registerEvents.call(this);
			
			// Define the promise for MediaStream starting based on detected devices
			var initMediaDevicesPromise = $.Deferred(function(promise) {
				// Check if device has webcam, if not not video = false and show poster but still allow audio calls with gotVideoStream callback working
				checkDevices(promise);
			}).promise();

			// Thenable promise, always resolved indeed
			initMediaDevicesPromise.then(function(devices) {
				// Start local user media and show up in the local video source
				var videoConstraints = (webCamQuality === true) ? true : webCamConstraints[webCamQuality];
				
				// Is the webcam really available? Otherwise allow an audio call only
				if(!devices.hasWebcam) {
					videoConstraints = false;
					// Show the not supported poster
					$('#jchat_localvideo').attr('poster', jchat_livesite + 'components/com_jchat/images/default/placeholder.png');
					// Turn off webcam
					$('#jchat_webrtc_video').prop('checked', false).off('.jchatwebrtc').next().attr('for', null);
					// Show user notice
					showRTCMessages('jchat_top_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_nowebcam_detected);
				}
				
				try {
					navigator.mediaDevices.getUserMedia({
						'audio' : true,
						'video' : videoConstraints
					}).then(gotVideoStream, function(mediaStreamException) {
						// Show the not supported poster
						$('#jchat_localvideo').attr('poster', jchat_livesite + 'components/com_jchat/images/default/placeholder.png');
						
						// Show user notice
						var userError = mediaStreamException == 'HARDWARE_UNAVAILABLE' ? jchat_hardware_unavailable : jchat_mediastream_error;
						showRTCMessages('jchat_top_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', userError);
						
						// Log status
						if (debugLogging) {
							console.log('Error during video stream starting, resolution not supported? - ' + mediaStreamException);
						}
					});
				} catch(e){
					showRTCMessages('jchat_top_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_requires_https);
				};
			}, function(errorText, error) {
				// Log status
				if (debugLogging) {
					console.log('Error initialized app and local video: ' + errorText);
				}
			});
			
			// Log status
			if (debugLogging) {
				console.log('Initialized app and local video');
			}
			
			return true;
		};

		/**
		 * Reset the call buttons to original state: start call/and call
		 * 
		 * @access private
		 * @param Boolean skipCamQuality
		 * @return Void
		 */
		var resetButtons = function(skipCamQuality) {
			// Text reset
			$('#jchat_start_accept_call span.text', $tooltipContainer).text(jchat_start_call);
			$('#jchat_end_call span.text', $tooltipContainer).text(jchat_end_call);
			
			// Styles reset
			$('#jchat_start_accept_call', $tooltipContainer).removeClass('jchat_ringing jchat_disabled');
			$('#jchat_end_call', $tooltipContainer).addClass('jchat_disabled');

			// Enable the cam quality dropdown
			if(!skipCamQuality) {
				$('#jchat_webrtc_camquality').removeAttr('disabled');
			}
			
			// Reset interval state if any
			if(ringingInterval) {
				clearInterval(ringingInterval);
				ringingInterval = null;
			}
		};
		
		/**
		 * Reset the sounds if any looped both caller and callee
		 * 
		 * @access private
		 * @return Void
		 */
		var resetSounds = function() {
			// Stop ringing sounds, both caller or callee
			JChatNotifications.playRingingWebrtcCall(true, options.ringingTone);
			JChatNotifications.playWaitingWebrtcAnswer(true);
			JChatNotifications.playStartWebrtcCall(true);
			
			// Clear current timeout if any
			if(typeof startCallTimeout == 'number') {
				clearTimeout(startCallTimeout);
			}
		};
		
		/**
		 * Reset video placeholder and HTML5 elements to starting state
		 * 
		 * @access private
		 * @return Void
		 */
		var resetVideo = function() {
			// Show placeholder and hide remote video
			$('#jchat_remotevideo_placeholder').show();
			$('#jchat_remotevideo').hide();
			
			// Disable the tab led
			$('#jchat_wrapper_remotevideo').removeClass('active');
		};
		
		/**
		 * Reset the bandwidth metere and all the related resources
		 * 
		 * @access private
		 * @return Void
		 */
		var resetBandWidth = function() {
			// Reset global bytes sent
			bandwidthSentBytes = 0;

			// Reset the bandwidth meter
			$('#jchat_webrtc_bandwidth').html('<span>' + jchat_webrtc_bandwidth + '0.0 kbits/s</span>');
			
			// Clear timeout
			if(bandwidthInterval) {
				clearTimeout(bandwidthInterval);
			}
		};
		
		/**
		 * Reset the popups tooltip and triggers state
		 * 
		 * @access private
		 * @return Void
		 */
		var resetTooltips = function() {
			// A popup tooltip is found as open
			if($('#jchat_trigger_webrtc_tooltip').length) {
				$('#jchat_trigger_webrtc_tooltip').remove();
				$('.jchat_trigger_webrtc ').removeClass('jchat_webrtc_disabled');
			}
		};
		
		/**
		 * Manage the mic gain for the audio stream using the webaudio API
		 * 
		 * @access private
		 * @return Void
		 */
		var gainMicControl = function() {
		    // Check first of all if WebAudio API are supported
		    if (window.AudioContext && localGotStream.getAudioTracks()[0]) {
		        
		        // Retrieve the audio stream nodes, both source from local media stream and new destination
		        var audioStreamSourceNode = streamAudioContext.createMediaStreamSource(localGotStream);
		        var audioStreamDestinationNode = streamAudioContext.createMediaStreamDestination();
		        
		        // Connect nodes!
		        // Connect gain filter node to the source media stream to apply input gain
		        audioStreamSourceNode.connect(gainFilterNode);
		        
		        // Connect the gain filter node to the destination media stream to apply effect
		        gainFilterNode.connect(audioStreamDestinationNode);
		        
		        // Swap the audio tracks in the local media stream object
		        localGotStream.addTrack(audioStreamDestinationNode.stream.getAudioTracks()[0]);
		        localGotStream.removeTrack(localGotStream.getAudioTracks()[0]);
		    }
		};
		
		/**
		 * VU Meter for mic input of local peer
		 * 
		 * @access private
		 * @return Void
		 */
		var micVUMeter = function() {
			// Ensure that MediaStream track is available
			if(!localGotStream.getAudioTracks()[0]) {
				return;
			}
			
			// Get a mic stream node
			var microphoneStreamNode = streamAudioContext.createMediaStreamSource(localGotStream);
			
			// Create a new analyser node
		    var analyserNode = streamAudioContext.createAnalyser();
		    
		    // Globally instantiate the script node to avoid garbage and stop callback calls
		    window.jchatWebrtcScriptNode = streamAudioContext.createScriptProcessor(2048, 1, 1);

		    // Setup analyser param
		    analyserNode.smoothingTimeConstant = 0.3;
		    analyserNode.fftSize = 1024;

		    // Connect mic -> to -> analyser
		    microphoneStreamNode.connect(analyserNode);
		    
		    // Connect analyser -> to -> script node callback
		    analyserNode.connect(jchatWebrtcScriptNode);
		    
		    // Connect script node -> to -> audio context destination
		    jchatWebrtcScriptNode.connect(streamAudioContext.destination);

		    // Setup the canvas
		    var canvas = $("#mic_vumeter").get(0);
		    var canvasContext = $("#mic_vumeter")[0].getContext("2d");
		    canvas.width = 30;
	        canvas.height = 60;
	        
	        // Script process callback for the signal analyser
		    jchatWebrtcScriptNode.onaudioprocess = function() {
		        var array =  new Uint8Array(analyserNode.frequencyBinCount);
		        analyserNode.getByteFrequencyData(array);
		        var values = 0;

		        var length = array.length;
		        for (var i = 0; i < length; i++) {
		            values += array[i];
		        }

		        var average = values / length;
		        var color = '#00ff00';
		        if(average <= 50) {
		        	color = '#00ff00';
		        }
		        if(average > 50 && average <= 70) {
		        	color = '#ff9900';
		        }
		        if(average > 70) {
		        	color = '#ff0000';
		        }
		        canvasContext.clearRect(0, 0, 60, 180);
		        canvasContext.fillStyle = color;
		        canvasContext.fillRect(0, 58 - average, 15, 180);
		    }
		};
		
		/**
		 * Show user messages both info messages and exceptions by WebRTC framework
		 * 
		 * @access private
		 * @return Void
		 */
		var showRTCMessages = function(containerClass, iconClass, innerClass, translationText, hasClose) {
			// Videochat popup needs to be opened and initialized
			if(!$tooltipContainer) {
				return;
			}
			
			// Check if close is required
			var closer = '';
			if(hasClose) {
				closer = '<div class="' + hasClose + '"></div>';
				// Manage close tooltip
				$(document).on('click.jchatwebrtc', 'div.' + hasClose, function(jqEvent){
					$('div.' + containerClass).fadeOut(500, function(){
						$('div.jchat_exceptions').remove();
					});
				});
				
				// Clear current timeout if any
				if(typeof RTCMessagesTimeout == 'number') {
					clearTimeout(RTCMessagesTimeout);
				}
				
				// Ensure an autoclose after 8 seconds
				RTCMessagesTimeout = setTimeout(function(){
					$('div.' + hasClose).trigger('click.jchatwebrtc');
				}, 8000);
			}
			
			// Show calling user interface
			$('div.jchat_infouser_webrtc, div.jchat_exceptions', $tooltipContainer).remove();
			$tooltipContainer.append('<div class="' + containerClass + '">' +
					  					'<div class=" ' + iconClass + '"></div>' +
					  				 	'<div class=" ' + innerClass + '">' + translationText + '</div>' + 
					  				 	closer +
					  				'</div>');
			
			// Log status
			if (debugLogging) {
				console.log(translationText);
			}
			
		};
		
		/**
		 * Used for debugging purpouse
		 * 
		 * @access private
		 * @return Void
		 */
		var onSignalingError = function(error) {
			// Reset all
			resetButtons();
			resetSounds();
			resetBandWidth();
			
			// Throw a user message exceptions
			showRTCMessages('jchat_exceptions', 'jchat_icon_error', 'jchat_tooltip_error', jchat_session_error, 'jchat_exceptions_closer');
			
			// Log status
			if (debugLogging) {
				console.log('Failed to create signaling message : ' + error.name);
			}
		};
		
		/**
		 * Show the fallback layout when no WebRTC support is detected
		 * 
		 * @access private
		 * @return Void
		 */
		var showFallback = function($container) {
			// Define disclaimer
			$container.append('<div class="jchat_webrtc_nosupport">' + jchat_webrtc_nosupport + '</div>');

			// Define browsers source array
			var browsersArray = [ {
				browserName : 'chrome',
				support : 'jchat_support_ok',
				description : jchat_chrome_webrtc_support
			}, {
				browserName : 'firefox',
				support : 'jchat_support_ok',
				description : jchat_firefox_webrtc_support
			}, {
				browserName : 'opera',
				support : 'jchat_support_ok',
				description : jchat_opera_webrtc_support
			}, {
				browserName : 'internet-explorer',
				support : 'jchat_support_ko',
				description : jchat_ie_webrtc_support
			}, {
				browserName : 'safari',
				support : 'jchat_support_ko',
				description : jchat_safari_webrtc_support
			} ];
			
			// Now build the UX for browsers
			$.each(browsersArray, function(index, browser){
				var browserImage = 'components/com_jchat/images/default/' +browser.browserName + '_48x48.png';
				
				$container.append(	'<div class="jchat_browser_row">' +
										'<img src="' + jchat_livesite + browserImage + '" alt="browsers"/>' +
										'<span class="' + browser.support + '">' + browser.description + '</span>' +
									'</div>');
			});
			
			// Append the link to caniuse
			$container.append('<div class="jchat_webrtc_caniuse">' + jchat_webrtc_caniuse + '</div>');
		};
		
		/**
		 * Retrieve stats for bytes sent by local peer
		 * 
		 * @access private
		 * @return Void
		 */
		function getStats(peerConnection) {
			// Check if peerConnection object is now closed
			if (peerConnection.iceConnectionState == 'closed') {
				clearTimeout(bandwidthInterval);
				return;
			}

			// Set local function to calculate bandwidth
			var callBack = function(results) {
				var bytes = 0;
				
				// Initialize sent bytes counter
				if (!bandwidthSentBytes) {
					bandwidthSentBytes = 0;
				}
				
				// Calculate total sent bytes
				for ( var i = 0; i < results.length; ++i) {
					var res = results[i];
					if (res.bytesSent) {
						bytes += parseInt(res.bytesSent);
					}
				}
				bytes = Math.abs(bandwidthSentBytes - bytes);
				bandwidthSentBytes += parseInt(bytes);

				// Calculate in bits
				var bits = (bytes * 8);
				var kilobits = bits / 1000;
				$('#jchat_webrtc_bandwidth').html('<span>' + jchat_webrtc_bandwidth + kilobits.toFixed(1) + ' kbits/s' + '</span>');

				// Start a new stats cycle
				bandwidthInterval = setTimeout(function() {
					getStats(peerConnection);
				}, 2000);
			}

			// Preformat the stats items based on user agent
			if (!!navigator.mozGetUserMedia) {
				if (peerConnection.iceConnectionState != 'closed') {
					peerConnection.getStats(peerConnection.getRemoteStreams()[0].getVideoTracks()[0], function(res) {
						var items = [];
						res.forEach(function(result) {
							items.push(result);
						});
						callBack(items);
					}, function() {

					});
				}
			} else {
				peerConnection.getStats(function(res) {
					var items = [];
					res.result().forEach(function(result) {
						var item = {};
						result.names().forEach(function(name) {
							item[name] = result.stat(name);
						});
						item.id = result.id;
						item.type = result.type;
						item.timestamp = result.timestamp;
						items.push(item);
					});
					callBack(items);
				});
			}
		};

		/**
		 * Check for device support, test if webcam and mic is present, also allowing audio only calls
		 * 
		 * @access private
		 * @param Object promise
		 * @return Void
		 */
		 function checkDevices(promise) {
			 // Init devices object 
			 var detectedDevices = {};
			 
	        // "getSources" will be replaced with "getMediaDevices"
	        if (!MediaStreamTrack.getSources) {
	            MediaStreamTrack.getSources = MediaStreamTrack.getMediaDevices;
	        }
	        
	        // Still no support for getSources, probably the browser is Firefox when no devices support is available, assume devices are present
	        if(!MediaStreamTrack.getSources) {
	        	detectedDevices = {
	        			hasMicrophone: true,
	        			hasWebcam: true
	        	}
	        	
	        	 // Always resolve the promise with found devices
	            promise.resolve(detectedDevices);
	        	
	        	 // Log status
	            if (debugLogging) {
	            	console.log('No detection for audio/video devices is supported');
	            }

	            return;
	        }

	        // Loop over all audio/video input/output devices
	        if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
	        	// List cameras and microphones.
	        	navigator.mediaDevices.enumerateDevices()
			        	.then(function(devices) {
			        		var result = {};
			        		
			        		devices.forEach(function(device) {
			        			result[device.kind] = true;
				        	});
			        		
			        		detectedDevices.hasMicrophone = !!result.audioinput;
			        		detectedDevices.hasWebcam = !!result.videoinput;
			        		
			        		// Always resolve the promise with found devices
			        		promise.resolve(detectedDevices);
			        		
			        		// Log status
			        		if (debugLogging) {
			        			console.log('Detected audio/video devices: ' + JSON.stringify(detectedDevices));
			        		}
			        	})
			        	.catch(function(err) { });
	        } else {
	        	// Compat deprecated mode
	        	MediaStreamTrack.getSources(function (sources) {
	        		var result = {};
	        		
	        		for (var i = 0; i < sources.length; i++) {
	        			result[sources[i].kind] = true;
	        		}
	        		
	        		detectedDevices.hasMicrophone = !!result.audio;
	        		detectedDevices.hasWebcam = !!result.video;
	        		
	        		// Always resolve the promise with found devices
	        		promise.resolve(detectedDevices);
	        		
	        		// Log status
	        		if (debugLogging) {
	        			console.log('Detected audio/video devices: ' + JSON.stringify(detectedDevices));
	        		}
	        	});
	        }
	    };
	    
	    /**
		 * Detect the connection speed to auto select the Stream Contraints accordingly in optimized way
		 * 
		 * @access private
		 * @return Void
		 */
		function detectConnectionBandwidth() {
			// Avoid auto detection if not enabled
			if(!options.autoQualityBandwidthMgmt) {
				return;
			}
			
			// Avoid auto detection if an explicit value has been selected
			if($.jStorage.get('jchat_webrtc_videocam_quality')) {
				return;
			}
			
			// Avoid auto detection if navigation API not supported
			if(!"performance" in window || typeof(window.performance) === 'undefined') {
		    	return;
		    }
		    
		    // Avoid auto detection if navigation API not supported
		    if(!"timing" in window.performance) {
		    	return;
		    }

		    // Measure time needed to load the resource
		    var loadTimeSecs = (window.performance.timing.responseEnd - window.performance.timing.navigationStart) / 1000;
	 		    
 	        // Construct the select for webcam resolutions
			$.each(webCamConstraints, function(resolution, constraint){
				// Skip the auto mode
				if(resolution == 'Auto') {
					return true;
				}
				var bandwidthInterval = connectionSpeedConstraints[resolution];
				if(loadTimeSecs >= bandwidthInterval.minValue && loadTimeSecs < bandwidthInterval.maxValue) {
					webCamConstraintsSelect.val(resolution);
					webCamQuality = resolution;
					return false;
				}
			});
		};
		
		/**
		 * Set the received messaging data while the peer is listening for
		 * incoming call/answer
		 * 
		 * @access public
		 * @param Object data
		 * @param Object realtimeOptions
		 * @return Void
		 */
		this.setListeningData = function(data, realtimeOptions) {
			// Ensure getUserMedia and peer connectionobject are available
			if(!supportFullWebRTC) {
				// Log status
				if (debugLogging) {
					console.log('No WebRTC support detected for this device browser');
				}
				return false;
			}
			
			// Set/refresh call status
			callStatus = data.call_status;
			
			// Set incoming received SDP / ICE data if any, both for an offer or
			// answer
			if(data.sdp && data.icecandidate) {
				// Store SDP message
				receivedSdpMessage = data.sdp;
				
				// Store ICE candidates
				receivedICECandidates = data.icecandidate;
				
				// If this is the callee, turn the button to accept call and start ringing
				if(!this.caller) {
					// Set now as callee
					this.callee = true;
					
					// Play ringing sound incoming call
					JChatNotifications.playRingingWebrtcCall(false, options.ringingTone);
					
					// Manipulate tab title if not focused
					if(!realtimeOptions.tabFocused) {
                    	$('title').text(jchat_newvideocall_tab);
                    }
					
					// Start now
					if($('div.jchat_webrtctooltip[data-peerid=' +  data.peer1 + ']').length) {
						this.setCalleeRingingButton();
					} else {
						// All popups that are not target will be closed, so trigger click if any
						$('div[id!=jchat_user_' + data.peer1 + '_popup].jchat_tabpopup.jchat_tabopen .jchat_trigger_webrtc.toggle_on').trigger('click');
						// Disable all the video chat trigger not in the target context
						$('div[id!=jchat_user_' + data.peer1 + '_popup].jchat_tabpopup.jchat_tabopen .jchat_trigger_webrtc').addClass('jchat_webrtc_disabled');
						
						// Open the popup for the target user, this ensure it's opened
						$('#jchat_userlist_' + data.peer1).trigger('click');
						
						// Show the incoming call popover
						$('.jchat_trigger_webrtc', '#jchat_user_' + data.peer1 + '_popup').trigger('mouseover');
						
						// Bring in front the callee popup
						$('div[id^=jchat_user_][id!=jchat_user_' + data.peer1 + '_popup]').css('z-index', 10002);
						$('div[id=jchat_user_' + data.peer1 + '_popup], #jchat_trigger_webrtc_tooltip').css('z-index', 10005);
					}
					
					// Disable the cam quality dropdown, no changes during calls
					$('#jchat_webrtc_camquality').attr('disabled', true);
					
					// Log status
					if (debugLogging) {
						console.log('New incoming call arrived');
					}
				} else {
					// Stop button ringing
					$('#jchat_start_accept_call', $tooltipContainer).removeClass('jchat_ringing').addClass('jchat_disabled');
					if(ringingInterval) {
						clearInterval(ringingInterval);
						ringingInterval = null;
					}
					
					// Stop sounds waiting answer
					resetSounds();
					
					// Complete the session exchange from the caller perspective and manage callee SDP answer
					acceptCall.call(this);
					
					// Show user notice
					showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_call_starting, 'jchat_info_closer');

					// Log status
					if (debugLogging) {
						console.log('Exchanged SDP/ICE and call started between 2 peer');
					}
				}
			} else {
				// Log status
				if (debugLogging) {
					console.log('No SDP/ICE data received through signaling channel');
				}
			}
			
			// Always store local property
			if(data.videocam !== undefined) {
				// Always check update the videocam status for the remote video peer
				if(otherpeerVideoCamState != parseInt(data.videocam)) {
					// Remote videocam has been enabled
					if(data.videocam == 1) {
						$('#jchat_remotevideo_placeholder').hide();
						$('#jchat_remotevideo').show();
					} else {
						// Remote videocam has been disabled
						$('#jchat_remotevideo_placeholder').show();
						$('#jchat_remotevideo').hide();
					}
					// Toggle the tab led
					$('#jchat_wrapper_remotevideo').toggleClass('active');
				}
				otherpeerVideoCamState = parseInt(data.videocam);
			}
			
			// Check if the caller has had a declined call from other peer
			if(promiseResolved && this.caller && !data.caller_peer_state) {
				// Stop button ringing and reset
				resetButtons();
				resetBandWidth();
				
				// Play sound
				resetSounds();
				JChatNotifications.playEndWebrtcCall();
				
				// Reset video placeholder and tooltips if any
				resetVideo();
				resetTooltips();
				
				this.caller = false;
				promiseResolved = false;
				
				// Close if opened
				if(peerConnection) {
					peerConnection.close();
					peerConnection = null;
					peerCandidates = new Array();
					candidatesCounter = 0;
				}
				
				// Show user notice
				showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_connection_closed, 'jchat_info_closer');
				
				// Stop the duration timer here
				durationTimer(0);
				
				// Log status
				if (debugLogging) {
					console.log('Connection closed from callee');
				}
			}
			
			// Check if the caller has ended the call
			if(this.callee && !callStatus) {
				// Enable again the start call button
				resetButtons();
				resetBandWidth();
				
				// Stop ringing sound
				resetSounds();
				
				// Ended call
				JChatNotifications.playEndWebrtcCall();
				
				// Reset video placeholder and tooltips if any
				resetVideo();
				resetTooltips();
				
				this.callee = false;
				promiseResolved = false;
				
				// Manipulate tab title if not focused
				if(!realtimeOptions.tabFocused) {
                	$('title').text(realtimeOptions.tabTitle);
                }
				
				// Close if opened
				if(peerConnection) {
					peerConnection.close();
					peerConnection = null;
					peerCandidates = new Array();
					candidatesCounter = 0;
				}
				
				// Show user notice
				showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_connection_closed, 'jchat_info_closer');
				
				// Stop the duration timer here
				durationTimer(0);
				
				// Log status
				if (debugLogging) {
					console.log('Connection closed from caller');
				}
			}
		};

		/**
		 * Set ringing button when starting calls from caller perspective
		 * 
		 * @access public
		 * @return Void
		 */
		this.setCallerRingingButton = function() {
			// Videochat popup needs to be opened and initialized
			if(!$tooltipContainer) {
				return;
			}
			
			// Setup for buttons
			$('#jchat_start_accept_call', $tooltipContainer).toggleClass('jchat_ringing');
			$('#jchat_end_call').removeClass('jchat_disabled');
			
			// Start ringing effect and clear time if already started
			if(ringingInterval) {
				clearInterval(ringingInterval);
			}
			// Start now
			ringingInterval = setInterval(function(){
				$('#jchat_start_accept_call', $tooltipContainer).toggleClass('jchat_ringing');
			}, 500);
		};
		
		/**
		 * Set ringing button when incoming calls
		 * 
		 * @access public
		 * @return Void
		 */
		this.setCalleeRingingButton = function() {
			// Videochat popup needs to be opened and initialized
			if(!$tooltipContainer) {
				return;
			}
			
			// Disable the cam quality dropdown, no changes during calls
			$('#jchat_webrtc_camquality').attr('disabled', true);
			
			// Setup for buttons
			$('#jchat_start_accept_call span.text', $tooltipContainer).text(jchat_accept_call);
			$('#jchat_end_call span.text', $tooltipContainer).text(jchat_decline_call);
			$('#jchat_end_call', $tooltipContainer).removeClass('jchat_disabled');

			// Start ringing effect and clear time if already started
			if(ringingInterval) {
				clearInterval(ringingInterval);
			}
			ringingInterval = setInterval(function(){
				$('#jchat_start_accept_call', $tooltipContainer).toggleClass('jchat_ringing');
			}, 500);
			
			// Show user notice
			showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', jchat_incoming_started, 'jchat_info_closer');
		};
		
		/**
		 * Flush the media stream and clear it for next app usage, this minimize blocked devices
		 * 
		 * @access public
		 * @return Void
		 */
		this.flushMedias = function() {
			// Is there a valid local stream? Do it only for FF, webkit currently crashes
			 try {
				 if(localGotStream && navigator.mozGetUserMedia) {
					 var localStreamVideoTrack = localGotStream.getVideoTracks()[0];
					 if(localStreamVideoTrack) {
						 localStreamVideoTrack.stop();
					 }
					 // Log status
					 if (debugLogging) {
		            	console.log('Devices closed');
					 }
				 }
			 } catch (exception) {
			 	// Log status
	            if (debugLogging) {
	            	console.log('Error closing devices: ' + exception.message);
	            }
			 }
		};
		
		/**
		 * Get the local stream for the audio/video media
		 * Used to record medias
		 * 
		 * @access public
		 * @return Object
		 */
		this.getLocalStream = function() {
			return localGotStream;
		};
		
		/**
		 * Show an inner info user message
		 * 
		 * @access public
		 * @param String message
		 * @return Void
		 */
		this.showInnerMessage = function(message) {
			// Show user notice
			showRTCMessages('jchat_infouser_webrtc', 'jchat_icon_ok', 'jchat_tooltip_innermsg', message, 'jchat_info_closer');
		};
		
		/**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String
		 *            contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
		(function __construct() {
			// Set ICE servers
			servers = {
					"iceServers" : options.iceServers
			}
			
			if (window.AudioContext) {
		    	// Create the audio context
		        streamAudioContext = new AudioContext();
		        
		        // Create the gain filter node
		        gainFilterNode = streamAudioContext.createGain();
			}
			
			// Init status on load
			var isFirefox = false;
			if (navigator.mozGetUserMedia) {
				window.RTCSessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription;
				window.RTCIceCandidate = window.RTCIceCandidate || window.mozRTCIceCandidate;
				isFirefox = true;
			}
			
			// Set local vars
			debugLogging = options.debugEnabled;
			jsonLiveSite = options.jsonLiveSite;
			
			// Init WebAudio API
			window.AudioContext = window.AudioContext || window.webkitAudioContext;
			
			// Set video cam state by storage if present and the cam quality
			thispeerVideoCamState = $.jStorage.get('jchat_webrtc_videocam', 1);
			webCamQuality = $.jStorage.get('jchat_webrtc_videocam_quality', true);
			
			// Set sound status
			JChatNotifications.setAudioStatus(options.audiostatus);
			
			// Feature detection for Firefox, multiple resolutions constraints implements the new specifications
			if(isFirefox) {
				webCamConstraints = webCamNewConstraints;
			}
			// Construct the select for webcam resolutions
			$.each(webCamConstraints, function(resolution, constraint){
				var selected = (webCamQuality == resolution) ? 'selected' : '';
				var option = $('<option ' + selected + ' value="' + resolution + '">' + resolution + '</option>');
				webCamConstraintsSelect.append(option);
			});
			
			// Detect and store full support for WebRTC features
			supportFullWebRTC = !!navigator.mediaDevices && !!window.RTCPeerConnection;
			
			// Detect and store the connection speed bandwidth
			detectConnectionBandwidth();
		}).call(this);
	}
	
	// Make it global to instantiate as plugin
	window.JChatWebrtc = Webrtc;
})(jQuery);