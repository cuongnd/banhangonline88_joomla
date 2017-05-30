<?php
/** 
 * @package ShareThisBar Plugin for Joomla! 3.x
 * @subpackage Form Field Stbjscss
 * @version $Id: sharethisbar.php 3.7 2016-02-27 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2016 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');

/**
 * Inserts css and javascript code into plugin manager form for ShareThisBar plugin.
 * from directory /sharethisbar/images
 *
 * @package     ShareThisBar
 * @since       3.5
 */
 
class JFormFieldStbjscss extends JFormField {
 
	protected $type = 'Stbjscss'; 
 
	public function getInput() {
		$doc = & JFactory::getDocument();
                
                 $css = "
                   ul li span.spacer label{
                       color:#1669b6!important; /* oranz: ca7916  plava kao joom: 0d4c87 */
                       font-size:1.2em;
                       margin: 5px 0 0 0;
                    }
                    ul li span.spacer hr{
                       
                       border:1px solid #ca7916;
                       margin: 0 0 10px 0;
                    }
                ";
             
                $doc->addStyleDeclaration($css);
                
                $jsAdminCode = "<!--//--><![CDATA[//><!--  
                        window.addEvent('domready', function() {
                            
                          // language ---------------------------------
                          jQuery(document).ready(function(){
                          
                            var langmode = 0;
                            // var langmode1 = document.id('jform_params_langmode1');   
                            var langmode1 = jQuery('#jform_params_langmode1');  
                            if (langmode1) {
                               //if (langmode1.checked) {   
                               if (langmode1.is(':checked')) {
                                  langmode = 1;
                               }
                            }  
                            
                            // Kontrola za izbor jezika 
                            // var langcode = document.id('jform_params_langcode');
                            var langcode = jQuery('#jform_params_langcode');
                            
                            // document.id('jform_params_langmode0').addEvent('click',function(myevent) {  
                            jQuery('#jform_params_langmode0').click(function(){ 
                              // langcode.disabled='';   
                              langcode.attr('disabled', false).trigger('liszt:updated');
                            });
                            
                            // document.id('jform_params_langmode1').addEvent('click',function(myevent) {   
                            jQuery('#jform_params_langmode1').click(function(){ 
                               // langcode.disabled='true';  
                               langcode.attr('disabled', true).trigger('liszt:updated');
                            });
                          
                            if (langcode) {
                            // Ako je automatski izbor jezika disable-uj ovu kontrolu za izbor jezika.   
                            if (langmode === 1) {
                              //langcode.disabled='true';  
                              langcode.attr('disabled', true).trigger('liszt:updated');
                            } else {
                              //langcode.disabled='';  
                              langcode.attr('disabled', false).trigger('liszt:updated');
                            }
                            }
                            
                           // spreadword  ---------------------------------     
                                             
                            var spreadword = 0;
                            // var spreadword1 = document.id('jform_params_spreadword1');  
                            var spreadword1 = jQuery('#jform_params_spreadword1');  
                            if (spreadword1) {
                               // if (spreadword1.checked) {   
                               if (spreadword1.is(':checked')) {
                                 spreadword = 1;
                               }
                            } 
                            // MooTools 
                            //var spreadwordpic0=document.id('jform_params_spreadwordpic');   
                            
  			                var spreadwordpic = jQuery('#jform_params_spreadwordpic');
                                          
                            // Obrada click-a je za izmene posle ucitavanja strane. 
                            // document.id('jform_params_spreadword0').addEvent('click',function(ev) { 
                            jQuery('#jform_params_spreadword0').click(function(){   
                               spreadwordpic.attr('disabled', true).trigger('liszt:updated');
                            });

                           //document.id('jform_params_spreadword1').addEvent('click',function(ev) {   
                           jQuery('#jform_params_spreadword1').click(function(){     
                             spreadwordpic.attr('disabled', false).trigger('liszt:updated');  
                           });
                                                                  
                            // Ovo je za samo inicijalni prikaz strane. 
                            if (spreadwordpic) {
                            if (spreadword === 1) {  
                              spreadwordpic.attr('disabled', false).trigger('liszt:updated');                              
                            } else {
                              spreadwordpic.attr('disabled', true).trigger('liszt:updated');
                            }                             
                            }
                            
                          }); // end jQuery ready  
                            
                            // stbchoosestyle i btnstyle ---------------------------------    
                            
                            var arrStbChooseStyle = document.getElementsByName('jform[params][stbchoosestyle]');

                            var txtbtnstyle=document.id('jform_params_btnstyle');  
                            for (var i = 0; i < arrStbChooseStyle.length; i++ )
                            {
                                
                                // Ovo treba pri otvaranju strane za border oko slike stila.    
                                if (arrStbChooseStyle[i].checked) {
                                  $$('fieldset #jform_params_stbchoosestyle label img').setStyle('border','none');
                                  $$('fieldset #jform_params_stbchoosestyle label[for='+arrStbChooseStyle[i].id+'] img').setStyle('border','1px solid orange'); 
                                }

                                arrStbChooseStyle[i].addEvent('click',function() {
                                   
                                  txtbtnstyle.value=this.value;
                                  
                                  // Ovo treba za border oko slike stila.    
                                  $$('fieldset #jform_params_stbchoosestyle label img').setStyle('border','none');
                                  $$('fieldset #jform_params_stbchoosestyle label[for='+this.id+'] img').setStyle('border','1px solid orange');
     
                                });

                            }
                            
                            // ---------------------------------------------------------------------    
                             document.id('jform_params_cb1_code').addEvent('change',function(ev) { 
                                                                       
                             if (!this.value) return this; // Don't alter the empty string  
                             // Umesto jedne obrnute crte morao sam \\  
                             var cc = this.value.replace(/[\\r\\n]/g, ''); // Regular expression magic   
                             this.value = cc;
                              
                            }); 
                            
                            document.id('jform_params_cb2_code').addEvent('change',function(ev) { 
                                                                                
                             if (!this.value) return this; 
                             var cc = this.value.replace(/[\\r\\n]/g, ''); 
                             this.value = cc;
                             
                            }); 
                            
                            document.id('jform_params_cb3_code').addEvent('change',function(ev) { 
                                                                                
                             if (!this.value) return this; 
                             var cc = this.value.replace(/[\\r\\n]/g, ''); 
                             this.value = cc;
                             
                            }); 
                   
                        }); 
                        
                       //--><!]]> ";
                
                $doc->addScriptDeclaration($jsAdminCode);
           
		return ;
	}
        
	public function getLabel() {
                // Ovo je potrebno da se ne bi vraćala labela, koja mi ne treba.
		return '';
	}
              
}