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
#sptab<?php echo $uniqid ?> .tabs_mask {border-bottom:1px solid #ddd;padding:0 10px 1px 10px}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background:#f6f6f6;border:1px solid #ddd;color:#666;float:left;line-height:30px;cursor:pointer;padding:0 10px;margin:0 5px 0 0;font-weight:bold; -moz-border-radius:3px 3px 0 0; -webkit-border-radius:3px 3px 0 0; border-radius:3px 3px 0 0}
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.tab_over,#sptab<?php echo $uniqid ?> ul.tabs_container li.tab.active{background:#fff;border-bottom:1px solid #fff}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}

#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_red ul.tabs_container li.tab.active{color:#ba0202}

#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_green ul.tabs_container li.tab.active{color:#91ba02}

#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.tab_over,
#sptab<?php echo $uniqid ?>.sptab_blue ul.tabs_container li.tab.active{color:#01b0e2}