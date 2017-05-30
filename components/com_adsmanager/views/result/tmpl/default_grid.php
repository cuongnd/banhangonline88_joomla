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
    $linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid); ?>
    <!-- <div class="row image_container container">-->
        <div class="adsmanager-grid">
            <h4 class="no-margin-top"><?php echo "<a href='".$linkTarget."'>".$content->ad_headline."</a>"; ?></h4>
            <div class="text-center">
                <!--<img src="templates/ouacheteroutrouver/css/images/gold-ad-1.png"/>-->
                <?php
                if (isset($content->images[0])) {
                    echo "<a href='".$linkTarget."'><img name='adimage".$content->id."' src='".JURI_IMAGES_FOLDER."/".$content->images[0]->image."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                } else if ($this->conf->nb_images > 0) {
                    echo "<a href='".$linkTarget."'><img src='".ADSMANAGER_NOPIC_IMG."' alt='nopic' /></a>";
                }?>
            </div>
            <div>
                <?php 
                    $content->ad_text = strip_tags(str_replace ('<br />'," ",$content->ad_text));
                    $af_text = JString::substr($content->ad_text, 0, 100);
                    if (strlen($content->ad_text)>100) {
                        $af_text .= "[...]";
                    }
                    echo $af_text;
                ?>
            </div>
        </div>
    <?php 
    }
    ?>
</div>