<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

echo FSJ_Page::Popup_Begin("FSJ_TM_ADD");
?>

<style>
div.modal-body
{
	padding-top: 0px !important;
	overflow-x: hidden;
}
</style>
<div>

<div class="subhead">
	<div id="filter-bar" class="btn-toolbar">
		<div class="pull-left">
			<div class="input-append">
				<input type="text" class="input-small" name="filter_search" placeholder="<?php echo JText::_('FSJ_TM_SEARCH'); ?>" id="filter_search" value="" title="<?php echo JText::_('FSJ_TM_SEARCH'); ?>" />
				<button class="btn tip hasTooltip" type="button" id="clear_search" title="<?php echo JText::_('FSJ_SEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="pull-left" style="margin-left: 8px;">
			<select id="filter_category" onchange="filter_cat()">
				<option value=""> - <?php echo JText::_('FSJ_TM_SELECT_CATEGORY'); ?> - </option>	
			</select>
		</div>
		<div class="pull-left" style="margin-left: 8px;">
			<?php 
			
			$filter[] = JHTML::_('select.option', '0|g.general', JText::_('FSJ_TM_SITE'), 'id', 'title');
			$filter[] = JHTML::_('select.option', '1|g.general', JText::_('FSJ_TM_ADMIN'), 'id', 'title');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'folder_helper.php');
			$folder_extra = FSJ_TM_Folder_Helper::ScanForComponentLanguages();
			
			foreach ($folder_extra as $folder)
			{
				$admin = (int)$folder->admin;
				$key = "{$admin}|{$folder->prefix}.{$folder->component}";
				$display = FSJ_TM_Folder_Helper::describePath($admin, $folder->prefix, $folder->component);
				
				$filter[] = JHTML::_('select.option', $key, $display, 'id', 'title');
			}   
			
			$mainframe = JFactory::getApplication();
			$filter_val	= $mainframe->getUserStateFromRequest( 'fsj_tm_add.filter_xpath', 'filter_xpath', '', 'string' );
			
			echo JHTML::_('select.genericlist',  $filter, 'filter_xpath', 'class="inputbox" size="1" onchange="filter_xpath();"', 'id', 'title', $filter_val);
			
			?>
		</div>
	</div>
</div>

<p><?php echo JText::_('FSJ_TM_CURRENT_PATH'); ?>: <strong><?php echo FSJ_TM_File_Helper::$current_path; ?></strong></p>

<div id="file_list">
	<div>
	<?php $i = 0; $curcat = ""; ?>
	<?php foreach ($this->files as $file): ?>
		<?php if ($file->tstate != 1) continue; ?>
		<?php if ($file->category == "") continue; ?>
		<?php if ($file->category != $curcat): ?>
			</div>
			<div class="pf_category" category="<?php echo $file->category; ?>">
				<h4><?php echo $file->category; ?></h4>
			<?php $curcat = $file->category; ?>
		<?php endif; ?>
		<?php include JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'pickfiles'.DS.'snippet'.DS.'_file.php'; ?>	
				
		<?php $i++; ?>
	<?php endforeach; ?>
	<?php foreach ($this->files as $file): ?>
		<?php if ($file->tstate != 1) continue; ?>
		<?php if ($file->category != "") continue; ?>
		<?php if ($file->category != $curcat): ?>
			</div>
			<div class="pf_category" category="none" display="<?php echo JText::_('FSJ_TM_NO_CATEGORY'); ?>">
				<h4><?php echo JText::_('FSJ_TM_NO_CATEGORY'); ?></h4>
			<?php $curcat = $file->category; ?>
		<?php endif; ?>
		<?php include JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_transman'.DS.'views'.DS.'pickfiles'.DS.'snippet'.DS.'_file.php'; ?>	

		<?php $i++; ?>
	<?php endforeach; ?>
	</div>
</div>

</div>

<script>

jQuery(document).ready(function () {

	jQuery('div.modal-body').css('height', jQuery('div.modal-body').height() + "px");

    jQuery('#filter_search').each(function () {
        var elem = jQuery(this);

        // Save current value of element
        elem.data('oldVal', elem.val());

        // Look for changes in the value
        elem.bind("propertychange keyup input paste", function (event) {
            // If value has changed...
            if (elem.data('oldVal') != elem.val()) {
                // Updated stored value
                elem.data('oldVal', elem.val());

                //tm_input_changed(elem);
				
				update_filter(elem.val());
            }
        });
    });
	
	var current_xpath = jQuery('#filter_xpath').val();
	
	window.parent.jQuery('li.file').each( function() {
		var key = jQuery(this).attr('key');
		if (key != current_xpath)
			return;
			
		var filename = jQuery(this).attr('filename');
		
		jQuery('.pf_row[filename="' + filename + '"]').each( function () {
			jQuery(this).html("<i class='icon-ok'></i> " + filename);
		});
	});
	
	jQuery('#clear_search').click(function () {
		jQuery('#filter_search').val("");
		update_filter("");
	});
	
	jQuery('#add_checked').click(function () {
		var filelist = new Array();
		
		jQuery('#file_list input').each( function () {
			var cb = jQuery(this);
			if (cb.is(':checked'))
			{
				filelist.push(cb.val());
			}
		});
		
		window.parent.tm_pf_add_files_do(filelist, '<?php echo $this->client; ?>');
	});
	
	build_cat_select();
});

function tm_pu_word_in(find, line) {
    find = find.toString().toLowerCase();
    line = line.toString().toLowerCase();
    if (line.indexOf(find) === -1)
        return false;

    return true;
}

function update_filter(search)
{
	//alert(value);
	
	var words = search.split(" ");
	
	jQuery('#file_list div.pf_row').each(function () {
	
		var show = true;
		
	    if (search.length > 0) {
            var origtext = jQuery(this).attr('filename');
		
            for (i = 0; i < words.length; i++ ) {
                var word = words[i];

                if (!tm_pu_word_in(word, origtext)) {
                    show = false;
                }
            }
        }
		
        if (show) {
			jQuery(this).show();
        } else {
            jQuery(this).hide();
        }
		
	});
}

function build_cat_select() {
    jQuery('div.pf_category').each(function () {
        var cat = jQuery(this).attr('category');
		var disp = jQuery(this).attr('display') || cat;
		
        var opt = "<option value='" + cat + "'>" + disp + "</option>";

        jQuery('#filter_category').append(opt);
    });
}

function filter_cat() {
	var selcat = jQuery('#filter_category').val();
	
	jQuery('div.pf_category').each(function () {
        var cat = jQuery(this).attr('category');
		
		if (cat == selcat || selcat == "")
		{
			jQuery(this).show();
		} else {
			jQuery(this).hide();		
		}
    });
}

function filter_xpath()
{
	var value = jQuery('#filter_xpath').val();
	var url = "index.php?option=com_fsj_transman&view=pickfiles&tmpl=component&filter_xpath=" + encodeURIComponent(value);
	window.location = url;
}		

function addChecked()
{	
	jQuery('div.pf_row input').each( function () {
		if (jQuery(this).is(':checked'))
		{
			var key = jQuery(this).attr('value');
			var cat = jQuery(this).attr('category');
			window.parent.add_file_wrap(key, cat, false);
		}
	});
	
	window.parent.jQuery("#fsj_modal").modal("hide");
	window.parent.rebuild_file_list();
}

</script>
<?php 

$buttons = "<a class='btn btn-primary' href='#' onclick='addChecked();return false;'>".JText::_('FSJ_TM_ADD_CHECKED')."</a>";
$buttons .= "<a class='btn' href='#' onclick='window.parent.jQuery(\"#fsj_modal\").modal(\"hide\");'>".JText::_('FSJ_TM_CANCEL')."</a>";

echo FSJ_Page::Popup_End($buttons);
 ?>