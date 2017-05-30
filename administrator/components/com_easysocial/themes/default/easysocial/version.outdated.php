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
<h4>
	<i class="icon-es-lifebuoy mr-5"></i> <span><?php echo JText::_( 'COM_EASYSOCIAL_VERSION_HEADER_UPDATE_REQUIRED' );?></span>
</h4>
<hr />
<p class="fd-small">
	<?php echo JText::_( 'COM_EASYSOCIAL_VERSION_OUTDATED_VERSION_INFO' );?>
</p>

<table class="table table-striped">
	<tr>
		<td>
			<div class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_VERSION_INSTALLED_VERSION' );?></div>
		</td>
		<td>
			<div class="fd-small"><?php echo $localVersion;?></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_VERSION_LATEST_VERSION' );?></div>
		</td>
		<td>
			<div class="fd-small"><?php echo $onlineVersion;?></div>
		</td>
	</tr>
</table>

<div class="mt-20 center">
	<a href="index.php?option=com_easysocial&update=true" class="btn btn-es-success btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_GET_UPDATES_BUTTON' );?></a>
</div>
