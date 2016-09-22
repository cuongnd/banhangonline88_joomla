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
$esconfig = FD::config();
?>
<div class="fd-small">
	<div class="checkbox">
		<label style="color:#000;">
			<input type="checkbox" value="1" name="privacyReset" style="float:left;margin-left:-20px;"/> <?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_RESET_USER_DESCRIPTION' ); ?>
		</label>
	</div>
</div>

<div class="form-privacy" data-edit-privacy>
<?php foreach( $privacy as $group => $items ){ ?>

	<?php

		// var_dump($group);
		// photos / albums
		if (($group == 'albums' || $group == 'photos') && !$esconfig->get('photos.enabled')) {
			continue;
		}

		// badges / achievements
		if (($group == 'achievements') && !$esconfig->get('badges.enabled')) {
			continue;
		}

		// followers
		if (($group == 'followers') && !$esconfig->get('followers.enabled')) {
			continue;
		}
	?>

	<h3><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper( $group ) ); ?></h3>
	<hr />

	<table class="table table-striped table-noborder">
		<tbody>
			<?php foreach( $items as $item ){

				// profile - conversation
				if (($group == 'profiles' && $item->rule == 'post.message') && !$esconfig->get('conversations.enabled')) {
					continue;
				}

				$hasCustom = ( $item->custom ) ? true : false;
				$customIds = '';
				$curValue  = '';
			?>
			<tr>
				<td width="30%">
					<label class="control-label"><?php echo $item->label; ?></label>
				</td>

				<td>
					<div class="form-group mv-5" data-privacy-item>
						<div class="col-md-1">
							<i class="icon-es-help pull-left mr-10" <?php echo $this->html( 'bootstrap.popover' , $item->label , $item->tips , 'bottom' ); ?>></i>
						</div>
						<div class="col-md-10">
							<div class="">
								<select autocomplete="off" class="form-control input-sm privacySelection" name="privacy[<?php echo $item->groupKey;?>][<?php echo $item->rule;?>]" data-privacy-select>
									<?php foreach( $item->options as $option => $value ){
										if( $value )
										{
											$curValue = $option;
										}

										if( $this->config->get( 'general.site.lockdown.enabled' ) && $option == SOCIAL_PRIVACY_0 )
										{
											continue;
										}
									?>
										<option value="<?php echo $option;?>"<?php echo $value ? ' selected="selected"' : '';?>>
											<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper( $option ) );?>
										</option>
									<?php } ?>
								</select>

								<a <?php if( !$hasCustom ) { ?>style="display: none;"<?php } ?> href="javascript:void(0);" data-privacy-custom-edit-button>
									<i class="icon-es-settings"></i>
								</a>

								<div data-privacy-custom-form
									class="dropdown-menu dropdown-arrow-topleft privacy-custom-menu"
									style="display:none;"
								>
									<div class="small mb-10 row-fluid">
										<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_NAME'); ?>
										<a href="javascript:void(0);" class="pull-right" data-privacy-custom-hide-button>
											<i class="ies-cancel-2 ies-small" title="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_HIDE' , true );?>"></i>
										</a>
									</div>
									<div class="textboxlist" data-textfield >

										<?php
											if( $hasCustom )
											{
												foreach( $item->custom as $friend )
												{
													if( $customIds )
													{
														$customIds = $customIds . ',' . $friend->user_id;
													}
													else
													{
														$customIds = $friend->user_id;
													}

													$friend = FD::user( $friend->user_id );
										?>
											<div class="textboxlist-item" data-id="<?php echo $friend->id; ?>" data-title="<?php echo $friend->getName(); ?>" data-textboxlist-item>
												<span class="textboxlist-itemContent" data-textboxlist-itemContent><?php echo $friend->getName(); ?><input type="hidden" name="items" value="<?php echo $friend->id; ?>" /></span>
												<a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton></a>
											</div>
										<?php
												}

											}
										?>

										<input type="text" class="textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_ENTER_NAME'); ?>" autocomplete="off" />
									</div>
								</div>

								<input type="hidden" name="privacyID[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo $item->id . '_' . $item->mapid;?>" />
								<input type="hidden" name="privacyOld[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo $curValue; ?>" />
								<input type="hidden" data-hidden-custom name="privacyCustom[<?php echo $item->groupKey;?>][<?php echo $item->rule; ?>]" value="<?php echo $customIds; ?>" />
							</div>
						</div>


					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

<?php } ?>

</div>
