<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

if( ( !$system->config->get( 'attachment_questions' ) || !$acl->allowed('add_attachment', '0' ) ) && !DiscussHelper::isSiteAdmin() )
{
	return;
}

$attachments 	= false;

if ( $post )
{
	$attachments 	= $post->getAttachments();
}

$app 	= JFactory::getApplication();
?>

<script type="text/javascript">
EasyDiscuss
	.require()
	.script('attachments')
	.done(function($){

		EasyDiscuss.module("<?php echo $composer->id; ?>")
			.done(function(){
				$('#attachmentsTab-<?php echo $composer->id; ?>').implement(EasyDiscuss.Controller.Attachments);
			});
	});
</script>

<div class="tab-pane discussAttachments" id="attachmentsTab-<?php echo $composer->id; ?>">
	<div class="field-attachment discuss-attachments-upload">

		<ul class="upload-queue attach-list for-file unstyled uploadQueue">

		<?php if( isset( $attachments ) && !empty( $attachments ) ){ ?>

			<?php for($i = 0; $i < count( $attachments ); $i++ ){ ?>
				<li class="attachmentItem attachments-<?php echo $attachments[ $i ]->getType(); ?>">
					<i class="icon"></i>
					<span><?php echo $attachments[ $i ]->title;?></span>
					<?php if( $attachments[ $i ]->deleteable() && !$app->isAdmin() ){ ?>
					 - <a class="removeItem" href="javascript:void(0);" data-id="<?php echo $attachments[ $i ]->id; ?>"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' );?></a>
					<?php } ?>
				</li>
			<?php } ?>

		<?php } ?>

		</ul>

		<div class="attach-input">
			<input type="file" name="filedata[]" class="fileInput" />
		</div>
	</div>
</div>
