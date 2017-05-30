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
<div class="app-members app-groups" data-group-members data-id="<?php echo $group->id;?>">

	<div class="es-filterbar">
		<div class="h5 pull-left filterbar-title"><?php echo JText::_( 'APP_GROUP_MEMBERS_SUBTITLE' ); ?></div>
	</div>

	<div class="app-contents-wrap">
		<ul class="fd-nav es-filter-nav">
			<li>
				<a class="active" href="javascript:void(0);" data-group-members-filter data-filter="all"><?php echo JText::_( 'APP_GROUP_MEMBERS_FILTER_MEMBERS' );?></a>
			</li>
			<li>
				<a href="javascript:void(0);" data-group-members-filter data-filter="admin"><?php echo JText::_( 'APP_GROUP_MEMBERS_FILTER_ADMINS' );?></a>
			</li>
			<?php if( $group->isClosed() ){ ?>
			<li>
				<a href="javascript:void(0);" data-group-members-filter data-filter="pending"><?php echo JText::_( 'APP_GROUP_MEMBERS_FILTER_PENDING' );?></a>
			</li>
			<?php } ?>
		</ul>

		<div class="app-members-content app-contents" data-group-members-content>
			<?php echo $this->includeTemplate( 'apps/group/members/groups/default.list' ); ?>
		</div>
	</div>
</div>
