<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="row-fluid">
    <?php 
    foreach($this->contents as $content){
    $linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
    if (function_exists('getContentClass')) 
        $classcontent = " ".getContentClass($content,"grid");
    else
        $classcontent = "";
    
    //icon flag
    $iconflag = false;
    $iconText = '';
    if (($this->conf->show_new == true)&&($this->isNewcontent($content->date_created,$this->conf->nbdays_new))) {
            $iconText .= "<span class='iconflag'><img alt='new' src='".getImagePath('new.gif')."' /> ";
        $iconflag = true;
    }
    if (($this->conf->show_hot == true)&&($content->views >= $this->conf->nbhits)) {
        if ($iconflag == false)
                $iconText .= "<span class='iconflag'>";
        $iconText .= "<img alt='hot' src='".getImagePath('hot.gif')."' />";
        $iconflag = true;
    }
    if ($iconflag == true)
        $iconText .= "</span>";
    ?>
    <!-- <div class="row image_container container">-->
        <div class="adsmanager-grid<?php echo $classcontent; ?>">
            <h4 class="no-margin-top">
                <?php 
                    echo "<a href='".$linkTarget."'>".$content->ad_headline."</a>";
                    
                ?>
            </h4>
            <?php echo $iconText; ?>
            <div class="text-center fad-image">
                <!--<img src="templates/ouacheteroutrouver/css/images/gold-ad-1.png"/>-->
                <?php
                if (isset($content->images[0])) {
                    echo "<a href='".$linkTarget."'><img name='adimage".$content->id."' src='".JURI_IMAGES_FOLDER."/".$content->images[0]->image."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                } else if ($this->conf->nb_images > 0) {
                    echo "<a href='".$linkTarget."'><img src='".ADSMANAGER_NOPIC_IMG."' alt='nopic' /></a>";
                }?>
            </div>
            <div class="desc">
                <?php 
                    $content->ad_text = strip_tags(str_replace ('<br />'," ",$content->ad_text));
                    $af_text = JString::substr($content->ad_text, 0, 85);
                    if (strlen($content->ad_text)>85) {
                        $af_text .= "[...]";
                    }
                    echo htmlspecialchars($af_text);
                ?>
            </div>
        </div>
    <?php 
    }
    ?>
</div>