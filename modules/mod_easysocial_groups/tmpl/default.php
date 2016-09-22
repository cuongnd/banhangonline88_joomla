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
<div id="fd" class="es mod-es-groups module-register<?php echo $suffix;?> es-responsive">

	<ul class="es-groups-list fd-reset-list">
		<?php foreach( $groups as $group ){ ?>
		<li>
			<?php if( $params->get( 'display_avatar' , true ) ){ ?>
			<div class="es-group-avatar">
					<img class="es-avatar" src="<?php echo $group->getAvatar();?>" alt="<?php echo $modules->html( 'string.escape' , $group->getName() );?>" />
			</div>
			<?php } ?>
			<div class="es-group-object">
				<a href="<?php echo $group->getPermalink();?>" class="group-title"><?php echo $group->getName();?></a>
			</div>

			<div class="es-group-meta">
				<?php if( $params->get( 'display_category' , true ) ){ ?>
				<span>
					<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>" alt="<?php echo $modules->html( 'string.escape' , $group->getCategory()->get( 'title' ) );?>" class="group-category">
						<i class="ies-database"></i> <?php echo $modules->html( 'string.escape' , $group->getCategory()->get( 'title' ) );?>
					</a>
				</span>
				<?php } ?>

				<?php if($params->get('display_member_counter', true)){ ?>
				<span class="hit-counter">
					<i class="ies-users"></i> <?php echo JText::sprintf( FD::string()->computeNoun('MOD_EASYSOCIAL_GROUPS_MEMBERS_COUNT' , $group->getTotalMembers() ) , $group->getTotalMembers() ); ?>
				</span>
				<?php } ?>
			</div>
		</li>
		<?php } ?>
	</ul>

</div>
