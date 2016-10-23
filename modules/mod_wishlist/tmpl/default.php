<?php
defined('_JEXEC') or die;
$doc=JFactory::getDocument();
$doc->addScript(JUri::root().'modules/mod_wishlist/assets/js/mod_wishlist.js');
$doc->addLessStyleSheet(JUri::root().'modules/mod_wishlist/assets/less/mod_wishlist.less');
?>
<div id="mod_wishlist_<?php echo $module->id ?>" class="mod_wishlist">
    <div class="group_like_cart">
        <div class="fl header_like wishlist" title="<?php echo JText::_('Sản phẩm yêu thích') ?>" >
            <a rel="nofollow" href="#" class="follow">
                <i class="icon_vg40 icon_vg40_like"></i>
                <span>Yêu thích</span>
                <div class="notify">0</div>
            </a>
        </div>
        <div class="wrapper-wishlist">
            <?php
            if(!empty($html)){
                ?>
                <div class="hikashop_wishlist_module" id="hikashop_wishlist_module">
                    <?php echo $html; ?>
                </div>
            <?php } ?>

            </div>

    </div>
</div>
<?php
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#mod_wishlist_<?php echo $module->id ?>").mod_wishlist({
            module_id:<?php echo $module->id   ?>,
            params:<?php echo json_encode($params->toObject()) ?>
        });


    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);

?>
