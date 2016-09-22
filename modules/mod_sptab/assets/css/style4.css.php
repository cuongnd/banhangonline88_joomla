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
#sptab<?php echo $uniqid ?> ul.tabs_container {list-style:none;margin: 0!important; padding: 0!important}
#sptab<?php echo $uniqid ?> .tabs_buttons{background:#000 url(../images/style4/header_bg.gif) repeat-x 0 0;overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:none;color:#999;float:left;padding:0;margin:0;border:0!important;border-right:1px solid #333!important}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{display:inline-block;cursor:pointer;padding:0 10px;margin:0;font-weight:700}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:12px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden;color:#fff}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active{color:#fff}

#sptab<?php echo $uniqid ?>.sptab_red .items_mask{background:#a30d0b}
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active span{background:#a30d0b url(../images/style4/tab-red-active.png) repeat-x 0 0}

#sptab<?php echo $uniqid ?>.sptab_green .items_mask{background:#90a80a}
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active span{background:#90a80a url(../images/style4/tab-green-active.png) repeat-x 0 0}

#sptab<?php echo $uniqid ?>.sptab_blue .items_mask{background:#0EA5DE}
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active span{background:#0EA5DE url(../images/style4/tab-blue-active.png) repeat-x 0 0}