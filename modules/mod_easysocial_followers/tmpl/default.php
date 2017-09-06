<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es" class="mod-es mod-es-followers <?php echo $lib->getSuffix();?>">
	<div class="mod-bd">
		<div class="es-widget">
			<ul class="g-list-inline">
				<?php foreach ($users as $user) { ?>
				<li class="t-lg-mr--md t-lg-mb--md">
					<?php echo $lib->html('avatar.user', $user); ?>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
