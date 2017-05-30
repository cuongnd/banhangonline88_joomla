<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
?>
<style>
    .sourcecoast ul.nav-stacked li {
        padding: 0;
    }

    .sourcecoast ul.nav-tabs > .active > a {
        background-color: #0088cc;
        color: #fff;
    }
</style>
<div class="span2 pull-left autotune">
    <h3>Steps</h3>
    <ul class="nav nav-tabs nav-stacked">
        <?php

        $allSteps = array(
            'default' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_DEFAULT'),
            'basicinfo' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_BASICINFO'),
            'fbapp' => JText::_('Facebook App'),
            'siteconfig' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_SITECONFIG'),
            'errors' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_ERROR'),
            'finish' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_FINISH')
        );

        $currentLayout = $this->getLayout();
        foreach ($allSteps as $step => $display)
        {
            $class = ($step == $currentLayout) ? 'class="active"' : '';
            echo '<li ' . $class . '>';
            echo '<a href="index.php?option=com_jfbconnect&view=autotune&task=' . $step . '">' . $allSteps[$step] . '</a></li>';
        }
        ?>
    </ul>
</div>