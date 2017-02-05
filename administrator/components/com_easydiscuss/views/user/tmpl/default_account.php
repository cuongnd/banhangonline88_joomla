<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

<?php if( $this->config->get( 'layout_editor') == 'bbcode' ) { ?>
<script type="text/javascript">
EasyDiscuss.require()
	.library('markitup')
	.done(function($){

		var autogrowTimer;

		var autogrow = function(){
			clearTimeout(autogrowTimer);
			if ($.fn.autogrow) {
				$( '#signature' ).autogrow({lineBleed: 1});
			} else {
				autogrowTimer = setTimeout(autogrow, 500);
			}
		}

		$('#signature').markItUp({

			set: "bbcode_easydiscuss",

			onResizeUp: function(height) {

				autogrow({minHeight: height, lineBleed: 1});
			}
		});

		autogrowTimer = setTimeout(autogrow, 500);
	});
</script>
<?php } ?>

<style>
body .key{width:300px !important;}
#discuss-wrapper .markItUp{ width: 715px;}
</style>

<div class="row-fluid ">
	<div class="span12">
		<h3><?php echo JText::_( 'COM_EASYDISCUSS_USER_ACCOUNT' ); ?></h3>
		<hr />
		<table class="table table-striped" width="100%">
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR' ); ?>:
				</td>
				<td>
						<div class="item_content_lite">
							<?php
							if($this->config->get('layout_avatar'))
							{
								if(! $this->avatarIntegration=='default')
								{
									echo JText::sprintf('COM_EASYDISCUSS_INTEGRATED_WITH', $this->avatarIntegration);
								}
							}
							?>
						</div>

						<?php
						if($this->config->get('layout_avatar'))
						{
							$maxSize		= (int) $this->config->get( 'main_upload_maxsize', 0 );
							$maxSizeInMB	= $maxSize / (1000 * 1000);

						?>
						<img style="border-style:solid;" src="<?php echo $this->profile->getAvatar(); ?>" width="64" />
						<div id="avatar-upload-form" style="margin: 20px 0px 10px 0px;">
							<div>
								<input id="file-upload" type="file" name="Filedata" size="65" class=""/>
							</div>
							<div style="clear:both;"></div>
							<div class="alert mt-20">
								<?php echo JText::sprintf( 'COM_EASYDISCUSS_AVATAR_UPLOAD_CONDITION', $maxSizeInMB, $this->config->get( 'layout_avatarwidth' ), $this->config->get( 'layout_avatarheight' ) ); ?>
							</div>
						</div>
						<?php
						}
						else
						{
							echo JText::_('COM_EASYDISCUSS_AVATAR_DISABLE_BY_ADMINISTRATOR');
						}
						?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' ); ?>:
				</td>
				<td>
					<?php echo $this->user->username; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_USER_POINTS' ); ?>:
				</td>
				<td>
					<input type="text" class="" value="<?php echo $this->profile->points; ?>" name="points" style="width:50px;text-align:center;" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_FULL_NAME' ); ?>:
				</td>
				<td>
					<input type="text" class="input-large" value="<?php echo $this->escape( $this->user->name ); ?>" name="fullname" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_NICK_NAME' ); ?>:
				</td>
				<td>
					<input type="text" class="input-large" value="<?php echo $this->escape( $this->profile->nickname ); ?>" name="nickname" style="width:200px" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_NICK_EMAIL' ); ?>:
				</td>
				<td>
					<?php echo $this->user->email; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_SIGNATURE' ); ?>:
				</td>
				<td>
					<textarea name="signature" id="signature" class=""><?php echo $this->profile->getSignature( true ); ?></textarea>
				</td>
			</tr>
		</table>

	</div>

</div>
