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

function showPluginStatus($name, $status)
{
    if ($status)
        echo '<a href="javascript:void(0)" onclick="jfbcAdmin.autotune.enablePlugin(\'' . $name . '\', 0);"><img alt="' . JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PUBLISHED_DESC') . '" src="components/com_jfbconnect/assets/images/icon-16-allow.png"/></a> ' . JText::_('JPUBLISHED');
    else
        echo '<a href="javascript:void(0)" onclick="jfbcAdmin.autotune.enablePlugin(\'' . $name . '\', 1);"><img alt="' . JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PUBLISHED_DESC') . '" src="components/com_jfbconnect/assets/images/icon-16-deny.png"/></a> ' . JText::_('JUNPUBLISHED');
}

?>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <form method="post" id="adminForm" name="adminForm">
                <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_LABEL'); ?></h3>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_DESC'); ?></p>

                <h4><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_CONFIG_CHECK'); ?></h4>

                <?php if (count($this->joomlaErrors))
                {
                    echo '<span class="autotuneBad">' . JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_ERROR_COUNT_LABEL') . '</span>' . JText::sprintf('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_ERROR_COUNT', count($this->joomlaErrors));
                    echo '<ul>';
                    foreach ($this->joomlaErrors as $error)
                        echo '<li>' . $error . '</li>';
                    echo '</ul><br/>';
                } else
                    echo '<p>' . JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_ERROR_NONE') . '</p>';
                ?>
                <h4><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_CHECK'); ?></h4>

                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_CHECK_DESC'); ?></p>

                <table class="table table-striped">
                    <tr>
                        <th><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_LABEL'); ?></th>
                        <th><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_STATUS_LABEL'); ?></th>
                        <th><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_DESC_LABEL'); ?></th>
                    </tr>
                    <tr>
                        <td class="even"><strong>JFBCSystem </strong></td>
                        <td class="even"><?php showPluginStatus('jfbcsystem', $this->JFBCSystemEnabled); ?></td>
                        <td class="even"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_JFBCSYSTEM_DESC'); ?></td>
                    </tr>
                    <tr>
                        <td class="odd"><strong>JFBCAuthentication </strong></td>
                        <td class="odd"><?php showPluginStatus('jfbconnectauth', $this->JFBCAuthenticationEnabled); ?></td>
                        <td class="odd"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_JFBCAUTH_DESC'); ?></td>
                    </tr>
                    <tr>
                        <td class="even"><strong>JFBCUser </strong></td>
                        <td class="even"><?php showPluginStatus('jfbconnectuser', $this->JFBCUserEnabled); ?></td>
                        <td class="even"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_JFBCAUTH_DESC'); ?></td>
                    </tr>
                    <tr>
                        <td class="odd"><strong>JFBCContent </strong></td>
                        <td class="odd"><?php showPluginStatus('jfbccontent', $this->JFBCContentEnabled); ?></td>
                        <td class="odd"><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_JFBCCONTENT_DESC'); ?></td>
                    </tr>
                    <!-- <tr>
                        <td colspan="1" class="even">
                            <button type="input" class="btn btn-primary">Enable All</button>
                        </td>
                        <td colspan="1" class="even">
                            <button type="input" class="btn">Disable All</button>
                        </td>
                    </tr>-->
                </table>
                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_SITECONFIG_PLUGIN_INSTRUCTION'); ?></p>
                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="pluginName" value="" />
                <input type="hidden" name="pluginStatus" value="" />
                <input type="hidden" name="task" value="" />
            </form>
        </div>
    </div>
</div>