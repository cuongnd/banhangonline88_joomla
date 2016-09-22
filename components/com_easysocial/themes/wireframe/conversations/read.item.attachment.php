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
<?php if( $attachments && $this->config->get( 'conversations.attachments.enabled' ) ){ ?>
<div class="conversation-attachments row">

	<div class="col-md-12" data-conversation-attachment-wrapper>
		<h6><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ATTACHMENTS' ); ?>:</h6>

		<ul class="fd-reset-list">
			<?php foreach( $attachments as $attachment ){ ?>
				<li class="attach-item uploadItem<?php echo $attachment->hasPreview() ? ' preview' : ''; ?>" data-conversation-attachment>
					<div class="">
						<a href="<?php echo $attachment->getPermalink();?>" class="attach-link itemLink"
							data-es-provide="tooltip"
							data-original-title="<b><?php echo $this->html( 'string.escape' , $attachment->name );?></b><br /><br /><?php echo JText::sprintf( 'COM_EASYSOCIAL_CONVERSATIONS_FILE_UPLOADED_ON' , $attachment->getUploadedDate()->toLapsed() );?>"
							data-html="true"
							data-placement="bottom"
						>
							<i class="icon-es-<?php echo $attachment->getIconClass();?> mr-5"></i>
							<?php echo $attachment->name; ?>
						</a>

						<span class="attach-size fd-small">
							- <?php echo $attachment->getSize( 'kb' );?> <?php echo JText::_( 'COM_EASYSOCIAL_UNIT_KILOBYTES' );?>
						</span>

						<?php if( $attachment->isOwner( $this->my->id ) ){ ?>
							<a href="javascript:void(0);" class="pull-right delete-attachment" data-attachment-delete data-id="<?php echo $attachment->id;?>"><i class="ies-cancel-2 ies-small"></i></a>
						<?php } ?>

						<?php if( $attachment->hasPreview() ){ ?>
						<div class="attachment-preview">
							<a href="<?php echo $attachment->getPreviewURI();?>" target="_blank"><img src="<?php echo $attachment->getPreviewURI();?>" /></a>
						</div>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
