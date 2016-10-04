/* 
 * Original script by http://webdesignersdesk.com/
 */

jQuery(document).ready(function() {
	var popupid = 'popuprel';
	jQuery('#' + popupid).delay(800).fadeIn(1000);
	var popuptopmargin = (jQuery('#' + popupid).height() + 10) / 2;
	var popupleftmargin = (jQuery('#' + popupid).width() + 10) / 2;
	jQuery('#' + popupid).css({
		'margin-top' : -popuptopmargin,
		'margin-left' : -popupleftmargin
	});
});