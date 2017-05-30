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
	<i class="icon-es-cloud mr-5"></i> <span><?php echo JText::_( 'COM_EASYSOCIAL_VERSION_HEADER_UP_TO_DATE' );?></span>
</h4>
<hr />
<div>
	<?php echo JText::_( 'COM_EASYSOCIAL_VERSION_LATEST_VERSION_INFO' );?>
</div>
<div class="mt-10">
	<?php echo JText::_( 'COM_EASYSOCIAL_VERSION_INSTALLED_VERSION' );?>: <u><b><?php echo $localVersion;?></b></u>
</div>
