<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class TImage {

	static public function displayImageUploader($conf,$content,$adext) {
                
                $app = JFactory::getApplication();
        
				$imagepack = false;
				if (PAIDSYSTEM) {
					$paidconfig = getPaidSystemConfig();
					if (isset($paidconfig->enable_image_pack) && $paidconfig->enable_image_pack == 1) {
						$imagepack = true;
					}
				}

				$document = JFactory::getDocument();
				$baseurl = JURI::root();

				$document->addStyleSheet($baseurl.'components/com_adsmanager/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css');	
				$document->addScript($baseurl.'components/com_adsmanager/js/plupload/plupload.full.js');
				$document->addScript($baseurl.'components/com_adsmanager/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
				$lang = JFactory::getLanguage();
				$tag = $lang->getTag();
				$tag = substr($tag,0,strpos($tag,'-'));
				if (file_exists(JPATH_BASE."/components/com_adsmanager/js/plupload/i18n/{$tag}.js"))
					$document->addScript($baseurl."components/com_adsmanager/js/plupload/i18n/{$tag}.js");	


				$maxnbimages = $conf->nb_images;
				
				if (PAIDSYSTEM) {
					if(@$adext->images == 1) {
						if ($imagepack == true) {
							$paidconfig = getPaidSystemConfig();
							$maxnbimages += $paidconfig->num_images;
						}
					}
					if ($imagepack == false) {
						$app = JFactory::getApplication();
						
						if ($app->isAdmin() == true) {
							$maxnbimages += $paidconfig->num_images;
						}
					}
				}
				?>
				<div id="imagesupload"></div>
				<div><?php echo JText::_('ADSMANAGER_MAX_NUMBER_OF_PICTURES')?>: <span id="maximum"></span> / <span id="totalcount"><?php echo $maxnbimages;?></span></div>
				<?php
					$width = $conf->max_width_t + 10; 
					$height = $conf->max_height_t + 40; 
					?>
				<style>				
					#currentimages li { 
                        width: <?php echo $width ?>px; 
                        height: <?php echo $height ?>px;
                    }
				</style>
				<ul id="currentimages">
					<?php 
					$currentnbimages = 0;
					$i=1;
					if (isset($content->images)) {
						$ad_id = $content->id;
						foreach($content->images as $img) {
							$dir = JURI_IMAGES_FOLDER."/tmp/";
							if (isset($img->tmp) && ($img->tmp == 1)) {
								$thumb = JURI_IMAGES_FOLDER."/waiting/".$img->thumbnail;
							} else {
								$thumb = JURI_IMAGES_FOLDER."/".$img->thumbnail;
							}
							$index = $img->index;
							echo "<li class='ui-state-default' id='li_img_$index'>";
                                echo "<div class='thumbImgCont'>";
                                    echo "<img src='".$thumb."?time=".time()."' align='top' border='0' alt='image".$ad_id."' />";
                                echo "</div>";
                                echo "<div class='thumbImgDelete'>";
                                    echo "<a href='#' name='cb_image$i' onClick='removeImage($index)' value='1' title='image' />".JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE')."</a>";
                                echo "</div>";
							echo "</li>";
							$currentnbimages++;
							$i++;
						}
					}
				?>
				</ul>
				<div style="clear: both"></div>
				<input type="hidden" name="nbCurrentImages" id="nbCurrentImages" value="0" />
				<?php if (isset($content->pendingdata->delimages) && count($content->pendingdata->delimages) > 0) { 
					foreach($content->pendingdata->delimages as $img) {
						$indexes[] = $img->index;
					}
					$deleted_images = implode(',',$indexes);
				} else {
					$deleted_images = "";
				}?>
				<input type="hidden" name="deleted_images" id="deleted_images" value="<?php echo $deleted_images?>"/>
				<input type="hidden" name="orderimages" id="orderimages" value="" />
			<script type="text/javascript">
			var current_uploaded_files_count = <?php echo (int)$currentnbimages?>;
			var nb_files_in_queue = 0;
			var max_total_file_count =  <?php echo (int) $maxnbimages?>;
			var upload_url = <?php echo json_encode(JURI::root().'index.php?option=com_adsmanager&task=upload&tmpl=component')?>;

			var max_width = <?php echo (int)$conf->max_width?>;
			var max_height = <?php echo (int)$conf->max_height?>;
			var base_url = <?php echo json_encode(JURI::root())?>;

			var text_image_delete = <?php echo json_encode(JText::_('ADSMANAGER_CONTENT_DELETE_IMAGE'))?>;

			var text_alert_max_images_reached = <?php echo json_encode(JText::_(sprintf("%s image(s) maximum",$maxnbimages)))?>;

			var text_confirm_delete_image = <?php echo json_encode(JText::_('ADSMANAGER_CONFIRM_DELETE_IMAGE'))?>;
			
			function removeTmpImage(fileid){
				if (confirm(text_confirm_delete_image)) {
					jQ('#li_img_'+fileid).remove();
					var imagesupload = jQ('#imagesupload').pluploadQueue();
					jQ.each(imagesupload.files, function(i, file) {

						if (file != undefined) {
							if (file.id == fileid)
								imagesupload.removeFile(file);
						}
					});
					var inputCount = 0, inputHTML= "";
					jQ.each(imagesupload.files, function(i, file) {
						if (file.status == plupload.DONE) {
							if (file.target_name) {
								inputHTML += '<input type="hidden" name="imagesupload_' + inputCount + '_tmpname" value="' + plupload.xmlEncode(file.target_name) + '" />';
							}
		
							inputHTML += '<input type="hidden" name="imagesupload_' + inputCount + '_id" value="' + plupload.xmlEncode(file.id) + '" />';
							inputHTML += '<input type="hidden" name="imagesupload_' + inputCount + '_name" value="' + plupload.xmlEncode(file.name) + '" />';
							inputHTML += '<input type="hidden" name="imagesupload_' + inputCount + '_status" value="' + (file.status == plupload.DONE ? 'done' : 'failed') + '" />';
		
							inputCount++;
		
							jQ('#imagesupload_count').val(inputCount);
						} 
					});
					jQ('#pluploadfield').html(inputHTML);
					<?php if (!$app->isAdmin() && (PAIDSYSTEM && $imagepack == false)) { ?>
						updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
												   current_uploaded_files_count+nb_files_in_queue-1);
					<?php } ?>
					nb_files_in_queue = imagesupload.files.length;
					setCurrentFileCount();
				} else {
					jQ('#li_img_'+fileid+' input:checkbox').attr('checked',false);
				}
			}
			
			function removeImage(index) {
				if (confirm(text_confirm_delete_image)) {
					deleted_images = jQ('#deleted_images').val();
					if (deleted_images == "")
						deleted_images = index;
					else
						deleted_images = deleted_images+","+index;
					jQ('#deleted_images').val(deleted_images);
					
					jQ('#li_img_'+index).remove();
					<?php if (!$app->isAdmin() && (PAIDSYSTEM && $imagepack == false)) { ?>
						updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
												   current_uploaded_files_count+nb_files_in_queue-1);
					<?php } ?>
					current_uploaded_files_count -= 1;
					setCurrentFileCount();
				} else {
					jQ('#li_img_'+index+' input:checkbox').attr('checked',false);
				}
			}
			
			function setCurrentFileCount() {
				jQ('#maximum').html(current_uploaded_files_count+nb_files_in_queue);
				jQ('#nbCurrentImages').val(current_uploaded_files_count+nb_files_in_queue);
				jQ( "#currentimages" ).sortable(
					{
					 placeholder: "ui-state-highlight",
					 stop: function(event, ui) { 
						 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
					 },
					 create:function(event,ui) {
						 jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
					}
					}
					 );
				
				jQ( "#currentimages" ).disableSelection();
				jQ('#orderimages').val(jQ('#currentimages').sortable('toArray'));
			}
			
			function setTotalFileCount(number) {
				jQ('#totalcount').html(number);
				if (number == 0) {
					$('#tr_images').remove();
				}
			}
			
			setCurrentFileCount();
			// Convert divs to queue widgets when the DOM is ready
			jQ(function() {
				jQ("#imagesupload").pluploadQueue({
					// General settings
					runtimes : 'html5,flash,html4',
					url : upload_url,
					max_file_size : '10mb',
					chunk_size : '1mb',
					unique_names : true,
			
					// Resize images on clientside if we can
					resize : {width : max_width, height : max_height, quality : 100},
			
					// Specify what files to browse for
					filters : [
						{title : "Image files", extensions : "jpeg,jpg,gif,png"}
					],
			
					// Flash settings
					flash_swf_url : base_url+'components/com_adsmanager/js/plupload/plupload.flash.swf',

					init : {
						FilesAdded: function(up, files) {
							maxnewimages = max_total_file_count - current_uploaded_files_count;
							// Check if the size of the queue is bigger than max_file_count
							if(up.files.length > maxnewimages)
							{
								// Removing the extra files
								while(up.files.length > maxnewimages)
								{
									if(up.files.length > maxnewimages)
										up.removeFile(up.files[maxnewimages]);
								}
								alert(text_alert_max_images_reached);
							}
							nb_files_in_queue = up.files.length;
							<?php if (!$app->isAdmin() && (PAIDSYSTEM && $imagepack == false)) { ?>
							updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
	    							   current_uploaded_files_count+up.files.length);
						    <?php }?>
							setCurrentFileCount();
							this.start();
						},
						FilesRemoved: function(up, files) {
							<?php if (!$app->isAdmin() && (PAIDSYSTEM && $imagepack == false)) { ?>
							updatePaidCurrentFileCount(current_uploaded_files_count+nb_files_in_queue,
													   current_uploaded_files_count+up.files.length);
							<?php } ?>
							nb_files_in_queue = up.files.length;
							setCurrentFileCount();
						},
						FileUploaded: function(up, file,info) {
							name = '<?php echo JURI_IMAGES_FOLDER; ?>/uploaded/'+file.target_name;
                            html = '<li class="ui-state-default" id="li_img_'+file.id+'">';
                            html += '<div class="thumbImgCont">';
                            html += '<img src="'+name+'" align="top" border="0" />';
                            html += "</div>";
                            html += "<div class='thumbImgDelete'>";
                            html += "<a href='#' onClick='removeTmpImage(\""+file.id+"\"); return false;' value='1' title=''>"+text_image_delete+"</a>";
                            html += "</div>";
							html += "</li>";
							jQ('#currentimages').append(html);
							setCurrentFileCount();
						}
					}
				});
			});
			</script>
<?php }

	static function displayImageUploaderFormChecker($nbcats=1) {
		?>
		nbCurrentImages = document.getElementById('nbCurrentImages').value;
	
		if(max_total_file_count < nbCurrentImages) {
			if(max_total_file_count == 1){
				errorMSG += "Veuillez selectionner l'option photos pour pouvoir mettre plus d'une image."+'\n';
			} else {
				errorMSG += 'Vous avez trop de photos, le maximum autorisé est de '+max_total_file_count+'.'+'\n';
			}
			iserror=1;
		}
		<?php
	}
}