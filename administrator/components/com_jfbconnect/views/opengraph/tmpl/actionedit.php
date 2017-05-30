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
<style type="text/css">
    div.current .inline {
        float: none;
    }
</style>
<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">
        <div class="row-fluid">
            <div class="span7">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_SETTINGS'); ?></legend>
                    <ul class="adminformlist">

                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_TITLE_DESC'); ?>"
                                   for="display_name"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_TITLE_LABEL'); ?></label>
                            <input id="display_name" type="text" size="20" name="display_name"
                                   value="<?php echo $this->action->display_name; ?>"
                                   placeholder="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_TITLE_PLACEHOLDER'); ?>">
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_ACTION_DESC'); ?>"
                                   for="action"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_ACTION_LABEL'); ?></label>
                            <input id="action" type="text" size="20" name="action" value="<?php echo $this->action->action; ?>"
                                   placeholder="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_ACTION_PLACEHOLDER'); ?>">
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_PUBLISHED_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_PUBLISHED_LABEL'); ?></label>
                            <select name="published">
                                <option value="1" <?php echo $this->action->published ? 'selected' : "" ?> ><?php echo JText::_('JPUBLISHED'); ?></option>
                                <option value="0" <?php echo $this->action->published ? "" : 'selected' ?> ><?php echo JText::_('JUNPUBLISHED'); ?></option>
                            </select>
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_BUILTIN_DESC'); ?>"
                                   for="fb_built_in"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_BUILTIN_LABEL'); ?></label>
                            <input id="fb_built_in" type="checkbox" name="fb_built_in"
                                   value="1" <?php echo $this->action->fb_built_in ? "checked" : "" ?> />
                        </li>
                    </ul>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_CAPABILITIES_LABEL');?></legend>
                    <p><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_CAPABILITIES_DESC');?></p>
                    <ul class="adminformlist">
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_FRIEND_TAGS_TITLE');?>"
                                   for="allows_tags"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_FRIEND_TAGS_LABEL');?></label>
                            <input id="allows_tags" type="checkbox" name="params[og_capabilities][tags]"
                                   value="1" <?php echo $this->action->params->get('og_capabilities.tags', 0) == "1" ? "checked" : ""; ?> >
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_USER_MESSAGES_TITLE');?>"
                                   for="allows_messages"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_USER_MESSAGES_LABEL');?></label>
                            <input id="allows_messages" type="checkbox" name="params[og_capabilities][messages]"
                                   value="1" <?php echo $this->action->params->get('og_capabilities.messages', 0) == "1" ? "checked" : ""; ?>>
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_PLACE_TITLE');?>"
                                   for="allows_places"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_PLACE_LABEL');?></label>
                            <input id="allows_places" type="checkbox" name="params[og_capabilities][places]"
                                   value="1" <?php echo $this->action->params->get('og_capabilities.places', 0) == "1" ? "checked" : ""; ?>>
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_EXPLICITLY_SHARED_TITLE');?>"
                                   for="explicitly_shared"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_EXPLICITLY_SHARED_LABEL');?></label>
                            <input id="explicitly_shared" type="checkbox" name="params[og_capabilities][explicitly_shared]"
                                   value="1" <?php echo $this->action->params->get('og_capabilities.explicitly_shared', 0) == "1" ? "checked" : ""; ?>>
                        </li>


                </fieldset>

                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_OBJECT_ASSOCIATIONS_LABEL'); ?></legend>
                    <p><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_OBJECT_ASSOCIATIONS_DESC'); ?></p>
                    <ul>
                        <?php foreach ($this->objects as $object)
                        {
                            $checked = $this->action->isAssociatedTo($object) ? 'checked="checked"' : "";
                            echo '<li style="clear:left">';
                            echo '<input style="margin:10px" id="objects_' . $object->id . '" type="checkbox" name="objects[]" value="' . $object->id . '" ' . $checked . '>';
                            echo '<label style="clear:none" for="objects_' . $object->id . '" > <strong>' . $object->display_name . '</strong> [' . $object->plugin . ' - ' . $object->system_name . ']</label>';
                            echo '</li>';
                        }?>
                    </ul>
                </fieldset>
            </div>
            <div class="span5">
                <?php
                #echo JHtml::_('sliders.start', 'ogaction-sliders-' . $this->action->id, array('useCookie' => 1));
                #echo JHtml::_('sliders.panel', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_BASIC_SETTINGS'), 'Action');
                ?>
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_BASIC_SETTINGS'); ?></legend>
                    <dl>
                        <dt><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_UNIQUE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_UNIQUE_LABEL'); ?></label>
                        </dt>
                        <dd><select name="params[og_unique_action]">
                                <option value="1" <?php echo $this->action->params->get('og_unique_action') == "1" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_UNIQUE_SELECT_ONETIME'); ?></option>
                                <option value="0" <?php echo $this->action->params->get('og_unique_action') == "0" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_UNIQUE_SELECT_MULTIPLE'); ?></option>
                            </select>
                        </dd>
                        <dt><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_LABEL'); ?></label>
                        </dt>
                        <dd><input type="text" size="4"
                                   name="params[og_interval_duration]"
                                   value="<?php echo $this->action->params->get('og_interval_duration'); ?>"
                                   style="width:30px">
                            <select style="width:157px" name="params[og_interval_type]">
                                <option value="SECOND" <?php echo $this->action->params->get('og_interval_type') == "SECOND" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_SECONDS'); ?></option>
                                <option value="MINUTE" <?php echo $this->action->params->get('og_interval_type') == "MINUTE" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_MINUTES'); ?></option>
                                <option value="HOUR" <?php echo $this->action->params->get('og_interval_type') == "HOUR" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_HOURS'); ?></option>
                                <option value="DAY" <?php echo $this->action->params->get('og_interval_type') == "DAY" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_DAYS'); ?></option>
                                <option value="WEEK" <?php echo $this->action->params->get('og_interval_type') == "WEEK" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_WEEKS'); ?></option>
                                <option value="MONTH" <?php echo $this->action->params->get('og_interval_type') == "MONTH" ? "selected" : ""; ?> ><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_MULTIPLE_ACTION_FREQ_SELECT_MONTHS'); ?></option>
                            </select>
                        </dd>
                        <dt><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_CAN_DISABLE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_CAN_DISABLE_LABEL'); ?></label>
                        </dt>
                        <dd><select type="radio" name="can_disable">
                                <option value="1" <?php echo $this->action->can_disable == "1" ? "selected" : ""; ?> ><?php echo JText::_('JYES'); ?></option>
                                <option value="0" <?php echo $this->action->can_disable == "0" ? "selected" : ""; ?> ><?php echo JText::_('JNO'); ?></option>
                            </select>
                        </dd>
                    </dl>
                </fieldset>
                <?php
                #echo JHtml::_('sliders.panel', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOMATIC_ACTIONS'), 'Action');
                ?>

                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOMATIC_ACTIONS'); ?></legend>
                    <dl>
                        <dt><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOTYPE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOTYPE_LABEL'); ?></label>
                        </dt>
                        <dd><select name="params[og_auto_type]">
                                <option value="none" <?php echo $this->action->params->get('og_auto_type') == "none" ? "selected" : ""; ?>><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOTYPE_SELECT_MANUAL'); ?></option>
                                <option value="page_load" <?php echo $this->action->params->get('og_auto_type') == "page_load" ? "selected" : ""; ?>><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_AUTOTYPE_SELECT_ONPAGELOAD'); ?></option>
                            </select></dd>
                    </dl>
                    <dl>
                        <dt><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_ONPAGELOAD_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_ONPAGELOAD_LABEL'); ?></label>
                        </dt>
                        <dd><input type="text" name="params[og_auto_timer]" value="<?php echo $this->action->params->get('og_auto_timer'); ?>" /></dd>
                    </dl>
                </fieldset>
                <?php
                /*foreach ($this->params->getFieldsets() as $fieldsets => $fieldset):
                    echo JHtml::_('sliders.panel', $fieldset->name, $fieldset->name); ?>
                    <fieldset class="panelform">
                        <dl>
                            <?php
                            // Iterate through the fields and display them.
                            foreach ($this->params->getFieldset($fieldset->name) as $field):
                                // If the field is hidden, only use the input.
                                if ($field->hidden):
                                    echo $field->input; else:
                                    ?>
                                    <dt>
                                        <?php echo $field->label; ?>
                                    </dt>
                                    <dd>
                                        <?php echo $field->input ?>
                                    </dd>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                        </dl>
                    </fieldset>
                    <?php
                endforeach;*/
                #echo JHtml::_('sliders.end');
                ?>
            </div>
        </div>
        <input type="hidden" name="plugin" value="<?php echo $this->action->plugin ?>" />
        <input type="hidden" name="system_name" value="<?php echo $this->action->system_name ?>" />

        <input type="hidden" name="id" value="<?php echo $this->action->id; ?>" />
        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="controller" value="opengraph" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="formtype" value="action" />
        <?php echo JHTML::_('form . token'); ?>

    </form>
</div>