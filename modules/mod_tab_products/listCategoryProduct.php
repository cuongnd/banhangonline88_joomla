<?php
/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 8/26/2017
 * Time: 11:56 PM
 */

class modTabProducts_listCategoryProduct
{
    public $category_id=0;
    public $detail;
    public $is_loaded=0;
    public $list_category=array();
    public $list_sub_category_detail=array();
    public $list=array();
    public $list_small_product=array();
    /**
     * ddfds constructor.
     */
    public function __construct($category_id)
    {
        $this->category_id=$category_id;
    }
}