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
$model = $this->model;

?>
<script type="text/javascript">
    function toggleHide(rowId, styleType)
    {
        document.getElementById(rowId).style.display = styleType;
    }
</script>
<style type="text/css">
    div.config_setting {
        width: 225px;
    }
    div.config_option {
        width: 250px;
    }
</style>

<?php

echo '<form method="post" id="adminForm" name="adminForm">';

?>
    <div>
        <div class="config_row">
            <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_SETTING_LABEL');?></div>
            <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_OPTIONS_LABEL');?></div>
            <div class="config_description header"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_DESC_LABEL');?></div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip" style="width:100px"
                 title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_DEFAULTS_DESC');?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_DEFAULTS_LABEL');?></div>
            <div class="config_option" style="width:375px">
                <textarea rows="15" cols="43" name="social_graph_fields"><?php echo $model->getSetting('social_graph_fields') ?></textarea>
            </div>
            <div class="config_description" style="min-width:400px; margin-left:10px">
                <?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_DEFAULTS_DESC2');?>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_DEBUGTOOL_DESC');?></div>

    <input type="hidden" name="option" value="com_jfbconnect" />
    <input type="hidden" name="controller" value="opengraph" />
    <input type="hidden" name="cid[]" value="0" />
    <input type="hidden" name="formtype" value="settings" />
    <input type="hidden" name="task" value="apply" />
    <?php echo JHTML::_('form.token'); ?>
</form>
