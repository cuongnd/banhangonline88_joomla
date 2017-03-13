<?php

/**
 * Created by PhpStorm.
 * User: cuongnd
 * Date: 21/01/2017
 * Time: 5:10 CH
 */
class templateHelper
{

    protected static $instances = null;
    public  $list_var_template_config=array();
    public function __construct(  ){

        $this->list_var_template_config=array();
    }

    public static function getInstance()
    {
        if (empty(static::$instances)) {
            static::$instances = new static();
        }
        return static::$instances;
    }

}