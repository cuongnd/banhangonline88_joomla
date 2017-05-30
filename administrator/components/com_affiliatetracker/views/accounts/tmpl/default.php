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
        <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" class="text_area keywords" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'TYPE_TO_SEARCH' ); ?>" />
      
       <input type="text" name="user_id" id="user_id" value="<?php echo $this->user_id;?>" class="input-mini" placeholder="<?php echo JText::_( 'USER_ID' ); ?>" />

      <?php echo $this->lists['status']; ?>
      
      <button class="btn btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('FILTER_RESULTS'); ?>"><i class="icon-search"></i> <?php echo JText::_('GO'); ?></button>
      
        <button class="btn" type="button" onclick="document.getElementById('keywords').value='';document.getElementById('user_id').value='';document.getElementById('status_id').selectedIndex = 0;document.getElementById('adminForm').submit();" title="<?php echo JText::_('RESET'); ?>"><i class="icon-remove"></i></button>
      </div>
    </div>

    <?php     
    $modules = JModuleHelper::getModules("at_accounts_backend");
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
          <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'acc.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
          <th > <?php echo JHTML::_( 'grid.sort', 'ACCOUNT', 'acc.account_name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th colspan="2"> <?php echo JHTML::_( 'grid.sort', 'ACCOUNT_OWNER', 'u.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th > <?php echo JText::_('AFFILIATE_LINK'); ?> </th>
          <th width="5" class="hidden-phone"><?php echo JHTML::_( 'grid.sort', 'APPROVED', 'acc.publish', $this->lists['order_Dir'], $this->lists['order']); ?></th>
          
          <th align="right" class="hidden-phone width55"><?php echo JText::_('TOTAL_ACCOUNT'); ?></th>
          <th align="right" class="hidden-phone width55"><?php echo JText::_('TOTAL_EARNED'); ?></th>
          <th align="right" class="hidden-phone width55"><?php echo JText::_('TOTAL_PAID'); ?></th>
          <th align="right" class="hidden-phone width55"><?php echo JText::_('TOTAL_OWED'); ?></th>

          <th class="hidden-phone"> <?php echo JText::_('ACTIONS'); ?> </th>
        </tr>
      </thead>
      <?php
	$k = 0;
	
	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=account&task=edit&cid[]='. $row->id );
		
		if($row->publish){
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/tick.png','Published');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=account&task=unpublish&cid[]='. $row->id); 
		}
		else{
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/publish_x.png','Not Published');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=account&task=publish&cid[]='. $row->id); 
		}

    $link_payment = JRoute::_('index.php?option=com_affiliatetracker&controller=payment&task=edit&fromuser='. $row->user_id); 
		
    $total_owed = $row->total_earned - $row->total_paid ;

    $link_user    = JRoute::_( 'index.php?option=com_affiliatetracker&controller=accounts&user_id='. $row->user_id );

		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td><a href="<?php echo $link; ?>"><?php echo $row->account_name; ?></a></td>
        <td><?php echo $row->username; ?></td>
        <td width="24"><a href="<?php echo $link_user; ?>" data-original-title="<?php echo JText::_('CLICK_FILTER_USER'); ?>" rel="tooltip"><i class="icon-share-alt hide-icon"></i></a></td>
        <td><?php echo AffiliateHelper::get_account_link($row->id, $row->ref_word); ?></td>
        
        <td align="center" class="hidden-phone center"><a href="<?php echo $link_publicat; ?>"><?php echo $publicat; ?></a></td>
        
        <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($row->total_account); ?></td>
        <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($row->total_earned); ?></td>
        <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($row->total_paid); ?></td>
        <td align="right" class="hidden-phone"><?php echo AffiliateHelper::format($total_owed); ?></td>

        <td class="hidden-phone">
          <?php if($total_owed > 0){ ?>
          <a href="<?php echo $link_payment; ?>" class="btn btn-mini"><?php echo JText::sprintf('CREATE_PAYMENT_X', AffiliateHelper::format($total_owed)); ?></a>
          <?php } ?>
        </td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
      <tfoot>
       
        <tr>
          <td colspan="12" ><?php  echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
    </table>
    
  </div>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="account" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>