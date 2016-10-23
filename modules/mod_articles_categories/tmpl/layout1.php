<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_categories
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
$doc=JFactory::getDocument();
$doc->addLessStyleSheet(JUri::root().'modules/mod_articles_categories/assets/less/layout1.less');
defined('_JEXEC') or die;
?>

    <div id="vertical_accordian_drop_down_menu_bar">
        <div class='menu'>
            <ul class="categories-module<?php echo $moduleclass_sfx; ?>">
                <?php require JModuleHelper::getLayoutPath('mod_articles_categories', $params->get('layout', 'default') . '_items'); ?>
            </ul>
        </div>
    </div>
<?php
$js_content = '';
$uri = JFactory::getUri();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $.fn.vertical_accordian_drop_down_menu_bar('#vertical_accordian_drop_down_menu_bar');
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);