<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_SIGNIN'); ?>

			<div style="margin-top:5px">
			<?php if( !$this->isAssociated ){ ?>
				<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=autoposting&task=request&type=twitter');?>"><img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/twitter_signon.png" /></a>
			<?php } else { ?>
				<span class="completed"><?php echo JText::_( 'COM_EASYBLOG_COMPLETED' );?></span>
			<?php } ?>
			</div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_CENTRALIZED_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_twitter_centralized' , $this->config->get( 'integrations_twitter_centralized' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_AUTO_UPDATE_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_auto_post' , $this->config->get( 'integrations_twitter_centralized_auto_post' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_AUTO_UPDATE_FOR_UPDATES_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_send_updates' , $this->config->get( 'integrations_twitter_centralized_send_updates' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
	</ul>

	<input type="button" class="button" onclick="previousPage();" value="<?php echo JText::_( 'COM_EASYBLOG_PREVIOUS_STEP_BUTTON' );?>" />
	<input type="submit" class="button social twitter" value="<?php echo JText::_( 'COM_EASYBLOG_NEXT_STEP_BUTTON' );?>" />
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="twitter" />
	<input type="hidden" name="c" value="autoposting" />
	<input type="hidden" name="option" value="com_easyblog" />
	<?php echo JHTML::_( 'form.token' );?>
</form>
