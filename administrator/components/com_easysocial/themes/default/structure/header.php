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
<div class="es-panel-hd-wrap">
	<div class="es-panel-hd es-panel-hd-title latest" data-es-version-header>
		<strong><?php echo JText::_( 'EasySocial' );?></strong>
		<strong data-es-version>v<?php echo $version;?></strong>

		<span class="outdated">
			&mdash; <a href="javascript:void(0);" class="label label-danger" data-es-outdated><?php echo JText::_( 'COM_EASYSOCIAL_OUTDATED' );?></a>
		</span>
	</div>

	<div class="es-panel-hd es-panel-hd-searchbar">&nbsp;</div>
</div>
