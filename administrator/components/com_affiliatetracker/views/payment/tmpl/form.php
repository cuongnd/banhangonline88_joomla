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
JHTML::_('behavior.formvalidation');

?>
<script type="text/javascript">
/* Override joomla.javascript, as form-validation not work with ToolBar */
Joomla.submitbutton = function(pressbutton){
    if (pressbutton == 'cancel') {
        submitform(pressbutton);
    }else{
        var f = document.adminForm;
        if (document.formvalidator.isValid(f)) {
            //f.check.value='<?php echo JSession::getFormToken(); ?>'; //send token
            submitform(pressbutton);    
        }
    
    }    
}
</script>
<div class="row-fluid">
  <div class="span6">
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate">
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'PAYMENT_DETAILS' ); ?></legend>


    <div class="control-group">
        <label class="control-label" for="payment_amount"> <?php echo JText::_( 'AMOUNT' ); ?></label>
        <div class="controls">
          
        
        <div class="input-prepend input-append">
              <span class="add-on currency_before"><?php echo $params->get('currency_before');?></span>
              <input class="inputbox input-mini" type="text" name="payment_amount" id="payment_amount" size="10" maxlength="250" value="<?php echo $this->payment->payment_amount;?>" />
              <span class="add-on currency_after"><?php echo $params->get('currency_after');?></span>
            </div>

        </div>
      </div>
      <div class="control-group">
      <label class="control-label" for="user_id"> <?php echo JText::_( 'USER' ); ?> </label>
      <div class="controls">
        <input class="inputbox" type="text" name="username" id="username" size="30" maxlength="250" disabled="disabled" value="<?php echo $this->payment->username;?> [<?php echo $this->payment->user_id;?>]" />
        <input type="hidden" class="required" value="<?php echo $this->payment->user_id;?>" name="user_id" id="user_id" />
        <div class="input-append ">
          <input type="text" name="search_user" id="search_user"  value="" size="30" placeholder="<?php echo JText::_('TYPE_SOMETHING'); ?>" />
          <input type="button" class="btn btn-inverse" id="button_search_user" value="<?php echo JText::_('SEARCH_USER'); ?>" />
        </div>
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
        <div id="log_users"></div>
      </div>
    </div>
      <div class="control-group">
        <label class="control-label" for="created_datetime"> <?php echo JText::_( 'PAYMENT_CREATION' ); ?></label>
        <div class="controls">
          <?php echo JHTML::calendar($this->payment->created_datetime, "created_datetime", "created_datetime", "%Y-%m-%d", array("class" => "inputbox input-medium")); ?>
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label" for="payment_datetime"> <?php echo JText::_( 'PAYMENT_DATETIME' ); ?></label>
        <div class="controls">
          <?php echo JHTML::calendar($this->payment->payment_datetime, "payment_datetime", "payment_datetime", "%Y-%m-%d", array("class" => "inputbox input-medium")); ?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="payment_status"> <?php echo JText::_( 'PAID' ); ?></label>
        <div class="controls">

          <div class="btn-group" id="sign_group">
  
          <label class="btn" for="publish_1" ><?php echo JText::_('JYES');?>
            <input class="radio_toggle" type="radio" value="1" name="payment_status" id="publish_1" <?php if($this->payment->payment_status == 1) echo "checked='checked'";?> />
          </label>
            <label class="btn" for="publish_0"><?php echo JText::_('JNO');?>
          <input class="radio_toggle" type="radio" value="0" name="payment_status" id="publish_0" <?php if($this->payment->payment_status == 0) echo "checked='checked'";?> /> </label>
          <label class="btn" for="publish_2" ><?php echo JText::_('PENDING');?>
            <input class="radio_toggle" type="radio" value="2" name="payment_status" id="publish_2" <?php if($this->payment->payment_status == 2) echo "checked='checked'";?> />
          </label>
        </div>

        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="payment_description"> <?php echo JText::_( 'PAYMENT_DESCRIPTION' ); ?></label>
        <div class="controls">
          <textarea name="payment_description" id="payment_description" cols="40" rows="4"><?php echo $this->payment->payment_description; ?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="payment_type"> <?php echo JText::_( 'PAYMENT_METHOD' ); ?></label>
        <div class="controls">
          <input class="text_area" type="text" name="payment_type" id="payment_type" size="40" maxlength="250" value="<?php echo $this->payment->payment_type;?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="payment_details"> <?php echo JText::_( 'PAYMENT_DETAILS' ); ?></label>
        <div class="controls">
          <textarea name="payment_details" id="payment_details" cols="40" rows="10"><?php echo $this->payment->payment_details; ?></textarea>
        </div>
      </div>
    
  </fieldset>

<input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="id" value="<?php echo $this->payment->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="payment" />
  <input type="hidden" name="from" value="<?php echo JRequest::getVar('from'); ?>" />
</form>
</div>
<div class="span6">
  <fieldset class="adminform">
  <legend><?php echo JText::_( 'PAYMENT_OPTIONS' ); ?></legend>
    <?php 
    if($this->payment->id) echo $this->loadTemplate('payment');
    else echo JText::_( 'SAVE_FIRST_PAYMENT' );
    ?>
  </fieldset>
</div></div>
  
  
