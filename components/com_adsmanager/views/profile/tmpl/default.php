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
    <script type="text/javascript">
    function submitbutton() {
        var form = document.adminForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

        // do field validation
        if (form.name.value == "") {
            alert( "<?php echo JText::_('ADSMANAGER_REGWARN_NAME');?>" );
        } else if (form.email.value == "") {
            alert( "<?php echo JText::_('ADSMANAGER_REGWARN_EMAIL');?>" );
        } else if ((form.password.value != "") && (form.password.value != form.verifyPass.value)){
            alert( "<?php echo JText::_('ADSMANAGER_REGWARN_VPASS2');?>" );
        } else if (r.exec(form.password.value)) {
            alert( "<?php printf( JText::_('ADSMANAGER_VALID_AZ09'), JText::_('ADSMANAGER_REGISTER_PASS'), 6 );?>" );
        } else {
            form.submit();
        }
    }
    </script>
    <div class="row-fluid">
    <?php $target = TRoute::_("index.php?option=com_adsmanager&task=saveprofile"); ?>
        <form action="<?php echo $target; ?>" method="post" class="form-horizontal" name="adminForm" id="adminForm">
            <fieldset>
                <legend><?php echo JText::_('ADSMANAGER_EDIT_PROFILE') ?></legend>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="name"><?php echo JText::_('ADSMANAGER_UNAME'); ?></label>
                            <div class="controls">
                                <input type="text" name="username" readonly="readonly" value="<?php echo htmlspecialchars($this->user->username);?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
    <?php if(function_exists("showBalance")){
        showBalance($this->user->id);
    } ?>
            <fieldset id="profile-password">
                <legend><?php echo JText::_('ADSMANAGER_PROFILE_PASSWORD'); ?></legend>
                <div class="row-fluid">
                    <div class="span12">
                        <span class="help-block"><?php echo JText::_('ADSMANAGER_PROFILE_PASSWORD_MESSAGE'); ?></span>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="password"><?php echo JText::_('ADSMANAGER_PASSWORD'); ?></label>
                            <div class="controls">
                                <input type="password" name="password" autocomplete="off" value="" size="40" />
                            </div>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="verifyPass"><?php echo JText::_('ADSMANAGER_VPASS'); ?></label>
                            <div class="controls">
                                <input type="password" name="verifyPass" autocomplete="off" size="40" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset id="profile-contact">
                <legend><?php echo JText::_('ADSMANAGER_PROFILE_CONTACT'); ?></legend>
                <div class="row-fluid">
                    <div class="span12">
                        <span class="help-block"><?php echo JText::_('ADSMANAGER_PROFILE_CONTACT_MESSAGE'); ?></span>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="name"><?php echo JText::_('ADSMANAGER_PROFILE_NAME'); ?></label>
                            <div class="controls">
                                <input type="text" name="name" value="<?php echo htmlspecialchars($this->user->name);?>" size="40" />
                            </div>
                        </div>
                    </div>
                    <div class="span12">
                        <div class="control-group">
                            <label class="control-label" for="email"><?php echo JText::_('ADSMANAGER_FORM_EMAIL'); ?></label>
                            <div class="controls">
                                <input type="text" name="email" value="<?php echo htmlspecialchars($this->user->email);?>" size="40" />
                            </div>
                        </div>
                    </div>
    <?php
    $user = $this->user;

    if (isset($this->fields)) {
                foreach($this->fields as $f) {
        if (($f->name != "name")&&($f->name != "email")){
                    ?>
                        <div id="<?php echo "row_{$f->name}"; ?>" class="span12">
                            <div class="control-group">
                                <label class="control-label" for="<?php echo "{$f->name}"; ?>"><?php echo $this->field->showFieldLabel($f,$this->user,null); ?></label>
                                <div class="controls">
                                    <?php echo $this->field->showFieldForm($f,$this->user,null); ?>
                                </div>
                            </div>
                        </div>
                    <?php
        }
    }
    }
    ?>
                </div>
            </fieldset>
            <div class="row-fluid">
                <div class="span12">
                    <span class="help-block">
    <?php echo $this->event->onUserAfterForm ?>
                    </span>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <input class="btn btn-primary" type="button" value="<?php echo JText::_('ADSMANAGER_FORM_SUBMIT_TEXT'); ?>" onclick="submitbutton()" />
                </div>
            </div>
    <?php echo JHTML::_( 'form.token' ); ?>
    </form>
    </div>
    <script>
    function checkdependency(child,parentname,parentvalue) {
        //Simple checkbox
        if (jQ('input[name="'+parentname+'"]').is(':checkbox')) {
            //alert("test");
            if (jQ('input[name="'+parentname+'"]').attr('checked')) {
                jQ('#adminForm #'+child).show();
                jQ('#adminForm #row_'+child).show();
            }
            else {
                jQ('#adminForm #'+child).hide();
                jQ('#adminForm #row_'+child).hide();

                //cleanup child field 
                if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
                    jQ('#adminForm #'+child).attr('checked', false);
                }
                else {
                    jQ('#adminForm #'+child).val = '';
                }
            } 
        }
        //If checkboxes or radio buttons, special treatment
        else if (jQ('input[name="'+parentname+'"]').is(':radio')  || jQ('input[name="'+parentname+'[]"]').is(':checkbox')) {
            var find = false;
            var allVals = [];
            jQ("input:checked").each(function() {
                if (jQ(this).val() == parentvalue) {	
                    jQ('#adminForm #'+child).show();
                    jQ('#adminForm #row_'+child).show();
                    find = true;
                }
            });

            if (find == false) {
                jQ('#adminForm #'+child).hide();
                jQ('#adminForm #row_'+child).hide();

                //cleanup child field 
                if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
                    jQ('#adminForm #'+child).attr('checked', false);
                }
                else {
                    jQ('#adminForm #'+child).val = '';
                }
            }

        }
        //simple text
        else if (jQ('#adminForm #'+parentname).val() == parentvalue) {
            jQ('#adminForm #'+child).show();
            jQ('#adminForm #row_'+child).show();
        } 
        else {
            jQ('#adminForm #'+child).hide();
            jQ('#adminForm #row_'+child).hide();

            //cleanup child field 
            if (jQ('#adminForm #'+child).is(':checkbox') || jQ('#adminForm #'+child).is(':radio')) {
                jQ('#adminForm #'+child).attr('checked', false);
            }
            else {
                jQ('#adminForm #'+child).val = '';
            }
        }
    }
    function dependency(child,parentname,parentvalue) {
        //if checkboxes
        jQ('input[name="'+parentname+'[]"]').change(function() {
            checkdependency(child,parentname,parentvalue);
        });
        //if buttons radio
        jQ('input[name="'+parentname+'"]').change(function() {
            checkdependency(child,parentname,parentvalue);
        });
        jQ('#'+parentname).click(function() {
            checkdependency(child,parentname,parentvalue);
        });
        checkdependency(child,parentname,parentvalue);
    }

    jQ(document).ready(function() {
        <?php foreach($this->fields as $field) { 
            if (@$field->options->is_conditional_field == 1) { ?>
        dependency('<?php echo $field->name?>',
                   '<?php echo $field->options->conditional_parent_name?>',
                   '<?php echo $field->options->conditional_parent_value?>');
            <?php } 
        }?>
    });
    </script>
</div>