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
<div>
	<?php foreach ($users as $user) { ?>
	<div class="es-users-item" data-item data-id="<?php echo $user->id;?>" data-return="<?php echo $returnUrl;?>">
	    <div class="o-grid">
	        <div class="o-grid__cell">
	            <div class="o-flag">
	                <div class="o-flag__image">
	                    <?php echo $this->html('avatar.user', $user, 'md', false, true); ?>  
	                </div>

	                <div class="o-flag__body">
	                    <a href="<?php echo $user->getPermalink();?>" class="es-user-name"><?php echo $user->getName();?></a>
	                    <div class="es-user-meta">

	                    	<ol class="g-list-inline g-list-inline--delimited es-user-item-meta">

	                    		<li>
		                    		<?php if ($group->isOwner($user->id)) { ?>
									<span class="o-label o-label--danger-o"><?php echo JText::_('APP_GROUP_MEMBERS_OWNER'); ?></span>
									<?php } ?>

									<?php if ($group->isAdmin($user->id) && !$group->isOwner($user->id)) { ?>
									<span class="o-label o-label--success-o"><?php echo JText::_('APP_GROUP_MEMBERS_ADMIN'); ?></span>
									<?php } ?>

									<?php if ($group->isMember($user->id) && !$group->isAdmin($user->id) && !$group->isOwner($user->id)) { ?>
									<span class="o-label o-label--clean-o"><?php echo JText::_('APP_GROUP_MEMBERS_MEMBER'); ?></span>
									<?php } ?>

									<?php if ($group->isPendingMember($user->id)) { ?>
									<span class="o-label o-label--warning-o label-pending"><?php echo JText::_('APP_GROUP_MEMBERS_PENDING');?></span>
									<?php } ?>

									<?php if ($group->isPendingInvitationApproval($user->id)) { ?>
									<span class="o-label o-label--default-o label-pending-invitation"><?php echo JText::_('APP_GROUP_MEMBERS_INVITED');?></span>
									<?php } ?>
								</li>

								<?php if ($group->isPendingInvitationApproval($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_GROUP_MEMBERS_INVITED_BY', $this->html('html.user', $group->getInvitor($user->id)->id, true), $group->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
								</li>
								<?php } ?>

								<?php if ($group->isMember($user->id) && !$group->isInvited($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_GROUP_MEMBERS_JOINED', $group->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
								</li>
								<?php } ?>

								<?php if ($group->isPendingMember($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_GROUP_MEMBERS_REQUESTED', $group->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
								</li>
								<?php } ?>
							</ol>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <?php if ($this->access->allowed('reports.submit') || $filter == 'list' || $filter == 'all') { ?>
	        <div class="o-grid__cell o-grid__cell--auto-size">
	            <div role="toolbar" class="btn-toolbar">

	                <?php echo $this->html('user.conversation', $user); ?>
	                
	                <?php echo $this->html('user.clusterActions', $user, $group); ?>
	                
	            </div>
	        </div>
	        <?php } ?>
	    </div>

	</div>
	<?php } ?>

	<?php echo $this->html('html.emptyBlock', 'APP_GROUP_MEMBERS_EMPTY', 'fa-users'); ?>
</div>

<?php if ($pagination) { ?>
<div class="es-pagination-footer">
	<?php echo $pagination->getListFooter('site');?>
</div>
<?php } ?>