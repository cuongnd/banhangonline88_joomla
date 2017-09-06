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
<div class="es-side-widget">
	<?php echo $this->html('widget.title', 'APP_BIRTHDAYS_TITLE_UPCOMING_BIRTHDAYS'); ?>

	<div class="es-side-widget__bd">
		<div class="t-fs--sm">
			<?php if ($today) { ?>
			<div>
				<span class="label label-info"><?php echo JText::_('APP_BIRTHDAYS_TODAY'); ?></span><br />
				<div class="o-flag-list">
					<?php foreach ($today as $item) { ?>
						<?php echo $this->loadTemplate('themes:/apps/user/birthday/widgets/item', array('item' => $item)); ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($otherDays) { ?>
			<div class="t-lg-mt--md">
				<span class="t-fs--sm"><b><?php echo JText::sprintf( 'APP_BIRTHDAYS_NEXT_OTHER_DAYS', '7');?></b></span>
				<br />
				<div class="o-flag-list">
					<?php foreach ($otherDays as $item) { ?>
						<?php echo $this->loadTemplate('themes:/apps/user/birthday/widgets/item', array('item' => $item)); ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if( empty( $ids ) ){ ?>
			<div class="mt-5">
				<span><?php echo JText::_( 'APP_BIRTHDAYS_NO_BIRTHDAY');?></span>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
