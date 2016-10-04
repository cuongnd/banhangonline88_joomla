<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
$conf= $this->conf;

/*if (function_exists('getContentClass')) 
	$classcontent = getContentClass($this->content,"details");
else
	$classcontent = "";*/
?>
<div class="juloawrapper">
    <?php echo $this->content->event->onContentBeforeDisplay; ?>
    <div class="<?php //echo $classcontent;?> adsmanager-details row-fluid">	
            <div class="span12 page-header">
                <div class="span8">
                    <h1 class="no-margin-top">	
                        <?php 
                        if (isset($this->fDisplay[1]))
                        {
                            foreach($this->fDisplay[1] as $field)
                            {
                                $c = $this->field->showFieldValue($this->content,$field); 
                                if (($c !== "")&&($c !== null)) {
                                    $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                                    if ($title != "")
                                        echo "<b>".htmlspecialchars($title)."</b>: ";
                                    echo "$c ";
                                }
                            }
                        } ?>
                    </h1>
                    <?php echo $this->content->event->onContentAfterTitle; ?>
                    <div>
                        <?php 
                        if ($this->content->userid != 0)
                        {
                            echo JText::_('ADSMANAGER_SHOW_OTHERS'); 
                            $target = TLink::getUserAdsLink($this->content->userid);

                            if ($conf->display_fullname == 1)
                                echo "<a href='$target'><b>".$this->content->fullname."</b></a>";
                            else
                                echo "<a href='$target'><b>".$this->content->user."</b></a>";
                        }
                        ?>
                    </div>
                </div>
                <div class="span4">

                </div>
            </div>
        <div class="row-fluid">
            <div class="span8">
                <?php
                    $this->loadScriptImage($this->conf->image_display);
                    if (count($this->content->images) == 0)
                        $image_found = 0;
                    else
                        $image_found = 1;
                    switch($this->conf->image_display)
                    {
                        case 'jssor':
                            ?>
                            <div id="gallery_container" style="max-width:100%;width: 521px;height: 391px;">
                                <div class="slides" u="slides" style="max-width:100%;width: 521px;height: 391px;">
                            <?php 
                            break;
                        default:
                            ?>
                            <div class="adsmanager_ads_image">
                            <?php 
                    }
                    foreach($this->content->images as $img)
                    {
                        if (isset($img->tmp) && ($img->tmp == 1)) {
                            $thumbnail = JURI_IMAGES_FOLDER."/waiting/".$img->thumbnail;
                            $image = JURI_IMAGES_FOLDER."/waiting/".$img->image;
                        } else {
                            $thumbnail = JURI_IMAGES_FOLDER."/".$img->thumbnail;
                            $image = JURI_IMAGES_FOLDER."/".$img->image;
                        }

                        switch($this->conf->image_display)
                        {
                            case 'jssor':
                                ?>
                                <div>
                                    <img u="image" src="<?php echo $image?>" />
                                    <img u="thumb" src="<?php echo $thumbnail?>" />
                                </div>
                                <?php 
                                break;
                            case 'popup':
                                echo "<a href=\"javascript:popup('$image');\"><img src='".$thumbnail."' alt=\"".htmlspecialchars($this->content->ad_headline)."\" /></a>";
                                break;
                            case 'lightbox':
                            case 'lytebox':
                                echo "<a href='".$image."' rel='lytebox[roadtrip".$this->content->id."]'><img src='".$thumbnail."' alt=\"".htmlspecialchars($this->content->ad_headline)."\" /></a>"; 
                                break;
                            case 'highslide':
                                echo "<a id='thumb".$this->content->id."' class='highslide' onclick='return hs.expand (this)' href='".$image."'><img src='".$thumbnail."' alt=\"".htmlspecialchars($this->content->ad_headline)."\" /></a>";
                                break;
                            case 'default':	
                            default:
                                echo "<a href='".$image."' target='_blank'><img src='".$thumbnail."' alt=\"".htmlspecialchars($this->content->ad_headline)."\" /></a>";
                                break;
                        }
                    }
                    switch($this->conf->image_display)
                    {
                        case 'jssor':
                            ?>
                            </div>
                            <!-- Arrow Left -->
                            <span u="arrowleft" class="jssora05l" style="width: 40px; height: 40px; top: 158px; left: 8px;">
                            </span>
                            <!-- Arrow Right -->
                            <span u="arrowright" class="jssora05r" style="width: 40px; height: 40px; top: 158px; right: 8px">
                            </span>
                            <!-- Arrow Navigator Skin End -->

                            <!-- Thumbnail Navigator Skin Begin -->
                            <div u="thumbnavigator" class="jssort01" style="position: absolute; width: 531px; height: 100px; left:0px; bottom: 0px;">
                                <div u="slides" style="cursor: move;">
                                    <div u="prototype" class="p" style="position: absolute; width: 72px; height: 72px; top: 0; left: 0;">
                                        <div class=w><thumbnailtemplate style=" width: 100%; height: 100%; border: none;position:absolute; top: 0; left: 0;"></thumbnailtemplate></div>
                                        <div class=c></div>
                                    </div>
                                </div>
                            <!-- Thumbnail Item Skin End -->
                            </div>
                            </div>
                        <?php 
                        break;
                        default:
                            if (($image_found == 0)&&($conf->nb_images >  0))
                            {
                                echo '<img src="'.ADSMANAGER_NOPIC_IMG.'" alt="nopic" />'; 
                            }?>
                            </div>
                        <?php 
                    }
                ?>
            </div>
            <div class="span4">
                <?php 
                if (!empty($this->fDisplay[4])) {
                    echo '<div class="row-fluid">
                            <div class="span12">';
                    $strtitle = @JText::_($this->positions[3]->title);
                    if ($strtitle != "") echo "<h2 class='section-header'>".@$strtitle."</h2>"; 
                    foreach($this->fDisplay[4] as $field)
                    {
                        $c = $this->field->showFieldValue($this->content,$field); 
                        if (($c !== "")&&($c !== null)) {
                            $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                            echo '<h2>';
                            if ($title != "")
                                echo htmlspecialchars($title).": ";
                            echo "$c</h2>";
                        }
                    }
                    echo '</div></div>';
                }
                if (!empty($this->fDisplay[2])) {
                    $strtitle = @JText::_($this->positions[1]->title);
                    if ($strtitle != "") echo "<h2 class='section-header'>".@$strtitle."</h2>"; 
                    foreach($this->fDisplay[2] as $field) {
                        $c = $this->field->showFieldValue($this->content,$field); 
                        if (($c !== "")&&($c !== null)) {
                            $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                            if ($title != "")
                                echo "<b>".htmlspecialchars($title)."</b>: ";
                            echo "$c<br/>";
                        }
                    }
                }

                if (!empty($this->fDisplay[5])) {
                    $strtitle = @JText::_($this->positions[4]->title);
                    if ($strtitle != "") echo "<h2 class='section-header'>".@$strtitle."</h2>"; 
                    if ($this->showContact) {		
                        if (isset($this->fDisplay[5]))
                        {		
                            foreach($this->fDisplay[5] as $field)
                            {	
                                $c = $this->field->showFieldValue($this->content,$field); 
                                if(($c !== "")&&($c !== null)) {
                                    $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                                    if ($title != "")
                                        echo "<b>".htmlspecialchars($title)."</b>: ";
                                    echo "$c<br/>";
                                }
                            } 
                        }
                        if (($this->content->userid != 0)&&($conf->allow_contact_by_pms == 1))
                        {
                            echo TLink::getPMSLink($this->content);
                        }
                    }
                    else
                    {
                        echo JText::_('ADSMANAGER_CONTACT_NO_RIGHT');
                    }
                }
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                 <?php if (!empty($this->fDisplay[3])) {
                    $strtitle = @JText::_($this->positions[2]->title);
                    if ($strtitle != "") echo "<h2 class='section-header'>".@$strtitle."</h2>"; 
                    foreach($this->fDisplay[3] as $field) {
                        $c = $this->field->showFieldValue($this->content,$field); 
                        if (($c !== "")&&($c !== null)) {
                            $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                            if ($title != "")
                                echo "<b>".htmlspecialchars($title)."</b>: ";
                            echo "$c<br/>";
                        }
                    }
                }?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php if (!empty($this->fDisplay[6])) {
                    $strtitle = @JText::_($this->positions[5]->title);
                    if ($strtitle != "") echo "<h2 class='section-header'>".@$strtitle."</h2>"; 
                    foreach($this->fDisplay[6] as $field) {
                        $c = $this->field->showFieldValue($this->content,$field); 
                        if (($c !== "")&&($c !== null)) {
                            $title = $this->field->showFieldTitle(@$this->content->catid,$field);
                            if ($title != "")
                                echo "<b>".htmlspecialchars($title)."</b>: ";
                            echo "$c<br/>";
                        }
                    }
                } ?>

            </div>
        </div>
    </div>
    <?php echo $this->content->event->onContentAfterDisplay; ?>
    <div align='center'>
        <a class="btn" href="<?php echo TRoute::_("index.php?option=com_adsmanager&task=write&pending=1&catid=".$this->content->catid."&id=".$this->content->id); ?>"><?php echo JText::_('ADSMANAGER_FORM_EDIT_TEXT'); ?></a>
        &nbsp;
        <a class="btn btn-primary" href="<?php echo TRoute::_("index.php?option=com_adsmanager&task=valid&id=".$this->content->id); ?>"><?php echo JText::_('ADSMANAGER_FORM_VALID_TEXT'); ?></a>
    </div>
</div>