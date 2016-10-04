<?php

/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . '/media/system/js/Zozo_Tabs_v.6.5/js/zozo.tabs.js');
$doc->addScript(JUri::root() . '/components/com_hikashop/assets/js/view_modules_listing_tab.js');
$doc->addStyleSheet(JUri::root() . '/media/system/js/Zozo_Tabs_v.6.5/css/zozo.tabs.min.css');

$scriptId = "view_modules_listing_tab";
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.view_modules_listing_tab').view_modules_listing_tab({});


    });
</script>
<div class="view_modules_listing_tab">
    <div class="tabbed-nav">

        <!-- Tab Navigation Menu -->
        <ul>
            <?php
            foreach($this->list_rows AS $rows){
                ?>
                <li><a>Overview</a></li>
                <?php
            }
            ?>
        </ul>

        <!-- Content container -->
        <div>
            <?php
            foreach($this->list_rows AS $rows){
                ?>
                <div>
                    <?php



                    ?>


                </div>
            <?php
            }
            ?>


        </div>

    </div>
</div>




