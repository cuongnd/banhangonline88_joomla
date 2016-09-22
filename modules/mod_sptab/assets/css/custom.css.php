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
#sptab<?php echo $uniqid ?> ul.tabs_container li.tab{background-image:none;float:left;cursor:pointer;padding:0 10px;margin:0}
#sptab<?php echo $uniqid ?> .items_mask {position:relative;overflow:hidden}
#sptab<?php echo $uniqid ?> .tab-padding {padding:10px}