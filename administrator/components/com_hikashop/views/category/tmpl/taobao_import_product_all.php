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
$filter_by=$session->get('product_filter_by','min_price');
$filter_page_number=$session->get('filter_page_number',1);
$doc=JFactory::getDocument();
$debug=JUtility::get_debug();
if($debug)
{
    $doc->addScript('/administrator/components/com_hikashop/assests/js/view_importproducttaobao_all_debug.js');

}else{
    $doc->addScript('/administrator/components/com_hikashop/assests/js/view_importproducttaobao_all.js');
}
$doc->addLessStyleSheet('/administrator/components/com_hikashop/assests/less/view_category_taobao_add_product_all.less');
$list_filter=array(
    hot=>"Hot",
    min_price=>"Min->max product",
    promotion=>"Promotion",
    top_week=>"Top week"
);
$list_tb_product=JUtility::getCurl('http://banhangonline88.com/index.php?option=com_hikashop&ctrl=product&task=get_tao_bao_id_from_system');
$list_tb_product=(array)json_decode($list_tb_product);
$total_page=100;
$sub_category_item=$this->sub_category_item;
$sub_category_item_pivot=JArrayHelper::pivot($sub_category_item,'category_id');
?>
<div class="view-importproducttaobao-all">
    <?php if($debug){ ?>
        <h4>you are using debug</h4>
    <?php }else{ ?>
        <h4>you are using final version</h4>
    <?php } ?>
    <form action="index.php">
        <div class="row-fluid">
            <div class="span12">
                <button class="btn btn-primary" type="button" data-toggle="collapse" aria-expanded="true" data-target="#list_category"><?php echo $this->category_item->category_name ?></button>
                <div id="list_category" class="collapse open">
                    <table class="table table-striped table-bordered list-category-tao-bao-and-category-system">
                        <thead>
                            <tr>
                                <td><label class="checked"><input class="checkbox" type="checkbox">Select all</label></td>
                                <td>Category name</td>
                                <td>Category id</td>
                                <td>parent Category</td>
                                <td>TB Category</td>
                                <td>TB Category name</td>
                                <td>TB link</td>
                                <td>Total page
                                    <select class="total_page">
                                        <?php for($i=1;$i<=$total_page;$i++){ ?>
                                            <option <?php echo $page_selected==$i?' selected ':'' ?>  value="<?php echo $i ?>"><?php echo $i ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                                <td>Filter
                                    <select  class="filter_by">
                                        <?php foreach($list_filter as $key=>$value){ ?>
                                            <option <?php echo $filter_by==$key?' selected ':'' ?>  value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                                <td>Is deal</td>
                                <td>Test page</td>
                                <td><?php echo JText::_('Test') ?></td>
                                <td><?php echo JText::_('State') ?></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($this->sub_category_item as $item_category){ ?>
                            <?php
                            if($item_category->has_sub_category==1){
                                continue;
                            }
                            $parent_category=$sub_category_item_pivot[$item_category->category_parent_id];
                            if(!$parent_category){
                                $parent_category=$this->category_item;
                            }
                            ?>
                            <tr class="item-category">
                                <td class=""><input type="checkbox" value="1" name="selected[]"></td>
                                <td><?php echo $item_category->category_name ?></td>
                                <td><input readonly value="<?php echo $item_category->category_id ?>"  type="text" name="category_id[]"></td>
                                <td><?php echo $parent_category->category_name ?></td>
                                <td><input value="<?php echo $item_category->taobao_category_id ?>"  type="text" name="taobao_category_id[]"></td>
                                <td><input value="<?php echo $item_category->taobao_category_name ?>"  type="text" name="taobao_category_name[]"></td>
                                <td><input value="<?php echo $item_category->taobao_link_category ?>"  type="text" name="taobao_link_category[]"></td>
                                <td>
                                    <select name="total_page[]" class="total_page">
                                        <?php for($i=1;$i<=$total_page;$i++){ ?>
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
                                <td><input type="checkbox" value="1" name="taobao_deal[]"></td>
                                <td>
                                    <select name="filter_page_number[]" class="filter_page_number">
                                        <?php for($i=1;$i<=$total_page;$i++){ ?>
                                            <option   value="<?php echo $i ?>"><?php echo $i ?></option>
                                        <?php } ?>
                                    </select>

                                </td>
                                <td><button type="button" class="btn btn-info btn-test-get-content"><?php echo JText::_('Test') ?></button></td>
                                <td><span class="state"></span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>


            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <input type="hidden" name="hika_category_id" value="<?php echo $this->category_item->category_id ?>">
                <button class="btn btn-primary importproducttaobao" type="button"><?php echo JText::_('import product') ?></button>
                <button class="btn btn-primary cancel_importproducttaobao" type="button"><?php echo JText::_('cancel import product') ?></button>
            </div>

        </div>
        <h4 class="link"></h4>

        <div class="taobao-div-loading"></div>
        <div class="taobao-import-product-div-loading"></div>
        <hr/>
        <h1 class="product_name"></h1>
        <div> product link:<span class="product_link"></span></div>
        <img class="src_image" src="">
        <div>taobao_product_id:<span class="taobao_product_id"></span></div>
        <div>Price<span class="product_price"></span></div>
        <div>promotion Price<span class="price_promotion"></span></div>
        <div>promotion Price time<span class="price_promotion_time"></span></div>
        <div class="">vendor : <span class="vendor_name"></span></div>
        <div class="">meta description : <span class="meta_description"></span></div>
        <div class="">product keywords : <span class="product_keywords"></span></div>
        <div class="">list_image : <div class="list_image"></div></div>
        <br>
        <h4>product description</h4>
        <div class="taobao-wrapper-product">

        </div>
        <div class="taobao-wrapper">

        </div>

        <!-- Modal -->
        <div id="test_get_content_product_vt" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="test_get_content_product_vt" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Modal header</h3>
            </div>
            <div class="modal-body">
                <div class="modal-taobao-wrapper">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </form>
</div>
<?php
$uri=JFactory::getUri();
$uri->setVar('taobao',1);
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(".view-importproducttaobao-all").view_importproducttaobao_all({
            list_tb_product:<?php echo json_encode($list_tb_product) ?>
        });
    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>

