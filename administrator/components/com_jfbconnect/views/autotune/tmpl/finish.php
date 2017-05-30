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
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <form method="post" id="adminForm" name="adminForm">
                <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_LABEL'); ?></h3>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_DESC') ?></p>

                <h4><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_NEXT_LABEL'); ?></h4>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_NEXT_DESC'); ?>
                <ul>
                    <li><strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_CUSTOMIZE_LABEL');?></strong>
                        <?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_CUSTOMIZE_DESC');?>
                    </li>
                    <li><strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_ADD_BUTTON_LABEL');?></strong>
                        <?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_ADD_BUTTON_DESC');?>
                        <ul>
                            <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_ADD_BUTTON_STEP1_DESC');?></li>
                            <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_ADD_BUTTON_STEP2_DESC');?></li>
                        </ul>
                    </li>
                    <li><strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_SOCIAL_INTEGRATION_LABEL');?></strong>
                        <?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FINISH_SOCIAL_INTEGRATION_DESC');?>
                    </li>
                </ul>
                </p>

                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="task" value="" />
            </form>
            <div style="clear:both"></div>
        </div>
    </div>
</div>
