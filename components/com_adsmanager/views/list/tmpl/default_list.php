<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="container-fluid">
<table class="adsmanager_table table table-striped">
    <tr>
      <th><?php echo JText::_('ADSMANAGER_CONTENT'); ?>
      <?php /*<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=5&orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
      <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=5&orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
       */?>
      </th>
      <?php 
          foreach($this->columns as $col)
          {
            echo "<th class='hidden-phone'>".JText::_($col->name);
            /*$order = @$this->fColumns[$col->id][0]->fieldid;
            ?>
            <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=$order&orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
            <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=$order&orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
            */?>
            <?php echo "</th>";
          }
      ?>
      <?php if(!isset($this->conf->display_column_date) || $this->conf->display_column_date == 1): ?>
      <th class="hidden-phone" width="15%"><?php echo JText::_('ADSMANAGER_DATE'); ?>
      <?php endif; ?>
      <?php /*<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=orderdir=ASC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_asc.png" alt="+" /></a>
      <a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=list".$urloptions."&order=orderdir=DESC");?>"><img src="<?php echo $this->baseurl ?>administrator/images/sort_desc.png" alt="-" /></a>
      */?>
      </th>
    </tr>
<?php
foreach($this->contents as $content) 
{
    $linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
    if (function_exists('getContentClass')) 
        $classcontent = getContentClass($content,"list");
    else
        $classcontent = "";
    ?>   
    <tr class="adsmanager_table_description <?php echo $classcontent;?> trcategory_<?php echo $content->catid?>"> 
        <td>
                <h4 class="no-margin-top">
                    <?php echo '<a href="'.$linkTarget.'">'.$content->ad_headline.'</a>'; ?>
                    <?php if(!isset($this->conf->display_category_list_label) || $this->conf->display_category_list_label == 1): ?>
                        <span class="adsmanager-cat"><?php echo "(".$content->parent." / ".$content->cat.")"; ?></span>
                    <?php endif; ?>
                </h4>
            <?php
            if (isset($content->images[0])) {
                    echo "<a href='".$linkTarget."'><img class='fad-image' name='ad-image".$content->id."' src='".JURI_IMAGES_FOLDER."/".$content->images[0]->thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
            } else if ($this->conf->nb_images > 0) {
                    echo "<a href='".$linkTarget."'><img class='fad-image' src='".ADSMANAGER_NOPIC_IMG."' alt='nopic' /></a>";
            }
            ?>
            <div class="desc">
            <?php 
                $content->ad_text = strip_tags(str_replace ('<br />'," ",$content->ad_text));
                $af_text = JString::substr($content->ad_text, 0, 100);
                if (strlen($content->ad_text)>100) {
                    $af_text .= "[...]";
                }
                echo $af_text;
            ?>
            </div>
        </td>
        <?php 
            foreach($this->columns as $col) {
                echo '<td class="tdcenter column_'.$col->id.' hidden-phone">';
                if (isset($this->fColumns[$col->id]))
                    foreach($this->fColumns[$col->id] as $field)
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
                echo "</td>";
            }
        ?>
        <?php if(!isset($this->conf->display_column_date) || $this->conf->display_column_date == 1): ?>
        <td class="tdcenter column_date hidden-phone">
            <?php 
            $iconflag = false;
            if (($this->conf->show_new == true)&&($this->isNewcontent($content->date_created,$this->conf->nbdays_new))) {
                    echo "<div class='text-center'><img alt='new' src='".getImagePath('new.gif')."' /> ";
                $iconflag = true;
            }
            if (($this->conf->show_hot == true)&&($content->views >= $this->conf->nbhits)) {
                if ($iconflag == false)
                        echo "<div class='text-center'>";
                echo "<img alt='hot' src='".getImagePath('hot.gif')."' />";
                $iconflag = true;
            }
            if ($iconflag == true)
                echo "</div>";
            if(!isset($this->conf->display_column_date_date) || $this->conf->display_column_date_date == 1){
                echo $this->reorderDate($content->date_created);
            }
            ?>
            <br />
            <?php
            if ($content->userid != 0 && (!isset($this->conf->display_column_date_user) || $this->conf->display_column_date_user == 1))
            {
               echo JText::_('ADSMANAGER_FROM')." "; 

               $target = TLink::getUserAdsLink($content->userid);

               if ($this->conf->display_fullname == 1)
                    echo "<a href='".$target."'>".$content->fullname."</a><br/>";
               else
                    echo "<a href='".$target."'>".$content->user."</a><br/>";
            }
            ?>
            <?php 
            if(!isset($this->conf->display_column_date_view) || $this->conf->display_column_date_view == 1){
                echo sprintf(JText::_('ADSMANAGER_VIEWS'),$content->views);
            }
            ?>
            <?php if(isset($this->conf->favorite_enabled) && $this->conf->favorite_enabled == 1 && ($this->conf->favorite_display == 'all' || $this->conf->favorite_display == 'list')): ?>
                <br/>
                <?php
                    $favoriteClass = '';
                    $favoriteLabel = addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE'));
                    if(array_search($content->id, $this->favorites) !== false){
                        $favoriteClass = ' like_active';
                        $favoriteLabel = addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETEMSG'));
                    }
                ?>
                    <button id="like_<?php echo $content->id; ?>" class="btn favorite_ads like_ad<?php echo $favoriteClass; ?>"><?php echo $favoriteLabel; ?></button>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>
<?php	
}
?>
    </table>
</div>