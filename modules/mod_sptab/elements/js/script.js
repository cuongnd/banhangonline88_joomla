/*---------------------------------------------------------------
# SP Tab - Next generation tab module for joomla
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2014 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
jQuery(function($){
	showhide();
	$('#jform_params_tab_style,#jform_params_body_height').change(function() {showhide()});
	
	function showhide(){
		if ($("#jform_params_tab_style").val()=="raw" || $("#jform_params_tab_style").val()=="custom") {
			$("#jform_params_color").parent().parent().fadeOut();
		} else {
			$("#jform_params_color").parent().parent().fadeIn();		
		}	
		if ($("#jform_params_tab_style").val()=="raw" || $("#jform_params_tab_style").val()!="custom") {
			$('.minicolors, .sp-input').parent().parent().fadeOut();
		} else {
			$('.minicolors, .sp-input').parent().parent().fadeIn();		
		}
	}
});