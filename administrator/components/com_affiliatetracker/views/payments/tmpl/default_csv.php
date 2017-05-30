<?php 

/*------------------------------------------------------------------------
# com_finances - Invoice Manager for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2012 JoomlaContentStatistics.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaContentStatistics.com
# Technical Support:  Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access'); 

$separator = ";" ;

?><?php echo JText::_('INVOICE_NUM'); ?><?php echo $separator; ?><?php echo JText::_('CONTACT'); ?><?php echo $separator; ?><?php echo JText::_('EMAIL'); ?><?php echo $separator; ?><?php echo JText::_('LINKED_TO_JOOMLA_USER'); ?><?php echo $separator; ?><?php echo JText::_('DUE_DATE'); ?><?php echo $separator; ?><?php echo JText::_('PAYMENT_DATETIME'); ?><?php echo $separator; ?><?php echo JText::_('PAYMENT_AMOUNT'); ?><?php echo $separator; ?><?php echo JText::_('PAYMENT_TYPE'); ?><?php echo $separator; ?><?php echo JText::_('STATUS'); ?><?php echo $separator; ?><?php echo "\n"; ?><?php
  $k = 0;
  for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
    $row =$this->items[$i];
	
	$subtotal += $row->payment_amount ;
	
    ?><?php echo $row->invoice_num; ?><?php echo $separator; ?><?php echo $row->contact_name; ?><?php echo $separator; ?><?php echo $row->to_email; ?><?php echo $separator; ?><?php echo $row->username; ?><?php echo $separator; ?><?php if($row->payment_duedate != "0000-00-00 00:00:00"){echo $row->payment_duedate;} ?><?php echo $separator; ?><?php if($row->payment_datetime != "0000-00-00 00:00:00"){ echo $row->payment_datetime;} ?><?php echo $separator; ?><?php echo InvoicesHelper::format_simple($row->payment_amount); ?><?php echo $separator; ?><?php echo $row->payment_type; ?><?php echo $separator; ?><?php echo $payment_status[$thestatus]; ?><?php echo $separator; ?><?php echo "\n"; ?><?php
    

    $k = 1 - $k;
  }
  ?>