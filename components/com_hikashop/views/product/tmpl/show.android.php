<?php
$app=JFactory::getApplication();

$image_helper = hikashop_get('helper.image');
$debug=JUtility::get_debug();
$product_response=new stdClass();
$product_response->categories=array();
foreach($product_response->categories as &$category) {
    $category->medium_image = $image_helper->getThumbnail($category->category_medium_image_file_path, array(500, 500), array('default' => true), true);
}
$product_response->product=$this->element;

$images=&$product_response->images;
foreach($images as &$image) {
    $image = $image_helper->getThumbnail($image->file_path, array(500, 500), array('default' => true), true);

}
echo json_encode($product_response);






/**
 * Created by PhpStorm.
 * User: cuongnd
 * Date: 04/04/2017
 * Time: 8:44 SA
 */