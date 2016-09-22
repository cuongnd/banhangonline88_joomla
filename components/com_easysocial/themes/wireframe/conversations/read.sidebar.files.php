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
<h5 class="mt-20">
	<i class="icon-es-attachment mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILES' );?>
</h5>
<hr />
<?php if( $files ){ ?>
	<ul class="fd-reset-list files-list">
		<?php foreach( $files as $attachment ){ ?>
		<li>
			<div class="row">
				<div class="col-md-12">
					<a href="<?php echo FRoute::conversations( array( 'layout' => 'download' , 'fileid' => $attachment->id ) );?>"
						data-es-provide="tooltip"
						data-original-title="<b><?php echo $this->html( 'string.escape' , $attachment->name );?></b><br /><br /><?php echo JText::sprintf( 'COM_EASYSOCIAL_CONVERSATIONS_FILE_UPLOADED_ON' , $attachment->getUploadedDate()->toLapsed() );?>"
						data-html="true"
						data-placement="bottom"
					>

						<i class="icon-es-<?php echo $attachment->getIconClass();?> mr-5"></i>

						<?php if( JString::strlen( $attachment->name ) > 15 ){ ?>
							<?php echo JString::substr( $attachment->name , 0 , 15 ); ?><?php echo JText::_( 'COM_EASYSOCIAL_ELLIPSES' ); ?>
						<?php } else { ?>
							<?php echo $attachment->name;?>
						<?php } ?>
					</a>
					<div class="pull-right">
						<span ><?php echo $attachment->getSize();?> <?php echo JText::_( 'COM_EASYSOCIAL_UNIT_KILOBYTES' );?></span>
					</div>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<div class="fd-small">
		<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_NO_FILES_FOUND' ); ?>
	</div>
<?php } ?>
