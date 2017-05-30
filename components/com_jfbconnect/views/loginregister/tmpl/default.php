<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

if ($this->defaultValidationNeeded)
{
    JHtml::_('behavior.formvalidation');
}

$profileFields = $this->profileFields;
?>

<div id="jfbc_loginregister">

    <h1><?php echo JText::_('COM_JFBCONNECT_WELCOME') . ' ' . $this->profile->get('first_name') ?>!</h1>

    <?php
    if ($this->displayType == 'register-only')
        echo '<p>'.JText::sprintf('COM_JFBCONNECT_THANKS_FOR_SIGNING_IN_REGISTER_ONLY', ucfirst($this->providerName)).'</p>';
    else
        echo '<p>'.JText::sprintf('COM_JFBCONNECT_THANKS_FOR_SIGNING_IN', ucfirst($this->providerName)).'</p>';
    ?>

    <div id="jfbc_loginregister_userinfo" class="<?php echo $this->displayType; ?>">
        <div id="jfbc_loginregister_existinguser">
            <form action="" method="post" name="form">
                <fieldset>
                    <legend><?php echo JText::_('COM_JFBCONNECT_EXISTING_USER_REGISTRATION') ?></legend>
                    <p><?php echo JText::sprintf('COM_JFBCONNECT_EXISTING_USER_INSTRUCTIONS', ucfirst($this->providerName)); ?></p>
                    <dl>
                        <dt><label><?php echo JText::_('COM_JFBCONNECT_USERNAME') ?> </label></dt>
                        <dd><input type="text" class="inputbox" name="username" value="" size="20" /></dd>
                        <dt><label><?php echo JText::_('COM_JFBCONNECT_PASSWORD') ?> </label></dt>
                        <dd><input type="password" class="inputbox" name="password" value="" size="20" /></dd>
                        <dt></dt>
                        <dd><input type="submit" class="button" value="<?php echo JText::_('COM_JFBCONNECT_LOGIN'); ?>" />
                        </dd>
                    </dl>
                </fieldset>

                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="task" value="loginregister.loginMap" />
                <input type="hidden" name="provider" value="<?php echo $this->providerName; ?>" />
                <?php echo JHTML::_('form.token');
                echo JFBCFactory::getLoginButtons($this->altParams);
                ?>
            </form>
            <div style="clear:both"></div>
        </div>
        <div id="jfbc_loginregister_newuser">
            <form action="" method="post" id="adminForm" class="form-validate">
                <?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
                    <?php $fields = $this->form->getFieldset($fieldset->name); ?>
                    <?php if (count($fields))
                    {
                        ?>
                        <fieldset>
                            <?php if (isset($fieldset->label))
                            { // If the fieldset has a label set, display it as the legend.
                                ?>
                                <legend><?php echo JText::_($fieldset->label); ?></legend>
                            <?php } ?>
                            <dl>
                                <?php foreach ($fields as $field): // Iterate through the fields in the set and display them.?>
                                    <?php if ($field->hidden)
                                    { // If the field is hidden, just display the input.
                                        ?>
                                        <?php echo $field->input; ?>
                                    <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <dt>
                                            <?php echo $field->label; ?>
                                            <?php if (!$field->required && (!$field->type == "spacer"))
                                            {
                                                ?>
                                                <span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
                                            <?php } ?>
                                        </dt>
                                        <dd><?php echo $field->input; ?></dd>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </dl>
                        </fieldset>
                    <?php } ?>
                <?php endforeach; ?>
                <div class="profile-fields">
                    <?php
                    foreach ($profileFields as $profileForm)
                        echo $profileForm;
                    ?>
                </div>

                <input type="submit" class="button validate" value="<?php echo JText::_('COM_JFBCONNECT_REGISTER') ?>" />

                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="task" value="loginregister.createNewUser" />
                <input type="hidden" name="provider" value="<?php echo $this->providerName; ?>" />
                <?php echo JHTML::_('form.token'); ?>
                </fieldset>
            </form>
        </div>
    </div>

    <div style="clear:both;"></div>

    <?php
    if ($this->configModel->getSetting('show_powered_by_link'))
    {
        $link = SCSocialUtilities::getAffiliateLink($this->configModel->getSetting('affiliate_id'), EXT_JFBCONNECT);
        ?>
        <div id="powered-by"><?php echo JText::_('COM_JFBCONNECT_POWERED_BY'); ?> <a target="_blank"
                                                                                     href="<?php echo $link; ?>"
                                                                                     title="Joomla Facebook Integration">JFBConnect</a>
        </div>
    <?php } ?>

</div>
