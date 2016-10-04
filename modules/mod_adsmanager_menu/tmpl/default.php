<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="juloawrapper">
    <ul class="menu<?php echo $moduleclass_sfx; ?>">
    <?php if ($displayhome) {?>
    <li><a href="<?php echo $link_front; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_HOME');?></span></a></li>
    <?php } ?>
    <?php if ($displaywritead) {?>
    <li><a href="<?php echo $link_write_ad; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_WRITE');?></span></a></li>
    <?php } ?>
    <?php if ($displayprofile) {?>
    <li><a href="<?php echo $link_show_profile; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_PROFILE');?></span></a></li>
    <?php } ?>
    <?php if ($displaymyads) {?>
    <li><a href="<?php echo $link_show_user; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_USER_ADS');?></span></a></li>
    <?php } ?>
    <?php if ($displayfavorites) {?>
    <li><a href="<?php echo $link_favorites; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_FAVORITES');?></span></a></li>
    <?php } ?>
    <?php if ($displayrules) {?>
    <li><a href="<?php echo $link_show_rules; ?>"><span><?php echo JText::_('ADSMANAGER_MENU_RULES');?></span></a></li>
    <?php } ?>
    <?php if (($displayhome|$displaywritead|$displayprofile|$displaymyads|$displayrules)&&($displayallads|$displaycategories)) {?>
    <?php if ($displayseparators) {?>
    <span class="separator" ><hr/></span>
    <?php } ?>
    <?php } ?>
    <?php if ($displayallads) {?>
        <?php
        if ($displaynumads == 1)
            $all = JText::_('ADSMANAGER_MENU_ALL_ADS'). " ($nbcontents)";
        else
            $all = JText::_('ADSMANAGER_MENU_ALL_ADS');
        ?>
        <li><a href="<?php echo $link_show_all; ?>"><span><?php echo $all;?></span></a></li>

        <?php if ($displaycategories) {?>
        <?php if ($displayseparators) {?>
        <span class="separator" ><hr/></span>
        <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php if ($displaycategories) {?>
    <?php
    displayMenuCats($rootid, 0, $cats,$current_list,$displaynumads,$rootid);
    ?>
    <?php } ?>
    </ul>
</div>