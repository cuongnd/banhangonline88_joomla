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

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate">
  <div class="row-fluid">
    <div class="span6">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'ACCOUNT_DETAILS' ); ?></legend>
        <div class="control-group">
          <label class="control-label" for="account_name"> <?php echo JText::_( 'ACCOUNT_NAME' ); ?> </label>
          <div class="controls">
            <input class="inputbox" type="text" name="account_name" id="account_name" size="30" maxlength="250" value="<?php echo $this->account->account_name;?>" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="search_user"> <?php echo JText::_( 'USER' ); ?> </label>
          <div class="controls">
            <input class="inputbox" type="text" name="username" id="username" size="30" maxlength="250" disabled="disabled" value="<?php echo $this->account->username;?> [<?php echo $this->account->user_id;?>]" />
            <input type="hidden" value="<?php echo $this->account->user_id;?>" name="user_id" id="user_id" />
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
          <label class="control-label" for="publish"> <?php echo JText::_( 'APPROVED' ); ?> </label>
          <div class="controls">

          <div class="btn-group" id="sign_group">

            <label class="btn" for="publish_1" ><?php echo JText::_('JYES');?>
              <input class="radio_toggle" type="radio" value="1" name="publish" id="publish_1" <?php if($this->account->publish == 1) echo "checked='checked'";?> />
            </label>
              <label class="btn" for="publish_0"><?php echo JText::_('JNO');?>
            <input class="radio_toggle" type="radio" value="0" name="publish" id="publish_0" <?php if(!$this->account->publish) echo "checked='checked'";?> /> </label>

            </div>
          </div>

        </div>

        <div class="control-group">
          <label class="control-label" for="comission"> <?php echo JText::_( 'DEFAULT_COMMISSION' ); ?> </label>
          <div class="controls">
            <input class="inputbox input-mini" type="text" name="comission" id="comission" size="8" maxlength="250" value="<?php echo $this->account->comission;?>" />
            <select name="type" id="type">
              <?php
                $publish = "";
                $unpublish = "";
                if($this->account->type == "percent") $publish = "selected";
                else $unpublish = "selected";

                ?>
              <option <?php echo $publish;?> value="percent"><?php echo JText::_('PERCENT');?></option>
              <option <?php echo $unpublish;?> value="flat"><?php echo JText::_('FLAT');?></option>
            </select>
          </div>
        </div>

        <div class="control-group">
          <div class="controls">
            <div></div>
          </div>
        </div>

        <div class="control-group" id="ref_word_group">
          <label class="control-label" for="ref_word"> <?php echo JText::_( 'REF_WORD' ); ?> </label>
          <div class="controls">
            <input class="inputbox" type="text" name="ref_word" id="ref_word" size="30" maxlength="250" value="<?php echo $this->account->ref_word;?>" />
            <span class="help-inline" id="ref_word_error"><?php echo JText::_( 'REF_WORD_TAKEN' ); ?></span>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="refer_url"> <?php echo JText::_( 'REFER_URL' ); ?> </label>
          <div class="controls">
            <input class="inputbox" type="text" name="refer_url" id="refer_url" size="30" maxlength="250" value="<?php echo $this->account->refer_url;?>" placeholder="<?php echo JText::_('REFER_URL_PLACEHOLDER');?>"/>
          </div>
        </div>

        <div class="control-group">
          <div class="controls">
            <div></div>
          </div>
        </div>

        <div class="control-group">
          <div class="controls">
            <div id="log_user_parents"></div>
          </div>
        </div>

      </fieldset>

      <fieldset class="adminform">
        <legend><?php echo JText::_( 'CONTACT_DETAILS' ); ?></legend>

        <div class="control-group">
            <label class="control-label" for="name"> <?php echo JText::_( 'YOUR_NAME' ); ?></label>
            <div class="controls">
              <input class="inputbox" type="text" name="name" id="name" size="80" maxlength="250" value="<?php echo $this->account->name;?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="company"> <?php echo JText::_( 'COMPANY' ); ?></label>
            <div class="controls">
              <input class="inputbox" type="text" name="company" id="company" size="80" maxlength="250" value="<?php echo $this->account->company;?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="email"> <?php echo JText::_( 'EMAIL' ); ?></label>
            <div class="controls">
              <input class="inputbox" type="text" name="email" id="email" size="80" maxlength="250" value="<?php echo $this->account->email;?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="address"> <?php echo JText::_( 'ADDRESS' ); ?></label>
            <div class="controls">
              <textarea name="address" id="address" cols="40" rows="4"><?php echo $this->account->address; ?></textarea>
            </div>
          </div>


        <div class="control-group">
            <label class="control-label" for="city"> <?php echo JText::_( 'LOCATION_CITY' ); ?></label>
            <div class="controls">
              <input class="inputbox input-small" type="text" name="zipcode" id="zipcode" size="80" maxlength="250" value="<?php echo $this->account->zipcode;?>" placeholder="<?php echo JText::_( 'RECIPIENT_ZIPCODE_PLACEHOLDER' ); ?>" />
              <input class="inputbox input-small" type="text" name="city" id="city" size="80" maxlength="250" value="<?php echo $this->account->city;?>" placeholder="<?php echo JText::_( 'RECIPIENT_CITY_PLACEHOLDER' ); ?>" />

            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="country"> <?php echo JText::_( 'LOCATION_COUNTRY' ); ?></label>
            <div class="controls">

              <input class="inputbox input-small" type="text" name="state" id="state" size="80" maxlength="250" value="<?php echo $this->account->state;?>" placeholder="<?php echo JText::_( 'RECIPIENT_STATE_PLACEHOLDER' ); ?>" />
              <input class="inputbox input-small" type="text" name="country" id="country" size="80" maxlength="250" value="<?php echo $this->account->country;?>" placeholder="<?php echo JText::_( 'RECIPIENT_COUNTRY_PLACEHOLDER' ); ?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="phone"> <?php echo JText::_( 'PHONE' ); ?></label>
            <div class="controls">
              <input class="inputbox" type="text" name="phone" id="phone" size="80" maxlength="250" value="<?php echo $this->account->phone;?>" />
            </div>
          </div>

        </fieldset>
    </div>
    <div class="span6">
      <?php echo $this->loadTemplate('commissions'); ?>
    </div>
  </div>
  <input type="hidden" name="option" value="com_affiliatetracker" />
  <input type="hidden" name="id" value="<?php echo $this->account->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="account" />
</form>
