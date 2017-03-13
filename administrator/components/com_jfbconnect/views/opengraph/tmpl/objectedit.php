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

?>
<style type="text/css">
    div.current .inline {
        float: none;
    }
</style>

<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">
        <div class=row-fluid>
            <div class="span7">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_OBJECT_SETTINGS_LABEL'); ?></legend>
                    <ul class="adminformlist">
                        <li><label class="readonly"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_EXTENSION_LABEL'); ?></label> <input type="text"
                                                                                                                                                  class="readonly"
                                                                                                                                                  readonly="readonly"
                                                                                                                                                  name="plugin"
                                                                                                                                                  value="<?php echo $this->object->plugin; ?>">
                        </li>
                        <li><label class="readonly"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_LAYOUT_LABEL'); ?></label> <input type="text"
                                                                                                                                               class="readonly"
                                                                                                                                               readonly="readonly"
                                                                                                                                               name="system_name"
                                                                                                                                               value="<?php echo $this->object->system_name; ?>">
                        </li>
                        <li><label for="display_name" class="required hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_NAME_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_NAME_LABEL'); ?></label>
                            <input id="display_name" class="inputbox" type="text" name="display_name"
                                   value="<?php echo $this->object->display_name; ?>"</li>
                        <li><label for="type" class="required hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_TYPE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_TYPE_LABEL'); ?></label>
                            <input id="type" class="inputbox" type="text" name="type" value="<?php echo $this->object->type; ?>"
                                   placeholder="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_TYPE_PLACEHOLDER'); ?>" /></li>
                        <li><label for="fb_built_in" class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_BUILTIN_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_BUILTIN_LABEL'); ?></label>
                            <input id="fb_built_in" class="checkbox" type="checkbox" name="fb_built_in"
                                   value="1" <?php echo $this->object->fb_built_in ? "checked" : "" ?> />
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_STATUS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_STATUS_LABEL'); ?></label>
                            <select name="published">
                                <option value="1" <?php echo $this->object->published ? 'selected' : "" ?> ><?php echo JText::_('JPUBLISHED'); ?></option>
                                <option value="0" <?php echo $this->object->published ? "" : 'selected' ?> ><?php echo JText::_('JUNPUBLISHED'); ?></option>
                            </select>
                        </li>
                    </ul>
                </fieldset>
            </div>
            <?php if ($this->params)
            {
                echo '<div class="span5">';
                foreach ($this->params->getFieldsets() as $fieldsets => $fieldset)
                {
                    ?>
                    <fieldset class="adminform">
                        <legend><?php echo $fieldset->name; ?></legend>
                        <dl>
                            <?php
                            // Iterate through the fields and display them.
                            foreach ($this->params->getFieldset($fieldset->name) as $field)
                            {
                                // If the field is hidden, only use the input.
                                if ($field->hidden)
                                {
                                    echo $field->input;
                                } else
                                {
                                    ?>
                                    <dt>
                                        <?php echo $field->label; ?>
                                    </dt>
                                    <dd>
                                        <?php echo $field->input ?>
                                    </dd>
                                <?php
                                }
                            }
                            ?>
                        </dl>
                    </fieldset>
                <?php
                }
                echo '</div>';
                ?>
            <?php } ?>
        </div>
        <input type="hidden" name="plugin" value="<?php echo $this->object->plugin ?>" />
        <input type="hidden" name="system_name" value="<?php echo $this->object->system_name ?>" />

        <input type="hidden" name="id" value="<?php echo $this->object->id; ?>" />
        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="controller" value="opengraph" />
        <input type="hidden" name="formtype" value="object" />
        <input type="hidden" name="task" value="" />
        <?php echo JHTML::_('form.token'); ?>

    </form>
</div>