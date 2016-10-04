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
$conf= $this->conf;
?>
<div class="row-fluid">
    <fieldset>
        <legend><?php echo JText::_('ADSMANAGER_TITLE_FAVORITE');?></legend>
    </fieldset>
</div>
<script language="JavaScript" type="text/JavaScript">
<!--
function jumpmenu(target,obj){
  eval(target+".location='"+obj.options[obj.selectedIndex].value+"'");	
  obj.options[obj.selectedIndex].innerHTML="<?php echo JText::_('ADSMANAGER_WAIT');?>";			
}		

jQ(function() {
	jQ('#order').change(function() {
		order = jQ(this).val();
		orderdir = jQ(":selected",this).attr('dir');
		var form= document.createElement('form');
        form.method= 'post';
        form.action= '<?php echo TLink::getMyFavoritesLink() ?>';  
        var input= document.createElement('input');
        input.type= 'hidden';
        input.name= "order";
        input.value= order;
        form.appendChild(input);
        var input2= document.createElement('input');
        input2.type= 'hidden';
        input2.name= "orderdir";
        input2.value= orderdir;
        form.appendChild(input2);
    	document.body.appendChild(form);
    	form.submit();
	});
});
//-->
</script>

<?php if (($conf->display_list_sort == 1)||($conf->display_list_search == 1)) { ?>
<div class="row-fluid">
<div class="span12">
	<?php if ($conf->display_list_search == 1) { ?>
        <div class="span6">
		<form action="<?php echo TLink::getMyFavoritesLink() ?>" method="post">
                <div class="span12">
                    <input name="tsearch" id="tsearch" placeholder="<?php echo JText::_('ADSMANAGER_LIST_SEARCH'); ?>" maxlength="20" alt="search" type="text" size="20" value="<?php echo $this->tsearch;?>"  onblur="if(this.value=='') this.value='';" onfocus="if(this.value=='<?php echo $this->tsearch;?>') this.value='';" />
		</div>
		</form> 
        </div>
	<?php } ?>
	<?php if ($conf->display_list_sort == 1) { ?>
		<?php if (isset($this->orders)) { ?>
        <div class="span6 text-right">
		<?php echo JText::_('ADSMANAGER_ORDER_BY_TEXT'); ?>
		<select name="order" size="1" id="order">
				<option value="0" dir="DESC" <?php if ($this->order == "0") { echo "selected='selected'"; } ?>><?php echo JText::_('ADSMANAGER_DATE'); ?></option>
			   <?php foreach($this->orders as $o)
			   {
	               ?>
				<option value="<?php echo $o->fieldid ?>" dir="DESC" <?php if (($this->orderdir == "DESC") && ($this->order == $o->fieldid)) { echo "selected='selected'"; } ?>><?php echo sprintf(JText::_('ADSMANAGER_ORDER_BY_DESC'),JText::_($o->title))?></option>
				<option value="<?php echo $o->fieldid ?>" dir="ASC" <?php if (($this->orderdir == "ASC") && ($this->order == $o->fieldid)) { echo "selected='selected'"; } ?>><?php echo sprintf(JText::_('ADSMANAGER_ORDER_BY_ASC'),JText::_($o->title))?></option>
				<?php
			   }
			 ?>
		</select>	
        </div>
		<?php } ?>	
	<?php } ?>		  
</div>
</div>
<?php } ?>
<?php $this->general->showGeneralLink() ?>
<?php
if ($this->pagination->total == 0 ) 
{
	echo JText::_('ADSMANAGER_NOENTRIES'); 
}
else
{
	?>
    <div class="container-fluid">
		<table class="adsmanager_table table table-striped">
			<tr>
			  <th><?php echo JText::_('ADSMANAGER_CONTENT'); ?>
			  </th>
			  <th class="hidden-phone"><?php echo JText::_('ADSMANAGER_INFO'); ?>
              </th>
			  <th class="hidden-phone"><?php echo JText::_('ADSMANAGER_ACTIONS'); ?>
              </th>
			</tr>
		<?php
		foreach($this->contents as $content) 
		{
			$linkTarget = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
			?>   
			<tr> 
				<td>
					<h4 class="no-margin-top">
                        <?php echo '<a href="'.$linkTarget.'">'.$content->ad_headline.'</a>'; ?>
                        <span class="adsmanager-cat"><?php echo "(".$content->parent." / ".$content->cat.")"; ?></span>
                    </h4>
					<?php
					if (isset($content->images[0])) {
                            echo "<a href='".$linkTarget."'><img class='fad-image' name='ad-image".$content->id."' src='".JURI_IMAGES_FOLDER."/".$content->images[0]->thumbnail."' alt=\"".htmlspecialchars($content->ad_headline)."\" /></a>";
					} else  {
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
				<td class="tdcenter hidden-phone">
					<?php 
					
					echo $this->reorderDate($content->date_created); 
					?>
					<br />
					<?php
					if ($content->userid != 0)
					{
					   echo JText::_('ADSMANAGER_FROM')." "; 
	
					   $target = TLink::getUserAdsLink($content->userid);
					   
					   if ($conf->display_fullname == 1)
					   		echo "<a href='".$target."'>".$content->fullname."</a><br/>";
					   else
					   		echo "<a href='".$target."'>".$content->user."</a><br/>";
					}
					?>
					<?php echo sprintf(JText::_('ADSMANAGER_VIEWS'),$content->views); ?><br/>
				</td>
				<td class="tdcenter hidden-phone">
					<?php
					$target = TRoute::_( "index.php?option=com_adsmanager&view=details&id=".$content->id."&catid=".$content->catid);
                    echo "<a href='".$target."'>".JText::_('ADSMANAGER_CONTENT_OPEN_AD')."</a>";
					echo "<br/>";
					$target = TRoute::_("index.php?option=com_adsmanager&task=deletefavorite&catid=".$content->catid."&id=$content->id");
					echo "<a onclick='return confirm(\"".htmlspecialchars(JText::_('ADSMANAGER_CONFIRM_DELETE_FAVORITE'),ENT_QUOTES)."\")' href='".$target."'>".JText::_('ADSMANAGER_CONTENT_DELETE_FAVORITE')."</a>";
					?>
				</td>
			</tr>
		<?php	
		}
		?>
		</table>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <?php echo $this->pagination->getResultsCounter() ?>
        </div>
    </div>
<?php 
} $this->general->endTemplate();
?>
</div>