<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.sliders');
JHTML::_('behavior.tooltip');
?>
<style>
    .disclaimer {
        margin: 20px 0;
    }

    .disclaimer p.fields {
        border: 1px solid #333;
        background: white;
        padding: 5px;
        margin: 5px 75px;
        text-align: center;
    }

    div.autotune .disclaimer p {
        font-size: 12px;
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>

        <div class="span9 autotune" xmlns="http://www.w3.org/1999/html">
            <form method="post" id="adminForm" name="adminForm">
                <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_LABEL');?></h3>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DESC');?></p>
                <ul>
                    <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DESC_SUB1');?></li>
                    <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DESC_SUB2');?></li>
                    <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DESC_SUB3');?></li>
                </ul>
                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_RECOMMENDATION_LABEL');?></p>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_START_LABEL');?></p>

                <div style="text-align: center">
                    <input type="submit" value="Start" class="btn btn-primary" />
                </div>
                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="task" value="basicinfo" />
            </form>
            <div class="row-fluid">
                <div class="span12">
                    <fieldset>
                        <legend><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_BASIC_CHECKS_LABEL');?></legend>
                        <table class="table table-striped">
                            <tr>
                                <td><strong>PHP</strong></td>
                                <td><?php echo $this->phpVersion; ?></td>
                            </tr>
                            <tr>
                                <td><strong>cURL</strong></td>
                                <td><?php echo $this->curlCheck; ?></td>
                            </tr>
                        </table>
                        <?php if ($this->errorsFound)
                            echo '<div class="autotuneBad" style="font-size: 15px; text-align: center">'.JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_ERROR_LABEL').'</div>'.JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_ERROR_DESC'); ?>
                    </fieldset>

                    <fieldset>
                        <legend><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_LABEL');?></legend>
                        <div class="disclaimer">
                            <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_SC_LABEL');?></p>

                            <p class="fields"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_SC_FIELDS');?></p>

                            <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_FB_LABEL');?></p>

                            <p class="fields"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_FB_FIELDS');?></p>

                            <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_DEFAULT_DISCLAIMER_DESC');?></p>
                        </div>
                    </fieldset>

                </div>

            </div>
        </div>
    </div>
</div>