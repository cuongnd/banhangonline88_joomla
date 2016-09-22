<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-activities>
	<div class="es-container">
		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
		</a>
		<div class="es-sidebar" data-activities-sidebar data-sidebar>

			<?php echo $this->render( 'module' , 'es-activities-sidebar-top' ); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'activities' , 'sidebarTop' ); ?>

			<?php echo $this->includeTemplate( 'site/activities/sidebar' ); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'activities' , 'sidebarMiddle' ); ?>

			<?php echo $this->includeTemplate( 'site/activities/sidebar.apps'); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'activities' , 'sidebarBottom' ); ?>

			<?php echo $this->render( 'module' , 'es-activities-sidebar-bottom' ); ?>
		</div>

		<div class="es-content">

			<?php echo $this->render( 'module' , 'es-activities-before-contents' ); ?>

			<?php echo $this->includeTemplate( 'site/activities/content', array( 'filtertype' => $filtertype ) ); ?>

			<?php echo $this->render( 'module' , 'es-activities-after-contents' ); ?>
		</div>
	</div>
</div>
