<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class EditorController extends acymailingController{

	function __construct($config = array())
	{
		parent::__construct($config);
		JHTML::_('behavior.tooltip');
		JRequest::setVar('tmpl','component');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');

		$this->registerDefaultTask('browse');
	}

 	function browse(){
		$this->_setCss();
		$this->_setJs();
		$this->_displayHTML();
	}

	private function _setCss(){
		if(JRequest::getVar('inpopup','')=='true'){
			$height_acy_media_browser_table=420;
			$height_acy_media_browser_list=310;
			$width_acy_media_browser_actions=393;
			$width_acy_media_browser_hidden_elements=395;
			$height_acy_media_browser_image_details=415;
			$width_acy_media_browser_buttons_block=365;
			$width_acy_media_browser_url_input=60;
		}else{
			$height_acy_media_browser_table=540;
			$height_acy_media_browser_list=450;
			$width_acy_media_browser_actions=522;
			$width_acy_media_browser_hidden_elements=522;
			$height_acy_media_browser_image_details=550;
			$width_acy_media_browser_buttons_block=492;
			$width_acy_media_browser_url_input=70;
		}

		$css="
			#acy_media_browser_table{
				height:".$height_acy_media_browser_table."px;
				width:100%;
				margin: 0px;
				border: 1px solid rgb(233, 233, 233);
				box-shadow: 4px 4px 4px -4px rgba(0, 0, 0, 0.1);
			}

			#acy_media_browser_path_dropdown{
					margin-left:21px;
				margin-top:15px;
			}

			#acy_media_browser_message{
				height:450px;
				overflow:auto;
				margin:0px;
				padding:5px;
				border-bottom: 1px solid rgb(233, 233, 233);
			}

			#acy_media_browser_list{
				height:".$height_acy_media_browser_list."px;
				overflow-x:hidden;
				margin:0px;
					padding:5px;
				border-bottom: 1px solid rgb(233, 233, 233);
			}

			.acy_media_browser_image_size{
				color: #AAAAAA;
			}

			#acy_media_browser_actions{
				text-align:center;
					box-shadow: 0px -4px 4px -4px rgba(0, 0, 0, 0.3);
					width:".$width_acy_media_browser_actions."px;
				overflow:hidden;
			}

			#acy_media_browser_containing_block{
				height: 70px;
					width:1044px;
			}

			#acy_media_browser_buttons_block{
				padding:22px 15px 0px;
				width: ".$width_acy_media_browser_buttons_block."px;
				float:left;
				display:inline-block;
			}

			#acy_media_browser_hidden_elements{
				float:left;
					width:".$width_acy_media_browser_hidden_elements."px;
					display:inline-block;
			}

			#acy_media_browser_url_input{
				width:".$width_acy_media_browser_url_input."%;
				margin:0px;
			}

			#acy_media_browser_insert_message{
				margin-top:5px;
			}

			#acy_media_browser_image_details_row{
				width:35%;
				vertical-align:top;
				background-color: rgb(246, 246, 246);
				border: 1px solid rgb(233, 233, 233);
				font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
				font-size: 13px;
				line-height: 18px;
				color: rgb(102, 102, 102);
			}

			#acy_media_browser_image_details{
				position: relative;
				width: 85%;
				overflow-x:hidden;
				height: ".$height_acy_media_browser_image_details."px;
				padding: 15px;
			}

			#acy_media_browser_image_selected_info{
				width:230px;
				float:left;
				margin-bottom:10px;
			}

			#acy_media_browser_image_selected_details label{
				font-weight: bold;
			}

			#acy_media_browser_image_selected_details input {
				margin-bottom: 7px;
				}

			#acy_media_browser_image_selected_details select {
				margin-bottom: 7px;
				}

			.alert{
					padding: 8px 35px 8px 14px;
					margin-bottom: 18px;
					text-shadow: 0px 1px 0px rgba(255, 255, 255, 0.5);
					background-color: rgb(252, 248, 227);
					border: 1px solid rgb(251, 238, 213);
					border-radius: 4px;
			}

			.alert-error{
				background-color: rgb(242, 222, 222);
				border-color: rgb(238, 211, 215);
				color: rgb(185, 74, 72);
			}

			.alert-success {
					background-color: rgb(223, 240, 216);
					border-color: rgb(214, 233, 198);
					color: rgb(70, 136, 71);
			}


		";

		echo '<style>'.$css.'</style>';
	}

	private function _setJs(){
		$doc = JFactory::getDocument();
		$config =& acymailing_config();
		$websiteurl = rtrim(JURI::root(),'/').'/';


		if (ACYMAILING_J16){
			$doc->addScript($websiteurl.'plugins/editors/acyeditor/acyeditor/scripts/jquery-1.9.1.min.js?v='.str_replace('.','',$config->get('version')));
		}
		else{
			$doc->addScript($websiteurl.'plugins/editors/acyeditor/scripts/jquery-1.9.1.min.js?v='.str_replace('.','',$config->get('version')));
		}


		$imageZone = JRequest::getVar( 'image_zone', array(), '', 'array' );
		if(empty($imageZone)){
			$getAdditionalTags = "
					var selectedImageWidth = document.getElementById('acy_media_browser_image_width').value;
					var selectedImageHeight = document.getElementById('acy_media_browser_image_height').value;
							var selectedImageAlign = document.getElementById('acy_media_browser_image_align').value;
							var selectedImageBorder = document.getElementById('acy_media_browser_image_border').value;
							var selectedImageMargin = document.getElementById('acy_media_browser_image_margin').value;

							var width = ''; var height =''; var align=''; var border = ''; var margin = '';
							if(selectedImageWidth>0) width =  ' width:' + selectedImageWidth + 'px; ';
							if(selectedImageHeight>0) height = ' height:' +  selectedImageHeight + 'px; ';
							if(selectedImageAlign) align = 'float:' + selectedImageAlign + ';';
							if(selectedImageWidth>0 && selectedImageAlign.trim()=='center') align = 'margin:auto;';
							if(selectedImageBorder) border = ' border:' +  selectedImageBorder + '; ';
							if(selectedImageBorder>0 ) border = ' border: solid ' +  selectedImageBorder + 'px; ';
							if(selectedImageMargin>0) margin = ' margin:' +  selectedImageMargin + 'px; ';
							";
			$sizeAndAlignTags = " style=\"  ' + height + width + align + border + margin +' \" ";

			$insertImage="window.parent.insertImageTag(tag);";
		}else{
			$getAdditionalTags = "var selectedImageRef = document.getElementById('acy_media_browser_image_target').value;
								 	if(selectedImageRef.substring(0,4)!='http') selectedImageRef = 'http://' + selectedImageRef; ";
			$sizeAndAlignTags = "";
			$insertImage=" window.parent.jInsertEditorText(tag, this.editor);";
		}

		if(JRequest::getVar('inpopup','')=='true'){
			$imgMaxHeight = 150;
			$slideValue=-395;
		}else{
			$imgMaxHeight = 190;
			$slideValue=-522;
		}

			$js="
				function checkSelected(imageZone) {
					if(imageZone){
						var editor = window.parent.CKEDITOR.editor;

						o = this._getUriObject(window.self.location.href);
						q = new Hash(this._getQueryObject(o.query));
						zone = decodeURIComponent(q.get('e_name'));

						var html = window.parent.getSelectedHTML(zone);
						var parsedSelection = jQuery.parseHTML(html);

						if(!parsedSelection)
							return false;

						if(parsedSelection[0].tagName == 'A'){
							var parsedImage = jQuery.parseHTML(parsedSelection[0].innerHTML);
							parsedImage = parsedImage[0];

							if(parsedSelection[0].href)
									document.getElementById('acy_media_browser_image_target').value =  parsedSelection[0].href;
						}else if(parsedSelection[0].tagName == 'IMG'){
							var parsedImage = parsedSelection[0];
						}

						if(!parsedImage) return false;

						var name = parsedImage.src.substr(parsedImage.src.lastIndexOf('/') + 1);
						if(parsedImage.src.substring(0,4)=='http'){
							var imageUrl =  parsedImage.src;
						}else{
							var imageUrl =  '".ACYMAILING_LIVE."' + parsedImage.src;
						}
						displayImageFromUrl(imageUrl, 'success', name, size);
						if(parsedImage.alt)
							document.getElementById('acy_media_browser_image_title').value =  parsedImage.alt;
					}else{
						var editor =  window.parent.editor;
						var sel = editor.getSelection();
							var ranges = sel.getRanges();
							var el = new window.parent.CKEDITOR.dom.element('div');
							for (var i = 0, len = ranges.length; i < len; ++i) {
									el.append(ranges[i].cloneContents());
							}
							var selection = el.getHtml();

						var parsedSelection = jQuery.parseHTML(selection);

						if(!parsedSelection)
							return false;

						if(parsedSelection[0].tagName == 'IMG'){
							var name = parsedSelection[0].src.substr(parsedSelection[0].src.lastIndexOf('/') + 1);
							var size = parsedSelection[0].width+'x'+parsedSelection[0].height;
							if($(selection).attr('src').substring(0,4)=='http'){
								var imageUrl =  $(selection).attr('src');
							}else{
								var imageUrl =  '".ACYMAILING_LIVE."' + $(selection).attr('src');
							}
							displayImageFromUrl(imageUrl, 'success', name, size);

							if(parsedSelection[0].alt)
								document.getElementById('acy_media_browser_image_title').value =  parsedSelection[0].alt;
							if(parsedSelection[0].style.width)
								document.getElementById('acy_media_browser_image_width').value =  parsedSelection[0].style.width.slice(0,-2);
							if(parsedSelection[0].style.height)
								document.getElementById('acy_media_browser_image_height').value =  parsedSelection[0].style.height.slice(0,-2);
							if(parsedSelection[0].style.cssFloat)
								document.getElementById('acy_media_browser_image_align').value =  parsedSelection[0].style.cssFloat;
							if(parsedSelection[0].style.margin)
								document.getElementById('acy_media_browser_image_margin').value =  parsedSelection[0].style.margin.slice(0,-2);
							if(parsedSelection[0].style.border){
								document.getElementById('acy_media_browser_image_border').value =  parsedSelection[0].style.border;
							}
						}
					}
				}

				function displayImageFromUrl(url, result, name, size, fromUrl){
					if(result=='success'){
							var infos = '<div style=\"width:100%; display:block: height:1px; float:left; margin-top:10px;\"></div>';
							document.getElementById('acy_media_browser_image_selected').innerHTML='<img id=\"acy_media_browser_selected_image\" src=\"' + url + '\"  style=\"border: 1px solid rgb(233, 233, 233); float:left; margin-right:15px; max-width: 230px; max-height:".$imgMaxHeight."px;\"></img>'+infos;
							document.getElementById('acy_media_browser_image_selected').style.display=\"\";
							if(!name){ var name = url.substr(url.lastIndexOf('/') + 1); }
							document.getElementById('acy_media_browser_image_selected_info').innerHTML='<div><span id=\"acy_media_browser_image_selected_name\" style=\"font-weight:bold;\"> '+name+'</span><br/>'+size+'<br/>';
							document.getElementById('acy_media_browser_image_selected_info').style.display=\"\";
							if(fromUrl){
								document.getElementById('acy_media_browser_insert_message').innerHTML='<span style=\"color:green;\">".str_replace("'","\'",JText::_('IMAGE_FOUND'))."</span>';
							}
					}else{
							document.getElementById('acy_media_browser_image_selected').innerHTML=\"\";
							document.getElementById('acy_media_browser_image_selected').style.display=\"none\";
							document.getElementById('acy_media_browser_image_selected_info').innerHTML=\"\";
							if(fromUrl){
								if(result='error'){
									document.getElementById('acy_media_browser_insert_message').innerHTML='<span style=\"color:red;\">".str_replace("'","\'",JText::_('IMAGE_NOT_FOUND'))."</span>';
								}else if(result='timeout'){
									document.getElementById('acy_media_browser_insert_message').innerHTML='<span style=\"color:red;\">".str_replace("'","\'",JText::_('IMAGE_TIMEOUT'))."</span>';
								}
							}
					}
				}


				function calculateSize(newHeight, newWidth){
					var img = document.getElementById('acy_media_browser_selected_image');
					if(img){
						var width = img.naturalWidth;
						var height = img.naturalHeight;

						var newWidht = 0;
						if(newHeight == 0) newHeight = 9999;
						if(newWidth == 0) newWidth = 9999;

						if (height>0) var rHeight = newHeight / height;
						if (width>0) var rWidth= newWidth / width;

						if (rHeight > rWidth)
							var ratio = rWidth;
						else
							var ratio = rHeight;

						var calculatedHeight = parseInt(height * ratio);
						var calculatedWidth = parseInt(width * ratio);

						if(newHeight == 9999){
							document.getElementById('acy_media_browser_image_height').value =  calculatedHeight;
						}else{
							 document.getElementById('acy_media_browser_image_width').value = calculatedWidth;
						}
					}
				}


				function testImage(url, callback, timeout) {
					timeout = timeout || 5000;
						var timedOut = false, timer;
						var img = new Image();
						img.onerror = img.onabort = function() {
								if (!timedOut) {
										clearTimeout(timer);
										callback(url, \"error\", '', '', true);
								}
						};
						img.onload = function() {
								if (!timedOut) {
										clearTimeout(timer);
										callback(url, \"success\",'','',true);
								}
						};
						img.src = url;
						timer = setTimeout(function() {
								timedOut = true;
								callback(url, \"timeout\", '', '', true);
						}, timeout);
				}

				function displayAppropriateField(id){
					if(id==\"import_from_url_btn\"){
							document.getElementById('upload_image').style.display=\"none\";
							document.getElementById('import_from_url').style.display=\"\";
							jQuery('#acy_media_browser_containing_block').stop().animate({marginLeft: '".$slideValue."px' }, 400);
					}else if(id==\"upload_image_btn\"){
							document.getElementById('upload_image').style.display=\"\";
							document.getElementById('import_from_url').style.display=\"none\";
							jQuery('#acy_media_browser_containing_block').stop().animate({marginLeft: '".$slideValue."px' }, 400);
					}else{
							jQuery('#acy_media_browser_containing_block').stop().animate({marginLeft: '-0px' }, 400);
					}
				}

				function toggleImageInfo(id, action){
					if(action==\"display\"){
							document.getElementById('acy_media_browser_image_info_'+id+'').style.display = \"\";
					}else{
							document.getElementById('acy_media_browser_image_info_'+id+'').style.display = \"none\";
					}
				}

				function _getQueryObject(q) {
					var vars = q.split(/[&;]/);
					var rs = {};
					if (vars.length) vars.each(function(val) {
							var keys = val.split('=');
							if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
					});
					return rs;
				}

				function _getUriObject(u){
					var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
					return (bits)
						? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
						: null;
				}

				function validateImage(){
					var urlInput = document.getElementById('acy_media_browser_url_input').value;
					var urlImageName = urlInput.substr(urlInput.lastIndexOf('/') + 1);
					var selectedImageName = document.getElementById('acy_media_browser_image_selected_name').innerHTML;

					var selectedImageAlt = document.getElementById('acy_media_browser_image_title').value;
					var selectedImageRef = '';
					var selectedImageUrl = document.getElementById('acy_media_browser_selected_image').src;

					".$getAdditionalTags."

					o = this._getUriObject(window.self.location.href);
					q = new Hash(this._getQueryObject(o.query));
					this.editor = decodeURIComponent(q.get('e_name'));

					var dropdown = document.getElementById('acy_media_browser_files_path');
					var path = dropdown.options[dropdown.selectedIndex].text;
					var base = ' ".ACYMAILING_LIVE." ';

					if(urlInput!='http://' && selectedImageName.trim()==urlImageName.trim()){
							var tag = '<img src=\"' + urlInput + '\" alt=\"' + selectedImageAlt + '\" ".$sizeAndAlignTags." />';
					}else{
							var tag = '<img src=\"' + selectedImageUrl + '\" alt=\"' + selectedImageAlt + '\" ".$sizeAndAlignTags." />';
					}

					if(selectedImageRef){
							tag = '<a href=\"' + selectedImageRef + '\">' + tag + '</a>';
					}

					".$insertImage."
					return false;
				}

				function changeFolder(folderName){
					var url = window.location.href;
					if (url.indexOf('?') > -1){
							var lastParam = url.substring(url.lastIndexOf('&') + 1);
							lastParam = lastParam.split('=');
							if(lastParam=='selected_folder')
								url = url.replace(lastParam, 'selected_folder='+folderName);
							else
								url += '&selected_folder='+folderName;
					}else{
							 url += '?selected_folder='+folderName;
					}
					window.location.href = url;
				}

			";

			$doc->addScriptDeclaration($js);
		}

	private function _displayHTML(){
		$config =& acymailing_config();
		$message = '';

		$mediaFolder = $config->get('mediafolder');
		$mediaFolder = explode(',', $mediaFolder);

		$receivedFolder = JRequest::getVar( 'selected_folder', array(), '', 'array' );
		if(!empty($receivedFolder)){
			$defaultFolder=$receivedFolder[0];
		}else if(isset($mediaFolder[0])){
			$defaultFolder = $mediaFolder[0];
		}

		if(isset($defaultFolder)){
				$tempPath= trim($defaultFolder,DS.' ').DS;
			$tempPath = JPath::clean($tempPath);
			$uploadPath = JPath::clean(ACYMAILING_ROOT.$tempPath);
		}else{
				$uploadPath = '';
		}


			$uploadedImage = JRequest::getVar( 'uploadedImage', array(), 'files', 'array' );
			if(!empty($uploadedImage)){
				if(!empty($uploadedImage['name'])){
					if($this->_importImage($uploadedImage, $uploadPath)) $uploadMessage='success';
					else $uploadMessage='error';
				} else{
					$uploadMessage = 'error';
					$this->message = JText::_('BROWSE_FILE');
				}
			}

			?>

			<div id="acy_media_browser" >
					<!-- <br style="font-size:1px"/> -->
						<table id="acy_media_browser_table" style="height:420px;">
						<tr>
							<td style="width:65%; vertical-align:top;">
									<?php

									$selectedFolder = $defaultFolder;
									$scannedFolders = $this->_generateArborescence($mediaFolder);
									$folders = $this->_mergeFoldersLists($scannedFolders, $mediaFolder);

									$folders = $this->_generateSpecificFolders($folders);

									foreach($folders as $folder){
											$this->values[] = JHTML::_('select.option', $folder, $folder);
									}

									echo '<div id="acy_media_browser_path_dropdown" >';
									echo JHTML::_('select.genericlist',   $this->values, 'acy_media_browser_files_path', 'class="inputbox chzn-done" size="1" onchange="changeFolder(this.value)" style="width:350px" ', 'value', 'text', $selectedFolder).'<br/>';
									echo '</div>';

									acymailing_createDir($uploadPath);

									$files = JFolder::files($uploadPath);
									$defaultFolder = $defaultFolder.'/';

									echo '<ul id="acy_media_browser_list">';

									if(!empty($uploadMessage)){
										 if($uploadMessage=='success') acymailing_display($this->message);
										 else if($uploadMessage=='error') acymailing_display($this->message, 'error');
									}

									$images = array();
									$imagesFound=false;
									foreach($files as $k => $file){
										if(strrpos($file, '.') === false)
											continue;

										$ext = strtolower(substr($file, strrpos($file, '.') + 1));
										$extensions = array('jpg', 'jpeg', 'png', 'gif');
										if(!in_array($ext, $extensions))
											continue;

										$imagesFound = true;
											$images[] = $file;
											$imageSize = getimagesize ($uploadPath.$file);
											$imageSize = $imageSize[0].'x'.$imageSize[1];
											?>
											<li id="acy_media_browser_images_<?php echo $k; ?>" style="position: relative; height: 135px; width:135px; display:inline-block; margin:14px; margin-top:7px; box-shadow: 0px 2px 2px 2px rgba(0, 0, 0, 0.2);"  onmouseover="toggleImageInfo(<?php echo $k; ?>, 'display')" onmouseout="toggleImageInfo(<?php echo $k; ?>, 'hide')" >
											<img id="acy_media_browser_image_<?php echo $k; ?>" src="<?php echo ACYMAILING_LIVE.$defaultFolder.$file; ?>" height="135" width="135" style="height: 135px; width:135px;"></img>
											<a href="#" onclick="displayImageFromUrl('<?php echo ACYMAILING_LIVE.$defaultFolder.$file; ?>', 'success', '<?php echo $file; ?>', '<?php echo $imageSize; ?>'); return false;" >
													<div id="acy_media_browser_image_info_<?php echo $k; ?>" style="box-shadow: 0px 3px 3px 3px rgba(0, 0, 0, 0.3); padding-top:40px; text-align:center; vertical-align:middle; color: white; position:absolute; top:0px; left:0px; bottom:0px; right:0px; display:none; background-color: rgba(0,0,0,0.5);">
														<?php echo $file; ?><br/>
														<span class="acy_media_browser_image_size" ><?php echo $imageSize; ?> - <?php echo round((filesize($uploadPath.$file)* 0.0009765625),2).' ko'; ?><br/></span>
													</div>
											</a>
										</li>
										<?php
									}

									if(!$imagesFound){
										echo '<div class="alert">';
										echo JText::_( 'NO_FILE_FOUND' );
										echo '</div>';
									}
									?>
									</ul>

									<!-- Here we give the possibility to import a file or specify and url -->
										<div id="acy_media_browser_actions" >
											<div id="acy_media_browser_containing_block">
												<div id="acy_media_browser_buttons_block" >
														<button type="button"  class="btn" id="upload_image_btn" onclick="displayAppropriateField(this.id)"> <?php echo JText::_('UPLOAD_NEW_IMAGE'); ?></button>
														<?php echo JText::_('ACY_OR'); ?>
														<button type="button"  class="btn" id="import_from_url_btn" onclick="displayAppropriateField(this.id)"> <?php echo JText::_('INSERT_IMAGE_FROM_URL'); ?> </button>
												</div>
												<div id="acy_media_browser_hidden_elements">
														<div id="upload_image" style="position: relative; padding-top:5px;	display:none; text-align: center;">
															<form method="post"  name="adminForm" id="adminForm" enctype="multipart/form-data" style="margin:0px; margin-top:3px;" >
																	<input type="file" style="width:auto;" name="uploadedImage" /><br/>
																	<?php  ?>
															</form>
															<button class="btn btn-primary" type="button" onclick="submitbutton();"> <?php echo JText::_('IMPORT'); ?> </button>
															<span style="position:absolute; top:5px; left:5px;" id="acy_back_from_upload" onclick="displayAppropriateField(this.id)" ><a href="javascript:void(0);">&#8592 <?php echo JText::_('MEDIA_BACK'); ?></a></span>
														</div>
														<div id="import_from_url" style="padding-top:9px; position:relative; ">
															<input type="text" id="acy_media_browser_url_input" class="inputbox" oninput="testImage(this.value, displayImageFromUrl)" value="http://" />
															<?php  ?>
															<div id="acy_media_browser_insert_message"></div>
															<span style="position:absolute; top:5px; left:5px;" id="acy_back_from_url" onclick="displayAppropriateField(this.id)" ><a href="javascript:void(0);">&#8592 <?php echo JText::_('MEDIA_BACK'); ?></a></span>
														</div>
												</div>
											</div>
									</div>
								</td>
								<!-- IMAGE INFORMATION -->
								<td id="acy_media_browser_image_details_row" >
									<div id="acy_media_browser_image_details"  >
											<div id="acy_media_browser_image_selected" style=" max-width:230px; max-height:190px; display:none;	margin:auto; margin-bottom:10px;"></div>
										<div id="acy_media_browser_image_selected_info" style=""></div>
									<div id="acy_media_browser_image_selected_details" >
										<label for="acy_media_browser_image_title" style="float:left;"><?php echo JText::_( 'ACY_TITLE' ); ?></label>
										<input type="text" id="acy_media_browser_image_title" class="inputbox" style="width:100%"  value="" />
										<?php $imageZone = JRequest::getVar( 'image_zone', array(), '', 'array' );
											if(!empty($imageZone)){ ?>
												<label for="acy_media_browser_image_target"><?php echo JText::_( 'ACY_LINK' ); ?></label>
												<input type="text" id="acy_media_browser_image_target" placeholder="<?php echo ACYMAILING_LIVE; ?>" class="inputbox" style="width:100%"  value="" />
											<?php }else{ ?>
															<label for="acy_media_browser_image_width" style="display:inline;"><?php echo JText::_( 'CAPTCHA_WIDTH' ); ?></label>	<input type="text" id="acy_media_browser_image_width"  style="width:23%;"  value="" oninput="calculateSize(0, this.value)" />
															<label for="acy_media_browser_image_height" style="display:inline;"><?php echo JText::_( 'CAPTCHA_HEIGHT' ); ?></label>	<input type="text" id="acy_media_browser_image_height"  style="width:22%;"  value="" oninput="calculateSize(this.value, 0)" />
															<label for="acy_media_browser_image_align" style="display:inline;"><?php echo JText::_( 'ALIGNMENT' ); ?></label>
															<select id="acy_media_browser_image_align" class="chzn-done" style="width:50%">
																	<option value=""><?php echo JText::_( 'NOT_SET' ); ?></option>
																	<option value="left"><?php echo JText::_( 'ACY_LEFT' ); ?></option>
																	<?php  ?>
																	<option value="right"><?php echo JText::_( 'ACY_RIGHT' ); ?></option>
															</select><br/>
										<label for="acy_media_browser_image_margin" style="display:inline;"><?php echo JText::_( 'ACY_MARGIN' ); ?></label>	<input type="text" style="width:23%;"  id="acy_media_browser_image_margin"  value="" /><br/>
										<label for="acy_media_browser_image_border" style="display:inline;"><?php echo JText::_( 'ACY_BORDER' ); ?></label>	<input type="text" style="width:23%;"  id="acy_media_browser_image_border"  value="" />
												<?php } ?>
											</div>
										<button class="btn btn-primary" type="button" onclick="validateImage();window.parent.SqueezeBox.close();" style=" position:absolute; bottom:6px; right:6px; "><?php echo JText::_('INSERT'); ?> </button>
							 </div>
						</td>
				</tr>
		 	</table>
		</div>
		<?php

		$imageZone = JRequest::getVar( 'image_zone', array(), '', 'array' );
		if($imageZone){
			echo '<script>checkSelected(true);</script>';
		}else{
			echo '<script>checkSelected();</script>';
		}
		if(isset($uploadMessage) && $uploadMessage=='success')
			echo  '<script> displayImageFromUrl(\''.ACYMAILING_LIVE.$defaultFolder. $this->imageName.'\',\'success\', \''.$this->imageName.'\');</script>';
		}

	private function _mergeFoldersLists($scannedFolders, $mediaFolders){
		foreach($scannedFolders as $k => $folder){
				$scannedFolders[$k]=trim($folder);
			}
			$scannedFolders = array_unique($scannedFolders);

		foreach($mediaFolders as $k => $folder){
				$mediaFolders[$k]=trim($folder);
		}

		foreach($scannedFolders as $scannedFolder){
				$scannedTempName = strtolower($scannedFolder);
				foreach($mediaFolders as $k => $mediaFolder){
					$mediaTempName = strtolower($mediaFolder);
					if($mediaTempName == $scannedTempName && $mediaFolder!=$scannedFolder){
							$mediaFolderPath = JPath::clean($mediaTempName);
							$mediaUploadPath = JPath::clean(ACYMAILING_ROOT.$mediaFolderPath);

							$scannedFolderPath = JPath::clean($scannedTempName);
							$scannedUploadPath = JPath::clean(ACYMAILING_ROOT.$scannedFolderPath);

							if(JFolder::folders($mediaUploadPath) == JFolder::folders($scannedUploadPath)){
								$mediaFolders[$k] = '';
							}
					}
				}
		}

		$folders = array_merge($mediaFolders, $scannedFolders);
		$folders = array_filter( $folders );
		$folders = array_unique($folders);
		foreach($folders as $k => $folder){
				$folders[$k]=trim($folder);
		}
		sort($folders);

		return $folders;
	}

	private function _generateArborescence($folders){
		foreach($folders as $folder){
			$defaultFolder = $folder;
			$defaultFolder = trim($defaultFolder,DS.' ').DS;
			$defaultFolder = JPath::clean($defaultFolder);
			$uploadPath = JPath::clean(ACYMAILING_ROOT.$defaultFolder);
			if(file_exists($uploadPath)){
				$files = JFolder::folders($uploadPath);
			}else{
				$files = array();
			}

			$subFolders = array();
			foreach($files as $file){
				if(is_dir($uploadPath.$file) && $file!='.' && $file!='..'){
						$subFolders[]=$folder.'/'.$file;
				}
			}

			if(!empty($subFolders)){
				$folders = array_merge($subFolders, $folders);
				$newFolders = $this->_generateArborescence($subFolders);
				if($newFolders != $subFolders) $folders = array_merge($subFolders, $newFolders);
			}
		}
		return $folders;
	}

	private function _generateSpecificFolders($folders){
		$my = JFactory::getUser();

		foreach($folders as $k => $folder){
			$dirName = basename($folder);
			if(strpos($dirName,'{userid}') !== false){
				if(empty($my->id)){
					$folders[$k] = str_replace('{userid}', '', $folders[$k]);
				}else{
					$folders[$k] = str_replace('{userid}', $my->id, $folders[$k]);
				}
			}

			$newFolders = array();
			if(strpos($dirName,'{groupid}') !== false){
				if(!ACYMAILING_J16){
					$groups = array($my->gid);
				}else{
					jimport('joomla.access.access');
					$groups = JAccess::getGroupsByUser($my->id,false);
				}

				if(empty($groups)){
					str_replace('{groupid}', '', $dirName);
				}else{
					foreach($groups as $group){
						$newFolders[] = str_replace('{groupid}', $group, $folders[$k]);
					}
					$folders[$k] = '';
				}
			}
		}

		$folders = array_merge($folders, $newFolders);
		$folders = array_filter( $folders );
		sort($folders);

		return $folders;
	}

		private function _importImage($image, $uploadPath){
			$app = JFactory::getApplication();
			$config =& acymailing_config();
			$additionalMsg='';

			if($image["error"] > 0){
				$this->message = "Error Uploading code: " . $image["error"] . "<br/>";
				return false;
			}

			if(!preg_match('#\.(jpg|jpeg|png|gif)$#Ui',$image["name"],$extension)){
				$ext = substr($image["name"], strrpos($image["name"], '.')+1);
				$this->message = JText::sprintf( 'ACCEPTED_TYPE', $ext, 'jpg, jpeg, png, gif' );
				return false;
			}

			$image["name"] = str_replace(array('.',' '),'_',substr($image["name"], 0,strrpos($image["name"], '.'))).'.'.$extension[1];

			$imageSize = getimagesize($image['tmp_name']);
			if(empty($imageSize)){
				$this->message = 'Invalid image';
				return false;
			}

			if(file_exists($uploadPath.DS.$image["name"])){
				$i = 1;
				$nameFile = preg_replace("/\\.[^.\\s]{3,4}$/", "", $image["name"]);
				$ext = substr($image["name"], strrpos($image["name"], '.')+1);
				while(file_exists($uploadPath. DS . $nameFile.'_'.$i.'.'.$ext)){
					$i++;
				}

				$image["name"] = $nameFile.'_'.$i.'.'.$ext;
				$additionalMsg = '<br/>'.JText::sprintf('FILE_RENAMED', $image["name"]);
			}

			if(!JFile::upload($image["tmp_name"],rtrim($uploadPath,DS).DS. $image["name"])){
				if(!move_uploaded_file($image["tmp_name"],	rtrim($uploadPath,DS).DS. $image["name"])){
					$this->message = JText::sprintf( 'FAIL_UPLOAD','<b><i>'.$image["tmp_name"].'</i></b>','<b><i>'.rtrim($uploadPath,DS).DS. $image["name"].'</i></b>');
					return false;
				}
			}

			if($imageSize[0]>1000){
				$pictureHelper = acymailing_get('helper.acypict');
				if($pictureHelper->available()){
					$pictureHelper->maxHeight=9999;
					$pictureHelper->maxWidth=700;
					$pictureHelper->destination =  $uploadPath;
					$thumb = $pictureHelper->generateThumbnail(rtrim($uploadPath,DS).DS. $image["name"], $image["name"]);
					$resize = JFile::move($thumb['file'],$uploadPath.DS.$image["name"]);
					if($thumb) $additionalMsg .='<br/>'.JText::_( 'IMAGE_RESIZED' );
				}
			}
			$this->message = '<strong>'.JText::_( 'SUCCESS_FILE_UPLOAD' ).'</strong>'.$additionalMsg;
			$this->imageName = $image["name"];
			return true;
		}
}
