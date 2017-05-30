<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

$conf= $this->conf;

$document	= JFactory::getDocument();
if ($conf->metadata_mode != 'nometadata') {
	$document->setMetaData("description", $this->content->metadata_description);
	$document->setMetaData("keywords", $this->content->metadata_keywords);
}

?>
<div class="juloawrapper">
<?php if ($conf->display_inner_pathway == 1) { ?>
<div class="breadcrumb row-fluid">
<?php 
	$pathway ="";
	$nb = count($this->pathlist);
	for ($i = $nb - 1 ; $i >0;$i--)
	{
		$pathway .= '<a href="'.$this->pathlist[$i]->link.'">'.$this->pathlist[$i]->text.'</a>';
		$pathway .= ' <img src="'.getImagePath('arrow.png').'" alt="arrow" /> ';
	}
	$pathway .= '<a href="'.$this->pathlist[0]->link.'">'.$this->pathlist[0]->text.'</a>';
echo $pathway;

if (function_exists('getContentClass')) 
	$classcontent = getContentClass($this->content,"details");
else
	$classcontent = "";
?>   
</div>
<?php } ?>
<?php echo $this->content->event->onContentBeforeDisplay; ?>
<?php if (@$conf->print==1) {?>
<div class="text-right">
<?php if (JRequest::getInt('print',0) == 1) {
	echo TTools::print_screen();
} else {
	$url = "index.php?option=com_adsmanager&view=details&catid=".$this->content->catid."&id=".$this->content->id;
	echo TTools::print_popup($url); 
}?>
</div>
<?php } ?>
<div class="<?php echo $classcontent;?> adsmanager-details row-fluid">	
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
								if($field->name != 'ad_headline')
                                	echo "<span class='f".$field->name."'>";
                                if ($title != "")
                                    echo "<b>".htmlspecialchars($title)."</b>: ";
                                echo "$c ";
                                if($field->name != 'ad_headline')
                                    echo "</span>";
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
                <?php
                    echo '<div class="text-right">';
                    if ($this->content->userid != 0 && $this->userid == $this->content->userid)	{
                    ?>
                    <div>
                    <?php
                        $target = TRoute::_("index.php?option=com_adsmanager&task=write&catid=".$this->content->category."&id=".$this->content->id);
                        echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_EDIT')."</a>";
                        echo "&nbsp;";
                        $target = TRoute::_("index.php?option=com_adsmanager&task=delete&catid=".$this->content->category."&id=".$this->content->id);
                        echo "<a onclick='return confirm(\"".htmlspecialchars(JText::_('ADSMANAGER_CONFIRM_DELETE'),ENT_QUOTES)."\")' href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DELETE')."</a>";
                    ?>
                    </div>
                    <?php
                    }
                    if(isset($this->conf->favorite_enabled) && $this->conf->favorite_enabled == 1 && ($this->conf->favorite_display == 'all' || $this->conf->favorite_display == 'details')){
                        echo '<div class="row-fluid adsmanager-favorite">
                                <div class="span12">';
                        $favoriteClass = '';
                        $favoriteLabel = JText::_('ADSMANAGER_CONTENT_FAVORITE');
                        if(array_search($this->content->id, $this->favorites) !== false){
                            $favoriteClass = ' like_active';
                            $favoriteLabel = JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETEMSG');
                        }
                        echo '<button id="like_'.$this->content->id.'" class="btn favorite_ads like_ad'.$favoriteClass.'">'.$favoriteLabel.'</button>';
                        echo '</div></div>';
                    }
                    echo '</div>';
                    ?>
			</div>
		</div>
	<div class="row-fluid">
			<?php
				$this->loadScriptImage($this->conf->image_display);
				if (count($this->content->images) == 0)
					$image_found = 0;
				else
					$image_found = 1;
        ?>
        <?php if($image_found): ?>
		<div class="span8">
			<?php
				switch($this->conf->image_display)
				{
					case 'jssor':
						$thumbnailcarousel = "";?>
						<div id="adgallery">
						<div id="slider" class="flexslider">
  							<ul class="slides">
						<?php 
						break;
					default:
						?>
						<div class="adsmanager_ads_image">
						<?php 
				}
				foreach($this->content->images as $img)
				{
					$thumbnail = JURI_IMAGES_FOLDER."/".$img->thumbnail;
					$image = JURI_IMAGES_FOLDER."/".$img->image;
					switch($this->conf->image_display)
				    {
				    	case 'jssor':
				    	case 'slider':
				    		?>
				    		<li>
								<span class="alignver"></span>
				    			<img u="image" src="<?php echo $image?>" />
				    		</li>
				    		<?php 
							$thumbnailcarousel .= '<li><img u="thumb" src="'.$thumbnail.'" /></li>';
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
						<?php 
						if (count($this->content->images) > 1) { 
						?>
						<div id="carousel" class="flexslider">
 						 <ul class="slides">
						<?php echo $thumbnailcarousel ?>
						</div>
						<?php
						}
						?>
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
        <?php else: ?>
            <div class="span12">
        <?php endif; ?>
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
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
                            echo htmlspecialchars($title).": ";
                        echo "$c</span></h2>";
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
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
                        echo "</span>";
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
		                        echo "<span class='f".$field->name."'>";
                                if ($title != "")
									echo "<b>".htmlspecialchars($title)."</b>: ";
								echo "$c<br/>";
                                echo "</span>";
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
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
                        echo "</span>";
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
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
							echo "<b>".htmlspecialchars($title)."</b>: ";
						echo "$c<br/>";
                        echo "</span>";
					}
				}
			} ?>
		
		</div>
	</div>
</div>
<?php echo $this->content->event->onContentAfterDisplay; ?>
<div class="back_button">
<a href='javascript:history.go(-1)'>
<div class="btn"><?php echo JText::_('ADSMANAGER_BACK_TEXT'); ?></div>
</a>
</div>
<script type="text/JavaScript">
jQ(function() {
	jQ('.favorite_ads').click(function() {
        var favoriteId = this.getAttribute( "id" );
        favoriteId = favoriteId.split('like_');
        var adId = favoriteId[1];
        var id = '#like_'+adId;

        if(jQ(id).hasClass("like_active")) {
            jQ.ajax({ url: <?php echo json_encode(JRoute::_('index.php?option=com_adsmanager&task=deletefavorite&mode=1'))?>,
                data: {adId: adId},
                type: 'post',
                success: function(result) {
                    if(result == 1){
                        jQ(id).removeClass("like_active");
                        jQ(id).html('<span class="glyphicon glyphicon-heart" aria-hidden="true"></span> <?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE')); ?>');
                        //alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETE')); ?>');
                    } else if(result == 2) {
                        <?php if(COMMUNITY_BUILDER): ?>
                        window.location.replace(<?php echo json_encode(JRoute::_('index.php?option=com_comprofiler&task=login'))?>);
                        <?php else: ?>
                        window.location.replace(<?php echo json_encode(JRoute::_('index.php?option=com_easysocial&view=login'))?>);
                        <?php endif; ?>
                    } else if(result == 3) {
                        alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_NO_AD_SELECTED')); ?>');
                    }
                }
            });
        } else {
            jQ.ajax({ url: <?php echo json_encode(JRoute::_('index.php?option=com_adsmanager&task=favorite'))?>,
                data: {adId: adId},
                type: 'post',
                success: function(result) {
                    if(result == 1){
                        jQ(id).addClass("like_active");
                        jQ(id).html('<span class="glyphicon glyphicon-heart" aria-hidden="true"></span> <?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETEMSG')); ?>');
                       // alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_SUCCESS')); ?>');
                    } else if(result == 2) {
                        <?php if(COMMUNITY_BUILDER): ?>
                        window.location.replace(<?php echo json_encode(JRoute::_('index.php?option=com_comprofiler&task=login'))?>;
                        <?php else: ?>
                        window.location.replace(<?php echo json_encode(JRoute::_('index.php?option=com_easysocial&view=login'))?>);
                        <?php endif; ?>
                    } else if(result == 3) {
                        alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_NO_AD_SELECTED')); ?>');
                    } else {
                        alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_ALREADY_EXIST')); ?>');
                    }
                }
            });
        }
        return false;       
    });

    <?php 
    	    $maxitems = count($this->content->images);
    		if ($maxitems > 4) { 
    			$maxitems = 4;
    		}
    		?>

    		// The slider being synced must be initialized first
    	  <?php if ($maxitems > 1) { ?>
    	  jQ('#carousel').flexslider({
    		animation: "slide",
    		controlNav: false,
    		animationLoop: false,
    		slideshow: false,
    		itemWidth: 150,
    		itemMargin: 5,
    		minItems: 0,
    		maxItems: <?php echo $maxitems;?>,
    		asNavFor: '#slider'
    	  });
    	  
    	  jQ('#adgallery #carousel').css('width',"<?php echo (150*$maxitems)?>px");
    	<?php } ?>
    	 
    	  jQ('#slider').flexslider({
    		animation: "slide",
    		controlNav: false,
    		animationLoop: false,
    		slideshow: false
    		<?php if ($maxitems > 1) { ?>
    		,sync: "#carousel"
    		<?php } ?>
    	  });
});
</script>
</div>