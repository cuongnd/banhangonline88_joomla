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
		                    		<?php if ($page->isOwner($user->id)) { ?>
									<span class="o-label o-label--danger-o"><?php echo JText::_('APP_PAGE_FOLLOWERS_OWNER'); ?></span>
									<?php } ?>

									<?php if ($page->isAdmin($user->id) && !$page->isOwner($user->id)) { ?>
									<span class="o-label o-label--success-o"><?php echo JText::_('APP_PAGE_FOLLOWERS_ADMIN'); ?></span>
									<?php } ?>

									<?php if ($page->isMember($user->id) && !$page->isAdmin($user->id) && !$page->isOwner($user->id)) { ?>
									<span class="o-label o-label--clean-o"><?php echo JText::_('APP_PAGE_FOLLOWERS_FOLLOWER'); ?></span>
									<?php } ?>

									<?php if ($page->isPendingMember($user->id)) { ?>
									<span class="o-label o-label--warning-o label-pending"><?php echo JText::_('APP_PAGE_FOLLOWERS_PENDING');?></span>
									<?php } ?>

									<?php if ($page->isPendingInvitationApproval($user->id)) { ?>
									<span class="o-label o-label--default-o label-pending-invitation"><?php echo JText::_('APP_PAGE_FOLLOWERS_INVITED');?></span>
									<?php } ?>
								</li>

								<?php if ($page->isPendingInvitationApproval($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_PAGE_FOLLOWERS_INVITED_BY', $this->html('html.user', $page->getInvitor($user->id)->id, true), $page->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
								</li>
								<?php } ?>

								<?php if ($page->isMember($user->id) && !$page->isInvited($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_PAGE_FOLLOWERS_LIKED', $page->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
								</li>
								<?php } ?>

								<?php if ($page->isPendingMember($user->id)) { ?>
								<li class="t-text--muted">
									<?php echo JText::sprintf('APP_PAGE_FOLLOWERS_REQUESTED', $page->getJoinedDate($user->id, SOCIAL_TYPE_USER, true)); ?>
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
	                
	                <?php echo $this->html('user.clusterActions', $user, $page); ?>
	                
	            </div>
	        </div>
	        <?php } ?>
	    </div>

	</div>
	<?php } ?>

	<?php echo $this->html('html.emptyBlock', 'APP_PAGE_FOLLOWERS_EMPTY', 'fa-users', true); ?>
</div>

<?php if ($pagination) { ?>
<div class="es-pagination-footer">
	<?php echo $pagination->getListFooter('site');?>
</div>
<?php } ?>