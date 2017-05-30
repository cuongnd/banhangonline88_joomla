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
<div class="es-container" data-readConversation data-id="<?php echo $conversation->id;?>">
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-conversations-read-sidebar-top' ); ?>

		<div class="es-filter conversation-sidebar mt-10">
			<div class="text-center">
				<a href="<?php echo FRoute::conversations();?>" class="btn btn-es btn-sm mb-10">
					<i class="ies-arrow-left-2 ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_BACK_TO_INBOX' );?>
				</a>
			</div>
			<hr />

			<h5>
				<i class="icon-es-friends mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_PARTICIPANTS' );?>
			</h5>
			<hr />

			<p class="fd-small">
				<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_LATEST_REPLY_TIME' );?> <strong><?php echo FD::date( $conversation->lastreplied )->toLapsed();?></strong>.
			</p>
			<ul class="fd-reset-list conversation-participants">
			<?php foreach( $participants as $participant ){ ?>
				<li>
					<div class="row">
						<div class="col-md-12">
							<div class="es-avatar-wrap">

								<a href="<?php echo ( $participant->isBlock() ) ? 'javascript:void(0);' : $participant->getPermalink(); ?>"
									class="es-avatar es-avatar-sm pull-left"
									data-es-provide="tooltip"
									data-original-title="<?php echo $this->html( 'string.escape' , $participant->getName() );?>"
								>
									<img alt="<?php echo $this->html( 'string.escape' , $participant->getName() );?>" src="<?php echo $participant->getAvatar();?>" />
								</a>


								<?php if( !$participant->isBlock() ) { ?>
									<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $participant->isOnline() , 'size' => 'mini' ) ); ?>
								<?php } ?>
							</div>
							<div class="es-username-wrap">
								<div class="es-conversation-username">
									<?php if( !$participant->isBlock() ) { ?>
										<a href="<?php echo $participant->getPermalink();?>"><?php echo $participant->getStreamName();?></a>
									<?php } else { ?>
										<?php echo $participant->getStreamName();?>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</li>
			<?php } ?>
			</ul>

			<?php if( $this->config->get( 'conversations.attachments.enabled' ) ){ ?>
				<?php echo $this->includeTemplate( 'site/conversations/read.sidebar.files' ); ?>
			<?php } ?>
		</div>

		<?php echo $this->render( 'module' , 'es-conversations-read-sidebar-bottom' ); ?>
	</div>

	<div class="es-content">

		<?php echo $this->render( 'module' , 'es-conversations-read-before-contents' ); ?>

		<div class="mt-10">

			<?php if( $conversation->isParticipant( $this->my->id ) ){ ?>
			<div class="pull-right btn-group btn-group-xs">
				<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-es conversation-action">
					<i class="ies-menu ies-small"></i> <b class="caret"></b>
				</a>
				<ul class="dropdown-menu dropdown-conversation-actions">
					<li>
						<?php if( $conversation->isArchived( $this->my->id ) ){ ?>
							<a class="action-unarchive" href="<?php echo FRoute::tokenize( 'index.php?option=com_easysocial&controller=conversations&task=unarchive&id=' . $conversation->id );?>">
								<i class="ies-box-remove ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_UNARCHIVE_BUTTON' );?>
							</a>
						<?php } else { ?>
							<a class="action-archive" href="<?php echo FRoute::tokenize( 'index.php?option=com_easysocial&controller=conversations&task=archive&id=' . $conversation->id );?>">
								<i class="ies-box-add ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_ARCHIVE_BUTTON' );?>
							</a>
						<?php } ?>
					</li>
					<li>
						<a href="<?php echo FRoute::tokenize( 'index.php?option=com_easysocial&controller=conversations&task=markUnread&ids=' . $conversation->id );?>" class="reaction-unread">
							<i class="ies-eye ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_MARK_UNREAD_BUTTON' );?>
						</a>
					</li>

					<?php if( $conversation->isWritable( $this->my->id ) && $this->config->get( 'conversations.multiple' ) && $this->access->allowed( 'conversations.invite' ) ){ ?>
					<li data-readConversation-addParticipant>
						<a href="javascript:void(0);">
							<i class="ies-user-add ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ADD_PARTICIPANTS' );?>
						</a>
					</li>
					<?php } ?>

					<?php if( $conversation->isMultiple() && $this->config->get( 'conversations.multiple' ) ){ ?>
					<li data-readConversation-leaveConversation>
						<a href="javascript:void(0)">
							<i class="ies-exit ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_LEAVE_CONVERSATION' ); ?>
						</a>
					</li>
					<?php } ?>
					<li data-readConversation-delete>
						<a class="delete-item" href="javascript:void(0);">
							<i class="ies-remove-2 ies-small"></i> <?php echo JText::_( 'COM_EASYSOCIAL_DELETE_BUTTON' );?>
						</a>
					</li>
				</ul>
			</div>
			<?php } ?>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php if( $loadPrevious ){ ?>

				<div class="center">

					<i class="loading-indicator fd-small"></i>

					<a  href="javascript:void(0);"
						class="btn btn-xblock btn-link"
						data-readconversation-load-more
						data-id="<?php echo $conversation->id; ?>"
						data-limitstart="<?php echo $pagination->limit; ?>"
					>
						<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_LOAD_PREVIOUS_MESSAGES' ); ?> <i class="ies-arrow-up ies-small"></i>
					</a>
				</div>
				<?php }?>

				<ul class="fd-reset-list conversation-messages" data-conversationMessages data-readConversation-items>

					<?php if( $messages ){ ?>
						<?php
							$curDay = '';
							foreach( $messages as $message ){
								if( $curDay != $message->day )
								{
									$curDay = $message->day;
									$date 	= FD::date( $message->created );

									$dateText = ( $message->day > 0 ) ? $date->toFormat( 'F d Y' ) : JText::_('COM_EASYSOCIAL_CONVERSATIONS_TODAY');

						?>

							<li class="conversation-date">
								<span class="conversation-timestamp"><?php echo $dateText; ?></span>
							</li>
							<?php } ?>

							<?php echo $this->loadTemplate( 'site/conversations/read.item.' . $message->getType() , array( 'message' => $message ) ); ?>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php if( $conversation->isWritable( $this->my->id ) && !$this->access->exceeded('conversations.send.daily', $totalSentDaily)){ ?>
			<div class="mt-10" data-readConversation-composer>
				<form name="conversation-compose" class="form-vertical conversationComposer" enctype="multipart/form-data" method="post">

					<div class="reply-form mb-20">
						<h5>
							<i class="icon-es-chatgroup"></i>
							<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_REPLY_TITLE' );?>
						</h5>
						<hr />
						<div data-readConversation-replyNotice></div>

						<div class="composer-textarea input-wrap" data-composer-editor-header>
							<div class="es-story-textbox mentions-textfield" data-composer-editor-area>
								<div class="mentions">
									<div data-mentions-overlay data-default=""></div>
									<textarea class="input-sm input-shape form-control" name="message" autocomplete="off"
										data-mentions-textarea
										data-default=""
										data-initial="0"
										data-composer-editor
										placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_WRITE_YOUR_MESSAGE_HERE' );?>"></textarea>
								</div>
							</div>
						</div>
					</div>

					<?php if( $this->config->get( 'conversations.attachments.enabled' ) ){ ?>
					<!-- File attachments -->
					<div class="attachment-service" data-readConversation-attachment>
						<?php echo $this->loadTemplate( 'site/uploader/form' , array( 'size' => $this->config->get( 'conversations.attachments.maxsize' ) ) ); ?>
					</div>
					<?php } ?>

					<div class="form-actions">
						<div class="">
							<div class="pull-right">
								<button class="btn btn-es-primary btn-medium" data-readConversation-replyButton><?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' );?></button>
							</div>
						</div>
					</div>

					<input type="hidden" name="option" value="com_easysocial" />
					<input type="hidden" name="controller" value="conversations" />
					<input type="hidden" name="task" value="create" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</div>
		<?php } else { ?>
			<div class="fd-small">
				<?php if ($this->access->exceeded('conversations.send.daily', $totalSentDaily)) { ?>
					<div class="alert alert-warning"><?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_EXCEEDED_DAILY_SEND_LIMIT'); ?></div>
				<?php } else { ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_YOU_CANNOT_REPLY_TO_THIS_CONVERSATION' );?>
				<?php } ?>
			</div>
		<?php } ?>

		<?php echo $this->render( 'module' , 'es-conversations-read-after-contents' ); ?>
	</div>

</div>
