<?php
/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 6/24/2017
 * Time: 11:10 AM
 */
JHtml::_('jquery.framework');
JHtml::_('jquerybackend.base64');
JHtml::_('jquerybackend.auto_numeric');
$session=JFactory::getSession();
$app=JFactory::getApplication();
$filter_by=$session->get('product_filter_by','hot');
$filter_page_number=$session->get('filter_page_number',1);
$doc=JFactory::getDocument();
$doc->addScript('/administrator/components/com_hikashop/assests/js/view_importproductvatgia.js');
$doc->addLessStyleSheet('/administrator/components/com_hikashop/assests/less/view_category_vatgia_add_product.less');
$list_filter=array(
    hot=>"Hot",
    min_price=>"Min->max product",
    promotion=>"Promotion",
    top_week=>"Top week"
);
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
        <div class="pull-left">
            <select name="filter_by" class="filter_by">
                <?php foreach($list_filter as $key=>$value){ ?>
                    <option <?php echo $filter_by==$key?' selected ':'' ?>  value="<?php echo $key ?>"><?php echo $value ?></option>
                <?php } ?>
            </select>
            select page number
            <select name="filter_page_number" class="filter_page_number">
                <?php for($i=1;$i<=10;$i++){ ?>
                    <option <?php echo $page_selected==$i?' selected ':'' ?>  value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select>
        </div>
        <label><input type="checkbox" value="1" name="vatgia_deal">is vat gia deal</label>
        <input type="hidden" name="hika_category_id" value="<?php echo $this->category_item->category_id ?>">
        <button class="btn btn-primary get_product"  type="button"><?php echo JText::_('get product') ?></button>
        <button class="btn btn-primary importproductvatgia" type="button"><?php echo JText::_('import product') ?></button>
        <button class="btn btn-primary cancel_importproductvatgia" type="button"><?php echo JText::_('cancel import product') ?></button>
        <h4 class="link"></h4>

        <div class="vatgia-div-loading"></div>
        <div class="vatgia-import-product-div-loading"></div>
        <hr/>
        <h1 class="product_name"></h1>
        <img class="src_image" src="">
        <div>Price<span class="product_price"></span></div>
        <div>promotion Price<span class="price_promotion"></span></div>
        <div>promotion Price time<span class="price_promotion_time"></span></div>
        <div class="">vendor : <span class="vendor_name"></span></div>
        <div class="">meta description : <span class="meta_description"></span></div>
        <div class="">product keywords : <span class="product_keywords"></span></div>
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

