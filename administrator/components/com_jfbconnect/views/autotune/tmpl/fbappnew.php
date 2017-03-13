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
    .sourcecoast button {
        width: 150px;
        height: 60px;
        padding: 0px;
        font-weight: bold;
        font-size: 12px;
        float: left;
        margin: 0 5px;
    }
</style>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <form method="post" id="adminForm" name="adminForm">
                <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPPNEW_LABEL');?></h3>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPPNEW_DESC'); ?></p>

                <p style="height:80px">
                    <button type="button" class="btn btn-primary"
                            onclick="Joomla.submitbutton('saveAppRecommendations');"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_YES_AUTOCONFIGURE'); ?></button>
                    <button type="button" class="btn"
                            onclick="Joomla.submitbutton('fbapp');"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NO_AUTOCONFIGURE'); ?></button>
                </p>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NO_DESC'); ?></p>
                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="task" value="saveBasicInfo" />
            </form>
            <div style="clear:both"></div>
        </div>
    </div>
</div>
