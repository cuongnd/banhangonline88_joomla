/**
 * CPanel main JS APP class, manage chart generation
 * 
 * @package JCHAT::CPANEL::administrator::components::com_jchat 
 * @subpackage js 
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
*/
//'use strict';
(function ($) {
    var CPanel = function(targetSelector) {
    	/**
		 * Reference to ChartJS lib object
		 * 
		 * @access private
		 * @var Object
		 */
    	var chartJS = new Array();
    	
    	/**
		 * Charts options
		 * 
		 * @access private
		 * @var Object
		 */
    	var chartOptions = {animation:true, scaleFontSize: 11, scaleOverride: true, scaleSteps:1, scaleStepWidth: 50};
    	
    	/**
		 * Chart data to render, copy from global injected scope
		 * 
		 * @access private
		 * @var Object
		 */
    	var chartData = {};
    	
    	/**
		 * Element target to render chart
		 * 
		 * @access private
		 * @var HTMLElement
		 */
    	var context;

		/**
    	 * Update status selector
    	 * 
    	 * @access private
    	 * @var String
    	 */
    	var updateStatusSelector = '#updatestatus label.label-important';
    	
    	/**
    	 * Update process button snippet with placeholder to trigger the update process
    	 * 
    	 * @access private
    	 * @var String
    	 */
    	var updateButtonSnippet = '<button id="updatebtn" data-content="' + COM_JCHAT_EXPIREON + '%EXPIREON%" class="btn btn-small btn-primary">' + 
	    							'<span class="icon-upload"></span>' + COM_JCHAT_CLICKTOUPDATE + 
	    						  '</button>';
    	
    	/**
    	 * Update progressbar snippet
    	 * 
    	 * @access private
    	 * @var String
    	 */
    	var firstProgress = '<div class="progress progress-striped active">' +
									'<div id="progressBar1" class="bar" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">' +
									'<span class="sr-only"></span>' +
								'</div>' +
							'</div>';

        /**
		 * Interact with ChartJS lib to generate charts
		 * 
		 * @access private
		 * @return Void
		 */
        function generateLineChart(context, elem, animation) {
        	var elemIndex = $(elem).attr('id');
        	chartData = {};
        	
        	// Instance Chart object lib
        	chartJS[elemIndex] = new Chart(context);
        	
        	// Max value encountered
        	var maxValue = 10;
        	
        	// Normalize chart data to render
        	chartData.labels = new Array();
        	chartData.datasets = new Array();
        	var subDataSet = new Array();
            $.each(jchatChartData[elemIndex], function(label, value){
            	var labelSuffix = label.replace(/([A-Z])/g, "_$1").toUpperCase()
            	chartData.labels[chartData.labels.length] = eval('COM_JCHAT_' + labelSuffix + '_CHART');
            	subDataSet[subDataSet.length] = parseInt(value);
            	if(value > maxValue) {
            		maxValue = value;
            	}
            });
            
            // Override scale
            var konstant = 1;
            if(maxValue > 100) {
            	konstant = 10;
            }
            if(maxValue > 1000) {
            	konstant = 80;
            }
            if(maxValue > 10000) {
            	konstant = 500;
            }
            if(maxValue > 100000) {
            	konstant = 5000;
            }
            chartOptions.scaleStepWidth = parseInt((maxValue * konstant) / (maxValue / 10)); 
            chartOptions.scaleSteps = parseInt((maxValue / chartOptions.scaleStepWidth) + 1);
            
            chartData.datasets[0] = {
            		fillColor : "rgba(151,187,205,0.5)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : subDataSet
            };
        	
            // Override options
            chartOptions.animation = animation;
            
            // Paint chart on canvas
        	chartJS[elemIndex].Line(chartData, chartOptions);
        }
        
		/**
		 * Perform the remote check to validate the updates status license
		 * If the license is valid the update button will be shown
		 * 
		 * @access private
		 * @return Void 
		 */
		function checkUpdatesLicenseStatus() {
			var updateSnippet = updateButtonSnippet;
			var replacements = {"%EXPIREON%":""};

			// Is there an outdated status?
			if($(updateStatusSelector).length) {
				// Extra object to send to server
				var ajaxParams = { 
						idtask : 'getLicenseStatus',
						param: {}
				     };
				// Unique param 'data'
				var uniqueParam = JSON.stringify(ajaxParams); 

				// Request JSON2JSON
				$.ajax({
			        type: "POST",
			        url: "../administrator/index.php?option=com_jchat&task=ajaxserver.display&format=json",
			        dataType: 'json',
			        context: this,
			        async: true,
			        data: {data : uniqueParam } , 
			        success: function(data, textStatus, jqXHR)  {
						// If the updates informations are successful go on, ignore every error condition
						if(data.success) {
							replacements = {"%EXPIREON%":data.expireon};
							
							updateSnippet = updateSnippet.replace(/%\w+%/g, function(all) {
								   return replacements[all] || all;
								});
							
							// Now append the update button beside the status label
							$(updateStatusSelector).parent().after(updateSnippet);
							
							// Apply the popover
							$('#updatebtn').popover({trigger:'hover', placement:'right'})
						}
		            }
				}); 
			}
		}
		
		/**
		 * Start the managed update process of the componenent showing 
		 * progress bar and error messages to the user
		 * 
		 * @access public
		 * @property prototype
		 * @return void */
		 
		function performComponentUpdate() {
			var context = this;
			
			// Build modal dialog
			var modalDialog =	'<div class="modal fade" id="progessModalUpdate" tabindex="-1" role="dialog" aria-labelledby="progressModal" aria-hidden="true">' +
									'<div class="modal-dialog">' +
										'<div class="modal-content">' +
											'<div class="modal-header">' +
								        		'<h4 class="modal-title">' + COM_JCHAT_UPDATEPROGRESSTITLE + '</h4>' +
							        		'</div>' +
							        		'<div class="modal-body">' +
								        		'<p>' + firstProgress + '</p>' +
								        		'<p id="progressInfo1"></p>' +
							        		'</div>' +
							        		'<div class="modal-footer">' +
								        	'</div>' +
							        	'</div><!-- /.modal-content -->' +
						        	'</div><!-- /.modal-dialog -->' +
						        '</div>';
			// Inject elements into content body
			$('body').append(modalDialog);
			
			var modalOptions = {
					backdrop : 'static',
					keyboard : false
				};
			$('#progessModalUpdate').on('shown.bs.modal', function(event) {
				$('#progessModalUpdate div.modal-body').css({'width':'90%', 'margin':'auto'});
				$('#progressBar1').css({'width':'50%'});
				// Inform user process initializing
				$('#progressInfo1').empty().append('<p>' + COM_JCHAT_DOWNLOADING_UPDATE_SUBTITLE + '</p>');
				
				// Extra object to send to server
				var ajaxParams = { 
						idtask : 'downloadComponentUpdate',
						param: {}
				     };
				var uniqueParam = JSON.stringify(ajaxParams); 

				// Requests JSON2JSON chained
				var chained = $.ajax("../administrator/index.php?option=com_jchat&task=ajaxserver.display&format=json", {
					type : "POST",
					data : {
						data : uniqueParam
					},
					dataType : "json"
				}).then(function(data) {
					$('#progressBar1').css({'width':'75%'});
					// Inform user process initializing
					$('#progressInfo1').empty().append('<p>' + COM_JCHAT_INSTALLING_UPDATE_SUBTITLE + '</p>');
					
					// Phase 1 OK, go with the next Phase 2
					if(data.result) {
						// Extra object to send to server
						var ajaxParams = { 
								idtask : 'installComponentUpdate',
								param: {}
						     };
						var uniqueParam = JSON.stringify(ajaxParams); 
						return $.ajax("../administrator/index.php?option=com_jchat&task=ajaxserver.display&format=json", {
							type : "POST",
							data : {
								data : uniqueParam
							},
							dataType : "json"
						});
					} else {
						// Phase 1 KO, stop the process with error here and don't go on
						$('#progressBar1').css({'width':'100%'}).addClass('bar-danger');
						// Append exit message
						$('#progressInfo1').empty().append('<p>' + data.exception_message + '</p>');
						setTimeout(function(){
							// Remove all
							$('#progessModalUpdate').modal('hide');
						}, 3000);
						
						// Stop the chained promises
						return $.Deferred().reject();
					}
				});
				 
				chained.done(function( data ) {
					// Data retrieved from url2 as provided by the first request
					if(data.result) {
						// Phase 2 OK, set 100% width and mark as completed the whole process
						$('#progressBar1').css({'width':'100%'}).addClass('bar-success');
						// Inform user process initializing
						$('#progressInfo1').empty().append('<p>' + COM_JCHAT_COMPLETED_UPDATE_SUBTITLE + '</p>');
						
						// Now refresh page
						setTimeout(function(){
							window.location.reload();
						}, 1500);
					} else {
						// Set 100% for progress
						$('#progressBar1').css({'width':'100%'}).addClass('bar-danger');
						// Append exit message
						$('#progressInfo1').empty().append('<p>' + data.exception_message + '</p>');
						setTimeout(function(){
							// Remove all
							$('#progessModalUpdate').modal('hide');
						}, 3000);
					}
				});
			});
			
			$('#progessModalUpdate').modal(modalOptions);
			
			// Remove backdrop after removing DOM modal
			$('#progessModalUpdate').on('hidden.bs.modal',function(){
				$('.modal-backdrop').remove();
				$(this).remove();
			});
		}

        /**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
        (function __construct() {
            // Get target canvas context 2d to render chart
        	if(!!document.createElement('canvas').getContext) {
        		$.each(targetSelector, function(k, elem){
        			// Get context
        			context = $(elem).get(0).getContext('2d');
        			// Get HTMLCanvasElement
                    var canvas = $(elem).get(0);
                    // Get parent container width
                    var containerWidth = $(canvas).parent().width() / 2;
                    // Set dinamically canvas width
                    canvas.width  = containerWidth;
                    canvas.height = 180;
                    // Repaint canvas contents
                    generateLineChart(context, elem, true);
        		}); 
        	}
        	
			// Check updates license status
			setTimeout(function(){
        		checkUpdatesLicenseStatus();
        	}, 500);
			
			// Component updater ignition start
			$(document).on('click', '#updatebtn', function(jqEvent){
				performComponentUpdate();
			});
        }).call(this);
    }

    // On DOM Ready
    $(function () {
        var JChatCPanel = new CPanel(['#chart_users_canvas', '#chart_messages_canvas', '#chart_videochat_canvas']);
    });
})(jQuery);