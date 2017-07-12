<?phpdefined('_JEXEC') or die;JHtml::_('jqueryfrontend.framework');JHtml::_('jqueryfrontend.slick');$doc->addLessStyleSheet('/modules/mod_search/assets/less/mod_search.less');$menu=JFactory::getApplication()->getMenu();$active_menu=$menu->getActive();$menu_id= $active_menu->id;JHtml::_('jqueryfrontend.utility');$doc->addScript( '/modules/mod_search/assets/js/mod_search.js');$style = $params->get('product_style', 'table');$currencyHelper = hikashop_get('class.currency');$mainCurr = $currencyHelper->mainCurrency();$image = hikashop_get('helper.image');$input=JFactory::getApplication()->input;$keyword=$input->getString('keyword','');$key=$input->getString('key','');if($keyword){    if(JUtility::isJson($keyword)){        $keyword=json_decode($keyword);        $product_id=$keyword->product_id;        $keyword=$keyword->product_name;    }else{    }}$list_type=array(    (object)array(        page_show_result=>$params->get('page_show_all_result',0),        key=>'all',        text=>JText::_('MOD_SEARCH_ALL'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_ALL'),    ),    (object)array(        page_show_result=>$params->get('page_show_product_result',0),        key=>'product',        text=>JText::_('MOD_SEARCH_PRODUCT'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_PRODUCT'),    ),    (object)array(        page_show_result=>$params->get('page_show_category_result',0),        key=>'category',        text=>JText::_('MOD_SEARCH_CATEGORY'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_CATEGORY'),    ),    (object)array(        page_show_result=>$params->get('page_show_all_result',0),        key=>'discount',        text=>JText::_('MOD_SEARCH_DISCOUNT'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_DISCOUNT'),    ),    (object)array(        page_show_result=>$params->get('page_show_all_result',0),        key=>'question',        text=>JText::_('MOD_SEARCH_QUESTION'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_QUESTION'),    ),    (object)array(        page_show_result=>$params->get('page_show_all_result',0),        key=>'ad',        text=>JText::_('MOD_SEARCH_AD'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_AD'),    ),    (object)array(        page_show_result=>$params->get('page_show_all_result',0),        key=>'shop',        text=>JText::_('MOD_SEARCH_SHOP'),        placeholder=>JText::_('MOD_SEARCH_PLACE_HOLDER_SHOP'),    ),);$list_type_pivot_key=JArrayHelper::pivot($list_type,'key');?><div class="mod_search" id="mod_search_<?php echo $module->id ?>">    <form action="/index.php" method="post" name="search" id="search-<?php echo $module->id ?>">        <div class="search-form">            <div class="input-group">                <div class="input-group-btn" >                    <div class="btn-group">                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">                            <span class="text"><?php echo $key?$list_type_pivot_key[$key]->text:JText::_('MOD_SEARCH_ALL') ?></span> <span class="caret"></span>                        </button>                        <ul class="dropdown-menu" role="menu">                            <?php foreach($list_type AS $type){ ?>                            <li><a class="key" data-page_show_result="<?php echo $type->page_show_result ?>"  data-key="<?php echo $type->key ?>" data-text="<?php echo $type->text ?>" data-placeholder="<?php echo $type->placeholder ?>" href="#"><?php echo $type->text ?></a></li>                            <?php } ?>                        </ul>                    </div>                </div>                <input type="text" id="relative" name="keyword" value="<?php echo $keyword ?>" placeholder="<?php echo JText::_('MOD_SEARCH_PLACE_HOLDER_ALL') ?>" class="form-control" />                <span class="input-group-btn">                    <button  class="btn btn-primary " type="submit">                        <?php echo JText::_('MOD_SEARCH') ?>                    </button>                </span>            </div>            <div class="search-result hide">                <div class="history">                    <div class="row">                        <div class="col-lg-12">                            <h4 class="title"><?php echo JText::_('keyword history') ?></h4>                            <ul class="">                                <li  class="tags sub"><a href="/thoi-trang-va-phu-kien/thoi-trang-nu/quan-ao-nu.html">Quần áo nữ</a>                                </li>                                <li  class="tags sub"><a href="/thoi-trang-va-phu-kien/thoi-trang-nu/quan-ao-nu.html">Quần áo nữ</a>                                </li>                                <li  class="tags sub"><a href="/thoi-trang-va-phu-kien/thoi-trang-nu/quan-ao-nu.html">Quần áo nữ</a>                                </li>                                <li  class="tags sub"><a href="/thoi-trang-va-phu-kien/thoi-trang-nu/quan-ao-nu.html">Quần áo nữ</a>                                </li>                                <li  class="tags sub"><a href="/thoi-trang-va-phu-kien/thoi-trang-nu/quan-ao-nu.html">Quần áo nữ</a>                                </li>                            </ul>                        </div>                    </div>                </div>                <div class="product">                    <h4 class="title"><?php echo JText::_('Product result') ?></h4>                    <div class="header">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                    </div>                    <div class="body">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php  JText::_('product name') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                    </div>                </div>                <div class="category">                    <h4 class="title"><?php echo JText::_('Category result') ?></h4>                    <div class="header">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                    </div>                    <div class="body">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php  JText::_('product name') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                        </div>                    </div>                </div>                <div class="coupon">                    <h4 class="title"><?php echo JText::_('Coupon code result') ?></h4>                    <div class="header">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                            <div class="col-lg-9"><?php echo JText::_('Coupon code') ?></div>                        </div>                    </div>                    <div class="body">                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php  JText::_('product name') ?></div>                            <div class="col-lg-9"><?php echo JText::_('Coupon code') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                            <div class="col-lg-9"><?php echo JText::_('Coupon code') ?></div>                        </div>                        <div class="row">                            <div class="col-lg-3"><?php echo JText::_('product id') ?></div>                            <div class="col-lg-9"><?php echo JText::_('product name') ?></div>                            <div class="col-lg-9"><?php echo JText::_('Coupon code') ?></div>                        </div>                    </div>                </div>            </div>        </div>        <input type="hidden" name="option" value="com_hikashop">        <input type="hidden" name="ctrl" value="product">        <input type="hidden" name="task" value="search_by_keyword">        <input type="hidden" name="key" value="<?php echo $key?$key:'product' ?>">        <input type="hidden" name="Itemid" value="<?php echo $params->get('page_show_all_result',0) ?>">        <?php echo JHtml::_('form.token'); ?>    </form></div><?php$js_content = '';$doc = JFactory::getDocument();ob_start();$list_language=array(    MOD_SEARCH_PLACE_INPUT_KEY_WORD=>JText::_('MOD_SEARCH_PLACE_INPUT_KEY_WORD'))?><script type="text/javascript">    jQuery(document).ready(function ($) {        $("#mod_search_<?php echo $module->id ?>").mod_search({            module_id:<?php echo $module->id   ?>,            style: "<?php echo $style ?>",            key: "<?php echo $key ?>",            params:<?php echo json_encode($params->toObject()) ?>,            list_language:<?php echo json_encode($list_language) ?>        });    });</script><?php$js_content = ob_get_clean();$js_content = JUtility::remove_string_javascript($js_content);$doc->addScriptDeclaration($js_content);?>