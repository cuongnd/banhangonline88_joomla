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
    <?php
    if ($image == 1) {
    ?>
    <div class='row-fluid adsmanager_box_module_2<?php echo $moduleclass_sfx; ?>'>
    <table class='table table-striped' width="100%">
    <tr align="center">
    <?php
    //$ads_by_row = 4;
    $num_ads = 0;
    if (isset($contents[0])) {
    foreach($contents as $row) {
        if ($num_ads >= $ads_by_row) {
            echo "</tr><tr>";
            $num_ads = 0;
        }
        ?>
        <td>
        <?php	
        $linkTarget = TRoute::_("index.php?option=com_adsmanager&view=details$urlparamroot&id=".$row->id."&catid=".$row->catid);			
        echo "<h4 class='text-center no-margin-top'><a href='$linkTarget'>".$row->ad_headline."</a>"; 
        if ($displaycategory == 1)
        {
            echo "<div class='adsmanager-cat'>(".$row->parent." / ".$row->cat.")</div>";
        }
        echo "</h4>";
        if (isset($row->images[0])) {
            echo "<div class='text-center'><a href='".$linkTarget."'><img src='".JURI_IMAGES_FOLDER."/".$row->images[0]->$imagetype."' alt=\"".htmlspecialchars($row->ad_headline)."\" border='0' /></a></div>";
        }
        else
        {
            echo "<div class='text-center'><a href='".$linkTarget."'><img src='".ADSMANAGER_NOPIC_IMG."' alt='noimage' border='0' /></a></div>"; 
        }   


        $iconflag = false;
        if (($conf->show_new == true)&&(isNewContent($row->date_created,$conf->nbdays_new))) {
            echo "<div class='text-center'><img align='center' src='".getImagePath('new.gif')."' /> ";
            $iconflag = true;
        }
        if (($conf->show_hot == true)&&($row->views >= $conf->nbhits)) {
            if ($iconflag == false)
                echo "<div class='text-center'>";
            echo "<img align='center' src='".getImagePath('hot.gif')."' />";
            $iconflag = true;
        }
        if ($iconflag == true)
            echo "</div>";

        if ($displaydate == 1)
        {
            echo "<div class='text-center'>".reorderDate($row->date_created)."</div>";
            $iconflag = true;
        }
        foreach($adfields as $f) {
            $fieldname = $f->name;
            if ($row->$fieldname != null) {
                $value = $field->showFieldValue($row,$f);
                echo "<div class='text-center'>$value</div>";
            }
        }
        echo "</div>";
        ?>
        </td>
    <?php
        $num_ads ++;
    } }
    for(;$num_ads < $ads_by_row;$num_ads++)
    {
        echo "<td></td>";
    }
    ?>
    </tr>
    </table>
    </div>
    <?php
    } else {
        ?>
        <ul class="mostread<?php echo $moduleclass_sfx; ?>">
        <?php
        if (isset($contents[0])) {
        foreach($contents as $row) {
        ?>
            <li class="mostread">
            <?php	
                $linkTarget = TRoute::_("index.php?option=com_adsmanager&view=details$urlparamroot&id=".$row->id."&catid=".$row->catid);
                echo "<div class='text-center'><a href='$linkTarget'>".$row->ad_headline."</a></div>"; 
                if ($displaycategory == 1)
                    echo "<div class='text-center'><span class=\"adsmanager-cat\">(".$row->parent." / ".$row->cat.")</span></div>";
                if ($displaydate == 1)
                    echo "<div class='text-center'>".reorderDate($row->date_created)."</div>";
            ?>
            </li>
    <?php
        } }
        ?>
        </ul>
        <?php
    }
    ?>
</div>