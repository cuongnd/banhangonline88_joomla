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
<?php if( $this->my->isSiteAdmin() || $group->isOwner() || $group->isAdmin() ){ ?>
<div class="list-media-options pull-right btn-group">
	<a class="dropdown-toggle_ loginLink btn btn-es btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
		<i class="icon-es-dropdown"></i>
	</a>

	<ul class="dropdown-menu dropdown-menu-user messageDropDown">
		<?php if ($this->my->isSiteAdmin()) { ?>
			<?php if( $featured ){ ?>
			<li>
				<a href="javascript:void(0);" data-groups-item-remove-featured><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_REMOVE_FEATURED' );?></a>
			</li>
			<?php } else { ?>
			<li>
				<a href="javascript:void(0);" data-groups-item-set-featured><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_SET_FEATURED' );?></a>
			</li>
			<?php } ?>
		<?php } ?>
		<li>
			<a href="<?php echo FRoute::groups( array( 'layout' => 'edit' , 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_EDIT_GROUP' );?></a>
		</li>
		<?php if ($this->my->isSiteAdmin()) { ?>
		<li class="divider"></li>
		<li>
			<a href="javascript:void(0);" data-groups-item-unpublish><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISH_GROUP' );?></a>
		</li>
		<li>
			<a href="javascript:void(0);" data-groups-item-delete><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_DELETE_GROUP' );?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>

<div class="media">
	<a class="media-object pull-left" href="<?php echo $group->getPermalink();?>">
		<img src="<?php echo $group->getAvatar( SOCIAL_AVATAR_SQUARE );?>" alt="<?php echo $this->html( 'string.escape' , $group->getName() );?>" />

	</a>

	<div class="media-body">

        <?php if ($featured || $group->isFeatured()) { ?>
        <div class="label label-warning mb-10 media-featured-label">
            <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FEATURED_GROUPS' );?>
        </div>
        <?php } ?>


		<div class="media-name">
			<a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a>
		</div>

		<div class="media-meta mt-5 muted">
			<?php if( $group->isOpen() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
				<i class="ies-earth"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
			</span>
			<?php } ?>

			<?php if( $group->isClosed() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
				<i class="ies-locked"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
			</span>
			<?php } ?>

			<?php if( $group->isInviteOnly() ){ ?>
			<span data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
				<i class="ies-locked"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?>
			</span>
			<?php } ?>

			<span>
				<i class="ies-folder-3"></i>
				<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>">
					<?php echo $group->getCategory()->get( 'title' ); ?>
				</a>
			</span>

			<span>
				<i class="ies-users"></i>
				<?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_MEMBERS' , $group->getTotalMembers() ) , $group->getTotalMembers() ); ?>
			</span>
		</div>

		<?php if( $this->template->get( 'groups_description' , true ) ){ ?>
		<div class="media-brief mv-10">
			<?php if( $group->description ){ ?>
				<?php echo $this->html('string.truncater', $group->getDescription(), 350);?>
			<?php } else { ?>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION_YET' ); ?>
			<?php }?>
		</div>
		<?php } ?>

		<div class="media-meta mt-5 muted">
			<span>
				<i class="ies-user"></i>
				<a href="<?php echo $group->getCreator()->getPermalink();?>">
					<?php echo $group->getCreator()->getName();?>
				</a>
			</span>

			<span>
				<i class="ies-calendar"></i>
				<?php echo $group->getCreatedDate()->format( JText::_( 'DATE_FORMAT_LC' ) ); ?>
			</span>
		</div>

		<?php if ($group->isInvited() && !$group->isMember()){ ?>
		<div class="media-actions mt-10">
			<?php if( $group->isInvited() ){ ?>
			<a class="btn btn-es-success btn-sm mr-5" href="javascript:void(0);" data-groups-item-respond>
				<i class="ies-eye mr-5"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_RESPOND_TO_INVITATION' );?>
			</a>
			<?php }?>

			<?php if( !$group->isMember( $this->my->id ) && !$group->isInvited() ){ ?>
			<a class="btn btn-es-success btn-sm mr-5" href="javascript:void(0);" data-groups-item-join>
				<i class="ies-power mr-5"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP' );?>
			</a>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>
