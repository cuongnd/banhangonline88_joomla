<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
$doc=JFactory::getDocument();
$doc->addLessStyleSheet('/modules/mod_hikashop/assets/less/style.less');
$current_module_image=$params->get('module_image','');
?><?php if (!empty($html)) { ?>
    <div id="hikashop_module_<?php echo $module->id; ?>"
         class="hikashop_module <?php echo @$module->params['moduleclass_sfx']; ?>">
            <?php if($module->showtitle): ?><h3 class="module-title"> <?php if($current_module_image!=''): ?><img class="icon" src="<?php echo JUri::root().$current_module_image ?>"><?php endif; ?> <?php echo $module->title ?></h3> <?php endif; ?>
        <?php echo $html; ?>
    </div>
<?php } ?>
