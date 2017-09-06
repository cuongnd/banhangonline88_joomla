<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$doc = JFactory::getDocument();
JHtml::_('jQuery.help_step');
JHtml::_('jQuery.auo_typing_text');
JHtml::_('jqueryfrontend.notify');
JHtml::_('jqueryfrontend.uisortable');
JHtml::_('jqueryfrontend.serialize_object');
$doc->addScript('/components/com_hikashop/assets/js/view_user_form.min.js');
global $Itemid;
$url_itemid = '';
if (!empty($Itemid)) {
    $url_itemid = '&Itemid=' . $Itemid;
}
$session = JFactory::getSession();
$userClass = hikashop_get('helper.user');
$key_user_dont_show_help = $userClass::KEY_DONT_SHOW_HELP;
$user_dont_show_help = $session->get($key_user_dont_show_help, 1);

?>
    <div class="view-user-form">
        <form action="<?php echo hikashop_completeLink('user&task=register' . $url_itemid); ?>" method="post"
              name="hikashop_registration_form" enctype="multipart/form-data"
              onsubmit="hikashopSubmitForm('hikashop_registration_form'); return false;">
            <div class="hikashop_user_registration_page">
                <div class="list-btn-social">
                    <h2><?php echo JText::_('REGISTER_BY_SOCIAL_ACCOUNT') ?></h2>
                    <?php
                    $fb = JFactory::getFaceBook();
                    $helper = $fb->getRedirectLoginHelper();
                    $permissions = ['public_profile', 'email']; // Optional permissions
                    $task_create_user_by_facebook = JUri::root() . 'index.php?option=com_hikashop&ctrl=user&task=active_user_partner_activated_by_current_user_login_by_facebook';
                    $loginUrl = $helper->getLoginUrl($task_create_user_by_facebook, $permissions);
                    ?>
                    <a target="_self" href="<?php echo $loginUrl ?>" class="btn btn-facebook"><i
                            class="fa fa-facebook"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_FACEBOOK') ?></a>
                    <a target="_self" href="javascript:void(0)" class="get-google-plus-login btn btn-google-plus"><i
                            class="fa fa-google-plus"></i> | <?php echo JText::_('REGISTER_BY_ACOUNT_GOOGLE') ?></a>
                </div>


                <fieldset class="input">
                    <h2><?php echo JText::_('HIKA_REGISTRATION'); ?></h2>
                    <?php
                    $this->setLayout('registration');
                    $this->registration_page = true;
                    $this->form_name = 'hikashop_registration_form';
                    $usersConfig = JComponentHelper::getParams('com_users');
                    $allowRegistration = $usersConfig->get('allowUserRegistration');
                    if ($allowRegistration || $this->simplified_registration == 2) {
                        echo $this->loadTemplate();
                    } else {
                        echo JText::_('REGISTRATION_NOT_ALLOWED');
                    }
                    ?>
                </fieldset>
            </div>
        </form>
    </div>
<?php
$list_messenger = array();
$key = "HIKA_NAME_REQUIRED";
$list_messenger[$key] = JText::_($key);

$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("body.site.com_hikashop.view-user.layout-form.task-form").view_user_form({
                list_messenger:<?php echo json_encode($list_messenger) ?>,
                user_dont_show_help:<?php echo (int)$user_dont_show_help ?>,
                key_user_dont_show_help: "<?php echo $key_user_dont_show_help ?>"
            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>