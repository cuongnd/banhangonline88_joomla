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
<div class="row-fluid">
	<div class="span12">

		<div class="span8">

			<div class="tabbable">
				
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#user-account" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TAB_ACCOUNT_DETAILS' ); ?></a>
					</li>
					<li>
						<a href="#user-info" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TAB_ACCOUNT_INFO' ); ?></a>
					</li>
				</ul>

			</div>

			<div class="tab-content">

				<div class="tab-pane active" id="user-account">
					<?php echo $this->loadTemplate( 'account_bootstrap' ); ?>
				</div>

				<div class="tab-pane" id="user-info">
					<?php echo $this->loadTemplate( 'blogger_bootstrap' ); ?>
				</div>
			</div>

		</div>

		<div class="span4">

			<div class="accordion" id="options">

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#feedburner"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER' ); ?></a>
					</div>
					<div id="feedburner" class="accordion-body collapse in">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'feedburner' ); ?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#facebook"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FACEBOOK' ); ?></a>
					</div>
					<div id="facebook" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'facebook' ); ?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#twitter"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_TWITTER' ); ?></a>
					</div>
					<div id="twitter" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'twitter' ); ?>
						</div>
					</div>
				</div>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#linkedin"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_LINKEDIN' ); ?></a>
					</div>
					<div id="linkedin" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'linkedin' ); ?>
						</div>
					</div>
				</div>

				<?php if( $this->config->get( 'integration_google_adsense_enable' ) ) { ?>
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#adsense"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE' ); ?></a>
					</div>
					<div id="adsense" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'adsense' ); ?>
						</div>
					</div>
				</div>
				<?php } ?>

				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#options" href="#google"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_GOOGLE' ); ?></a>
					</div>
					<div id="google" class="accordion-body collapse">
						<div class="accordion-inner">
							<?php echo $this->loadTemplate( 'google' ); ?>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>