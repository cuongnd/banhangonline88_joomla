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

$idArray = array();

if (isset($id) && $id) {
	$idArray['id'] = $id;
}

?>
<?php if (isset($searchFilter) && $searchFilter) { ?>
<div class="user-profile">
	<div class="media">
		<div class="media-body">
			<h4><?php echo $this->html( 'string.escape' , $searchFilter->title ); ?></h4>
		</div>
	</div>
</div>
<?php } ?>


<?php if (isset($activeProfile) && $activeProfile) { ?>
<div class="user-profile">
	<div class="media">
		<div class="media-object pull-left">
			<img src="<?php echo $activeProfile->getAvatar();?>" class="es-avatar" title="<?php echo $this->html('string.escape', $activeProfile->get('title'));?>" />
		</div>

		<div class="media-body">
			<h4><?php echo $activeProfile->get('title'); ?></h4>
		</div>
	</div>

	<p class="fd-small">
		<?php echo $activeProfile->get('description'); ?>
	</p>

	<div class="mt-15">
		<a href="<?php echo $activeProfile->getPermalink();?>" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_USERS_VIEW_PROFILE_TYPE'); ?> &rarr;</a>
	</div>
</div>
<hr />
<?php } ?>

<?php if( !$isSort ){ ?>
<div class="row mb-10">
	<div class="col-md-12">
		<div data-apps-sorting="" class="btn-group btn-group-sm btn-group-view-apps pull-right">
			<a href="<?php echo FRoute::users( array_merge( array( 'filter' => $filter , 'sort' => 'latest' ), $idArray) );?>"
				data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_USERS_SORT_LATEST' );?>"
				data-placement="bottom"
				data-es-provide="tooltip"
				data-users-sort
				data-type="latest"
				class="btn btn-es<?php echo $sort == 'latest' ? ' active' : '';?>">
				<i class="ies-fire ies-small"></i>
			</a>
			<a href="<?php echo FRoute::users( array_merge( array( 'filter' => $filter , 'sort' => 'alphabetical' ), $idArray) );?>"
				data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_USERS_SORT_ALPHABETICAL' );?>"
				data-placement="bottom"
				data-es-provide="tooltip"
				data-users-sort
				data-type="alphabetical"
				data-apps-sort=""
				class="btn btn-es trending<?php echo $sort == 'alphabetical' ? ' active' : '';?>">
				<i class="ies-bars ies-small"></i>
			</a>
		</div>
	</div>
</div>
<?php } ?>

<div class="users-wrapper<?php echo !$users ? ' is-empty' : '';?>">
	<ul class="es-item-grid es-item-grid_1col" data-users-listing>
		<?php foreach ($users as $user) { ?>
			<?php echo $this->render( 'module' , 'es-users-between-user' ); ?>
			<li data-users-item
			data-id="<?php echo $user->id;?>"
			>
				<div class="es-item">
					<div class="es-avatar-wrap pull-left">
						<a href="<?php echo $user->getPermalink();?>" class="es-avatar pull-left">
							<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_MEDIUM );?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
						</a>

						<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'small' ) ); ?>
					</div>

					<div class="es-item-body">

						<?php if( ($this->access->allowed( 'reports.submit' ) && $this->config->get( 'reports.enabled' ) ) || (FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) && $this->config->get( 'conversations.enabled' ) ) ){ ?>
						<div class="pull-right btn-group">
							<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-es btn-dropdown">
								<i class="icon-es-dropdown"></i>
							</a>
							<ul class="dropdown-menu dropdown-menu-user messageDropDown">

								<?php if( $this->access->allowed( 'reports.submit' ) && $this->config->get( 'reports.enabled' ) ){ ?>
								<li>
									<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true ) ); ?>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>

						<div class="es-item-detail">
							<div class="es-item-title">
								<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
							</div>

							<ul class="fd-reset-list list-inline user-meta">
								<li>
									<a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
										<i class="ies-users-2 ies-small"></i>

										<?php if( $user->getTotalFriends() ){ ?>
											<?php echo $user->getTotalFriends();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FRIENDS' , $user->getTotalFriends() ) ); ?>
										<?php } else { ?>
											<?php echo JText::_( 'COM_EASYSOCIAL_NO_FRIENDS_YET' ); ?>
										<?php } ?>
									</a>
								</li>

								<?php if( $this->config->get( 'followers.enabled' ) ) { ?>
								<li>
									<a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="fd-small muted">
										<i class="ies-tree-view ies-small"></i>
										<?php if( $user->getTotalFollowers() ){ ?>
											<?php echo $user->getTotalFollowers();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
										<?php } else { ?>
											<?php echo JText::_( 'COM_EASYSOCIAL_NO_FOLLOWERS_YET' ); ?>
										<?php } ?>
									</a>
								</li>
								<?php } ?>

								<?php if( $this->config->get('badges.enabled' ) ){ ?>
								<li>
									<a href="<?php echo FRoute::badges( array( 'userid' => $user->getAlias() , 'layout' => 'achievements') );?>" class="fd-small muted">
										<i class="ies-crown ies-small"></i>
										<?php if( $user->getTotalbadges() ){ ?>
											<?php echo $user->getTotalbadges();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_BADGES' , $user->getTotalbadges() ) ); ?>
										<?php } else { ?>
											<?php echo JText::_( 'COM_EASYSOCIAL_NO_BADGES_YET' ); ?>
										<?php } ?>
									</a>
								</li>
								<?php } ?>

								<?php
									$gender = $user->getFieldValue('GENDER');
									if ($gender) {
								?>
								<li><?php echo $gender->toDisplay('listing', true); ?></li>
								<?php } ?>
							</ul>

							<div class="users-actions">
								<?php if( $this->config->get( 'followers.enabled' ) ) { ?>
								<span class="mr-5">
									<?php if( $user->isFollowed( $this->my->id ) ){ ?>
										<?php echo $this->loadTemplate( 'site/users/button.following' ); ?>
									<?php } else { ?>
										<?php echo $this->loadTemplate( 'site/users/button.follow' , array( 'user' => $user ) ); ?>
									<?php } ?>
								</span>
								<?php } ?>

								<span>
								<?php if( $user->isFriends( $this->my->id ) ){ ?>
									<?php echo $this->loadTemplate( 'site/users/button.friends' ); ?>
								<?php } else { ?>
									<?php if( $user->getFriend( $this->my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ){ ?>
										<?php echo $this->loadTemplate( 'site/users/button.pending' ); ?>
									<?php } else { ?>
										<?php echo $this->loadTemplate( 'site/users/button.add' , array( 'user' => $user ) ); ?>
									<?php } ?>
								<?php } ?>
								</span>

								<?php if( $this->config->get('conversations.enabled') && FD::privacy( $this->my->id )->validate( 'profiles.post.message' , $user->id ) && $this->access->allowed( 'conversations.create' ) ){ ?>
								<span>
									<a href="javascript:void(0);"
										class="btn btn-es btn-sm"
										data-es-conversations-compose
										data-es-conversations-id="<?php echo $user->id;?>"><i class="ies-mail-5 ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_PROFILE_SEND_MESSAGE'); ?></a>
								</span>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</li>
		<?php } ?>
	</ul>

	<div class="empty empty-hero">
		<i class="ies-users"></i>
		<?php echo JText::_('COM_EASYSOCIAL_USERS_NO_USERS_HERE'); ?>
	</div>
</div>
