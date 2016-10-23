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
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'PAYMENT_DETAILS' ); ?></legend>
    <table class="admintable">
      <tr>
        <td width="100" align="right" class="key"><label for="payment_amount"> <?php echo JText::_( 'AMOUNT' ); ?>: </label></td>
        <td><input class="text_area" type="text" name="payment_amount" id="payment_amount" size="10" maxlength="250" value="<?php echo $this->payment->payment_amount;?>" /></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="created_datetime"> <?php echo JText::_( 'PAYMENT_CREATION'); ?>: </label></td>
        <td><?php echo JHTML::calendar($this->payment->created_datetime, "created_datetime", "created_datetime", "%Y-%m-%d", array("class" => "text_area date_item")); ?></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_duedate"> <?php echo JText::_( 'DUE_DATE'); ?>: </label></td>
        <td><?php echo JHTML::calendar($this->payment->payment_duedate, "payment_duedate", "payment_duedate", "%Y-%m-%d", array("class" => "text_area date_item")); ?></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_datetime"> <?php echo JText::_( 'PAYMENT_DATETIME'); ?>: </label></td>
        <td><?php echo JHTML::calendar($this->payment->payment_datetime, "payment_datetime", "payment_datetime", "%Y-%m-%d", array("class" => "text_area date_item")); ?></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_status"> <?php echo JText::_( 'PAID' ); ?>: </label></td>
        <td><select name="payment_status" id="payment_status">
            <?php
			
			$publish = ""; 
			$publish_pending = ""; 
			$unpublish = ""; 
			
			if($this->payment->payment_status == 1) $publish = "selected"; 
			elseif($this->payment->payment_status == 2) $publish_pending = "selected"; 
			else $unpublish = "selected"; 
			
			?>
            <option <?php echo $publish;?> value="1"><?php echo JText::_('YES');?></option>
            <option <?php echo $unpublish;?> value="0"><?php echo JText::_('NO');?></option>
            <option <?php echo $publish_pending;?> value="2"><?php echo JText::_('PENDING');?></option>
          </select></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_description"> <?php echo JText::_( 'PAYMENT_DESCRIPTION' ); ?>: </label></td>
        <td><textarea name="payment_description" id="payment_description" cols="40" rows="4"><? echo $this->payment->payment_description; ?></textarea></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_type"> <?php echo JText::_( 'PAYMENT_METHOD' ); ?>: </label></td>
        <td><input class="text_area" type="text" name="payment_type" id="payment_type" size="40" maxlength="250" value="<?php echo $this->payment->payment_type;?>" /></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="payment_details"> <?php echo JText::_( 'PAYMENT_DETAILS' ); ?>: </label></td>
        <td><textarea name="payment_details" id="payment_details" cols="40" rows="10"><? echo $this->payment->payment_details; ?></textarea></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><label for="vincular_cliente_checkbox"><?php echo JText::_( 'INVOICE' ); ?></label></td>
        <td><input type="text" disabled="disabled" id="name_invoice" size="40" value="<? if($this->payment->invoice_id) echo "[".$this->payment->invoice_num . "] ".$this->payment->name." (".$this->payment->username.")"; ?>" />
          <input class="text_area" type="text" name="invoice_id" id="invoice_id" size="4" maxlength="11" value="<?php echo $this->payment->invoice_id;?>" /></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"><? echo JText::_('SEARCH_INVOICE'); ?></td>
        <td><div class="search_user" >
            <input type="text" name="search_invoice" id="search_invoice"  value="" size="30" />
            <input type="button" id="button_search_invoice" value="<? echo JText::_('SEARCH_INVOICE'); ?>" />
          </div></td>
      </tr>
      <tr>
        <td width="100" align="right" class="key"></td>
        <td><div id="log_invoices"></div></td>
      </tr>
    </table>
  </fieldset>
  <div class="clr"></div>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="id" value="<?php echo $this->payment->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="payment" />
  <input type="hidden" name="from" value="<?php echo JRequest::getVar('from'); ?>" />
</form>
