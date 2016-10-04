<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<section class="discuss-post-viewers">
	<hr>
	<p>
		<b><?php echo JText::_( 'COM_EASYDISCUSS_VIEWERS_ON_PAGE' );?></b>
	</p>
	<div>
		<?php if(!empty($users)) { ?>
		<?php foreach( $users as $user ){ ?>
			<a href="<?php echo $user->getLink();?>" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo $this->escape( $user->getName() );?>" class="discuss-avatar">
				<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<img class="avatar" width="50" height="50" src="<?php echo $user->getAvatar();?>" alt="<?php echo $this->escape( $user->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $user->id );?> />
				<?php } else { ?>
					<?php echo $this->escape( $user->getName() );?>
				<?php } ?>
			</a>
		<?php } ?>
		<?php } ?>
	</div>
</section>
