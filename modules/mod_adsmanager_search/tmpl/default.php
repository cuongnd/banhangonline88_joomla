<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if($type == "horizontal") {
    $class = 'floatleft';
} else {
    $class = 'span12';
}
if($display_cat_label !== 0 || isset($searchfields))
    $searchLabel = '&nbsp;';
else
    $searchLabel = '';
?>
<div class="juloawrapper">
    <?php
        if($itemid != 0) {
			$input = JFactory::getApplication()->input;
			$itemid = $input->get('Itemid',0,'integer');
			$link = TRoute::_("index.php?option=com_adsmanager&view=result&Itemid=".$itemid);
		} else {
			$link = TRoute::_("index.php?option=com_adsmanager&view=result");
		}
    ?>
    <form action="<?php echo $link; ?>" id="modsimplesearch-form" method="post">
    <div class="row-fluid adsmanager_search_module<?php echo $moduleclass_sfx; ?>">
    <?php if ($search_by_text == 1){ ?>
                <div class="<?php echo $class; ?>">
                    <div class="control-group">
                        <label class="control-label" for="tsearch"><?php echo $searchLabel ?></label>
                        <div class="controls">
                            <input type="text" name="tsearch" placeholder="<?php echo JText::_('ADSMANAGER_LIST_SEARCH'); ?>" value="<?php echo $text_search; ?>" />
                        </div>
                    </div>
                </div>
    <?php }?>
            <?php if ($search_by_cat == 1) { ?>
                <div class="<?php echo $class; ?>">
                    <div class="control-group">
                        <?php if($display_cat_label == 1): ?>
                            <label class="control-label" for="catid"><?php echo JText::_('ADSMANAGER_SELECT_CATEGORY_LABEL') ?></label>
                        <?php else: ?>
                            <label class="control-label" for="catid"><?php echo $searchLabel ?></label>
                        <?php endif; ?>
                        <div class="controls">
        <?php 
        switch(@$conf->single_category_selection_type) {
            default:
            case 'normal':
                JHTMLAdsmanagerCategory::displayNormalCategories("catid",$cats,$catid,array("allow_empty"=>true,'id'=>"catid-".$moduleId));break;
            case 'color':
                JHTMLAdsmanagerCategory::displayColorCategories("catid",$cats,$catid,array("allow_empty"=>true,'id'=>"catid-".$moduleId));break;
            case 'combobox':
                JHTMLAdsmanagerCategory::displayComboboxCategories("catid",$cats,$catid,array("allow_empty"=>true,'id'=>"catid-".$moduleId));break;
                break;
            case 'cascade':
                if ($type == "horizontal") 
                    $separator = "";
                else
                    $separator = "<br/>";
                JHTMLAdsmanagerCategory::displaySplitCategories("catid",$cats,$catid,array('separator'=>$separator,'id'=>"catid-".$moduleId));break;
        }
        ?>
                        </div>
                    </div>	
                </div>
                <?php
    } else {
		if(isset($catid) && $catid != 0) {
			echo '<input type="hidden" name="catid" id="catid-'.$moduleId.'" value="'.$catid.'" />';
		}
	}
            ?>      
    <?php
    if (isset($searchfields)) {
        foreach($searchfields as $fsearch) {
                $currentvalue = JRequest::getVar($fsearch->name, "" );
                ?>
                <div class="<?php echo $class; ?> mod_adsmanager_search_field" id="searchfield_<?php echo $fsearch->name; ?>">
                    <div class="control-group">
                        <label class="control-label">
                        <?php 
                        $title = $field->showFieldTitle($catid,$fsearch,true);
                        echo htmlspecialchars($title)."&nbsp;";
                        ?></label>
                        <div class="controls">
                        <?php 
                        $field->showFieldSearch($fsearch,$catid,$defaultvalues,true);
                        ?>
                        </div>
                    </div>
                </div>
                <?php 
        }
    }
    ?>

    <?php if ($rootid != 0) {?>
    <input type="hidden" value="<?php echo $rootid ?>" name="rootid" />
    <?php } ?>
    <input type="hidden" value="1" name="new_search" />
    <div class="<?php echo $class; ?>">
        <div class="control-group">
            <label class="control-label"><?php echo $searchLabel ?></label>
            <div class="controls">
                <input type="submit" class="btn btn-primary" value="<?php echo JText::_('ADSMANAGER_SEARCH_BUTTON'); ?>"/>
            </div>
        </div>
    </div>
    <?php if ($advanced_search == 1)
    {
    ?>
    <div class="<?php echo $class; ?>">
        <div class="control-group">
            <label class="control-label"><?php echo $searchLabel ?></label>
            <div class="controls">
                <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=search&catid=$catid");?>"><?php echo JText::_('ADSMANAGER_ADVANCED_SEARCH'); ?></a>
            </div>
        </div>
    </div>
    <?php } ?>
    </div>
    </form>
    <script type="text/javascript">
        function updateModFields() {
            var form = document.modsimplesearch-form;
            catid = jQ('#modsimplesearch-form #catid-<?php echo $moduleId; ?>').val();

            <?php
            if(isset($searchfields)) {
                $fields = $searchfields;
                foreach($fields as $field)
                { 	
                    if (strpos($field->catsid, ",-1,") === false)
                    {
                        ?>
                        var field_condition = "<?php echo $field->catsid;?>";
                        var test = field_condition.indexOf( ","+catid+",", 0 );
                        var divfield = document.getElementById('searchfield_<?php echo $field->name;?>');
                        if (test != -1) {
                            jQ('#searchfield_<?php echo $field->name;?>').show();
                        }
                        else {
                            jQ('#searchfield_<?php echo $field->name;?>').hide();
                        }
                        <?php
                    }
                }
            }
            ?>
        }
        
        function checkdependency(child,parentname,parentvalues) {
        //Simple checkbox
        if (jQ('input[name="'+parentname+'"]').is(':checkbox')) {
            //alert("test");
            if (jQ('input[name="'+parentname+'"]').attr('checked')) {
                jQ('#modsimplesearch-form #f'+child).show();
                jQ('#modsimplesearch-form #searchfield_'+child).show();
            }
            else {
                jQ('#modsimplesearch-form #f'+child).hide();
                jQ('#modsimplesearch-form #searchfield_'+child).hide();

                //cleanup child field 
                if (jQ('#modsimplesearch-form #f'+child).is(':checkbox') || jQ('#modsimplesearch-form #f'+child).is(':radio')) {
                    jQ('#modsimplesearch-form #f'+child).attr('checked', false);
                }
                else {
                    jQ('#modsimplesearch-form #f'+child).val('');
                }
            } 
        }
        //If checkboxes or radio buttons, special treatment
        else if (jQ('input[name="'+parentname+'"]').is(':radio')  || jQ('input[name="'+parentname+'[]"]').is(':checkbox')) {
            var find = false;
            var allVals = [];
            jQ("input:checked").each(function() {
                for(var i = 0; i < parentvalues.length; i++) {
                    if (jQ(this).val() == parentvalues[i] && find == false) {
                        jQ('#modsimplesearch-form #f'+child).show();
                        jQ('#modsimplesearch-form #searchfield_'+child).show();
                        find = true;
                    }
                }
            });

            if (find == false) {
                jQ('#modsimplesearch-form #f'+child).hide();
                jQ('#modsimplesearch-form #searchfield_'+child).hide();

                //cleanup child field 
                if (jQ('#modsimplesearch-form #f'+child).is(':checkbox') || jQ('#modsimplesearch-form #f'+child).is(':radio')) {
                    jQ('#modsimplesearch-form #f'+child).attr('checked', false);
                }
                else {
                    jQ('#modsimplesearch-form #f'+child).val('');
                }
            }

        }
        //simple text
        else {
            var find = false;

            for(var i = 0; i < parentvalues.length; i++) {
                if (jQ('#modsimplesearch-form #f'+parentname).val() == parentvalues[i] && find == false) {	
                    jQ('#modsimplesearch-form #f'+child).show();
                    jQ('#modsimplesearch-form #searchfield_'+child).show();
                    find = true;
                }
            }

            if(find === false) {
                jQ('#modsimplesearch-form #f'+child).hide();
                jQ('#modsimplesearch-form #searchfield_'+child).hide();

                //cleanup child field 
                if (jQ('#modsimplesearch-form #f'+child).is(':checkbox') || jQ('#modsimplesearch-form #f'+child).is(':radio')) {
                    jQ('#modsimplesearch-form #f'+child).attr('checked', false);
                }
                else {
                    jQ('#modsimplesearch-form #f'+child).val('');
                }
            }
        }
    }
    function dependency(child,parentname,parentvalue) {
        var parentvalues = parentvalue.split(",");

        //if checkboxes
        jQ('input[name="'+parentname+'[]"]').change(function() {
            checkdependency(child,parentname,parentvalues);
        });
        //if buttons radio
        jQ('input[name="'+parentname+'"]').change(function() {
            checkdependency(child,parentname,parentvalues);
        });
        jQ('#f'+parentname).click(function() {
            checkdependency(child,parentname,parentvalues);
        });
        checkdependency(child,parentname,parentvalues);
    }
    jQ(document).ready(function() {
        updateModFields();

        jQ('#modsimplesearch-form #catid-<?php echo $moduleId; ?>').change(function(){
                updateModFields();
        });

        <?php
            if (isset($searchfields)) {
                foreach($searchfields as $fsearch) { 
                    if (@$fsearch->options->is_conditional_field == 1) { ?>
                    dependency('<?php echo $fsearch->name?>',
                               '<?php echo $fsearch->options->conditional_parent_name?>',
                               '<?php echo $fsearch->options->conditional_parent_value?>');
                    <?php 
                    } 
                }
            }
        ?>
    });
    </script>
</div>