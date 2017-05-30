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
#sptab<?php echo $uniqid ?> .tabs_buttons{overflow:hidden}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:url(../images/style5/tab-l.png) no-repeat 0 50%;color:#fff;float:left;padding:0 0 0 10px;margin:0;border:0!important}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{background:url(../images/style5/tab-r.png) no-repeat 100% 50%;display:inline-block;cursor:pointer;padding:0 10px 0 0;margin:0 4px 0 0;text-transform:uppercase}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab, #sptab<?php echo $uniqid ?> ul.tabs_container li.tab span{font-size:11px}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}

#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active{background:url(../images/style5/tab-red-active-l.png) no-repeat 0 50%}
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active span{background:url(../images/style5/tab-red-active-r.png) no-repeat 100% 50%}

#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active{background:url(../images/style5/tab-green-active-l.png) no-repeat 0 50%}
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active span{background:url(../images/style5/tab-green-active-r.png) no-repeat 100% 50%}

#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active{background:url(../images/style5/tab-blue-active-l.png) no-repeat 0 50%}
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active span{background:url(../images/style5/tab-blue-active-r.png) no-repeat 100% 50%}