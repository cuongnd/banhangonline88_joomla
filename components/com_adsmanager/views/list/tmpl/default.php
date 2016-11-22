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
<script type="text/javascript">
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;
 
        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>
<?php
$conf= $this->conf;

?>
<?php if ($conf->display_inner_pathway == 1) { ?>
	<div class="row-fluid">
	<?php 
		$pathway ="";
		$nb = count($this->pathlist);
		for ($i = $nb - 1 ; $i >0;$i--)
		{
			$pathway .= '<a href="'.$this->pathlist[$i]->link.'">'.$this->pathlist[$i]->text.'</a>';
			$pathway .= ' <img src="'.getImagePath('arrow.png').'" alt="arrow" /> ';
		}
		if (isset($this->pathlist[0]))
			$pathway .= '<a href="'.$this->pathlist[0]->link.'">'.$this->pathlist[0]->text.'</a>';
	echo '<div class="span12">'.$pathway.'</div>';
	?>
	</div>
<?php } ?>

<div class="row-fluid">
    <fieldset>
        <legend>
<?php
	if ($this->list_img != "") {
//        if($this->list_img_default) {
//            echo '<i class="icon-th-list"></i>';
//        } else {
            echo '<img  class="imgheading" src="'.$this->list_img.'" alt="'.$this->list_img.'" />';
//        }
    }
	echo JText::_($this->list_name);
	if ($this->conf->show_rss == 1)
	{
		if (isset($this->listuser))
			$linkrss = TRoute::_("index.php?option=com_adsmanager&view=list&format=feed&user=".$this->listuser);
		else
			$linkrss = TRoute::_("index.php?option=com_adsmanager&view=list&format=feed&catid=".$this->catid);
		echo '<a href="'.$linkrss.'" target="_blank"><img class="feed imgheading" src="'.getImagePath('rss.png').'" alt="rss" /></a>';
	}
?>
        </legend>
    </fieldset>
</div>

<div class="row-fluid">
    <div class="span12">
<?php foreach($this->subcats as $key => $subcat) {
	$subcat->link = TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$subcat->id);
	if ($key != 0)
		echo ' | ';
	echo '<a href="'.$subcat->link.'">'.$subcat->name.'</a>';
} 
?>
</div>
</div>

<div class="row-fluid">
<?php echo $this->list_description; ?>
</div>
<script type="text/JavaScript">
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
        <?php if ($this->catid != 0) { ?>
        form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=list&catid=".$this->catid) ?>';  
		<?php } else if ($this->modeuser == 1) {
		$userurl = TLink::getUserAdsLink($this->listuser);
		?>
		form.action= '<?php echo $userurl ?>';   
		<?php } else  {?>
		form.action= '<?php echo TRoute::_("index.php?option=com_adsmanager&view=list") ?>';  
		<?php } ?>  
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
</script>

<?php if (($conf->display_list_sort == 1)||($conf->display_list_search == 1)) { ?>
<div class="row-fluid">
<div class="span12">
	<?php if ($conf->display_list_search == 1) { ?>
        <div class="span6">
		<?php if ($this->catid != 0) { ?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=list&catid='.$this->catid) ?>" method="post">
		<?php } else if ($this->modeuser == 1) {
			$userurl = TLink::getUserAdsLink($this->listuser);
		?>
		<form action="<?php echo $userurl ?>" method="post">
		<?php } else  {?>
		<form action="<?php echo TRoute::_('index.php?option=com_adsmanager&view=list') ?>" method="post">
		<?php } ?>
        <div class="span12">
            <input name="tsearch" id="tsearch" placeholder="<?php echo JText::_('ADSMANAGER_LIST_SEARCH'); ?>" maxlength="20" alt="search" type="text" size="20" value="<?php echo $this->tsearch;?>"  onblur="if(this.value=='') this.value='';" onfocus="if(this.value=='<?php echo $this->tsearch;?>') this.value='';" />
        </div>
        <div class="span12">
			<a href="<?php echo TRoute::_("index.php?option=com_adsmanager&view=search&catid=".$this->catid);?>"><?php echo JText::_('ADSMANAGER_ADVANCED_SEARCH'); ?></a>
		</div>
		</form> 
        </div>
	<?php } ?>
	<?php if ($conf->display_list_sort == 1) { ?>
		<?php 
		 if (($this->catid != 0)&&($this->catid != -1)) { 
			$urloptions = "&catid=".$this->catid; 
		 } else if ($this->modeuser == 1) {
			$urloptions = "&user=".$this->listuser;
		 } else  {
		 	$urloptions = "";
		 } ?>
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
        if(@$conf->display_map_list == 1){
            echo TTools::loadModule('mod_adsmanager_adsmap', 'AdsMap');
        }
    ?>
	<form name="adminForm" id="adminForm" method="post" action="<?php echo $this->requestURL; ?>" >
	<input type="hidden" id="mode" name="mode" value="<?php echo $this->mode?>"/>
	</form>
    <?php 
        if ($this->mode == 0) { 
            echo $this->loadTemplate('list');
        } elseif($this->mode == 1) { 
            echo $this->loadTemplate('expand');
        } else {
            echo $this->loadTemplate('grid');
        }
    ?>
	<div class="row-fluid">
        <div class="span12 paginationblock">
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<?php if ($this->conf->display_expand == 2) { ?>
		<script type="text/javascript">
		function changeMode(mode)
		{
			element = document.getElementById("mode");
			element.value = mode;
			form = document.getElementById("adminForm");
			form.submit();
		}
		</script>
            <div class="span6 text-right">
		<?php 
		/* Display SubTitle */
			echo '<a href="javascript:changeMode(0)">'.JText::_('ADSMANAGER_MODE_TEXT')." ".JText::_('ADSMANAGER_SHORT_TEXT').'</a>';
		    echo " / ";
		    echo '<a href="javascript:changeMode(1)">'.JText::_('ADSMANAGER_EXPAND_TEXT').'</a>';
            echo " / ";
		    echo '<a href="javascript:changeMode(3)">'.JText::_('ADSMANAGER_GRID_TEXT').'</a>';
		?>
		</div>
	<?php } ?>
    </div>
    <script type="text/JavaScript">
        jQ(function() {
            jQ('.favorite_ads').click(function() {
                var favoriteId = this.getAttribute( "id" );
                favoriteId = favoriteId.split('like_');
                var adId = favoriteId[1];
                var id = '#like_'+adId;
                
                if(jQ(id).hasClass("like_active")) {
                    jQ.ajax({ url: <?php echo  json_encode(JRoute::_('index.php?option=com_adsmanager&task=deletefavorite&mode=1'))?>,
                        data: {adId: adId},
                        type: 'post',
                        success: function(result) {
                            if(result == 1){
                                jQ(id).removeClass("like_active");
                                jQ(id).html('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE')); ?>');
                                alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETE')); ?>');
                            } else if(result == 2) {
                                <?php if(COMMUNITY_BUILDER): ?>
                                window.location.replace(<?php echo  json_encode(JRoute::_('index.php?option=com_comprofiler&task=login')); ?>);
                                <?php else: ?>
                                window.location.replace(<?php echo  json_encode(JRoute::_('index.php?option=com_easysocial&view=login')); ?>);
                                <?php endif; ?>
                            } else if(result == 3) {
                                alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_NO_AD_SELECTED')); ?>');
                            }
                        }
                    });
                } else {
                    jQ.ajax({ url: <?php echo json_encode(JRoute::_('index.php?option=com_adsmanager&task=favorite')) ?>,
                        data: {adId: adId},
                        type: 'post',
                        success: function(result) {
                            if(result == 1){
                                jQ(id).addClass("like_active");
                                jQ(id).html('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_DELETEMSG')); ?>');
                                alert('<?php echo addslashes(JText::_('ADSMANAGER_CONTENT_FAVORITE_SUCCESS')); ?>');
                            } else if(result == 2) {
                                <?php if(COMMUNITY_BUILDER): ?>
                                window.location.replace(<?php echo  json_encode(JRoute::_('index.php?option=com_comprofiler&task=login')); ?>);
                                <?php else: ?>
                                window.location.replace(<?php echo  json_encode(JRoute::_('index.php?option=com_easysocial&view=login')); ?>);
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
        });
    </script>
<?php 
} $this->general->endTemplate();
?>
</div>