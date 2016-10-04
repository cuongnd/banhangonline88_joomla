<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="juloawrapper">
    <div class="row-fluid">
        <table class="adsmanager_table<?php echo $moduleclass_sfx; ?> table table-striped">
        <tr>
            <th><?php echo JText::_('ADSMANAGER_CONTENT'); ?></th>
            <?php 
            foreach($columns as $col)
            {
                echo "<th>".JText::_($col->name)."</th>";
            }
            ?>
            <th><?php echo JText::_('ADSMANAGER_DATE'); ?></th>
        </tr>
    <?php
        foreach($contents as $content) 
        {
            $linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details$urlparamroot&id=".$content->id."&catid=".$content->catid);
            if (function_exists('getContentClass')) 
                $classcontent = getContentClass($content,"list");
            else
                $classcontent = "adsmanager_table_description";
            ?>   
        <tr class="<?php echo $classcontent;?>"> 
            <td class="column_desc">
                    <h4 class="no-margin-top">
                        <?php echo '<a href="'.$linkTarget.'">'.$content->ad_headline.'</a>'; ?>
                        <span class="adsmanager-cat"><?php echo "(".$content->parent." / ".$content->cat.")"; ?></span>
                    </h4>
                <?php
                if (isset($content->images[0])) {
                        echo "<a href='".$linkTarget."'><img class='fad-image' name='ad-image".$content->id."' src='".JURI_IMAGES_FOLDER."/".$content->images[0]->thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
                } else {
                        echo "<a href='".$linkTarget."'><img class='fad-image' src='".ADSMANAGER_NOPIC_IMG."' alt='nopic' /></a>";
                }
                ?>
                <div>
                <?php 
                    $content->ad_text = str_replace ('<br />'," ",$content->ad_text);
                        $af_text = JString::substr($content->ad_text, 0, 100);
                        if (strlen($content->ad_text)>100) {
                            $af_text .= "[...]";
                        }
                    echo $af_text;
                ?>
                </div>
            </td>
            <?php 
                foreach($columns as $col) {
                    echo '<td class="tdcenter column_'.$col->id.'">';
                    if (isset($fColumns[$col->id]))
                        foreach($fColumns[$col->id] as $f)
                        {
                            $c = $field->showFieldValue($content,$f);
                            if ($c != "") {
                                $title = $field->showFieldTitle(@$content->catid,$f);
                                if ($title != "")
                                    echo htmlspecialchars($title).": ";
                                echo "$c<br/>";
                            }
                        }
                    echo "</td>";
                }
            ?>
            <td class="tdcenter column_date">
                <?php 
                $iconflag = false;
                if (($conf->show_new == true)&&(isNewcontent($content->date_created,$conf->nbdays_new))) {
                        echo "<div class='text-center'><img align='center' src='".getImagePath('new.gif')."' /> ";
                    $iconflag = true;
                }
                if (($conf->show_hot == true)&&($content->views >= $conf->nbhits)) {
                    if ($iconflag == false)
                            echo "<div class='text-center'>";
                    echo "<img align='center' src='".getImagePath('hot.gif')."' />";
                    $iconflag = true;
                }
                if ($iconflag == true)
                    echo "</div>";
                echo reorderDate($content->date_created); 
                ?>
                <br />
                <?php
                if ($content->userid != 0)
                {
                   echo JText::_('ADSMANAGER_FROM')." "; 
                   $target = TLink::getUserAdsLink($content->userid);
                   echo "<a href='".$target."'>".$content->user."</a><br/>";
                }
                ?>
                <?php echo sprintf(JText::_('ADSMANAGER_VIEWS'),$content->views); ?>
            </td>
        </tr>
    <?php	
    }
    ?>
        </table>
    </div>
</div>