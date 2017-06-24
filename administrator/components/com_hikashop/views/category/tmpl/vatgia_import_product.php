<?php
/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 6/24/2017
 * Time: 11:10 AM
 */
JHtml::_('jquery.framework');
JHtml::_('jquerybackend.base64');
$doc=JFactory::getDocument();

$doc->addScript(JUri::root().'administrator/components/com_hikashop/assests/js/view_importproductvatgia.js');
$doc->addLessStyleSheet('/administrator/components/com_hikashop/assests/less/view_category_vatgia_add_product.less');
?>
<div class="view-importproductvatgia">
    <form action="index.php">
        <h3><?php echo $this->category_item->category_name ?></h3>
        <table>
            <tr>
                <td>vat gia category id</td>
                <td><input value="<?php echo $this->category_item->vatgia_category_id ?>"  type="text" name="vatgia_category_id"></td>
            </tr>

        </table>
        <input type="hidden" name="hika_category_id" value="<?php echo $this->category_item->category_id ?>">
        <button class="btn btn-primary get_product"  type="button"><?php echo JText::_('get product') ?></button>
        <button class="btn btn-primary importproductvatgia" type="button"><?php echo JText::_('import product') ?></button>
        <h4 class="link"></h4>
        <div class="vatgia-div-loading"></div>
        <div class="vatgia-import-product-div-loading"></div>
        <hr/>
        <h1 class="detail_product_name"></h1>
        <img class="src_image" src="">
        <div class="product_price"></div>
        <div class="vatgia-wrapper-product">

        </div>
        <div class="vatgia-wrapper">

        </div>
    </form>
</div>
<?php
$uri=JFactory::getUri();
$uri->setVar('vatgia',1);
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".view-importproductvatgia").view_importproductvatgia({
        });
    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>

