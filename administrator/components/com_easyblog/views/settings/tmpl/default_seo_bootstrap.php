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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_SEO' );?></h3>
<hr />
<div class="row-fluid">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#seo-general" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SEO_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#seo-advanced" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SEO_SUBTAB_ADVANCED_SETTINGS' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">
		
		<div class="tab-pane active" id="seo-general">
			<?php echo $this->loadTemplate( 'seo_general_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="seo-advanced">
			<?php echo $this->loadTemplate( 'seo_advanced_' . $this->getTheme() ); ?>
		</div>

	</div>

</div>
