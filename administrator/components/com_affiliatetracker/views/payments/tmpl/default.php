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

defined('_JEXEC') or die('Restricted access');
$params =JComponentHelper::getParams( 'com_affiliatetracker' );

$payment_status = AffiliateHelper::getPaymentStatus();

 ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
  <div id="j-main-container" >
    
    <div class="navbar filter-bar">
    <div class="navbar-inner">
        
      
        <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" class="text_area keywords" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'TYPE_TO_SEARCH' ); ?>" />
      
       <?php echo $this->lists['status']; ?> 
      
       <?php echo JHTML::calendar($this->cal_start, "cal_start", "cal_start", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'FROM' ) )); ?>
      <?php echo JHTML::calendar($this->cal_end, "cal_end", "cal_end", "%Y-%m-%d", array("class" => "inputbox input-small", "placeholder" => JText::_( 'TO' ) )); ?> 
     <input placeholder="<?php echo JText::_( 'USER_ID' ); ?>" type="text" name="user_id" id="user_id" value="<?php echo $this->user_id;?>" class="input-mini user_id" size="4" />
      
        <button class="btn btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('GO'); ?>"><i class="icon-search"></i> <?php echo JText::_('FILTER_RESULTS'); ?></button>
      
      <!--a class="btn pull-right" href="<?php echo JRoute::_('index.php?option=com_affiliatetracker&controller=payments&task=export'); ?>"><?php echo JText::_('EXPORT_CSV'); ?> (<?php echo $this->pagination->total; ?> <?php echo JText::_('ROWS'); ?>)</a-->
      
        </div>
    </div>

    <?php     
    $modules = JModuleHelper::getModules("at_payments_backend");
    $document =JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $attribs  = array();
    $attribs['style'] = 'xhtml';
    foreach ( @$modules as $mod ) 
    {
      echo $renderer->render($mod, $attribs);
    }
    ?>

    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th width="20"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
          <th width="5"> <?php echo JHTML::_( 'grid.sort', 'ID', 'pa.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
        
          <th> <?php echo JHTML::_( 'grid.sort', 'PAYMENT_TO', 'pa.user_id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'CREATION_DATE', 'pa.created_datetime', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
         
          <th> <?php echo JHTML::_( 'grid.sort', 'PAYMENT_DATETIME', 'pa.payment_datetime', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th> <?php echo JHTML::_( 'grid.sort', 'PAYMENT_AMOUNT', 'pa.payment_amount', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
  
          <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'PAYMENT_TYPE', 'pa.payment_type', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
          <th colspan="2"><?php echo JHTML::_( 'grid.sort', 'STATUS', 'pa.payment_status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
          <th ><?php echo JText::_('ACTIONS'); ?></th>
        </tr>
      </thead>
      <?php
	$k = 0;
	$subtotal = 0 ;
	$total = 0 ;
	$total_taxes = array() ;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row =$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id, false, 'cid' );
		$link 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=payment&task=edit&cid[]='. $row->id );
		
		if($row->payment_status){
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/tick.png','Payed');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=payment&task=unpublish&cid[]='. $row->id); 
		}
		else{
			$publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/publish_x.png','Not Payed');
			$link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=payment&task=publish&cid[]='. $row->id); 
		}
		
		$subtotal += $row->payment_amount ;
		
		$link_contact 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=payment&task=edit&cid[]='. $row->user_id );
		
		$class = "";
		if($row->payment_duedate != "0000-00-00 00:00:00" && !$row->payment_status){
			if(strtotime($row->payment_duedate) <= time()) $class = " notontime";
			else $class = " ontime";
		}
		
		if($row->payment_status){
				$thestatus = 1;
				$class = " payment_paid";
        $trclass = "success";
			}
			elseif($row->payment_duedate != "0000-00-00 00:00:00" && !$row->payment_status){
				if(strtotime($row->payment_duedate) <= time()) {
					$class = " payment_notontime";
					$thestatus = 0;
          $trclass = "error";
				}
				else {
					$class = " payment_unpaid_ontime";
					$thestatus = 2;
          $trclass = "warning";
				}
			}
			elseif($row->payment_duedate == "0000-00-00 00:00:00"){
				$thestatus = 2;
				$class = " payment_unpaid_ontime";
        $trclass = "warning";
			}
			
			if($row->payment_status == 2){
				$thestatus = 3;
				$class = " payment_pending";
        $trclass = "";
			}

      $link_payment     = JRoute::_( 'index.php?option=com_affiliatetracker&controller=payment&view=payment&layout=form_payment&cid[]='. $row->id );
		
		?>
      <tr class="<?php echo "row$k"; ?> <?php echo $class; ?> <?php echo $trclass; ?>">
        <td><?php echo $checked; ?></td>
        <td><a href="<?php echo $link; ?>"><?php echo $row->id; ?></a></td>
        <td><a href="<?php echo $link; ?>"><?php echo $row->username; ?></a> [<?php echo $row->user_id; ?>]</td>
        
        <td class="hidden-phone"><?php if($row->created_datetime == "0000-00-00 00:00:00") echo JText::_('NOT_SETTED'); else echo JHTML::_('date', $row->created_datetime, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td><?php if($row->payment_datetime == "0000-00-00 00:00:00") echo JText::_('NOT_SETTED'); else echo JHTML::_('date', $row->payment_datetime, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td align="right"><?php echo AffiliateHelper::format($row->payment_amount); ?></td>
        
        <td class="hidden-phone"><?php echo JText::_($row->payment_type); ?></td>
        <td width="16"><a href="<?php echo $link_publicat; ?>"><?php echo $publicat; ?></a></td>
        
        <td class="statussentence"><?php echo $payment_status[$thestatus]; ?></td>
        <td class="" >
          <?php if( !$row->payment_status) { ?><a  class=" btn btn-small btn-block" href="<?php echo $link_payment; ?>"><i class="icon-arrow-right"></i> <?php echo JText::_('PAY'); ?><?php } ?>
          
        </td>
      </tr>
      <?php
		$k = 1 - $k;
	}
	?>
      <tfoot>
        <tr class="totals">
          <td colspan="3"></td>
          <td  class="hidden-phone"></td>
          <td ></td>
          <td align="right"><?php echo AffiliateHelper::format($subtotal); ?></td>
          <td  class="hidden-phone"></td>
          <td colspan="2"></td>
          <td ></td>
        </tr>
        <tr>
          <td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="payment" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>