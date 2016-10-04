<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<script type="text/javascript">
function CaracMax(text, max)
{
	if (text.value.length >= max)
	{
		text.value = text.value.substr(0, max - 1) ;
	}
}

<?php if(version_compare(JVERSION,'1.6.0','>=')){ ?>
Joomla.submitbutton = function(pressbutton) {
<?php } else { ?>
function submitbutton(pressbutton) {
<?php } ?>
	   if (pressbutton == 'cancel') {
		   submitform(pressbutton);	
		   return;
	   }
	   var form = document.adminForm;
	   var iserror = 0;
	   var errorMSG = '';
		
	   <?php if ($this->nbcats > 1) { ?>
			var srcList = eval( 'form.selected_cats' );
			var srcLen = srcList.length;
	  
		   if (srcLen == 0)
		   {
				errorMSG += <?php echo json_encode(JText::_('ADSMANAGER_FORM_CATEGORY')); ?>+" : "+<?php echo json_encode(JText::_('ADSMANAGER_REGWARN_ERROR')); ?>+'\n';
				srcList.style.background = "red";
				iserror=1;
			}
			else
			{
				for (var i=0; i < srcLen; i++) {
					srcList.options[i].selected = true;
				}
			}
		<?php } ?>
		
		if(iserror==1) {
			alert(errorMSG);
		} else {
		
			<?php
			if (function_exists("loadEditFormCheck")) {
				loadEditFormCheck("admin");
			}
		   ?>
	        
		   <?php if ($this->nbcats > 1) { ?>
			srcList.name = "selected_cats[]"; 
		   <?php } ?>
		   submitform(pressbutton);
		}
   }

function updateFields() {
	var form = document.adminForm;
	var singlecat = 0;
	var length = 0;
	
	if ( typeof(document.adminForm.category ) != "undefined" ) {
		singlecat = 1;
		length = 1;
	}
	else if ( typeof(document.adminForm.selected_cats ) != "undefined" ) 
	{
		length = form.selected_cats.length;
	} else {
		length = 1;
	}
	
	<?php
	foreach($this->fields as $field)
	{ 
		if (strpos($field->catsid, ",-1,") === false)
		{
			$name = $field->name;
			if (($field->type == "multicheckbox")||($field->type == "multiselect"))
				$name .= "[]";
		?>
		var input = document.getElementById('<?php echo $name;?>');
		var trzone = document.getElementById('tr_<?php echo $field->name;?>');
		if (((singlecat == 0)&&(length == 0))||
		    ((singlecat == 1)&&(document.adminForm.category.value == 0)))
		{
			if (input != null)
				input.style.visibility = 'hidden';
			trzone.style.visibility = 'hidden';
			trzone.style.display = 'none';
		}
		else
		{
			for (var i=0; i < length; i++) {
				
				
				var field_<?php echo $field->name;?> = '<?php echo $field->catsid;?>';
				var temp;
				if (singlecat == 0)
					temp = form.selected_cats.options[i].value;
				else
					temp = document.adminForm.category.value;
					
				var test = field_<?php echo $field->name;?>.indexOf( ","+temp+",", 0 );
				if (test != -1)
				{
					if (input != null)
						input.style.visibility = 'visible';
					trzone.style.visibility = 'visible';
					trzone.style.display = '';
					break;
				}
				else
				{
					if (input != null)
						input.style.visibility = 'hidden';
					trzone.style.visibility = 'hidden';
					trzone.style.display = 'none';
				}
			}
		}
	<?php
		}
	} 
	?>
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
<tr>
<td><?php echo JText::_('ADSMANAGER_FORM_CATEGORY');?></td>
<td>
<?php
if ($this->nbcats == 1)
{
	$without_page_reload = 0;
	
	if ($this->catid != 0)
		$catid = $this->catid;
	else if (isset($this->content->cats[0]))
		$catid = $this->content->cats[0]->catid;
	else 
		$catid = 0;
	if ($without_page_reload == 1) {
		$selectid = "category";
	} else {
		$selectid = "categoryselect";
	}
	switch($this->conf->single_category_selection_type) {
		default:
		case 'normal':
			JHTMLAdsmanagerCategory::displayNormalCategories($selectid,$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed,"display_price"=>true));break;
		case 'color':
			JHTMLAdsmanagerCategory::displayColorCategories($selectid,$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
		case 'combobox':
			JHTMLAdsmanagerCategory::displayComboboxCategories($selectid,$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
			break;
		case 'cascade':
			$separator = "<br/>";
			JHTMLAdsmanagerCategory::displaySplitCategories($selectid,$this->cats,$catid,array("root_allowed"=>$this->conf->root_allowed));break;
	}
	?>
	</td></tr></table>
	<?php if (@$this->content->id) { 
			$write_url = 'index.php?option=com_adsmanager&c=contents&task=edit&id='.$this->content->id;
		} else {
			$write_url = 'index.php?option=com_adsmanager&c=contents&task=edit';
		}?>
	<script type="text/javascript">
		jQ(document).ready(function() {
			jQ('#categoryselect').change(function() {
				if (jQ(this).val() != "") {
					location.href = "<?php echo $write_url?>&catid="+jQ(this).val();
				}
			});
		});
		</script>
	<?php
	if ($without_page_reload == 0) {
	echo "<input type='hidden' name='category' value='$catid' />";
	}
	?><table border='0'>
	<?php 
	
}
else
{
	?>
	</td></tr></table>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
	<table border='0'><tr><td colspan="2">
	<?php
	if (isset($this->content->catsid)) {
		$catids = $this->content->catsid;
	} else {
		$catids = array();
	}
	JHTMLAdsmanagerCategory::displayMultipleCategories("cats",$this->cats,$catids,array("root_allowed"=>$this->conf->root_allowed),$this->nbcats);
	
}
?>
<?php if (isset($this->content->userid)) { $userid = $this->content->userid; } else { $userid = $this->userid; } ?>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_USER'); ?></td>
<td>
<select name="userid" id="userid">
<option value=""></option>
<?php foreach($this->users as $user) { ?>
<option value="<?php echo $user->id;?>" <?php if ($user->id == $userid) { echo "selected"; } ?>><?php echo $user->username; ?></option>
<?php } ?>
</select>
</td>
<td>&nbsp;</td>
</tr>


<tr>
<td><?php echo JText::_('ADSMANAGER_TH_DATE'); ?></td>
<td>
<?php echo JHTML::_('behavior.calendar'); 
if (!isset($this->content->id)) 
	$created_date = date("Y-m-d");
else
	$created_date = $this->content->date_created;
$time = date('H:i:s',strtotime($created_date)); 
echo JHTML::_('calendar', $created_date, "date_created", "date_created", "%Y-%m-%d $time", null); ?>
</td>
<td>&nbsp;</td>
</tr>

<?php 
if (!isset($this->content->id)) 
	$expiration_date =  date("Y-m-d",time() + $this->conf->ad_duration * 3600 * 24);
else
	$expiration_date = $this->content->expiration_date;
?>
<tr>
<td><?php echo JText::_('ADSMANAGER_TH_EXPIRATION_DATE'); ?></td>
<td>
<?php echo JHTML::_('calendar', $expiration_date, "expiration_date", "expiration_date", "%Y-%m-%d", null); ?>
</td>
<td>&nbsp;</td>
</tr>


<?php
foreach($this->fields as $field)
{
	$fieldform = $this->field->showFieldForm($field,$this->content,$this->default);
	if ($fieldform != null) {
		echo "<tr id=\"tr_{$field->name}\"><td>".$this->field->showFieldLabel($field,$this->content,$this->default)."</td>";
		echo "<td>".$fieldform."</td></tr>";
	}
}
?>

<!-- fields -->
<!-- image -->
<tr>
	<td><?php echo JText::_('Pictures')?></td>
	<td id="uploader_td">
	<?php echo TImage::displayImageUploader($this->conf,$this->content,$this->adext);?>
	<?php 
	if (PAIDSYSTEM) {
		$paidconfig = getPaidSystemConfig(); 
		if (isset($paidconfig->enable_image_pack) && $paidconfig->enable_image_pack == 1) {
		 ?>
		<input type="checkbox" id="images_pack" value="1" <?php if (@$this->adext->images == 1) echo "checked" ?> name="images_pack" />
		<span class="option_photo"><?php echo sprintf(JText::_('PAIDSYSTEM_IMAGE_PACK_NB_IMAGES'),$paidconfig->num_images,getPrice($paidconfig->image_price))?></span>				
		<script type="text/javascript">
		<?php $maximages = $this->conf->nb_images+$paidconfig->num_images; ?>
			function updateImagesPackPrice() {
				if(jQ('#images_pack').is(':checked')) {
					jQ('#totalcount').html('<?php echo $maximages?>');
					max_total_file_count = <?php echo $maximages?>;
				} else {
					jQ('#totalcount').html('<?php echo $this->conf->nb_images?>');
					max_total_file_count = <?php echo ($this->conf->nb_images)?>;
				}
			}
			
			jQ('#images_pack').click(function () {
				updateImagesPackPrice();
			});
			updateImagesPackPrice();
		</script>
	<?php 
		}
	} ?>
	</td>
	<td>&nbsp;</td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_TH_PUBLISH'); ?></td>
<td>
<select name="published" id="published">
<option value="1" <?php if (@$this->content->published == 1) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_PUBLISH'); ?></option>
<option value="0" <?php if (@$this->content->published == 0) { echo "selected"; } ?>><?php echo JText::_('ADSMANAGER_NO_PUBLISH'); ?></option>
</select>
</td>
<td>&nbsp;

</td>
</tr>


<?php if (($this->conf->metadata_mode != 'nometadata')&&
		  ($this->conf->metadata_mode != 'automatic')) { ?>
<tr><td colspan='2'><strong><?php echo JText::_('ADSMANAGER_METADATA')?></strong></td></tr>
<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_DESCRIPTION'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_description"><?php echo htmlspecialchars(@$this->content->metadata_description)?></textarea>			
</td>
</tr>

<tr>
<td><?php echo JText::_('ADSMANAGER_METADATA_KEYWORDS'); ?></td>
<td>
<textarea cols="50" rows="10" name="metadata_keywords"><?php echo htmlspecialchars(@$this->content->metadata_keywords)?></textarea>			
</td>
</tr>

<?php } ?>

<?php 
if (function_exists("editAdminPaidAd")){
	editAdminPaidAd($this->content,$this->isUpdateMode,$this->conf);
}?>
<?php
    if(isset($this->conf->publication_date) && $this->conf->publication_date == 1) {
?>
<tr>
<td><?php echo JText::_('ADSMANAGER_PUBLICATION_DATE'); ?></td>
<td>
    <?php 
        if (isset($this->content->publication_date) && $this->content->publication_date != '0000-00-00 00:00:00'){
            $publication_date = $this->content->publication_date;
        }else{
            $publication_date = "";
        }
    
        $options = array();
        $options['size'] = 25;
        $options['maxlength'] = 19;
        $options['class'] = 'adsmanager_required';
        $options['mosReq'] = '1';
        $options['mosLabel'] = htmlspecialchars(JText::_('ADSMANAGER_PUBLICATION_DATE'));
        $return = JHTML::_('behavior.calendar');
        $return .= JHTML::_('calendar', $publication_date, "publication_date", "publication_date", '%Y-%m-%d', $options);
//      $return .= "<script type='text/javascript'>jQ(document).ready(function() {jQ('#publication_date').val(".json_encode($publication_date).");});</script>";

        echo $return;
    ?>
</td>
</tr>
<?php } ?>
</table>
<input type="hidden" name="id" value="<?php echo @$this->content->id; ?>" />
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="c" value="contents" />
<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
function checkdependency(child,parentname,parentvalue) {
	//Simple checkbox
	if (jQ('input[name="'+parentname+'"]').is(':checkbox')) {
		//alert("test");
		if (jQ('input[name="'+parentname+'"]').attr('checked')) {
			jQ('#adminForm #f'+child).show();
			jQ('#adminForm #tr_'+child).show();
		}
		else {
			jQ('#adminForm #f'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #f'+child).is(':checkbox') || jQ('#adminForm #f'+child).is(':radio')) {
				jQ('#adminForm #f'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #f'+child).val = '';
			}
		} 
	}
	//If checkboxes or radio buttons, special treatment
	else if (jQ('input[name="'+parentname+'"]').is(':radio')  || jQ('input[name="'+parentname+'[]"]').is(':checkbox')) {
		var find = false;
		var allVals = [];
		jQ("input:checked").each(function() {
			if (jQ(this).val() == parentvalue) {	
				jQ('#adminForm #f'+child).show();
				jQ('#adminForm #tr_'+child).show();
				find = true;
			}
		});
		
		if (find == false) {
			jQ('#adminForm #f'+child).hide();
			jQ('#adminForm #tr_'+child).hide();
			
			//cleanup child field 
			if (jQ('#adminForm #f'+child).is(':checkbox') || jQ('#adminForm #f'+child).is(':radio')) {
				jQ('#adminForm #f'+child).attr('checked', false);
			}
			else {
				jQ('#adminForm #f'+child).val = '';
			}
		}

	}
	//simple text
	else if (jQ('#adminForm #f'+parentname).val() == parentvalue) {
		jQ('#adminForm #f'+child).show();
		jQ('#adminForm #tr_'+child).show();
	} 
	else {
		jQ('#adminForm #f'+child).hide();
		jQ('#adminForm #tr_'+child).hide();
		
		//cleanup child field 
		if (jQ('#adminForm #f'+child).is(':checkbox') || jQ('#adminForm #f'+child).is(':radio')) {
			jQ('#adminForm #f'+child).attr('checked', false);
		}
		else {
			jQ('#adminForm #f'+child).val = '';
		}
	}
}
function dependency(child,parentname,parentvalue) {
	//if checkboxes
	jQ('input[name="'+parentname+'[]"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	//if buttons radio
	jQ('input[name="'+parentname+'"]').change(function() {
		checkdependency(child,parentname,parentvalue);
	});
	jQ('#f'+parentname).click(function() {
		checkdependency(child,parentname,parentvalue);
	});
	checkdependency(child,parentname,parentvalue);
}

jQ(document).ready(function() {
	<?php foreach($this->fields as $field) { 
		if (@$field->options->is_conditional_field == 1) { ?>
	dependency('<?php echo $field->name?>',
			   '<?php echo $field->options->conditional_parent_name?>',
			   '<?php echo $field->options->conditional_parent_value?>');
		<?php } 
	}?>
	updateFields();
});
</script>