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

$params = JComponentHelper::getParams( 'com_affiliatetracker' );
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'MARKETING_ELEMENT_DETAILS' ); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"> <?php echo JText::_( 'ELEMENT_TITLE' ); ?> </label>
            <div class="controls">
                <input class="inputbox" type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $this->element->title;?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="description"> <?php echo JText::_( 'ELEMENT_DESCRIPTION' ); ?> </label>
            <div class="controls">
                <input class="inputbox input-xxlarge" type="text" name="description" id="description" size="250" maxlength="250" value="<?php echo $this->element->description;?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="publish"> <?php echo JText::_( 'PUBLISHED' ); ?> </label>
            <div class="controls">

                <div class="btn-group" id="sign_group">

                    <label class="btn" for="publish_1" ><?php echo JText::_('JYES');?>
                        <input class="radio_toggle" type="radio" value="1" name="publish" id="publish_1" <?php if($this->element->publish == 1) echo "checked='checked'";?> />
                    </label>
                    <label class="btn" for="publish_0"><?php echo JText::_('JNO');?>
                        <input class="radio_toggle" type="radio" value="0" name="publish" id="publish_0" <?php if(!$this->element->publish) echo "checked='checked'";?> /> </label>

                </div>
            </div>

        </div>

        <div class="control-group">
            <label class="control-label" for="html_code"> <?php echo JText::_( 'HTML_CODE' ); ?></label>
            <div class="controls" id="html_code_wrapper">
                <textarea class="html_code_textarea" id="html_code" name="html_code"><?php echo $this->element->html_code; ?></textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"> <?php echo JText::_( 'HTML_PREVIEW' ); ?></label>
            <div class="controls">
                <?php if (!empty($this->element->html_code) && $this->element->html_code != JText::_( 'MARKETING_DEFAULT_HTML' )) echo $this->element->html_code;
                else echo JText::_( 'HTML_SAVE_TO_PREVIEW' ); ?>
            </div>
        </div>

    </fieldset>

    <input type="hidden" name="option" value="com_affiliatetracker" />
    <input type="hidden" name="id" value="<?php echo $this->element->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="marketing" />
</form>

<script type="text/javascript">

    // https://codemirror.net/
    var myCodeMirror = CodeMirror.fromTextArea(html_code);

</script>