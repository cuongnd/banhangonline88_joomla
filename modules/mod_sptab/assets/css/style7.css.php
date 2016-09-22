<?php
/*---------------------------------------------------------------
# SP Tab - Next generation tab module for joomla
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2014 JoomShaper.com. All Rights Reserved.
# license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
header("Content-Type: text/css");
$uniqid = $_GET['id'];
?>
#sptab<?php echo $uniqid ?> {border:1px solid #d6d6d6;padding:0 1px 0 1px;-moz-border-radius: 6px 6px 0 0;-webkit-border-radius: 6px 6px 0 0;border-radius: 6px 6px 0 0;}
#sptab<?php echo $uniqid ?> ul.tabs_container {list-style:none;margin: 0!important; padding: 0!important}
#sptab<?php echo $uniqid ?> .tabs_buttons{background:#ccd0d3 url(../images/style7/header_bg.gif) repeat-x 0 0;border-bottom:1px solid #a8acaf!important;padding:0 10px;overflow:hidden;-moz-border-radius:7px 7px 0 0;-webkit-border-radius:7px 7px 0 0;border-radius:7px 7px 0 0}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:none;color:#666;float:left;padding:0 0 0 10px;margin:0;border:0!important}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{display:inline-block;cursor:pointer;padding:0 10px 0 0;margin:0 5px 0 0;font-weight:700;text-transform:uppercase}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:12px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}

#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active{background:url(../images/style7/tab-active-l.png) no-repeat 0 0}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active span{background:url(../images/style7/tab-active-r.png) no-repeat 100% 0}

#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.tab_over{color:#ba0202}
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.tab_over{color:#91ba02}
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.tab_over{color:#01b0e2}