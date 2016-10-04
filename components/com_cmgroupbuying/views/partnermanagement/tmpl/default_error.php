<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<div class="cm-management-error">
	<div class="container">
		<div class="row-fluid">
			<div class="span12">
				<h1><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_ERROR_FORBIDDEN_HEADER'); ?></h1>
				<div class="error-message"><?php echo JText::_('COM_CMGROUPBUYING_PARTNER_MANAGEMENT_ERROR_FORBIDDEN_MESSAGE'); ?></div>
				<form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="login-form" class="form-vertical">
					<input type="submit" name="Submit" class="btn btn-primary btn-danger" value="<?php echo JText::_('JLOGOUT'); ?>" />
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.logout" />
					<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</form>
			</div>
		</div>
	</div>
</div>