// Turn radios into btn-group
jQuery(function($) {
	/**
	 * Enables bootstrap popover
	 */
	$('#updatestatus label.hasPopover, #cpanel span.leftlabel').popover({trigger:'hover', placement:'right', html:1});
	$('label.hasPopover, button.hasPopover, div.hasPopover, span.hasPopover, img.hasPopover').popover({trigger:'hover', placement:'top', html:1});
	
	/**
	 * Enables bootstrap tooltip
	 */
	$('label.hasTooltip, img.hasTooltip, a.hasTooltip').tooltip({trigger:'hover', placement:'top'});
	$('img.google_translate').tooltip({trigger:'hover', placement:'right'});
	
	// Recover the legacy save order button in async way on the next cycle
	setTimeout(function(){
		$('a.saveorder').removeAttr('onclick').removeAttr('style');
	}, 1);
	
	/**
	 * Calendars
	 */
	if($.datepicker) {
		$('input[data-role=calendar]').datepicker({
			dateFormat:'yy-mm-dd'
		}).prev('span').on('click', function(){
			$(this).datepicker('show');
		});
	}
	
	/**
	 * Accordion panels local storage memoize and set open
	 */
	var defaultAccordionObject = {'jchat_accordion_cpanel':'jchat_stats', 
								  'jchat_accordion_help':'jchat_functionalities_frontend'
								 };
	$('div.accordion').on('shown.bs.collapse', function (event) {
		if(!$(event.target).hasClass('accordion-body')) {
			return;
		}
		event.stopPropagation();
		$('div.accordion-heading').removeClass('opened');
		var localStorageAccordion = $.jStorage.get('jchatAccordionOpened', defaultAccordionObject);
		localStorageAccordion[this.id] = event.target.id;
		$.jStorage.set('jchatAccordionOpened', localStorageAccordion);
		
		// Scroll to accordion header if needed
		if(document.body.scrollHeight > window.innerHeight) {
			$('html, body').animate({ scrollTop: parseInt($("#"+event.target.id).prev().offset().top) - 185}, 500);
		}
		// Add open state
		$(event.target).prev().addClass('opened');
	});
	
	$.each($.jStorage.get('jchatAccordionOpened', defaultAccordionObject), function(namespace, element) {
		if($('#'+element, '#'+namespace).length) {
			$('#'+element, '#'+namespace).addClass('in').prev().addClass('opened');
		}
	});
	
	/**
	 * Tab panels local storage memoize and set open
	 */
	var defaultTabObject = {'tab_configuration':'general'};
	$('.nav.nav-tabs').on('shown.bs.tab', function (event) {
		var localStorageTab = $.jStorage.get('jchatTabOpened', defaultTabObject);
		localStorageTab[this.id] = $(event.target).data('element');
		$.jStorage.set('jchatTabOpened', localStorageTab);
	});
	// Check for a specific tab trigger using url hash
	var tabRequest = window.location.hash.substr(2);
	if(tabRequest) {
		$('a[data-element=' + tabRequest + ']').tab('show');
	}
	if(tabRequest == 'licensepreferences') {
		$('a[data-element=general]').tab('show');
		$('#params_registration_email-lbl').css('color', 'red');
		$('#params_registration_email').css('border', '2px solid red');
	}
	
	$.each($.jStorage.get('jchatTabOpened', defaultTabObject), function(namespace, element) {
		$('a[data-element='+element+']', '#'+namespace).tab('show');
	});
	
	// Manage the hide/show of subcontrols for chat rendering mode based on selected modality
	var chatRenderingMode = $('input[name=params\\[rendering_mode\\]]:checked').prop('value');
	if(chatRenderingMode != 'module') {
		$('div.module_rendering_ctrl').hide();
	}
	$('input[name=params\\[rendering_mode\\]]').on('click', function(){
		$('div.module_rendering_ctrl').toggle();
	});
});