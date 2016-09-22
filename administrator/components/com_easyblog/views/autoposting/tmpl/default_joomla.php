<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<ul class="autoposting-p1 reset-ul">
	<li>
		<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/facebook_setup.png" style="float:left;margin-right:20px;" />
		<h3 class="head-3">
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK');?>
			<?php if( $this->config->get( 'integrations_facebook_api_key' ) && $this->config->get( 'integrations_facebook_secret_key' ) && $this->config->get( 'integrations_facebook' ) && $this->isFacebookAssociated ){ ?>
			<small>- <?php echo JText::_( 'COM_EASYBLOG_CONFIGURED' );?></small>
			<?php } ?>
		</h3>
		<p>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_DESC' );?>
		</p>
		<div>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=facebook&step=1' );?>" class="button social facebook"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STEP_GUIDES' );?></a>
			<span class="small"><?php echo JText::_( 'COM_EASYBLOG_OR' );?></span>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=form&type=facebook' );?>" class="social facebook mll"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STANDARD_SETTINGS' );?></a>
		</div>

		<div style="clear:both;"></div>
	</li>
	<li>
		<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/twitter_setup.png" style="float:left;margin-right:20px;" />
		<h3 class="head-3">
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER');?>
			<?php if( $this->config->get( 'integrations_twitter_api_key' ) && $this->config->get( 'integrations_twitter_secret_key' ) && $this->config->get( 'integrations_twitter' ) && $this->isTwitterAssociated ){ ?>
			<small>- <?php echo JText::_( 'COM_EASYBLOG_CONFIGURED' );?></small>
			<?php } ?>
		</h3>
		<p>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_DESC' );?>
		</p>

		<div>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=twitter&step=1' );?>" class="button social twitter"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STEP_GUIDES' );?></a>
			<span class="small"><?php echo JText::_( 'COM_EASYBLOG_OR' );?></span>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=form&type=twitter' );?>"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STANDARD_SETTINGS' );?></a></div>
		<div style="clear:both;"></div>
	</li>
	<li>
		<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/linkedin_setup.png" style="float:left;margin-right:20px;" />
		<h3 class="head-3">
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_LINKEDIN');?>
			<?php if( $this->config->get( 'integrations_linkedin_api_key' ) && $this->config->get( 'integrations_linkedin_secret_key' ) && $this->config->get( 'integrations_linkedin' ) && $this->isLinkedinAssociated ){ ?>
			<small>- <?php echo JText::_( 'COM_EASYBLOG_CONFIGURED' );?></small>
			<?php } ?>
		</h3>
		<p>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_LINKEDIN_DESC' );?>
		</p>
		<div>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=linkedin&step=1' );?>" class="button social linkedin"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STEP_GUIDES' );?></a>
			<span class="small"><?php echo JText::_( 'COM_EASYBLOG_OR' );?></span>
			<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=autoposting&layout=form&type=linkedin' );?>"><?php echo JText::_( 'COM_EASYBLOG_SETUP_STANDARD_SETTINGS' );?></a></div>
		<div style="clear:both;"></div>
	</li>
</ul>
