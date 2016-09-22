<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>

<div class="sp-megadeal2-login sp-mod-login pull-right">
	<div class="sp-my-account-menu">
		<ul class="sp-my-account">
			<li>
				<div class="sp-signin logged-in">
					<div class="signin-img-wrap">
						<img src="//www.gravatar.com/avatar/<?php echo md5($user->get('email')); ?>?s=30" alt="">
					</div>
					<div class="info-wrap">
						<span class="info-text">
							<?php echo JText::_('MOD_LOGIN_HI'); ?>
							<?php echo htmlspecialchars($user->username); ?>
							<i class="sp-moview-icon-down"></i>
						</span>
					</div>
				</div> <!-- /.sp-signin loged-in -->
				<?php echo JFactory::getDocument()->getBuffer('modules', 'myaccount', array('style' => 'none')); ?>
			</li>
		</ul>
	</div><!-- /.sp-my-account-menu -->	
</div> <!-- /.sp-moviedb-login -->

