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
<div class="install-completed text-center">
	<img class="mt-20 mb-20" src="<?php echo JURI::root();?>administrator/components/com_easysocial/setup/assets/images/completed.png" />

	<p class="section-desc">
		<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_COMPLETED_DESC' );?>
	</p>

	<div class="alert alert-error text-left" style="display: none;" data-delete-error>
		<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_COMPLETED_ERROR' ); ?>
		<br /><br />
		<strong><?php echo JPATH_ROOT;?>/administrator/components/com_easysocial/setup</strong>
	</div>


	<a class="btn btn-es-primary btn-large btn-start mr-5" href="<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easysocial"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_LAUNCH_BACKEND' );?></a>
	<?php echo JText::_( 'COM_EASYSOCIAL_OR' ); ?>
	<a href="<?php echo rtrim( JURI::root() , '/' );?>/index.php?option=com_easysocial" target="_blank" class="ml-5"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_LAUNCH_FRONTEND' );?></a>
</div>
