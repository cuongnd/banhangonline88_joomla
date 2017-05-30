<?php $supportConfig = HelpdeskUtility::GetConfig(); ?>

<div class="maqmahelpdesk container-fluid">

    <div class="alert">
        <h4 class="alert-heading"><?php echo JText::_('access_denied_title');?></h4>
        <?php echo JText::_('access_denied');?>
    </div>

    <?php if ($supportConfig->show_login_form): ?>
    <form action="<?php echo JRoute::_("index.php");?>" method="post" name="login" id="form-login"
          class="well form-inline">
        <?php echo JHtml::_('form.token'); ?>
        <?php echo JText::_('Username') ?>: <input id="modlgn_username" type="text" name="username" class="input-small"
                                                   alt="" placeholder="<?php echo JText::_('username');?>"/>
        <?php echo JText::_('Password') ?>: <input id="modlgn_passwd" type="password"
                                                   name="password"
                                                   class="input-small" alt=""
                                                   placeholder="<?php echo JText::_('password');?>"/>
        <input type="submit" name="Submit" class="btn" value="<?php echo JText::_('LOGIN') ?>"/>
        <input type="hidden" name="option" value="com_users"/>
        <input type="hidden" name="task" value="user.login"/>
        <input type="hidden" name="return"
               value="<?php echo base64_encode(JURI::current()); ?>"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
    <?php endif;?>
</div>