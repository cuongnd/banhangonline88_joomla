<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgSystemK2ExtraFields extends JPlugin
{

	public $app;
	
	public function onAfterRoute()
	{
		
		if($this->app->input->get('option','') != 'com_k2') 
			return;
		
		if($this->app->isSite() && $this->app->input->get('layout','') == 'itemform' || $this->app->isAdmin() && 
			$this->app->input->get('view','') == 'item')
		{
			$script = "
				validateExtraFields = function() {
					\$K2('.k2Required').removeClass('k2Invalid');
					\$K2('#tabExtraFields a').removeClass('k2Invalid');
					var response = new Object();
					var efResults = [];
					response.isValid = true;
					response.errorFields = new Array();
					\$K2('.k2Required').each(function() {
						var id = \$K2(this).attr('id');
						var value;
						if (\$K2(this).hasClass('k2ExtraFieldEditor')) {
							if (CKEDITOR && CKEDITOR.tools.getData) {
								var value = CKEDITOR.instances[id].getData();
							}
							else if ( typeof tinymce != 'undefined') {
								var value = tinyMCE.get(id).getContent()
							}
						} else {
							var value = \$K2(this).val();
						}
						if ((\$K2.trim(value) === '') || (\$K2(this).hasClass('k2ExtraFieldEditor') && (\$K2.trim(value) === '<p></p>' || \$K2.trim(value) === '<p><br></p>'))) {
							\$K2(this).addClass('k2Invalid');
							response.isValid = false;
							var label = \$K2('label[for=\"' + id + '\"]').text();
							response.errorFields.push(label);
						}
					});
					\$K2.each(response.errorFields, function(key, value) {
						efResults.push('<li>' + value + '</li>');
					});
					if(response.isValid === false) {
						\$K2('#k2ExtraFieldsMissing').html(efResults);
						\$K2('#k2ExtraFieldsValidationResults').css('display','block');
						\$K2('#tabExtraFields a').addClass('k2Invalid');
					}
					return response.isValid;
				}

				initExtraFieldsEditor = function() {
					\$K2('.k2ExtraFieldEditor').each(function() {
						var id = \$K2(this).attr('id');
						if ( CKEDITOR && CKEDITOR.tools.callHashFunction) {
							if (CKEDITOR.instances[id])
								CKEDITOR.instances[id].destroy(true);
							CKEDITOR.tools.callHashFunction('text',id);
						} 
						else if ( typeof tinymce != 'undefined') {
							if (tinyMCE.get(id)) {
								tinymce.EditorManager.remove(tinyMCE.get(id));
							}
							if(tinymce.majorVersion == 4) {
								tinymce.init({selector: '#'+id});
								tinymce.editors[id].show();
							} else {
								tinyMCE.execCommand('mceAddControl', false, id);
							}

						}						
						else {
							new nicEditor({
								fullPanel : true,
								maxHeight : 180,
								iconsPath : (K2SitePath ? K2SitePath : '') + 'media/k2/assets/images/system/nicEditorIcons.gif'
							}).panelInstance(\$K2(this).attr('id'));
						}
					});
				}

			";
			JFactory::getDocument()->addScriptDeclaration($script);
		}
	}
	
}