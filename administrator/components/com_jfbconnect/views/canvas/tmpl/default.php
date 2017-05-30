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
        width: 150px;
    }

    div.config_option {
        width: 300px;
    }

    div.config_setting_option {
        width: 350px;
    }
</style>

<div class="sourcecoast">
<form method="post" id="adminForm" name="adminForm">
<div>
<?php echo JText::_('COM_JFBCONNECT_CANVAS_TAB_CONFIGURATION'); ?>
<br />
<?php
//print_r($this->canvasProperties);

if (defined('SC30')):
    echo '<div class="row-fluid">';
    echo '<ul class="nav nav-tabs">';
    echo '<li class="active"><a href="#tab_application_settings" data-toggle="tab">' . JText::_('COM_JFBCONNECT_CANVAS_MENU_PAGE_TAB') . '</a></li>';
    echo '<li><a href="#canvas_app_settings" data-toggle="tab">' . JText::_('COM_JFBCONNECT_CANVAS_MENU_CANVAS_APP') . '</a></li>';
    echo '</ul>';
    echo '</div>';
//Begin Tabs
    echo '<div class="tab-content">';
    echo '<div class="tab-pane active" id="tab_application_settings">';
endif; //SC30
if (defined('SC16')):
    jimport('joomla.html.pane');
    $pane = JPane::getInstance('tabs');
    echo $pane->startPane('content-pane');
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_CANVAS_MENU_PAGE_TAB'), 'tab_application_settings');
endif; //SC16

$tabReady = true;
$tabName = $this->canvasProperties->get('page_tab_default_name', "");
if (!$tabName)
{
    $tabName = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
    $tabReady = false;
}
$tabUrl = $this->canvasProperties->get('page_tab_url', "");
if (!$tabUrl)
{
    $tabUrl = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
    $tabReady = false;
}
$secureTabUrl = $this->canvasProperties->get('secure_page_tab_url', "");
if (!$secureTabUrl)
{
    $secureTabUrl = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
    $tabReady = false;
}

$websiteUrl = $this->canvasProperties->get('website_url', '');

?>
<div>
    <div class="config_setting header config_row" style="width:250px"><?php echo JText::_('COM_JFBCONNECT_CANVAS_PAGE_TAB_CONFIG_STATUS'); ?></div>
    <div style="clear:both"></div>
    <div class="config_row">
        <?php if ($tabReady)
        {
            ?>
            <?php echo JText::_('COM_JFBCONNECT_CANVAS_PAGE_TAB_CONFIG_DESC'); ?>
            <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo JFBCFactory::provider('facebook')->appId; ?>&display=popup&next=<?php echo $websiteUrl; ?>"
               target="_BLANK"><?php echo JText::_('COM_JFBCONNECT_CANVAS_PAGE_TAB_CONFIG_DESC1'); ?></a>
        <?php
        } else
        {
            ?>
            <?php echo JText::_('COM_JFBCONNECT_CANVAS_PAGE_TAB_CONFIG_DESC2'); ?>
        <?php } ?>
    </div>
    <br />
</div>
<div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_CANVAS_JOOMLA_DISPLAY_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_CANVAS_OPTIONS_LABEL'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_DISPLAY_TEMPLATE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_DISPLAY_TEMPLATE_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php echo JHTML::_('select.genericlist', $this->templates, 'canvas_tab_template', null, 'directory', 'name', $this->canvasTabTemplate, 'canvas_tab_template'); ?>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_PAGE_AUTOMATIC_RESIZING_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_PAGE_AUTOMATIC_RESIZING_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <fieldset id="canvas_tab_resize_enabled" class="radio btn-group">
                <input type="radio" id="canvas_tab_resize_enabled1" name="canvas_tab_resize_enabled"
                       value="1" <?php echo $model->getSetting('canvas_tab_resize_enabled') == '1' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_tab_resize_enabled1"><?php echo JText::_('JENABLED'); ?></label>
                <input type="radio" id="canvas_tab_resize_enabled0" name="canvas_tab_resize_enabled"
                       value="0" <?php echo $model->getSetting('canvas_tab_resize_enabled') == '0' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_tab_resize_enabled0"><?php echo JText::_('JDISABLED'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <br />
</div>
<?php
if (defined('SC30')):
    echo '</div>';
    echo '<div class="tab-pane" id="canvas_app_settings">';
endif; //SC30
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_CANVAS_MENU_CANVAS_APP'), 'canvas_app_settings');
endif; //SC16

$autoResizingEnabled = $model->getSetting('canvas_canvas_resize_enabled');
$canvasFluidHeight = $this->canvasProperties->get('canvas_fluid_height', false);
if ($canvasFluidHeight)
    $canvasFluidHeight = $autoResizingEnabled ? JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_HEIGHT_FLUID') : JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_HEIGHT_FLUID') . '<br/><span style="color:#FF4444">' . JText::sprintf('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_HEIGHT_FLUID_NEED_AUTORESIZING', JText::_('COM_JFBCONNECT_CANVAS_FIELD_PAGE_AUTOMATIC_RESIZING_LABEL')) . '</span>';
else
    $canvasFluidHeight = JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_HEIGHT_MANUAL') . '<span style="color:#FF4444">' . JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_HEIGHT_MANUAL_WARNING') . '</span>';

$canvasFluidWidth = $this->canvasProperties->get('canvas_fluid_width', false);
$canvasFluidWidth = $canvasFluidWidth ? JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_WIDTH_FLUID') : JText::_('COM_JFBCONNECT_CANVAS_FIELD_CANVAS_WIDTH_FIXED');

$canvasReady = true;
$canvasName = $this->canvasProperties->get('namespace', "");
if (!$canvasName)
{
    $canvasReady = false;
    $canvasName = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
}

$canvasUrl = $this->canvasProperties->get('canvas_url', '');
if ($canvasUrl == "")
{
    $canvasUrl = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
    $canvasReady = false;
}
$secureCanvasUrl = $this->canvasProperties->get('secure_canvas_url', '');
if ($secureCanvasUrl == "")
{
    $secureCanvasUrl = '<span style="color:#FF4444"><b>' . JText::_('COM_JFBCONNECT_CANVAS_WARNING_NOT_SET') . '</b></span>';
    $canvasReady = false;
}

if ($canvasReady)
    $canvasLink = '<a target="_blank" href="http://apps.facebook.com/' . $canvasName . '">https://apps.facebook.com/' . $canvasName . '</a>';
else
    $canvasLink = '';
?>
<div>
    <div class="config_setting header config_row" style="width:250px"><?php echo JText::_('COM_JFBCONNECT_CANVAS_CANVAS_STATUS'); ?></div>
    <div style="clear:both"></div>
    <div class="config_row">
        <?php if ($canvasLink)
        {
            ?>
            <?php echo JText::_('COM_JFBCONNECT_CANVAS_CANVAS_STATUS_DESC'); ?>
            <b><?php echo $canvasLink; ?></b>
        <?php
        } else
        {
            ?>
            <?php echo JText::_('COM_JFBCONNECT_CANVAS_CANVAS_STATUS_DESC2'); ?>
        <?php } ?>
    </div>
    <br />
</div>
<div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_CANVAS_JOOMLA_DISPLAY_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_CANVAS_OPTIONS_LABEL'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_DISPLAY_TEMPLATE_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_DISPLAY_TEMPLATE_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php echo JHTML::_('select.genericlist', $this->templates, 'canvas_canvas_template', null, 'directory', 'name', $this->canvasCanvasTemplate, 'canvas_canvas_template'); ?>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_PAGE_AUTOMATIC_RESIZING_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_CANVAS_FIELD_PAGE_AUTOMATIC_RESIZING_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <fieldset id="canvas_canvas_resize_enabled" class="radio btn-group">
                <input type="radio" id="canvas_canvas_resize_enabled1" name="canvas_canvas_resize_enabled"
                       value="1" <?php echo $model->getSetting('canvas_canvas_resize_enabled') == '1' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_canvas_resize_enabled1"><?php echo JText::_('JENABLED'); ?></label>
                <input type="radio" id="canvas_canvas_resize_enabled0" name="canvas_canvas_resize_enabled"
                       value="0" <?php echo $model->getSetting('canvas_canvas_resize_enabled') == '0' ? 'checked="checked"' : ""; ?> />
                <label for="canvas_canvas_resize_enabled0"><?php echo JText::_('JDISABLED'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <br />
</div>
<?php
if (defined('SC30')):
    echo '</div>';
    echo '</div>'; //tab-content
endif; //SC30
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->endPane();
endif; //SC16
?>
<input type="hidden" name="option" value="com_jfbconnect" />
<input type="hidden" name="controller" value="canvas" />
<input type="hidden" name="cid[]" value="0" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>
</div>
</form>
</div>