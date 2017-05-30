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

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate">
  <fieldset class="adminform">
    <legend><?php echo JText::_( 'CONVERSION_DETAILS' ); ?></legend>
    <div class="control-group">
      <label class="control-label" for="name"> <?php echo JText::_( 'NAME' ); ?></label>
      <div class="controls">
        <input class="inputbox" type="text" name="name" id="name" size="30" maxlength="250" value="<?php echo $this->conversion->name;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="extended_name"> <?php echo JText::_( 'EXTENDED_NAME' ); ?></label>
      <div class="controls">
        <input class="inputbox" type="text" name="extended_name" id="extended_name" size="30" maxlength="250" value="<?php echo $this->conversion->extended_name;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="atid"> <?php echo JText::_( 'AFFILIATE_ACCOUNT' ); ?></label>
      <div class="controls">
        <input class="inputbox" type="text" name="account_name" id="account_name" size="30" maxlength="250" disabled="disabled" value="<?php echo $this->conversion->account_name;?>" placeholder="<?php echo JText::_( 'NOT_ASSIGNED' ); ?>" />
        <input type="hidden" class="required" value="<?php echo $this->conversion->atid;?>" name="atid" id="atid" />
        <div class="input-append ">
          <input type="text" name="search_account" id="search_account"  value="" size="30" placeholder="<?php echo JText::_('TYPE_SOMETHING'); ?>" />
          <input type="button" class="btn btn-inverse" id="button_search_account" value="<?php echo JText::_('SEARCH_ACCOUNT'); ?>" />
        </div>
      </div>
    </div>
    
    <div class="control-group">
      <div class="controls">
        <div id="log_accounts"></div>
      </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="approved"> <?php echo JText::_( 'APPROVED' ); ?></label>
        <div class="controls">
          <div class="btn-group" id="sign_group">
  
        <label class="btn" for="publish_1" ><?php echo JText::_('JYES');?>
          <input class="radio_toggle" type="radio" value="1" name="approved" id="publish_1" <?php if($this->conversion->approved == 1) echo "checked='checked'";?> />
        </label>
          <label class="btn" for="publish_0"><?php echo JText::_('JNO');?>
        <input class="radio_toggle" type="radio" value="0" name="approved" id="publish_0" <?php if(!$this->conversion->approved) echo "checked='checked'";?> /> </label>

        </div>
      </div>
        
      </div>
    
    <div class="control-group">
      <label class="control-label" for="value"> <?php echo JText::_( 'VALUE' ); ?></label>
      <div class="controls">
       
        <div class="input-prepend input-append">
              <span class="add-on currency_before"><?php echo $params->get('currency_before');?></span>
              <input class="inputbox input-mini" type="text" name="value" id="value" size="10" maxlength="250" value="<?php echo $this->conversion->value;?>" placeholder="<?php echo JText::_( '0_00' ); ?>" />
              <span class="add-on currency_after"><?php echo $params->get('currency_after');?></span>
            </div>
      </div>

    </div>
    <div class="control-group">
      <label class="control-label" for="comission"> <?php echo JText::_( 'COMISSION' ); ?></label>
      <div class="controls">
        
        <div class="input-prepend input-append">
              <span class="add-on currency_before"><?php echo $params->get('currency_before');?></span>
              <input class="inputbox input-mini" type="text" name="comission" id="comission" size="10" maxlength="250" value="<?php echo $this->conversion->comission;?>" placeholder="<?php echo JText::_( '0_00' ); ?>" /> />
              <span class="add-on currency_after"><?php echo $params->get('currency_after');?></span>
            </div>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="type"> <?php echo JText::_( 'TYPE' ); ?></label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="type" id="type" size="8" maxlength="11" value="<?php echo $this->conversion->type;?>" placeholder="<?php echo JText::_( 'NUMBER' ); ?>" /> <input class="inputbox" type="text" name="component" id="component" size="30" maxlength="255" value="<?php echo $this->conversion->component;?>" placeholder="<?php echo JText::_( 'COMPONENT' ); ?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="type"> <?php echo JText::_( 'EXTERNAL_REFERENCE_ID' ); ?></label>
      <div class="controls">
        <input class="inputbox input-mini" type="text" name="reference_id" id="reference_id" size="8" maxlength="11" value="<?php echo $this->conversion->reference_id;?>" placeholder="<?php echo JText::_( 'NUMBER' ); ?>" /> 
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="date_created"> <?php echo JText::_( 'DATE_CREATED' ); ?></label>
      <div class="controls">
       <?php echo JHTML::calendar($this->conversion->date_created, "date_created", "date_created", "%Y-%m-%d", array("class" => "date_item input-small")); ?>
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="search_user"> <?php echo JText::_( 'USER' ); ?></label>
      <div class="controls">
        <input class="inputbox" type="text" name="username" id="username" size="30" maxlength="250" disabled="disabled" value="<?php echo $this->conversion->username;?> [<?php echo $this->conversion->user_id;?>]" />
        <input type="hidden" value="<?php echo $this->conversion->user_id;?>" name="user_id" id="user_id" />
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
    
    
  </fieldset>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="id" value="<?php echo $this->conversion->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="conversion" />
</form>
