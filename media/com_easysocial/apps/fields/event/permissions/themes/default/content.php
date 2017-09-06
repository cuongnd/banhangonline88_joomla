<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
// dump($profiles);
?>
<div>
	<div class="o-checkbox">
		<input type="checkbox" id="stream-permissions-admin" value="admin" name="stream_permissions[]" <?php if (!empty($value) && in_array('admin', $value)) { ?>checked="checked"<?php } ?> />
		<label for="stream-permissions-admin"><?php echo JText::_('COM_EASYSOCIAL_APP_EVENT_PERMISSIONS_ADMINS'); ?></label>
	</div>
	
	<div class="o-checkbox">
		<input type="checkbox" id="stream-permissions-everyone" value="member" name="stream_permissions[]" data-es-stream-permission-member <?php if (!empty($value) && in_array('member', $value)) { ?>checked="checked"<?php } ?> />
		<label for="stream-permissions-everyone"><?php echo JText::_('COM_EASYSOCIAL_APP_EVENT_PERMISSIONS_MEMBERS'); ?></label>
	</div>

	<div data-es-stream-permission-profile <?php echo !empty($value) && in_array('member', $value) ? '' : 'hidden'; ?>>
		<div class="o-form-group">
			<div class="o-select-group o-select-group--inline">
				<select name="permission_type[]" class="o-form-control" data-member-type>
					<option value="all" <?php echo !empty($type) && in_array('all', $type) ? 'selected="selected"' : ''; ?>><?php echo JText::_('All'); ?></option>
					<option value="selected" <?php echo !empty($type) && in_array('selected', $type) ? 'selected="selected"' : ''; ?>><?php echo JText::_('Selected'); ?></option>
				</select>
				<label for="" class="o-select-group__drop"></label>
			</div>
		</div>

		<div class="o-form-group <?php echo !empty($type) && in_array('selected', $type) ? '' : 't-hidden'; ?>" data-es-stream-profile-type>
			<select name="permission_profiles[]" multiple class="o-form-control">
				<?php foreach ($profiles as $profile) { ?>
					<option value="<?php echo $profile->id ?>" <?php echo !empty($profileType) && in_array($profile->id, $profileType) ? 'selected="selected"' : ''; ?>><?php echo $profile->title; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>