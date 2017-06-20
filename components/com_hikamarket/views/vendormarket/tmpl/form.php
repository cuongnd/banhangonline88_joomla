<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
// Include main engine
$doc = JFactory::getDocument();
JHtml::_('jQuery.help_step');
JHtml::_('jQuery.auo_typing_text');
JHtml::_('jqueryfrontend.uisortable');
JHtml::_('jqueryfrontend.serialize_object');


$doc->addScript('/components/com_hikamarket/assets/js/view_vendormarket_form.js');
$doc->addLessStyleSheet('/components/com_hikamarket/assets/less/view_vendormarket_form.less');
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
jimport('joomla.filesystem.file');
if (!JFile::exists($file)) {
    return;
}
require_once($file);
$language = JFactory::getLanguage();
$language->load('com_easysocial');
$language->load('mod_easysocial_login');
$modules = FD::modules('mod_easysocial_login');
// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
// We need these packages
$modules->addDependency('css', 'javascript');

// Include the engine file.
$session=JFactory::getSession();
$vendorClass = hikamarket::get('helper.vendor');
$key_user_dont_show_help=$vendorClass::KEY_DONT_SHOW_HELP;
$user_dont_show_help=$session->get($key_user_dont_show_help,1);
$facebook = FD::oauth('Facebook');
?><?php
if (empty($this->form_type))
    $this->form_type = 'vendor';
?>
    <div class="view-form-vendor">
        <div class="row">
            <div class="col-lg-12">
                <div class="pull-right">
                    <a class="btn btn-link help"><i class="glyphicon glyphicon-question-sign"></i></a>
                </div>
            </div>
        </div>
        <?php
        if ($this->form_type == 'vendorregister') {
            ?>
            <form id="hikamarket_registration_form" name="hikamarket_registration_form" method="post"
                  action="<?php echo hikamarket::completeLink('vendor&task=register' . $this->url_itemid); ?>"
                  enctype="multipart/form-data" class="form-horizontal"
                  onsubmit="if(window.localPage && window.localPage.checkForm){ return window.localPage.checkForm(this); }">
                <div class="center es-signin-social">
                    <p class="line">
                        <strong><?php echo JText::_('MOD_EASYSOCIAL_LOGIN_SIGN_IN_WITH_SOCIAL_IDENTITY'); ?></strong>
                    </p>
                    <?php echo $facebook->getLoginButton(FRoute::registration(array('layout' => 'oauthDialog', 'client' => 'facebook', 'external' => true), false)); ?>
                </div>

                <div class="hikamarket_vendor_registration_page">
                    <h1><?php echo JText::_('HIKA_VENDOR_REGISTRATION'); ?></h1>
                    <?php
                    $this->setLayout('registration');
                    echo $this->loadTemplate();
                    ?>
                    <input type="hidden" name="task" value="register"/>
                    <input type="hidden" name="ctrl" value="vendor"/>
                    <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
                </div>
            </form>
            <?php
        } else {
            ?>
            <form id="hikamarket_vendor_form" name="hikamarket_vendor_form" method="post"
                  action="<?php echo hikamarket::completeLink('vendor&task=form' . $this->url_itemid); ?>"
                  enctype="multipart/form-data" class="form-horizontal">
                <div class="hikamarket_vendor_edit_page">
                    <h1><?php echo JText::_('HIKAM_VENDOR_EDIT'); ?></h1>
                    <?php
                    if (hikamarket::acl('vendor/edit')) {
                        $this->setLayout('registration');
                        echo $this->loadTemplate();
                    }

                    if (hikamarket::acl('vendor/edit/users')) {
                        $this->setLayout('users');
                        echo $this->loadTemplate();
                    }
                    ?>
                    <input type="hidden" name="vendor_id" value="<?php echo $this->element->vendor_id; ?>"/>
                    <input type="hidden" name="task" value="save"/>
                    <input type="hidden" name="ctrl" value="vendor"/>
                    <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
                </div>
            </form>

            <?php
        }
        ?>
    </div>
<?php
$list_messenger = array();
$key="HIKA_NAME_REQUIRED";
$list_messenger[$key]=JText::_($key);
$key="HIKA_EMAIL_REQUIRED";
$list_messenger[$key]=JText::_($key);
$key="HIKA_PASSWORD_REQUIRED";
$list_messenger[$key]=JText::_($key);
$key="HIKA_PASSWORD_RETYPE_REQUIRED";
$list_messenger[$key]=JText::_($key);
$key="HIKA_EMAIL_INCORRECT";
$list_messenger[$key]=JText::_($key);
$key="HIKA_PASSWORD_RETYPE_INCORRECT";
$list_messenger[$key]=JText::_($key);
$key="HIKA_VENDOR_NAME_REQUIRED";
$list_messenger[$key]=JText::_($key);

$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("body.site.com_hikamarket.view-vendormarket.layout-form").view_vendormarket_form({
                list_messenger:<?php echo json_encode($list_messenger) ?>,
                user_dont_show_help:<?php echo (int)$user_dont_show_help ?>,
                key_user_dont_show_help:"<?php echo $key_user_dont_show_help ?>"
            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>