<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('form.popdown', 'page_notification', $value ? $value : 1, array(
        $this->html('form.popdownOption', 1, 'PLG_FIELDS_PAGE_NOTIFICATION_BOTH', 'PLG_FIELDS_PAGE_NOTIFICATION_BOTH_DESC', 'fa-globe'),
        $this->html('form.popdownOption', 2, 'PLG_FIELDS_PAGE_NOTIFICATION_EMAIL', 'PLG_FIELDS_PAGE_NOTIFICATION_EMAIL_DESC', 'fa-user'),
        $this->html('form.popdownOption', 3, 'PLG_FIELDS_PAGE_NOTIFICATION_INTERNAL', 'PLG_FIELDS_PAGE_NOTIFICATION_INTERNAL_DESC', 'fa-lock'),
        $this->html('form.popdownOption', 4, 'PLG_FIELDS_PAGE_NOTIFICATION_NONE', 'PLG_FIELDS_PAGE_NOTIFICATION_NONE_DESC', 'fa-lock')
    )); ?>

