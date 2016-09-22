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
<?php if( $toolbar ){ ?>

	<?php if( $this->template->get( 'toolbar_notification' , true ) ){ ?>
	<div class="navbar es-toolbar wide<?php echo ( ( !$this->my->id && ( $login ) ) || ( $this->my->id && ( $profile ) ) ) ? ' has-sides' : '';?>" data-notifications>

		<?php if( ( !$this->my->id && ( $login ) ) || ( $this->my->id && ( $profile ) ) ){ ?>
		<div class="es-toolbar-avatar">
			<ul class="fd-nav">

				<?php if( !$this->my->id && ( $login ) ){ ?>
				<li class="dropdown_">
					<?php echo $this->includeTemplate( 'site/toolbar/default.login' , array( 'facebook' => $facebook )); ?>
				</li>
				<?php } ?>

				<?php if( $this->my->id && ( $profile ) ){ ?>
					<?php echo $this->includeTemplate( 'site/toolbar/default.profile' ); ?>
				<?php } ?>

			</ul>
		</div>
		<?php } ?>


		<div class="navbar-inner">
			<div class="nav-collapse collapse">
				<?php if( $this->my->id ){ ?>
				<ul class="fd-nav">

					<?php if( $friends ){ ?>
						<?php echo $this->loadTemplate( 'site/toolbar/default.friends' , array( 'requests' => $newRequests ) ); ?>
					<?php } ?>

					<?php if( $conversations ){ ?>
						<?php echo $this->loadTemplate( 'site/toolbar/default.conversations' , array( 'newConversations' => $newConversations ) ); ?>
					<?php } ?>

					<?php if( $notifications ){ ?>
						<?php echo $this->loadTemplate( 'site/toolbar/default.notifications' , array( 'newNotifications' => $newNotifications ) ); ?>
					<?php } ?>

				</ul>
				<?php } ?>

				<?php if( $search ){ ?>
				<div class="fd-navbar-search" data-nav-search>
					<form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
						<i class="ies-search"></i>
						<input type="text" name="q" class="search-query" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_SEARCH' , true );?>" />

						<?php echo $this->html( 'form.itemid' ); ?>
						<input type="hidden" name="view" value="search" />
						<input type="hidden" name="option" value="com_easysocial" />
					</form>
				</div>
				<?php } ?>



			</div>

		</div>
	</div>
	<?php } ?>

    <div
        class="es-mainnav-wrap"
    >
        <a href="javascript:void(0);" class="btn btn-es btn-mainnav-toggle"
            data-popbox=""
            data-popbox-id="fd"
            data-popbox-component="es"
            data-popbox-type="frosty-mainnav"
            data-popbox-toggle="click"
            data-popbox-position="bottom"
            data-popbox-target=".frosty-mainnav"
        >
            <i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_TOGGLE_SUBMENU');?>
        </a>




    	<?php if ($this->my->id) { ?>
        <div class="frosty-mainnav" data-popbox-content>
    	<ul class="fd-nav es-mainnav fd-cf">
    		<?php if( $dashboard ){ ?>
    		<li class="<?php echo $view == 'dashboard' ? 'active' : '';?>">
    			<a data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_DASHBOARD' , true );?>"
    				data-placement="top"
    				data-es-provide="tooltip"
    				href="<?php echo FRoute::dashboard();?>"
    			>
    				<i class="ies-home"></i>
    			</a>
    		</li>
    		<?php } ?>

    		<li class="<?php echo $view == 'profile' && !$userId ? 'active' : '';?>">
    			<a href="<?php echo FRoute::profile();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_VIEW_YOUR_PROFILE' );?>
    			</a>
    		</li>
    		<li class="<?php echo $view == 'friends' && $layout != 'invite' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::friends();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_FRIENDS' );?>
    			</a>
    		</li>

    		<?php if ($this->config->get('friends.invites.enabled')) { ?>
    		<li class="<?php echo $view == 'friends' && $layout == 'invite' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::friends(array('layout' => 'invite'));?>">
    				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'followers.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'followers' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::followers();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_FOLLOWERS' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'photos.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'albums' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::albums( array( 'uid' => $this->my->getAlias() , 'type' => SOCIAL_TYPE_USER ) );?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'groups.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'groups' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::groups();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'events.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'events' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::events();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'badges.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'badges' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' ) );?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<?php if( $this->config->get( 'points.enabled' ) ){ ?>
    		<li class="<?php echo $view == 'points' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::points( array( 'layout' => 'history' , 'userid' => $this->my->getAlias() ) );?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY' );?>
    			</a>
    		</li>
    		<?php } ?>

    		<li class="<?php echo $view == 'apps' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::apps();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_APPS' );?>
    			</a>
    		</li>
    		<li class="<?php echo $view == 'activities' ? 'active' : '';?>">
    			<a href="<?php echo FRoute::activities();?>">
    				<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES' );?>
    			</a>
    		</li>
    	</ul>
        </div>
    	<?php } ?>
    </div>
<?php } ?>
