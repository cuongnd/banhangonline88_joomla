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
$doc->addScript('/administrator/components/com_hikashop/assests/js/view_importproductvatgia_all.js');
$doc->addLessStyleSheet('/administrator/components/com_hikashop/assests/less/view_category_vatgia_add_product_all.less');
$list_filter=array(
    hot=>"Hot",
    min_price=>"Min->max product",
    promotion=>"Promotion",
    top_week=>"Top week"
);
$sub_category_item=$this->sub_category_item;
$sub_category_item_pivot=JArrayHelper::pivot($sub_category_item,'category_id');
?>
<div class="view-importproductvatgia-all">
    <form action="index.php">
        <div class="row-fluid">
            <div class="span8">
                <button class="btn btn-primary" type="button" data-toggle="collapse" aria-expanded="true" data-target="#list_category"><?php echo $this->category_item->category_name ?></button>
                <div id="list_category" class="collapse open">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <td>Select</td>
                            <td>Category name</td>
                            <td>Category id</td>
                            <td>parent Category</td>
                            <td>VG Category</td>
                            <td>Page number</td>
                            <td>filter</td>
                            <td>Is deal</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($this->sub_category_item as $item_category){ ?>
                            <?php
                            if($item_category->has_sub_category==1){
                                continue;
                            }
                            $parent_category=$sub_category_item_pivot[$item_category->category_parent_id];
                            ?>
                            <tr class="item-category">
                                <td><input type="checkbox" value="1" name="selected[]"></td>
                                <td><?php echo $item_category->category_name ?></td>
                                <td><input readonly value="<?php echo $item_category->category_id ?>"  type="text" name="category_id[]"></td>
                                <td><?php echo $parent_category->category_name ?></td>
                                <td><input value="<?php echo $item_category->vatgia_category_id ?>"  type="text" name="vatgia_category_id[]"></td>
                                <td>
                                    <select name="filter_page_number[]" class="filter_page_number">
                                        <?php for($i=1;$i<=10;$i++){ ?>
                                            <option <?php echo $page_selected==$i?' selected ':'' ?>  value="<?php echo $i ?>"><?php echo $i ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                                <td>
                                    <select name="filter_by[]" class="filter_by">
                                        <?php foreach($list_filter as $key=>$value){ ?>
                                            <option <?php echo $filter_by==$key?' selected ':'' ?>  value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                                <td><input type="checkbox" value="1" name="vatgia_deal[]"></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>


            </div>
            <div class="span4">
                <input type="hidden" name="hika_category_id" value="<?php echo $this->category_item->category_id ?>">
                <button class="btn btn-primary get_product"  type="button"><?php echo JText::_('get product') ?></button>
                <button class="btn btn-primary importproductvatgia" type="button"><?php echo JText::_('import product') ?></button>
                <button class="btn btn-primary cancel_importproductvatgia" type="button"><?php echo JText::_('cancel import product') ?></button>

            </div>
        </div>
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
        $(".view-importproductvatgia-all").view_importproductvatgia_all({
        });
    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>

