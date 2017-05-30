<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

?>
<div class="cm-management-login">
	<div class="container">
		<div class="well">
			<h3 class="login-header"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_LOGIN_HEADER'); ?></h3>
			<form class="form-inline" id="login-form" method="post" action="<?php echo JRoute::_('index.php', true); ?>">
				<fieldset class="loginform">
					<div class="control-group">
						<div class="controls">
							<div class="input-prepend input-append">
								<span class="add-on"><i data-placement="left" class="icon-user" data-original-title="<?php echo JText::_('JGLOBAL_USERNAME'); ?>"></i> <label class="element-invisible" for="mod-login-username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label></span>
								<input type="text" size="15" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" id="username" tabindex="1" name="username">
							</div>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<div class="input-prepend input-append">
								<span class="add-on"><i data-placement="left" class="icon-lock" data-original-title="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>"></i> <label class="element-invisible" for="mod-login-password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label></span>
								<input type="password" size="15" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="password" tabindex="2" name="password">
							</div>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<div class="btn-group pull-right">
								<button class="btn btn-primary" tabindex="3"><i class="icon-lock icon-white"></i> <?php echo JText::_('JLOGIN'); ?></button>
							</div>
						</div>
					</div>
				</fieldset>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</div>
</div>