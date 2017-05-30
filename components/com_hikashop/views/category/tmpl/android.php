<?php
$image=hikashop_get('helper.image');
$currency_helper= hikashop_get('class.currency');

$list1=array();
foreach($this->products as &$item){
    $item1=new stdClass();
    $item1->id=$item->product_id;
    $item1->remote_id=$item->product_id;
    $item1->url= 'dddddddddddddddddd';
    $item1->name=$item->product_name;
    $item1->category=$item->product_id;
    $item1->type="category";
    //$item->description=JString::sub_string(strip_tags($item->product_description),200,'...') ;
    $fist_image=reset($item->images);
    $img = $image->getThumbnail($item->file_path);
    $item1->main_image=JUri::root().$img->url;
    $first_price=reset($item->prices);

    if($item->discount) {
        $item1->price=$first_price->price_value_without_discount_with_tax;
        $item1->price_formatted=$currency_helper->format($first_price->price_value_without_discount_with_tax, $first_price->price_currency_id);
        $item1->discount_price =$first_price->price_value_with_tax ;
        $item1->discount_price_formatted = $currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);
    }else{
        $item1->price=$first_price->price_value_with_tax;
        $item1->price_formatted=$currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);
        $item1->discount_price =$first_price->price_value_with_tax;
        $item1->discount_price_formatted =$currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);


    }
    $list1[]=$item1;
}
$response=array(
    metadata=>array(
        links=>array(
            first=>"first",
            last=>"last",
            prev=>null,
            next=>null,
            self=>"self"
        ),
        records_count=>3
    ),
    records=>$list1
);
echo json_encode($response);
?>