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
<li data-search-item
	data-search-item-id="<?php echo $user->id; ?>"
	data-search-custom-name="<?php echo $user->getName();?>"
	data-search-custom-avatar="<?php echo $user->getAvatar();?>"
	data-friend-uid="<?php echo $user->id; ?>"
	>
	<div class="es-item">
		<a class="es-avatar pull-left mr-10" href="<?php echo $user->getPermalink();?>">
			<img src="<?php echo $user->getAvatar();?>" title="<?php echo $this->html('string.escape', $user->getName());?>" />
		</a>
		<div class="es-item-body">
			<div class="pull-right">
				<?php if( $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ){ ?>
				<a href="javascript:void(0);" class="btn btn-clean" data-search-friend-pending-button>
					<i class="icon-es-aircon-checkmark mr-10"></i>
					<span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT' );?></span>
				</a>
				<?php } else if( $user->getFriend( $this->my->id )->state != SOCIAL_FRIENDS_STATE_FRIENDS ) { ?>

					<?php if( FD::privacy( $this->my->id )->validate( 'friends.request' , $user->id ) && !$user->isViewer() ){ ?>

					<a href="javascript:void(0);" class="btn btn-clean"
						data-search-friend-button
					>
						<i class="icon-es-aircon-user mr-10"></i>
						<span><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_ADD_AS_FRIEND' );?></span>
					</a>

					<?php } ?>

				<?php } ?>
			</div>

			<div class="es-item-detail">
				<div class="es-item-title">
					<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
				</div>

				<ul class="fd-reset-list list-inline user-meta">
					<li class="item-friend">
						<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>"> <?php echo FD::get( 'Language', 'COM_EASYSOCIAL_FRIENDS' )->pluralize( $user->getTotalFriends() , true ); ?></a>
					</li>

					<?php if (isset($displayOptions['showGender']) && $displayOptions['showGender']) { ?>
					<li class="item-friend">
						<?php $gender = $user->getFieldValue($displayOptions['GenderCode']); ?>
						<?php if ($gender) { ?>
						<li><?php echo $gender->toDisplay('listing', true); ?></li>
						<?php } ?>
					</li>
					<?php } ?>

					<?php if (isset($displayOptions['showDistance']) && $displayOptions['showDistance']) { ?>
					<?php $address = $user->getFieldValue($displayOptions['AddressCode']); ?>
						<?php if ($address) { ?>
						<?php $displays = array('display' => 'distance', 'lat' => $displayOptions['AddressLat'], 'lon' => $displayOptions['AddressLon']); ?>
						<li class="item-friend"><?php echo $address->toDisplay($displays, true); ?></li>
						<?php } ?>
					<?php } ?>

				</ul>
			</div>
		</div>
	</div>
</li>
