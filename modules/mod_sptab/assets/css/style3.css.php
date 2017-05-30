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
#sptab<?php echo $uniqid ?> .tabs_buttons{padding:0 10px;overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:url(../images/style3/tab-l.png) no-repeat 0 100%;color:#666;float:left;padding:0 0 0 5px;margin:0;border:0!important;}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{background:url(../images/style3/tab-r.png) no-repeat 100% 100%;display:inline-block;cursor:pointer;padding:0 10px 0 5px;margin:0 5px 0 0;font-weight:700}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:12px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active{color:#fff}

#sptab<?php echo $uniqid ?>.sptab_red .tabs_buttons{border-bottom:4px solid #a30d0b}
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active{background:url(../images/style3/tab-red-active-l.png) no-repeat 0 100%}
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active span{background:url(../images/style3/tab-red-active-r.png) no-repeat 100% 100%}

#sptab<?php echo $uniqid ?>.sptab_green .tabs_buttons{border-bottom:4px solid #90a80a}
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active{background:url(../images/style3/tab-green-active-l.png) no-repeat 0 100%}
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active span{background:url(../images/style3/tab-green-active-r.png) no-repeat 100% 100%}

#sptab<?php echo $uniqid ?>.sptab_blue .tabs_buttons{border-bottom:4px solid #0EA5DE}
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active{background:url(../images/style3/tab-blue-active-l.png) no-repeat 0 100%}
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active span{background:url(../images/style3/tab-blue-active-r.png) no-repeat 100% 100%}
