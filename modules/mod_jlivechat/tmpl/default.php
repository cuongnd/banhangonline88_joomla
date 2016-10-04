<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$onlineImgOverride = $params->get('online_img_override');
$offlineImgOverride = $params->get('offline_img_override');
$popupMode = modJLiveChatHelper::getPopupMode($params->get('popup_mode'));
?>
<div class="jlc-img-wrapper<?php echo $params->get('moduleclass_sfx'); ?>" style="width: auto; text-align: center;">
<?php if(modJLiveChatHelper::isHostedMode()) { ?>
    <a href="javascript:void(0);" onclick="requestLiveChat('<?php echo modJLiveChatHelper::getPopupUri($params->get( 'popup_mode' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>', '<?php echo $popupMode; ?>');"><img src="<?php echo modJLiveChatHelper::getDynamicImageUri($params->get( 'image_size' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>" alt="" border="0" /></a>
<?php } else { ?>
    <?php if(modJLiveChatHelper::isOnline($params->get( 'specific_operators' ), $params->get( 'specific_route_id' ))) { ?>
	<?php if(empty($onlineImgOverride)) { ?>
	<a href="javascript:void(0);" onclick="requestLiveChat('<?php echo modJLiveChatHelper::getPopupUri($params->get( 'popup_mode' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>', '<?php echo $popupMode; ?>');"><img src="<?php echo modJLiveChatHelper::getDynamicImageUri($params->get( 'image_size' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>" alt="" border="0" /></a>
	<?php } else { ?>
	<a href="javascript:void(0);" onclick="requestLiveChat('<?php echo modJLiveChatHelper::getPopupUri($params->get( 'popup_mode' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>', '<?php echo $popupMode; ?>');"><img src="<?php echo $onlineImgOverride; ?>" alt="" border="0" /></a>
	<?php } ?>
    <?php } else { ?>
	<?php if(empty($offlineImgOverride)) { ?>
	<a href="javascript:void(0);" onclick="requestLiveChat('<?php echo modJLiveChatHelper::getPopupUri($params->get( 'popup_mode' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>', '<?php echo $popupMode; ?>');"><img src="<?php echo modJLiveChatHelper::getDynamicImageUri($params->get( 'image_size' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>" alt="" border="0" /></a>
	<?php } else { ?>
	<a href="javascript:void(0);" onclick="requestLiveChat('<?php echo modJLiveChatHelper::getPopupUri($params->get( 'popup_mode' ), $params->get( 'specific_operators' ), $params->get( 'specific_department' ), $params->get( 'specific_route_id' )); ?>', '<?php echo $popupMode; ?>');"><img src="<?php echo $offlineImgOverride; ?>" alt="" border="0" /></a>
	<?php } ?>
    <?php } ?>
<?php } ?>
</div>
