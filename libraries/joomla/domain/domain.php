<?php

/**
 * Created by PhpStorm.
 * User: cuongnd
 * Date: 27/10/2016
 * Time: 10:53 SA
 */
class JDomain
{

    public static function check_exists_domain($domain_name)
    {
        $table_domain=JTable::getInstance('domain');
        $table_domain->load(array('domain_name'=>$domain_name));
        if($table_domain->id)
        {
            return true;
        }else{
            return false;
        }
        return false;
    }
}