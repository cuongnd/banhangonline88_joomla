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
    <script language="javascript" type="text/javascript">
    function submitbutton() {
        var form = document.forms["adminForm"];
        var r = new RegExp("[^0-9\.,]", "i");

        // do field validation
        if (form.email.value == "") {
            alert( "<?php echo JText::_('ADSMANAGER_REGWARN_EMAIL');?>" );
        } else {
            form.submit();
        }
    }
    </script>
    <div class="row-fluid">
        <fieldset id="contact-form">
        <legend>
        <?php  echo JText::_('ADSMANAGER_FORM_MESSAGE_WRITE'); ?>
        </legend>
    <?php $target = TRoute::_("index.php?option=com_adsmanager&task=sendmessage");?>
            <form action="<?php echo $target;?>" class="form-horizontal" method="post" name="adminForm" enctype="multipart/form-data">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="name"><?php echo JText::_('ADSMANAGER_FORM_NAME'); ?></label>
                            <div class="controls">
                                <input class='adsmanager_required' id='name' type='text' name='name' maxlength='50' value='<?php echo $this->user->name; ?>' />
                            </div>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="email"><?php echo JText::_('ADSMANAGER_FORM_EMAIL'); ?></label>
                            <div class="controls">
                                <input class='adsmanager_required' id='email' type='text' name='email' maxlength='50' value='<?php echo $this->user->email; ?>' />
                            </div>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="title"><?php echo JText::_('ADSMANAGER_FORM_MESSAGE_TITLE'); ?></label>
                            <div class="controls">
                                <input class='adsmanager_required' id='title' type='text' name='title' maxlength='50' value='<?php echo JText::_('ADSMANAGER_EMAIL_TITLE').htmlspecialchars(@$this->content->ad_headline); ?>' />
                            </div>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="body"><?php echo JText::_('ADSMANAGER_FORM_MESSAGE_BODY'); ?></label>
                            <div class="controls">
                                <textarea class='adsmanager_required' id='body' name='body' cols='40' rows='10' wrap='VIRTUAL'></textarea>
                            </div>
                        </div>
                    </div>
            <?php if ($this->conf->allow_attachement == 1) { ?>

                <?php for($i = 0; $i < $this->conf->number_allow_attachement; $i++){ ?>
                        <div class="span12">
                            <div class="control-group">
                                <label class="control-label" for="attach_file<?php echo $i; ?>"><?php echo JText::_('ADSMANAGER_ATTACH_FILE');?> <?php echo $i+1; ?></label>
                                <div class="controls">
                    <input id="attach_file<?php echo $i; ?>" type="file" name="attach_file<?php echo $i; ?>" />
                                </div>
                            </div>
                        </div>
                <?php } ?>
            <?php } ?>
                <div class="row-fluid">
                    <span class="help-block"><?php echo $this->event->onMessageAfterForm ?></span>
                </div>

                <label for="adid"></label>
                <input type="hidden" name="gflag" value="0">
                <?php
                echo "<input type='hidden' name='contentid' value='".@$this->content->id."' />";
                echo "<input type='hidden' name='fieldname' value='".$this->fieldname."' />";
                ?>
                    <div class="row-fluid">
                        <div class="span12 text-center">
                            <input type="button" class="btn btn-primary" value=<?php echo JText::_('ADSMANAGER_SEND_EMAIL_BUTTON'); ?> onclick="submitbutton()" />
                        </div>
                    </div>
          <?php echo JHTML::_( 'form.token' ); ?>
                </div>
          </form>

        </fieldset>
    </div>
</div>