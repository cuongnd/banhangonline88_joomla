<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');
$params =JComponentHelper::getParams( 'com_affiliatetracker' );
 ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
  <div id="j-main-container" >
    <div class="navbar filter-bar">
      <div class="navbar-inner">
        <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" class="text_area keywords" placeholder="<?php echo JText::_( 'TYPE_TO_SEARCH' ); ?>" />

        <input type="text" name="account_id" id="account_id" value="<?php echo $this->account_id;?>" class="input-mini" placeholder="<?php echo JText::_( 'ACCOUNT_ID' ); ?>" />

        <input type="text" name="user_id" id="user_id" value="<?php echo $this->user_id;?>" class="input-mini" placeholder="<?php echo JText::_( 'USER_ID' ); ?>" />

        <?php echo $this->lists['status']; ?> 
        <?php echo JHTML::calendar($this->cal_start, "cal_start", "cal_start", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'FROM' ) )); ?>
        <?php echo JHTML::calendar($this->cal_end, "cal_end", "cal_end", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'TO' ) )); ?> 
      
        <button class="btn btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('FILTER_RESULTS'); ?>"><i class="icon-search"></i> <?php echo JText::_('GO'); ?></button>
      
        <button class="btn" type="button" onclick="document.getElementById('keywords').value='';document.getElementById('account_id').value='';document.getElementById('user_id').value='';document.getElementById('status_id').selectedIndex = 0;document.getElementById('cal_start').value='';document.getElementById('cal_end').value='';document.getElementById('adminForm').submit();" title="<?php echo JText::_('RESET'); ?>"><i class="icon-remove"></i></button>
      </div>
    </div>

    <?php     
    $modules = JModuleHelper::getModules("at_conversions_backend");
    $document =JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $attribs  = array();
    $attribs['style'] = 'xhtml';
    foreach ( @$modules as $mod ) 
    {
      echo $renderer->render($mod, $attribs);
    }
    ?>

    <table class="table table-striped">
      <thead>
        <tr>
          <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'at.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
          <th colspan="2"> <?php echo JHTML::_( 'grid.sort', 'ACCOUNT', 'at.atid', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th colspan="2"> <?php echo JHTML::_( 'grid.sort', 'ACCOUNT_OWNER', 'u2.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'DATE_CREATED', 'at.date_created', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th> <?php echo JHTML::_( 'grid.sort', 'CONVERSION', 'at.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ITEM', 'at.extended_name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class=" width55"> <?php echo JHTML::_( 'grid.sort', 'VALUE', 'at.value', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone width55"> <?php echo JHTML::_( 'grid.sort', 'COMISSION', 'at.comission', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone width55"> <?php echo JHTML::_( 'grid.sort', 'USER', 'at.user_id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="5" class="hidden-phone"><?php echo JHTML::_( 'grid.sort', 'APPROVED', 'at.approved', $this->lists['order_Dir'], $this->lists['order']); ?></th>
          <!--th class="hidden-phone"> <?php echo JText::_('ACTIONS'); ?> </th-->
        </tr>
      </thead>
      <?php
	$k = 0;
	$total_comission = 0 ;
	$total = 0 ;
	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=conversion&task=edit&cid[]='. $row->id );
		if($row->approved){
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/tick.png','Published');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=conversion&task=unpublish&cid[]='. $row->id); 
		}
		else{
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/publish_x.png','Not Published');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=conversion&task=publish&cid[]='. $row->id); 
		}
		
		$total_comission += $row->comission ;
		$total += $row->value ;
		
		$link_account 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=conversions&account_id='. $row->atid );

    $link_user    = JRoute::_( 'index.php?option=com_affiliatetracker&controller=conversions&user_id='. $row->owner_id );
		
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td >[<?php echo $row->atid; ?>] <a href="<?php echo $link; ?>"><?php echo $row->account_name; ?></a></td>
        <td width="24"><a href="<?php echo $link_account; ?>" data-original-title="<?php echo JText::_('CLICK_FILTER_ACCOUNT'); ?>" rel="tooltip"><i class="icon-share-alt hide-icon"></i></a></td>
        <td>[<?php echo $row->account_user_id; ?>] <a href="<?php echo $link_user; ?>"><?php echo $row->account_owner; ?></a></td>
        <td width="24"><a href="<?php echo $link_user; ?>" data-original-title="<?php echo JText::_('CLICK_FILTER_USER'); ?>" rel="tooltip"><i class="icon-share-alt hide-icon"></i></a></td>
        <td class="hidden-phone"><?php echo JHTML::_('date', $row->date_created, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td class=""><?php echo $row->name; ?></td>
        <td class="hidden-phone"><?php echo $row->extended_name; ?></td>
        <td align="right" class=""><?php echo AffiliateHelper::format($row->value); ?></td>
        <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($row->comission); ?></td>
        <td class="hidden-phone"><?php echo $row->username . " [".$row->user_id."]"; ?></td>
        <td align="center" class="hidden-phone"><a href="<?php echo $link_publicat; ?>"><?php echo $publicat; ?></a></td>
        <!--td class="hidden-phone"></td-->
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
      <tfoot>
        <tr class="totals">
          <td colspan="2" class="hidden-phone"></td>
          <td colspan="4" ></td>
          <td class="hidden-phone"></td>
          <td ></td>
          <td class="hidden-phone"></td>
          <td align="right"><?php echo AffiliateHelper::format($total); ?></td>
          <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($total_comission); ?></td>
          <td colspan="2" class="hidden-phone"></td>
        </tr>
        <tr>
          <td colspan="12"><?php  echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <?php if($params->get('systeminfo', 1)){ ?>
    <script>
      jQuery( document ).ready(function() {
        jQuery('#sidebar').append("<?php echo AffiliateHelper::versionBox(); ?>");
        var version = null;
        var plugVersion = null;
        var url = 'index.php?option=com_installer&view=update&task=update.ajax&<?php echo JSession::getFormToken(); ?>=1&eid=0&skip=700';
        jQuery.ajax({
          url: url,
          dataType: 'json',
          success: function (result) {
            var compTrobat = false;
            var plugTrobat = false;
            var i = 0;
            while ((!plugTrobat || !compTrobat) && i < result.length) {
              if (result[i].element == 'com_affiliatetracker') {
                version = result[i].version;
                compTrobat = true;
              } else if (result[i].element == 'affiliate_tracker') {
                plugVersion = result[i].version;
                plugTrobat = true;
              }
              i++;
            }
            if (version == null && plugVersion == null) {
              jQuery('#update-info').html('<?php echo JText::_('SYSTEM_UP_TO_DATE'); ?>');
            } else {
              if (version != null) {
                jQuery('#latest-version').html('<strong>' + version + '</strong>');
              }
              if (plugVersion != null) {
                jQuery('#latest-plugin-version').html('<strong>' + plugVersion + '</strong>');
              }
              jQuery('#update-info').html('<?php echo JText::_('SYSTEM_NEEDS_UPDATE'); ?><div class="row-fluid"><a class="btn btn-info btn-get-updates" href="http://www.joomlathat.com/account/downloads" target="_blank"><?php echo JText::_('UPDATE_CLICK_DOWNLOAD'); ?></a></div>');
            }
          }
        });
      });
    </script>
  <?php } ?>

  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="conversion" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>
