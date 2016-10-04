<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

foreach($this->contents as $key => $content) 
{ 
    if ($key == 0)
        $this->loadScriptImage($this->conf->image_display);
    if (function_exists('getContentClass')) 
        $classcontent = getContentClass($content,"details");
    else
        $classcontent = "";
    ?>   
    <div class="<?php echo $classcontent?> adsmanager_ads">
    <div class="adsmanager_top_ads">	
        <h2>
        <?php 	
        if (isset($this->fDisplay[1]))
        {
            foreach($this->fDisplay[1] as $field)
            {
                $c = $this->field->showFieldValue($content,$field); 
                if (($c !== "")&&($c !== null)) {
                    $title = $this->field->showFieldTitle(@$content->catid,$field);
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
        </h2>
        <div>
        <?php 
        if ($content->userid != 0)
        {
            echo JText::_('ADSMANAGER_SHOW_OTHERS'); 
            $target = TLink::getUserAdsLink($content->userid);

            if ($this->conf->display_fullname == 1)
                echo "<a href='$target'><b>".$content->fullname."</b></a>";
            else
                echo "<a href='$target'><b>".$content->user."</b></a>";
        }
        ?>
        </div>
        <div class="addetails_topright">
        <?php $strtitle = "";if (@$this->positions[3]->title) {$strtitle = JText::_($this->positions[3]->title);} ?>
        <?php echo "<h3>".@$strtitle."</h3>"; 
        if (isset($this->fDisplay[4]))
        {
            foreach($this->fDisplay[4] as $field)
            {
                $c = $this->field->showFieldValue($content,$field);
                if (($c !== "")&&($c !== null)) {
                    $title = $this->field->showFieldTitle(@$content->catid,$field);
                    echo "<span class='f".$field->name."'>";
                    if ($title != "")
                        echo "<b>".htmlspecialchars($title)."</b>: ";
                    echo "$c<br/>";
                    echo "</span>";
                }
            }
        }
        ?>
        </div>
    </div>
    <div class="adsmanager_ads_main">
        <div class="adsmanager_ads_image">
            <?php
            if (count($content->images) == 0)
                $image_found =0;
            else
                $image_found =1;
            foreach($content->images as $img)
            {
                $thumbnail = JURI_IMAGES_FOLDER."/".$img->thumbnail;
                $image = JURI_IMAGES_FOLDER."/".$img->image;
                switch($this->conf->image_display)
                {
                    case 'popup':
                        echo "<a href=\"javascript:popup('$image');\"><img src='".$thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                        break;
                    case 'lightbox':
                    case 'lytebox':
                        echo "<a href='".$image."' rel='lytebox[roadtrip".$content->id."]'><img src='".$thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>"; 
                        break;
                    case 'highslide':
                        echo "<a id='thumb".$content->id."' class='highslide' onclick='return hs.expand (this)' href='".$image."'><img src='".$thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                        break;
                    case 'default':	
                    default:
                        echo "<a href='".$image."' target='_blank'><img src='".$thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                        break;
                }
            }
            if (($image_found == 0)&&($this->conf->nb_images >  0))
            {
                echo '<img src="'.ADSMANAGER_NOPIC_IMG.'" alt="nopic" />'; 
            }
            ?>
        </div>
        <div class="adsmanager_ads_body">
            <div class="adsmanager_ads_desc">
            <?php $strtitle = "";if (@$this->positions[2]->title) {$strtitle = JText::_($this->positions[2]->title);} ?>
            <?php echo "<h3>".@$strtitle."</h3>"; 
            if (isset($this->fDisplay[3]))
            {	
                foreach($this->fDisplay[3] as $field)
                {
                    $c = $this->field->showFieldValue($content,$field);
                    if (($c !== "")&&($c !== null)) {
                        $title = $this->field->showFieldTitle(@$content->catid,$field);
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
                            echo "<b>".htmlspecialchars($title)."</b>: ";
                        echo "$c<br/>";
                        echo "</span>";
                    }
                }
            } ?>
            </div>
            <div class="adsmanager_ads_price">
            <?php $strtitle = "";if (@$this->positions[1]->title) {$strtitle = JText::_($this->positions[1]->title); } ?>
            <?php echo "<h3>".@$strtitle."</h3>"; 
            if (isset($this->fDisplay[2]))
            {
                foreach($this->fDisplay[2] as $field)
                {
                    $c = $this->field->showFieldValue($content,$field);
                    if (($c !== "")&&($c !== null)) {
                        $title = $this->field->showFieldTitle(@$content->catid,$field);
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
                            echo "<b>".htmlspecialchars($title)."</b>: ";
                        echo "$c<br/>";
                        echo "</span>";
                    }
                } 
            }?>
            </div>
            <div class="adsmanager_ads_desc">
            <?php $strtitle = "";if (@$this->positions[5]->title) {$strtitle = JText::_($this->positions[5]->title);} ?>
            <?php echo "<h3>".@$strtitle."</h3>"; 
            if (isset($this->fDisplay[6]))
            {	
                foreach($this->fDisplay[6] as $field)
                {
                    $c = $this->field->showFieldValue($content,$field);
                    if (($c !== "")&&($c !== null)) {
                        $title = $this->field->showFieldTitle(@$content->catid,$field);
                        echo "<span class='f".$field->name."'>";
                        if ($title != "")
                            echo "<b>".htmlspecialchars($title)."</b>: ";
                        echo "$c<br/>";
                        echo "</span>";
                    }
                }
            } ?>
            </div>
            <div class="adsmanager_ads_contact">
            <?php $strtitle = "";if (@$this->positions[4]->title) {$strtitle = JText::_($this->positions[4]->title);} ?>
            <?php echo "<h3>".@$strtitle."</h3>";
            if ($this->showContact) {		
				if (isset($this->fDisplay[5]))
				{		
					foreach($this->fDisplay[5] as $field)
					{	
						$c = $this->field->showFieldValue($content,$field); 
						if(($c !== "")&&($c !== null)) {
							$title = $this->field->showFieldTitle(@$content->catid,$field);
	                        echo "<span class='f".$field->name."'>";
                            if ($title != "")
								echo "<b>".htmlspecialchars($title)."</b>: ";
							echo "$c<br/>";
                            echo "</span>";
						}
					} 
				}
				if (($content->userid != 0)&&($this->conf->allow_contact_by_pms == 1))
				{
					echo TLink::getPMSLink($content);
				}
			}
			else
			{
				echo JText::_('ADSMANAGER_CONTACT_NO_RIGHT');
			}
            ?>
            </div>
        </div>
        <div class="adsmanager_spacer"></div>
    </div>
</div>
<hr/>
<?php } ?>