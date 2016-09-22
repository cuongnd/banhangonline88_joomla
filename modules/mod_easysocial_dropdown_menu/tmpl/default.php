<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-dropdown-menu module-dropdown-menu<?php echo $suffix;?>">
	<div class="mod-bd">

		<?php if ($my->guest) { ?>
			<a href="javascript:void(0);" class="btn btn-default btn-sm"
                data-module-dropdown-login-wrapper
                data-popbox=""
                data-popbox-id="fd"
                data-popbox-component="es"
                data-popbox-type="toolbar"
                data-popbox-toggle="click"
                data-popbox-target=".mod-popbox-dropdown"
                data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
                data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
            >
				<i class="ies-locked ies-small"></i>&nbsp; <?php echo JText::_('MOD_EASYSOCIAL_DROPDOWN_MENU_SIGN_IN');?>
			</a>

			<div style="display:none;"
                data-module-dropdown-login class="mod-popbox-dropdown"
                >
				<div class="popbox-dropdown-menu dropdown-menu-login loginDropDown" style="display: block;">
					<form action="<?php echo JRoute::_('index.php');?>" method="post">

						<ul class="fd-reset-list">
							<li class="pb-0">
								<label for="es-username" class="fd-small">
									<?php if ($config->get('registrations.emailasusername')) { ?>
										<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_EMAIL'); ?>
									<?php } else { ?>
										<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_LOGIN_NAME'); ?>
									<?php } ?>
								</label>
								<input type="text" autocomplete="off" size="18" class="form-control input-sm" name="username" id="es-username" tabindex="101" />
							</li>
							<li class="pt-5 pb-0">
								<label for="es-password" class="fd-small">
									<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PASSWORD');?>
								</label>

								<input type="password" autocomplete="off" name="password" class="form-control input-sm" id="es-password" tabindex="102">
							</li>
							<li>
								<span class="pull-left">
									<span class="checkbox mt-0">
										<input type="checkbox" value="yes" class="pull-left mr-5" name="remember" id="remember" tabindex="103" />
										<label class="fd-small pull-left" for="remember">
											<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_REMEMBER_ME');?>
										</label>
									</span>

								</span>
								<input type="submit" class="btn btn-es-success pull-right btn-sm" name="Submit" value="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_BUTTON' , true );?>" tabindex="104" />
							</li>
							<li>
								<div class="dropdown-menu-footer">
									<ul class="fd-reset-list">
										<?php if (($config->get('registrations.enabled') && $config->get('general.site.lockdown.enabled') && $config->get('general.site.lockdown.registration')) || $config->get('registrations.enabled') && !$config->get('general.site.lockdown.enabled')) { ?>
										<li>
											<i class="ies-plus-2"></i>  <a href="<?php echo FRoute::registration();?>" class="pull-" tabindex="5"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_CREATE_NEW_ACCOUNT' );?></a>
										</li>
										<?php } ?>
										<?php if (!$config->get('registrations.emailasusername')) { ?>
										<li>
											<i class="ies-help"></i>  <a href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>" class="pull-" tabindex="6"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_FORGOT_USERNAME' );?></a>
										</li>
										<?php } ?>
										<li>
											<i class="ies-help"></i>  <a href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>" class="pull-" tabindex="6"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_FORGOT_PASSWORD' );?></a>
										</li>
									</ul>
								</div>
							</li>

							<?php if ($config->get('oauth.facebook.registration.enabled') && $facebook) { ?>
							<li class="item-social text-center">
								<?php echo $facebook->getLoginButton(FRoute::registration(array('layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true), false)); ?>
							</li>
							<?php } ?>
						</ul>

						<input type="hidden" name="option" value="com_easysocial" />
						<input type="hidden" name="controller" value="account" />
						<input type="hidden" name="task" value="login" />
						<input type="hidden" name="return" value="<?php echo $loginReturn;?>" />
						<?php echo $modules->html('form.token');?>
					</form>
				</div>
			</div>
		<?php } else { ?>
			<a href="javascript:void(0);" class="login-link loginLink"
                data-module-dropdown-menu-wrapper
                data-popbox=""
                data-popbox-id="fd"
                data-popbox-component="es"
                data-popbox-type="dropdown-menu"
                data-popbox-toggle="click"
                data-popbox-target=".mod-popbox-dropdown"
                data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
                data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
            >
				<span class="es-avatar">
					<img src="<?php echo $my->getAvatar();?>" alt="<?php echo $modules->html( 'string.escape' , $my->getName() );?>" />
				</span>
				<span class="dropdown-toggle-user-name"><?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_HI' );?>, <strong><?php echo $my->getName();?></strong></span>
				<b class="caret"></b>
			</a>

			<div class="dropdown-menu dropdown-menu-user mod-popbox-dropdown" style="display:none;"
                data-module-dropdown-menu
                data-popbox-position="<?php echo $params->get('popbox_position', 'bottom'); ?>"
                data-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>">
				<ul class="popbox-dropdown-menu dropdown-menu-user mt-5">

					<?php if( $params->get( 'show_my_profile' , true ) ){ ?>
					<li>
						<a href="<?php echo $my->getPermalink();?>">
							<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_MY_PROFILE' );?>
						</a>
					</li>
					<?php } ?>

					<?php if( $params->get( 'show_account_settings' , true ) ){ ?>
					<li>
						<a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ) );?>">
							<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_ACCOUNT_SETTINGS' );?>
						</a>
					</li>
					<?php } ?>

					<?php if( $items ){ ?>
						<?php foreach( $items as $item ){ ?>
						<li class="menu-<?php echo $item->id;?>">
							<a href="<?php echo $item->flink;?>"><?php echo $item->title;?></a>
						</li>
						<?php } ?>
					<?php } ?>


					<?php if( $params->get( 'show_sign_out' , true ) ){ ?>
					<li>
						<a href="javascript:void(0);" onclick="document.getElementById('es-dropdown-logout-form').submit();">
							<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_SIGN_OUT' );?>
						</a>
						<form class="logout-form" id="es-dropdown-logout-form">
							<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
							<input type="hidden" name="option" value="com_easysocial" />
							<input type="hidden" name="controller" value="account" />
							<input type="hidden" name="task" value="logout" />
							<?php echo $modules->html( 'form.token' ); ?>
						</form>
					</li>

					<?php } ?>
				</ul>
			</div>
		<?php } ?>

	</div>
</div>

