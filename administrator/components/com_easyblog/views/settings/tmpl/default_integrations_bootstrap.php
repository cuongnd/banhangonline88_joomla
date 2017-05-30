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
<h3><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_TAB_INTEGRATIONS' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#integrations-easysocial" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_EASYSOCIAL' ); ?></a>
			</li>
			<li>
				<a href="#integrations-easydiscuss" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_EASYDISCUSS' ); ?></a>
			</li>
			<li>
				<a href="#integrations-mightytouch" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_MIGHTYTOUCH' );?></a>
			</li>
			<li>
				<a href="#integrations-jomsocial" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_JOMSOCIAL' );?></a>
			</li>
			<li>
				<a href="#integrations-aup" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_AUP' );?></a>
			</li>
			<li>
				<a href="#integrations-phocapdf" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PHOCAPDF' );?></a>
			</li>
			<li>
				<a href="#integrations-adsense" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_ADSENSE' );?></a>
			</li>
			<li>
				<a href="#integrations-zemanta" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_ZEMANTA' );?></a>
			</li>
			<li>
				<a href="#integrations-pingomatic" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PINGOMATIC' );?></a>
			</li>
			<li>
				<a href="#integrations-flickr" data-toggle="tab"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_FLICKR' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="integrations-easysocial">
			<?php echo $this->loadTemplate( 'integrations_easysocial_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-easydiscuss">
			<?php echo $this->loadTemplate( 'integrations_easydiscuss_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-mightytouch">
			<?php echo $this->loadTemplate( 'integrations_mightytouch_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-jomsocial">
			<?php echo $this->loadTemplate( 'integrations_jomsocial_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-aup">
			<?php echo $this->loadTemplate( 'integrations_aup_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-phocapdf">
			<?php echo $this->loadTemplate( 'integrations_phocapdf_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-adsense">
			<?php echo $this->loadTemplate( 'integrations_adsense_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-zemanta">
			<?php echo $this->loadTemplate( 'integrations_zemanta_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-pingomatic">
			<?php echo $this->loadTemplate( 'integrations_pingomatic_' . $this->getTheme() ); ?>
		</div>

		<div class="tab-pane" id="integrations-flickr">
			<?php echo $this->loadTemplate( 'integrations_flickr_' . $this->getTheme() ); ?>
		</div>
	</div>

</div>
