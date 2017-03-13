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
<h1><?php echo JText::_('COM_JFBCONNECT_TIMELINE_SETTINGS_TITLE'); ?></h1>
<p><?php echo JText::_('COM_JFBCONNECT_TIMELINE_SETTINGS_DESC'); ?></p>
<p><?php echo JText::_('COM_JFBCONNECT_TIMELINE_SETTINGS_INSTRUCTIONS'); ?></p>
<form method="post" class="og_action_settings">
    <?php
    if (count($this->actions) == 0)
        echo '<div class="row"><strong>' . JText::_('COM_JFBCONNECT_TIMELINE_SETTINGS_NOACTIONS') . '</strong></div>';
    else
    {
        foreach ($this->actions as $action) :
            ?>
            <div class="row">
                <?php
                $actId = $action->id;
                if (is_object($this->actionsDisabled) && property_exists($this->actionsDisabled, $actId) && ($this->actionsDisabled->$actId == 1))
                    $checked = "";
                else
                    $checked = 'checked="checked"';
                ?>
                <input id="action_<?php echo $action->id; ?>" type="checkbox" name="allowed_actions[<?php echo $action->id; ?>]" value="1"
                        <?php echo $checked; ?>/><label
                        for="action_<?php echo $action->id; ?>"><?php echo $action->display_name ?></label>
            </div>
        <?php
        endforeach;
        echo '<p><input type="submit" class="btn btn-primary" value="' . JText::_('COM_JFBCONNECT_TIMELINE_UPDATESETTINGS') . '" /></p>';
    }
    ?>
    <input type="hidden" name="option" value="com_jfbconnect" />
    <input type="hidden" name="task" value="opengraph.saveSettings" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<p>
    <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=activity'); ?>"><?php echo JText::_('COM_JFBCONNECT_TIMELINE_VIEWACTIVITY'); ?></a>
</p>