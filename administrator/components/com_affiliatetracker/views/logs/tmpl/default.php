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

        <?php echo JHTML::calendar($this->cal_start, "cal_start", "cal_start", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'FROM' ) )); ?>
        <?php echo JHTML::calendar($this->cal_end, "cal_end", "cal_end", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'TO' ) )); ?> 
      
        <button class="btn btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('FILTER_RESULTS'); ?>"><i class="icon-search"></i> <?php echo JText::_('GO'); ?></button>
      
        <button class="btn" type="button" onclick="document.getElementById('keywords').value='';document.getElementById('account_id').value='';document.getElementById('user_id').value='';document.getElementById('cal_start').value='';document.getElementById('cal_end').value='';document.getElementById('adminForm').submit();" title="<?php echo JText::_('RESET'); ?>"><i class="icon-remove"></i></button>
      </div>
    </div>

    <?php     
    $modules = JModuleHelper::getModules("at_logs_backend");
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
          <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'log.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
          <th colspan="2"> <?php echo JHTML::_( 'grid.sort', 'ACCOUNT', 'log.atid', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th colspan="2"> <?php echo JHTML::_( 'grid.sort', 'ACCOUNT_OWNER', 'u2.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'DATETIME', 'log.datetime', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'USER', 'log.user_id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'IP', 'log.ip', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'REFERER', 'log.refer', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <!--th class="hidden-phone"> <?php echo JText::_('ACTIONS'); ?> </th-->
        </tr>
      </thead>
      <?php
	$k = 0;
	
	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=log&task=edit&cid[]='. $row->id );
		
		$link_contact 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=account&task=edit&cid[]='. $row->atid );

    $link_account     = JRoute::_( 'index.php?option=com_affiliatetracker&controller=logs&account_id='. $row->atid );

    $link_user     = JRoute::_( 'index.php?option=com_affiliatetracker&controller=logs&user_id='. $row->owner_id );
		
		?>
      <tr class="<?php echo "row$k"; ?>">
        <td class="hidden-phone"><?php echo $row->id; ?></td>
        <td class="hidden-phone"><?php echo $checked; ?></td>
        <td><?php echo $row->account_name; ?></td>
        <td width="24"><a href="<?php echo $link_account; ?>" data-original-title="<?php echo JText::_('CLICK_FILTER_ACCOUNT'); ?>" rel="tooltip"><i class="icon-share-alt hide-icon"></i></a></td>
        <td><?php echo $row->account_owner; ?></td>
        <td width="24"><a href="<?php echo $link_user; ?>" data-original-title="<?php echo JText::_('CLICK_FILTER_USER'); ?>" rel="tooltip"><i class="icon-share-alt hide-icon"></i></a></td>
        <td class="hidden-phone"><?php echo JHTML::_('date', $row->datetime, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td class="hidden-phone"><?php if($row->user_id){echo $row->username . " [".$row->user_id."]";} ?></td>
        <td><?php echo $row->ip; ?></td>
        <td><?php echo $row->refer; ?></td>
        <!--td class="hidden-phone"></td-->
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
      <tfoot>
       
        <tr>
          <td colspan="10"><?php  echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="log" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>