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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SOCIALINTEGRATIONS' );?></h3>
<hr />

<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#social-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#social-twitter" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_TWITTER' );?></a>
			</li>
			<li>
				<a href="#social-facebook" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_FACEBOOK' );?></a>
			</li>
			<li>
				<a href="#social-google" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_GOOGLE' );?></a>
			</li>
			<li>
				<a href="#social-digg" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_DIGG' );?></a>
			</li>
			<li>
				<a href="#social-linkedin" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_LINKEDIN' );?></a>
			</li>
			<li>
				<a href="#social-stumbleupon" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_STUMBLEUPON' );?></a>
			</li>
			<li>
				<a href="#social-pinterest" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_PINIT' );?></a>
			</li>
			<li>
				<a href="#social-addthis" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_ADDTHIS' );?></a>
			</li>
			<li>
				<a href="#social-sharethis" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SUBTAB_SHARETHIS' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="social-general">
			<?php echo $this->loadTemplate( 'social_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-twitter">
			<?php echo $this->loadTemplate( 'social_twitter_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-facebook">
			<?php echo $this->loadTemplate( 'social_facebook_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-google">
			<?php echo $this->loadTemplate( 'social_google_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-digg">
			<?php echo $this->loadTemplate( 'social_digg_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-linkedin">
			<?php echo $this->loadTemplate( 'social_linkedin_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-stumbleupon">
			<?php echo $this->loadTemplate( 'social_stumbleupon_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-pinterest">
			<?php echo $this->loadTemplate( 'social_pinit_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-addthis">
			<?php echo $this->loadTemplate( 'social_addthis_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="social-sharethis">
			<?php echo $this->loadTemplate( 'social_sharethis_' . $this->getTheme() ); ?>
		</div>
	</div>

</div>


