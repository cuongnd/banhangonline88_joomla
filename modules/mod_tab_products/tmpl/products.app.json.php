<?php
$app=JFactory::getApplication();
$input=$app->input;
$image_helper = hikashop_get('helper.image');
$post=json_decode(file_get_contents('php://input'));
if(!$post){
    $post=(object)$input->getArray();
}
$category_id= $post->category_id;
$params->set('categories',array($category_id));
$current_category = reset($modtab_productshelper->get_list_category_product($params, false));
$detail=&$current_category->detail;
$detail->icon=$image_helper->getThumbnail($detail->category_icon_file_path,array(500,500), array('default' => true), true);
foreach($current_category->list as &$product){
    $list_image=&$product->list_image;
    $list_image=explode(';',$list_image);
    foreach($list_image as &$image){
        $image=$image_helper->getThumbnail($image,array(500,500), array('default' => true), true);
    }
    $link='product&task=show&cid=' . $product->product_id . '&name=' . $product->alias;
    $product->link=hikashop_contentLink($link.'&partner_id='.$user->user_id, $product);

}

foreach($current_category->list_small_product as &$product){
    $list_image=&$product->list_image;
    $list_image=explode(';',$list_image);
    foreach($list_image as &$image){
        $image=$image_helper->getThumbnail($image,array(500,500), array('default' => true), true);
    }
    $link='product&task=show&cid=' . $product->product_id . '&name=' . $product->alias;
    $product->link=hikashop_contentLink($link.'&partner_id='.$user->user_id, $product);

}

foreach($current_category->list_sub_category_detail as &$category){
    $category->icon=$image_helper->getThumbnail($category->category_icon_file_path,array(500,500), array('default' => true), true);
    $category->link=hikashop_contentLink('category&task=listing&cid='.$category->category_id.'&name='.$category->alias,$category->category_id);
}
$debug=JUtility::get_debug();
if($debug){
    echo "<pre>";
    print_r(array($current_category),false);
    echo "</pre>";
    die;
}
echo json_encode(array($current_category));