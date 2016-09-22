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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_COMMENTS' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#comments-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#comments-antispam" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_ANTISPAM' );?></a>
			</li>
			<li>
				<a href="#comments-integrations" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_INTEGRATIONS' );?></a>
			</li>
			<li>
				<a href="#comments-facebook" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_COMMENTS_SUBTAB_FACEBOOK' );?></a>
			</li>
		</ul>
	</div>


	<div class="tab-content">
		
		<div class="tab-pane active" id="comments-general">
			<?php echo $this->loadTemplate( 'comments_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="comments-antispam">
			<?php echo $this->loadTemplate( 'comments_antispam_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="comments-integrations">
			<?php echo $this->loadTemplate( 'comments_integrations_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="comments-facebook">
			<?php echo $this->loadTemplate( 'comments_facebook_' . $this->getTheme() ); ?>
		</div>
	</div>

</div>