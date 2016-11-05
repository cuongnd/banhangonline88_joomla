<?php

/**
 * Created by PhpStorm.
 * User: cuongnd
 * Date: 27/10/2016
 * Time: 11:16 SA
 */
class JTableDomain extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__domain', 'id', $db);
    }
}