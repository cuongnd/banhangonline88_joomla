<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');

/**
 * ckeditor Lite for Joomla! WYSIWYG Editor Plugin
 *
 * @author WebxSolution Ltd <andrew@webxsolution.com>
 * @package Editors
 * @since 1.5
 */
require_once(JPATH_PLUGINS.'/system/inlinecontent/inlinemode.php');
 
class plgEditorArkEditor extends JPlugin {

	/**
	 * Method to handle the onInitEditor event.
	 *  - Initializes the arkeditor WYSIWYG Editor
	 *
	 * @access public
	 * @return string JavaScript Initialization string
	 * @since 1.5
	 */
	
	public $app;
	
	public $inlineMode = ArkInlineMode::NOTSET;  
		 
	
    function __construct(& $subject, $config) 
	{
		if(isset($config['inlineMode']))
            $this->inlineMode = $config['inlineMode'];
        parent::__construct($subject, $config);
	}
    
    
    public function onInit()
	{
		
        $document = JFactory::getDocument();
        $document->addCustomTag('<script src="'. JURI::root().'plugins/editors/arkeditor/ckeditor/ckeditor.js"></script>');
                                
		//$document->addScript(JURI::root().'plugins/editors/arkeditor/ckeditor/ckeditor.js');
		
		$document->addCustomTag("<script>
        (function()
        {
		    var jfunctions = {};
		    CKEDITOR.tools.extend(CKEDITOR.tools,
		    {
			    getData : function(IdOrName)
			    {
				     return CKEDITOR.instances[IdOrName] && CKEDITOR.instances[IdOrName].getData() || CKEDITOR.oEditor && CKEDITOR.oEditor.getData();	
			    },
			    setData : function(IdOrName,ohtml)
			    {
				     CKEDITOR.instances[IdOrName] && CKEDITOR.instances[IdOrName].setData(ohtml) || CKEDITOR.oEditor && CKEDITOR.oEditor.setData(ohtml);
			    },
			    addHashFunction : function( fn, ref)
			    {
				    jfunctions[ref] =  function()
				    {
					    fn.apply( window, arguments );
				    };
			    },
			    callHashFunction : function( ref )
			    {
				    var fn = jfunctions[ ref ];
				    return fn && fn.apply( window, Array.prototype.slice.call( arguments, 1 ) );
			    }
		    })
        })();</script>");
				
		
				
		$temp = JComponentHelper::getParams('com_arkeditor');
		
		$clone = clone $temp;//Clone this as merge function wipes out some values
				  
		$temp->merge($this->params,true); //wipes out 
		$params = $temp;
		
		//Restore wiped out values
		$params->set('exclude_stylesheets',$clone->get('exclude_stylesheets'));
		$params->set('exclude_selectors',$clone->get('exclude_selectors'));

       
		if($this->inlineMode == ArkInlineMode::INLINE)
        {
			$params->set('toolbar','inline');
			$params->set('toolbar_ft','inline');
		}
		else
		{
			if($params->get('enable_preloader',true) && $this->inlineMode != ArkInlineMode::INLINE) //Do not load when inline editing
			{
				$document->addStyleSheet( JURI::root(). 'layouts/joomla/arkeditor/css/preloader.css');
			}	
		}	

		//Fire ARK Events		
		JPluginHelper::importPlugin( 'arkevents' );	
		$dispatcher = JEventDispatcher::getInstance();
				
		$instanceCreatedResult = $dispatcher->trigger('onInstanceCreated',array( &$params));
		$instanceReadyResult = $dispatcher->trigger('onInstanceReady', array( &$params));
		
		        		
		JPluginHelper::importPlugin( 'arkeditor' );	
		
		$instanceBeforeCreatedResult = $dispatcher->trigger('onInstanceBeforeCreated',array( &$params));
        $instanceBeforeLoadedResult = $dispatcher->trigger('onBeforeInstanceLoaded',array( &$params));
		$instanceLoadedResult = $dispatcher->trigger('onInstanceLoaded',array( &$params));
				
		
		//backward compatibility with JCK
		$document->addCustomTag(
		"<script>if( !window.addDomReadyEvent)
				window.addDomReadyEvent = {};
				var editor_implementOnInstanceReady = editor_onDoubleClick = function(){}
				window.addDomReadyEvent.add = CKEDITOR.domReady;
		</script>");

		
        //Fire General Instance Created Events				
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceBeforeCreatedResult) ? implode(chr(13), $instanceBeforeCreatedResult) : '') ."	
		});
        </script>");
        
        
        //Fire General Instance Created Events				
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceCreatedResult) ? implode(chr(13), $instanceCreatedResult) : '') ."	
		});
        </script>");
				
		//Fire plugin specific Instance Created Event
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			var editor = evt.editor;
			 " .  (!empty($instanceBeforeLoadedResult) ? implode(chr(13), $instanceBeforeLoadedResult) : '') ."	
		});</script>");
		
		//Fire plugin specific Instance Loaded Event
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceLoaded',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceLoadedResult) ? implode(chr(13), $instanceLoadedResult) : '') ."	
		});</script>");
		
		//Fire General Instance ready Events
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceReady',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceReadyResult) ? implode(chr(13), $instanceReadyResult) : '') ."	
		});</script>");
		
	}


	public function onGetContent( $editor ) {
		return " CKEDITOR.tools.getData('$editor'); ";
	}
	
	public function onSetContent($editor, $html) {
		return " CKEDITOR.tools.setData('$editor',$html); ";
	}

	/**
	 * ckeditor Lite WYSIWYG Editor - copy editor content to form field
	 *
	 * @param string 	The name of the editor
	 */
	
	function onSave( $editor ) { /* We do not need to test for anything */	}

	
	public function onGetInsertMethod($name)
	{
		$document = JFactory::getDocument();
		$document->addCustomTag(
		"<script>	
		function ARKEditorUpdateSelectedImageOrLink(instanceName,text)
		{
			var editor = CKEDITOR.instances[instanceName];
			
			if(!editor.hasBookMarks)
			{	
				editor.hasBookMarks = function() { return this._bookmarks};
			}
			
			if(!editor.resetBookMarks)
			{	
				editor.resetBookMarks = function() { this._bookmarks = null;};
			}
						
			if(CKEDITOR.env.ie)
			{
				var bookmarks = null;
				
				if( (bookmarks = editor.hasBookMarks()))
				{
					var sel = editor.getSelection();
					sel && sel.selectBookmarks( bookmarks );
					editor.resetBookMarks();
				}
			
			}
			
			if(text.match(/^<a[^>]+?href/i))
			{
				if ( ( element = CKEDITOR.plugins.link.getSelectedLink( editor ) ) && element.hasAttribute( 'href' ) )
				{
						var newElement =  CKEDITOR.dom.element.createFromHtml(text);
						newElement.copyAttributes(element);
						element.data('cke-saved-href',element.getAttribute('href'));
                        element.setHtml(newElement.getHtml());   
						editor.getSelection().selectElement(element); //content changes so reselect element
  					    return true;
				}	
			}
			else if (text.match(/^<img/i))
			{
				var selection = editor.getSelection();
				if ( ( element = selection && selection.getSelectedElement()) && element.is( 'img' ) )
				{
						var newElement =  CKEDITOR.dom.element.createFromHtml(text);
						newElement.copyAttributes(element);
						element.data('cke-saved-src',element.getAttribute('src'));
			            if(CKEDITOR.plugins.image && CKEDITOR.plugins.image.resize)
                        {
                           var src = element.getAttribute('src').replace(/\?i=[0-9]+?$/i, '');
						   element.setAttribute('src',src);
                           element.data('cke-saved-src',src);
                           CKEDITOR.plugins.image.resize(element,editor, function()
                           {
                               editor.getSelection().selectElement(element);
                               
                            },[],this); 
                        }
						selection.selectElement(element); //content changes so reselect element
						return true;
				}
			}
			
			return false;
		}

		function jInsertEditorText( text,editor) 
		{
			if(!ARKEditorUpdateSelectedImageOrLink(editor,text))
				CKEDITOR.instances[editor].insertHtml( text );
		}
		function IeCursorFix() {} //Do Nothing
        </script>");
		
		return true;
	 
	}
	
	 /**
	 * Display the editor area.
	 *
	 * @param   string   $name     The name of the editor area.
	 * @param   string   $content  The content of the field.
	 * @param   string   $width    The width of the editor area.
	 * @param   string   $height   The height of the editor area.
	 * @param   int      $col      The number of columns for the editor area.
	 * @param   int      $row      The number of rows for the editor area.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea. If not supplied the name is used.
	 * @param   string   $asset    The object asset
	 * @param   object   $author   The author.
	 *
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null)
	{
		
		static	$loaded = false,
				$loadFunc = false;
		
		
		if (empty($id))
		{
			$id = $name;
		}
		
		$id = $this->_cleanString($id);
		
		$temp = JComponentHelper::getParams('com_arkeditor');
		$temp->merge($this->params);
		$params = $temp;		
		
		if($this->inlineMode == ArkInlineMode::NONE)
			return;
		
		if($this->inlineMode == ArkInlineMode::INLINE)
        {
          

			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onBeforeLoadToolbar',array( &$params));

			$config                     			=   new stdclass;
            $toolbars								=	$this->_decode($params->get('toolbars'));	
					
			$config->toolbar						= 	($params->get('toolbar','') == 'mobile' ?  $toolbars['mobile'] :  $toolbars['inline']);
			$config->toolbar_inline 				= 	$toolbars['inline'];
			$config->toolbar_mobile					= 	$toolbars['mobile'];	
			$config->toolbar_image 					= 	$toolbars['image'];		
			$config->toolbar_title					= 	$toolbars['title'];	
			$config->toolbarName 					= 	'inline';
			
		    $config->skin							= 	$params->def( 'skin', 'officemetro' );
			$lang_mode								= 	$params->def( 'lang_mode', 1 );
			$config->language						= 	$params->def( 'lang_code', 'en' );
			$config->entermode 						= 	$params->def( 'entermode', 1 );
			$config->shiftentermode 				= 	$params->def( 'shiftentermode', 2 );
			$config->imagepath						=   $params->def( 'imagePath','images');
			$config->bgcolor						= 	$params->def( 'bgcolor','#ffffff');
    		$config->ftcolor						= 	$params->def( 'ftcolor','');
            $config->ftfamily						= 	$params->def( 'ftfamily','');
            $config->ftsize			   				= 	$params->def( 'ftsize','');
			$config->textalign						= 	$params->def( 'textalign','');
			$config->entities						= 	$params->def( 'entities',0);
  			$formatsource							= 	$params->def( 'formatsource',1);
			$config->baseHref 						=   JURI::root();
			$config->base                           =   JURI::base();
			$config->dialog_backgroundCoverColor	=   $params->def( 'dialog_backgroundCoverColor','black'); 
			$config->dialog_backgroundCoverOpacity	=   $params->def( 'dialog_backgroundCoverOpacity','0.5'); 
			$config->autoDisableInline				=   (int) $params->def( 'auto_disable_inline',1);
			$config->enableUserWarnings				=	(int) $params->def( 'enable_user_warnings',1);
			

			//lets get language direction
			$language	= JFactory::getLanguage();
	
			if ($language->isRTL()) {
				$config->direction = 'rtl';
			} else {
				 $config->direction = 'ltr';
			}
			
			$config->defaultLanguage = "en"; 
			switch ($lang_mode)
			{
			 case 0:
			    $config->direction = $params->get( 'direction','ltr');
				break;
			 case 1:
				// Joomla Default
				//Access Joomla's global configuation and get the language setting from there
				if (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . strtolower($language->getTag()) . ".js"))
				{
					$config->language = strtolower($language->getTag());
				}
				elseif (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . substr($language->getTag(), 0, strpos($language->getTag(), '-')) . ".js"))
				{
					$config->language = strtolower(substr($language->getTag(), 0, strpos($language->getTag(), '-')));
				} 
				break;
			 case 2:
				$config->language = ""; // Browser default
				$config->direction = "";
				break; 
			}
			
		
			//let's get style format
						 
			if(!$formatsource)
			{
				$config->formatsource = "
					var format = [];
					format['indent'] = false;
					format['breakBeforeOpen'] = false; 
					format['breakAfterOpen'] =  false;
					format['breakBeforeClose'] = false;
					format['breakAfterClose'] = false;
					var dtd = CKEDITOR.dtd;
					for ( var e in CKEDITOR.tools.extend( {}, dtd.\$nonBodyContent, dtd.\$block, dtd.\$listItem, dtd.\$tableContent ) ) {
							editor.dataProcessor.writer.setRules( e, format); 
					} 
			
					editor.dataProcessor.writer.setRules( 'pre',
					{
						indent: false
					}); 
				";
			}	
			else
			{
				$config->formatsource = "
					editor.dataProcessor.writer.setRules( 'pre',
					{
						indent : false,
						breakAfterOpen : false,	
						breakBeforeClose: false
					}); 
				";
			}
			
			
			$inline = JLayoutHelper::render('joomla.arkeditor.inline', $config);

			$userDetails = new stdclass;
			$user = JFactory::getUser();
			$userDetails->name = $user->name;
			$userDetails->username = $user->username;
			$userDetails->email = $user->email;
			
			$browser = JBrowser::getInstance();
					
			//if (!preg_match('/(iPad|iPhone)/i',$browser->getAgentString()))
			$inline.= JLayoutHelper::render('joomla.arkeditor.sidebar', $userDetails);
			
			return $inline;
		}
		
		
						
		/* Load the CK Parameters */
	
	
		$skin							= 	$params->def( 'skin', 'officemetro' );
		$height							= 	$params->def( 'height', $height);
		$width							= 	$params->def( 'width',  $width );
		$lang_mode						= 	$params->def( 'lang_mode', 1 );
		$lang							= 	$params->def( 'lang_code', 'en' );
		$entermode 						= 	$params->def( 'entermode', 1 );
		$shiftentermode 				= 	$params->def( 'shiftentermode', 2 );
		$imagepath						=   $params->def( 'imagePath','images');
		$bgcolor						= 	$params->def( 'bgcolor','#ffffff');
		$ftcolor						= 	$params->def( 'ftcolor','');
        $ftfamily						= 	$params->def( 'ftfamily','');
        $ftsize			    			= 	$params->def( 'ftsize','');
		$textalign						= 	$params->def( 'textalign','');
		$entities						= 	$params->def( 'entities',0);
		$formatsource					= 	$params->def( 'formatsource',1);
		$toolbars						=	$this->_decode($params->get('toolbars'));
		$dialog_backgroundCoverColor	=   $params->def( 'dialog_backgroundCoverColor','black'); 
		$dialog_backgroundCoverOpacity	=   $params->def( 'dialog_backgroundCoverOpacity','0.5');
		$enable_preloader				=	$params->get('enable_preloader',true); 


			
		if(empty($height))
		{
			$height =  480;
		}
			
		
		if (is_numeric($width))
		{
			$width .= 'px';
		}

		if (is_numeric($height))
		{
			$height .= 'px';
		}
	
		//Diaplay textarea	
		$textarea = new stdClass;
		$textarea->name    = $name;
		$textarea->id      = $id;
		$textarea->cols    = $col;
		$textarea->rows    = $row;
		$textarea->width   = $width;
		$textarea->height  = $height;
		$textarea->content = $content;

		
		if($loaded)
		{
	
			
			$retunScript = JLayoutHelper::render('joomla.arkeditor.textarea', $textarea) . ($enable_preloader ?  JLayoutHelper::render('joomla.arkeditor.preloader', $textarea) : '').'<script>CKEDITOR.domReady(function(event){ 
			
			if(!CKEDITOR.textareas)
				CKEDITOR.textareas = [];

			CKEDITOR.textareas[CKEDITOR.textareas.length] = { "id":"'.$textarea->id.'", "width":"'.$textarea->width.'", "height":"'.$textarea->height.'" };';
			
			
			if(!$loadFunc)
			{
				$retunScript .='
			
				CKEDITOR.replacetextAreas = function(config) 
				{
					function process(textarea,config)
					{
						var editor = CKEDITOR.replace(textarea.id,config);
						var xtdbuttons = CKEDITOR.document.getById("editor-xtd-buttons");
						if(xtdbuttons)
						{  
							editor.on("loaded", function(evt)
							{
								buttonsHtml = xtdbuttons.getOuterHtml().replace(/'.$loaded.'/g,"'.$id.'");
								var buttonsElement = CKEDITOR.dom.element.createFromHtml(buttonsHtml); 
								this.container.getParent().append(buttonsElement);
													
								var elements = buttonsElement.getElementsByTag("a");
															
								for(i= 0; i < elements.count();i++)
								{
									//override mootools model click event
									if(elements.getItem(i).hasClass("modal-button"))
									{
										(function()
										{
											var el = $(elements.getItem(i).$);
											el.addEvent("click", function(e) 
											{
												new Event(e).stop();
												SqueezeBox.fromElement(el,	{
																				parse: "rel"
																			});
											});
										})();
									}		
								}				
							
							});
						}
					}
						
					var textareas = this.textareas;
					setTimeout(function step() {
					 process(textareas.shift(),config);
					 if(textareas.length > 0) {
						 setTimeout(step,25)
					 }
					},25);
				}	
				CKEDITOR.once("instanceReady",function(evt)
				{
			
					var config =  CKEDITOR.instances["'.$loaded.'"].config; 
					CKEDITOR.replacetextAreas(config);
				})';
				$loadFunc = true;
			}
			
		
			return $retunScript .'			
			});</script>';
			

		}
		else
			$loaded = $id;	
		
		$plugin = JPluginHelper::getPlugin('editors','arkeditor');	
		$plugin->inlineMode = ArkInlineMode::REPLACE;
		

			
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onBeforeLoadToolbar',array( &$params));
		
		$toolbar_name_bk	=	$params->def( 'toolbar', 'back' );
		$toolbar_name_ft 	=	$params->def( 'toolbar_ft', 'front' );
		
		$toolbar = $toolbars[$toolbar_name_bk];
		
		if($this->app->isSite())
		{
			$toolbar = $toolbars[$toolbar_name_ft];
		
		}
			

			
		//lets get language direction
		$language	= JFactory::getLanguage();

		if ($language->isRTL()) {
			$direction = 'rtl';
		} else {
			 $direction = 'ltr';
		}
		
		 $defaultLanguage = 'en';
		switch ($lang_mode)
		{
		 case 0:
			 $direction = $params->get( 'direction','ltr');
			 break;
		 case 1:
			// Joomla Default
			//Access Joomla's global configuation and get the language setting from there
			if (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . strtolower($language->getTag()) . ".js"))
			{
				$lang = strtolower($language->getTag());
			}
			elseif (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . substr($language->getTag(), 0, strpos($language->getTag(), '-')) . ".js"))
			{
				$lang = strtolower(substr($language->getTag(), 0, strpos($language->getTag(), '-')));
			} 
			break;
		 case 2:
			$lang = ""; // Browser default
			$direction = "";
			break; 
		}
		
		//let's get style format
					 
		if(!$formatsource)
		{
			$ormatsource = "
				var format = [];
				format['indent'] = false;
				format['breakBeforeOpen'] = false; 
				format['breakAfterOpen'] =  false;
				format['breakBeforeClose'] = false;
				format['breakAfterClose'] = false;
				var dtd = CKEDITOR.dtd;
				for ( var e in CKEDITOR.tools.extend( {}, dtd.\$nonBodyContent, dtd.\$block, dtd.\$listItem, dtd.\$tableContent ) ) {
						editor.dataProcessor.writer.setRules( e, format); 
				} 
		
				editor.dataProcessor.writer.setRules( 'pre',
				{
					indent: false
				}); 
			";
		}	
		else
		{
			$formatsource = "
				editor.dataProcessor.writer.setRules( 'pre',
				{
					indent : false,
					breakAfterOpen : false,	
					breakBeforeClose: false
				}); 
			";
		}
		
		$document = JFactory::getDocument();
		
				
		$document->addCustomTag("<script>
			CKEDITOR.domReady(function(event)
			{
				//addCustom CSS
				CKEDITOR.addCss( 'body { background: ". $bgcolor . " none;". ($textalign ? " text-align: ".$textalign.";" :"")."}' );
				".( $ftcolor ? "CKEDITOR.addCss( 'body { color: ". $ftcolor."; }' )" : "")."
				".( $ftfamily ? "CKEDITOR.addCss( 'body { font-family: ".$ftfamily."; }' )" : "")."
				".( $ftsize ? "CKEDITOR.addCss( 'body { font-size: ". $ftsize."; }' )" : "")."
			});
		</script>");
		
		$stylesheets = '[]';
		if($params->get('usetemplatecss', true) || $params->get('arktypography', true) || !$params->get('arkcustomtypographyfile', false))
			$stylesheets = $this->_getTemplateCSS();
		
		$document->addCustomTag("
            <script>
			CKEDITOR.on( 'instanceCreated', function( evt ) {
			
				evt.editor.on( 'configLoaded', function() {
					
					this.config.stylesheetParser_validSelectors = /^(\w|\-)*?(\.|#)(\w|\-)+$/; 
					
					var styleSheets = ".$stylesheets."
					this.config.contentsCss = [];
					
					for(var i = 0; i < styleSheets.length; i++)
					{
						this.config.contentsCss[i] = styleSheets[i].href;
					}
					". ($stylesheets == '[]' ? "this.config.contentsCss.push('".JURI::root()."index.php?option=com_ajax&plugin=arktypography&format=json')	":"")."  	
				});	
			})
		</script>");
		
		$document->addCustomTag("<script>
			CKEDITOR.domReady(function(event)
			{
				CKEDITOR.tools.callHashFunction('".$id."','".$id."');
			});
		</script>");
	
		
		$document->addCustomTag("<script>
			CKEDITOR.tools.addHashFunction(function(div)
			{
				//create editor instance
				var oEditor = CKEDITOR.replace(div,
				{ 
					 baseHref : '" .JURI::root() . "',
					 base : '" . JURI::base(). "',
					 imagePath :  '$imagepath',     
					 skin : '$skin',
					 toolbar : ".json_encode($toolbar).",
					 toolbar_inline : ".json_encode($toolbars['inline']).",
					 toolbar_image : ".json_encode($toolbars['image']).",
					 contentsLangDirection : '$direction',
					 language : '$lang',
					 defaultLanguage :'$defaultLanguage', 
					 enterMode : '$entermode',
					 shiftEnterMode : '$shiftentermode',
					 ".($width ? "width : '$width'," : "")."
					 ".($height ? "height : '$height'," : "")."
					 entities : ".(int)$entities.",
					 dialog_backgroundCoverColor :'$dialog_backgroundCoverColor', 
					 dialog_backgroundCoverOpacity :'$dialog_backgroundCoverOpacity', 
					 extraAllowedContent : 'hr[class,id]'
				});
			},'" . $id . "');</script>"); 
	
		
	
		
		$editor = JLayoutHelper::render('joomla.arkeditor.textarea', $textarea) . ($enable_preloader ?  JLayoutHelper::render('joomla.arkeditor.preloader', $textarea) : '').
		$this->_displayButtons($id, $buttons, $asset, $author);
  
		return $editor;
	}	
	
	private function _displayButtons($name, $buttons, $asset, $author)
	{
		
		
        $return = '';
        
        $args = array(
			'name'  => $name,
			'event' => 'onGetInsertMethod'
		);

		$results = (array) $this->update($args);

		if ($results)
		{
			foreach ($results as $result)
			{
				if (is_string($result) && trim($result))
				{
					$return .= $result;
				}
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{
			$buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);

			$return .= JLayoutHelper::render('joomla.arkeditor.buttons', $buttons);
		}

		return $return;
	
	}
	
	private function _getTemplateCSS()
	{
		//load up CSS sylesheets
		$stylesheetsString = 'CKEDITOR.document.$.styleSheets';
		
		if($this->app->isAdmin())
		{
			$params = JComponentHelper::getParams('com_arkeditor');
			$stylesheets = array();
			
			//Check to see if the DomDocument class has been installed. As not all hosting companies install it.
			if( !class_exists( 'DOMDocument' ) )
			{
				if( $params->get( 'enable_debug', false ) )
				{
					JFactory::getApplication()->enqueueMessage( 'Missing required class DomDocument. Please ask your server administrator to install PHP\'s XML library.', 'error');
				}//end if
				
				return '';
			}//end if
			
			$dom = new DOMDocument();
			$dom->strictErrorChecking = false;
			$dom->recover = true;
				
			$headers = array
				( 
					'Accept-Encoding'=>'gzip;q=1.0, *;q=0', 
					'User-Agent'=>'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36', 
					'Referer'=>'https://www.google.com' 
				);
				
			if( version_compare(JVERSION, '3.4.0', 'ge') && $params->get( 'useArkProxy' ) )
			{

				$http = JHttpFactory::getHttp();
				try
				{
					$response	= $http->get( 'https://www.arkextensions.com/index.php?option=com_ajax&plugin=arkproxy&format=json&url=' . base64_encode( JURI::root() ), null, 5 );
				}
				catch( Exception $e )
				{
					
					$response = new stdclass;
					$response->body = '';
				}
				
				@$dom->loadHTML($response->body);
			}
			elseif(version_compare(JVERSION, '3.4.0', 'ge'))
			{
                $http = JHttpFactory::getHttp();
				$response = null;	
				try
				{
					
					$response = $http->get(JURI::root(), $headers, 5 );
					if(!empty($response) && empty($response->body) || $response->code !== 200 )
					{
						$http = null;
						$http = JHttpFactory::getHttp(null,'socket');
						$response = $http->get(JURI::root(), $headers, 5 );
						
						//Check to see if the HTTP status code successful. Else try the ark proxy server.
						if( $response->code !== 200 )
						{
							throw new Exception( 'Unable to connect via local connection.', $response->code );
						}//end if
					}//end if
					
					//Decompress the data
					if( isset($response->headers['Content-Encoding']) && $response->headers['Content-Encoding'] == 'gzip' )
					{
						if (!function_exists('gzdecode')) 
						{
							function gzdecode($data)
							{
								return gzinflate(substr($data,10,-8));
							}
						}
						//check that headers are not faking it!
						$is_really_gzip = 0 === JString::strpos($response->body , "\x1f" . "\x8b" . "\x08");
						
						if($is_really_gzip)
							$response->body = gzdecode( $response->body );
					}//end if
				}
				catch(Exception $ex)
				{
					try
					{
						//The system has failed to acces the root index file. So try via the Ark Proxy. It is succeeds then force the editor
						//to use the proxy each time.
						$response	= $http->get( 'https://www.arkextensions.com/index.php?option=com_ajax&plugin=arkproxy&format=json&url=' . base64_encode( JURI::root() ), null, 5 );
						//Check to see if the HTTP status code is success. Else fail.
						if( $response->code !== 200 )
						{
							//Bail out as this isn't working.
							if( $params->get( 'enable_debug', false ) )
							{
								JFactory::getApplication()->enqueueMessage( 'Failed to read the website source code and thus parse the CSS. Originating error code: '.$ex->getCode().', Proxy error code: '.$response->code, 'error');
							}//end if
							return '';
						}//end if
						$table = JTable::getInstance( 'extension' );
						if( $table->load( array( 'element'=>'com_arkeditor' ) ) )
						{
							$params = new JRegistry( $table->params );
							$params->set( 'useArkProxy', true );
							$table->save( array( 'params' => $params->toArray() ) );
						}//end if
						unset( $table );
					}
					catch( Exception $e )
					{
						
						$response = new stdclass;
						$response->body = '';
					}

				}

				@$dom->loadHTML($response->body);
			}
			else
			{	
				$options=array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false
					)
				);  
							
				@$dom->loadHTML(file_get_contents(JURI::root(),false,stream_context_create($options)));	
			}
			$links = $dom->getElementsByTagName('link');
			
			foreach($links as $link)
			{
			   // if $link_tag rel == stylesheet
			   // get href value
			   if($link->hasAttribute('rel') && $link->getAttribute('rel') == 'stylesheet')
			   {
					$href = $link->getAttribute('href');
					if( !preg_match('/(^https?|^\/\/)/',$href))
					{
						$uri = JURI::getInstance();
						$base = $uri->toString(array('scheme', 'host', 'port'));
						$href = $base.$href;
					}	
					$stylesheets[] = array('href'=> $href);
			   }
			}
			
			$stylesheetsString = json_encode($stylesheets);
		}

		return $stylesheetsString;
	}
	
	private function _decode($decode)
	{
		return json_decode(base64_decode($decode),true);
	}
	
	private function _cleanString($str)
  	{
	// remove any whitespace, and ensure all characters are alphanumeric
     $str = preg_replace(array('/\s+/','/\[/','/[^A-Za-z0-9_\-]/'), array('-','_',''), $str);
     // trim
     $str = trim($str);
     return $str;
    }
	
}