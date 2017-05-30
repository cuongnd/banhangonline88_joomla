<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

$appConfig = $this->appConfig;

$boolOptions = array(
    JHTML::_('select.option', 'enabled', 'Enabled'),
    JHTML::_('select.option', 'disabled', 'Disabled'));
?>
<style>
    div.current select {
        margin:0px;
    }

    .autotune_setting {
        width:200px;
        float:left;
    }

    .autotune_option {
        width:300px;
        float:left;
    }

    .autotune_description {
        width:9px;
    }

    .autotune .hasTip {
        background:none;
        color:#000000;
    }
</style>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_LABEL'); ?></h3>

            <form method="post" id="adminForm" name="adminForm" style="min-width:850px">
                <?php
                if (!isset($appConfig->group))
                {
                    echo '<br/><p style="font-size:14px">'.JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_ERROR_INFO_DESC').'<br/>';
                    echo '<p style="font-size:14px">'.JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_CHECK_SUBSCRIBER_DESC').'<br/><strong>' . $this->subscriberId . '</strong> (<a href="index.php?option=com_jfbconnect&view=autotune&task=basicinfo">'.JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_CHANGE_SUBSCRIBER_LABEL').'</a>)</p>';
                    echo '<p>'.JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_SUBSCRIBER_SUPPORT_DESC').'</p>';
                }
                else
                {
                echo '<p>' . JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_FB_INFO_DESC') . '</p>';

                if (defined('SC30')):
                ?>
                <!-- Tab Header -->
                <div class="row-fluid">
                    <ul class="nav nav-tabs">
                        <?php
                        $activeStr = ' class="active"';
                        foreach ($appConfig->group as $group)
                        {
                            $group_name = preg_replace('/\s+/', '', $group->name);
                            if ($group->numRecommendations == 0)
                                echo '<li' . $activeStr . '><a href="#' . $group_name . '" data-toggle="tab"><span class="autotuneGood">' . ucwords($group->name) . '</span></a></li>';
                            else
                                echo '<li' . $activeStr . '><a href="#' . $group_name . '" data-toggle="tab"><span class="autotuneBad">' . ucwords($group->name) . ' (' . $group->numRecommendations . ')</span></a></li>';

                            $activeStr = '';
                        }
                        ?>
                    </ul>
                </div>
                <div class="tab-content">
                    <?php
                    endif; //SC30

                    if (defined('SC16')):
                        jimport('joomla.html.pane');
                        $pane = JPane::getInstance('tabs');
                        echo $pane->startPane('content-pane');
                    endif; //SC16

                    $activeStr = ' active';
                    foreach ($appConfig->group as $group)
                    {
                        if (defined('SC16')):
                            if ($group->numRecommendations == 0)
                                echo $pane->startPanel('<span class="autotuneGood">' . ucwords($group->name) . '</span>', $group->name);
                            else
                                echo $pane->startPanel('<span class="autotuneBad">' . ucwords($group->name) . ' (' . $group->numRecommendations . ')</span>', $group->name);
                        endif; //SC16

                        $group_name = preg_replace('/\s+/', '', $group->name);
                        echo '<div class="tab-pane' . $activeStr . '" id="' . $group_name . '">';
                        $activeStr = '';
                        echo '<p>' . $group->description . '</p>';
                        ?>

                        <div class="config_row">
                            <div class="autotune_setting header">
                                <strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_APPLICATION_SETTING_LABEL'); ?></strong></div>
                            <div class="autotune_option header"><strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_APP_SETTING_LABEL'); ?></strong>
                            </div>
                            <div class="autotune_option header">
                                <strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_JFBC_RECOMMENDATION_LABEL'); ?></strong></div>
                            <div style="clear:both"></div>
                        </div>
                        <?php

                        foreach ($group->field as $field)
                        {
                            ?>
                            <div class="config_row">
                                <div class="autotune_setting hasTip" title="<?php echo $field->description; ?>"><?php echo $field->display ?></div>
                                <?php if ($field->type == 'image')
                                    echo '<div class="autotune_option"><img src="' . $field->value . '" />&nbsp;</div>';
                                else if (isset($field->edit))
                                {
                                    if ($field->type == 'text' || $field->type == 'url')
                                        echo '<div class="autotune_option"><input type="text" name="' . $field->name . '" value="' . $field->value . '" size="45" /></div>';
                                    else if ($field->type == 'array')
                                        echo '<div class="autotune_option"><input type="text" name="' . $field->name . '" value="' . implode(', ', $field->value) . '" size="45" /></div>';
                                    else if ($field->type == "bool")
                                        echo '<div class="autotune_option">' . JHTML::_('select.genericlist', $boolOptions, $field->name, null, 'value', 'text', strtolower($field->value)) . '</div>';
                                }
                                else
                                    echo '<div class="autotune_option">' . $field->value . '&nbsp;</div>';

                                $recStyle = $field->recommendMet ? 'autotuneGood' : 'autotuneBad';
                                ?>
                                <div class="autotune_option <?php echo $recStyle ?>"><?php echo $field->recommend ?>&nbsp;</div>
                                <div style="clear:both">
                                </div>
                            </div>
                        <?php
                        }

                        echo '</div>'; //tab-pane
                        if (defined('SC16')):
                            echo $pane->endPanel();
                        endif; //SC16
                    }
                    if (defined('SC30')):
                        echo '</div>'; // tab-content
                    endif; //SC30
                    if (defined('SC16')):
                        echo $pane->endPane();
                    endif; //SC16
                    ?>
                    <br />

                    <div style="text-align: center">
                        <input type="button" value="<?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_SAVE_ALL_RECOMMENDATIONS_LABEL');?>" class="btn btn-primary"
                               onclick="Joomla.submitbutton('saveAppRecommendations');" />
                        <input type="submit" value="<?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_SAVE_CUSTOM_SETTINGS_LABEL');?>" class="btn btn-primary" />
                    </div>
                    <br />

                    <p style="text-align:center">
                        <strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_FBFETCHED_LABEL') ?></strong><?php echo $this->appConfigUpdated; ?> |
                        <strong><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_SCFETCHED_LABEL'); ?></strong><?php echo $this->fieldsUpdated; ?></p>
                    <br />

                    <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_FBAPP_FBFETCHED_DESC') ?></p>

                    <?php
                    } // end of if/else for field descriptors loaded
                    ?>
                    <input type="hidden" name="option" value="com_jfbconnect" />
                    <input type="hidden" name="view" value="autotune" />
                    <input type="hidden" name="task" value="saveAppConfig" />
            </form>
        </div>
    </div>
</div>