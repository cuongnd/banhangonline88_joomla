<?php
$doc = JFactory::getDocument();
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_menu/assests/less/default.less');
$doc->addScript(JUri::root().'modules/mod_menu/assests/js/mod_menu_default.js');
$total_item=10;
?>
<ul class="level-0 deconstruction">
    <?php for($i=0;$i<$total_item;$i++){ ?>
    <li class="menu-iem level-1"><a href="javascript:void(0)"><span class="animated-background">&nbsp;</span></a></li>
    <?php } ?>
</ul>
