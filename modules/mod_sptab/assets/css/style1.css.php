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
#sptab<?php echo $uniqid ?> {border:1px solid #e5e5e5}
#sptab<?php echo $uniqid ?> ul.tabs_container {list-style:none;margin: 0!important; padding: 0!important}
#sptab<?php echo $uniqid ?> .tabs_buttons{background:#fff url(../images/style1/header_bg.gif) repeat-x 0 100%;border-bottom:1px solid #e5e5e5;padding:0 10px;overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:url(../images/style1/tab-l.png) no-repeat 0 50%;color:#666;float:left;padding:0 0 0 10px;margin:0;border:0!important;}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{background:url(../images/style1/tab-r.png) no-repeat 100% 50%;display:inline-block;cursor:pointer;padding:0 10px 0 0;margin:0 8px 0 0;text-transform:uppercase}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:11px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}

#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active{color:#ba0202}

#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active{color:#91ba02}

#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active{color:#01b0e2}