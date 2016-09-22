<?php
/**
* @package		Joomla.Site
* @subpackage	mod_login
* @copyright	Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');

$user = JFactory::getUser();

?>
<div class="sp-megadeal2-login sp-mod-login pull-right">
	<span class="sp-login">
		<span class="info-text">
		<a href="#" role="button" data-toggle="modal" data-target="#login">
			<i class="megadeal-icon-user"></i>
			<span class="info-content">
			<?php echo JText::_('MEGADEAL2_LOGIN'); ?>
			</span>
		</a>  
	</span>

	<!--Modal-->
	<div id="login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content custom-box-shadow">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				<i class="megadeal-icon-close"></i>
				</button>
				<div class="modal-body">
					<h2 class="title"><?php echo ($user->id>0) ? JText::_('MY_ACCOUNT') : JText::_('MOD_SINGUP_TITLE'); ?></h2>
					 <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
					 	<?php if ($params->get('pretext')): ?>
					 		<div class="pretext">
					 			<p><?php echo $params->get('pretext'); ?></p>
					 		</div>
					 	<?php endif; ?>
					 	<fieldset class="userdata">
					 		<input id="modlgn-username" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" type="text" name="username" class="input-block-level" required="required"  />
					 		<input id="modlgn-passwd" type="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" name="password" class="input-block-level" required="required" />
					 		<div class="clearfix"></div>
					 		<div class="button-wrap">
					 			<input type="submit" name="Submit" class="button btn btn-success" value="<?php echo JText::_('JLOGIN') ?>" />
					 		</div>
					 		<p class="forget-name-link pull-left">
					 			<?php echo JText::_('MOD_LOGIN_FORGOT'); ?> <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					 			<?php echo JText::_('MOD_LOGIN_FORGOT_USERNAME'); ?></a> <?php echo jText::_('MOD_LOGIN_OR'); ?> <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
					 			<?php echo JText::_('MOD_LOGIN_FORGOT_PASSWORD'); ?></a>
					 		</p>
					 					 
					 		<input type="hidden" name="option" value="com_users" />
					 		<input type="hidden" name="task" value="user.login" />
					 		<input type="hidden" name="return" value="<?php echo $return; ?>" />
					 		<?php echo JHtml::_('form.token'); ?>
					 	</fieldset>
					 	<?php if ($params->get('posttext')): ?>
					 		<div class="posttext">
					 			<p><?php echo $params->get('posttext'); ?></p>
					 		</div>
					 	<?php endif; ?>
					 </form> <!-- //col-sm-6 --> <!-- //row -->
				</div>
				<!--/Modal body-->
				<div class="modal-footer">
					<?php
					$usersConfig = JComponentHelper::getParams('com_users');
					if ($usersConfig->get('allowUserRegistration')) : ?>
					<?php echo JText::_('MOD_NEW_REGISTER'); ?>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
						<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
					<?php endif; ?>
				</div>
				<!--/Modal footer-->
			</div> <!-- Modal content-->
		</div> <!-- /.modal-dialog -->
	</div><!--/Modal-->
</div>