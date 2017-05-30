<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="sourcecoast">
    <form action="index.php" method="post" name="adminForm" id="adminForm">
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th class="title"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIONS_TITLE_LABEL')?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIONS_EXTENSION_LABEL');?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIONS_ACTION_LABEL');?></th>
                <th><?php echo JText::_('JPUBLISHED');?></th>
                <th><?php echo JText::_('JGLOBAL_MODIFIED');?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIONS_ID_LABEL');?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $key = 0;
            if (count($this->actions) > 0)
            {

                foreach ($this->actions as $action) :
                    $key++;
                    if($action->plugin != "")
                        $pluginState = JPluginHelper::isEnabled('opengraph', $action->plugin)? "" : JText::_('COM_JFBCONNECT_OPENGRAPH_PLUGINS_DISABLED_WARNING');
                    ?>
                <tr class="row<?php echo ($key % 2); ?>">
                    <td><?php echo $key; ?></td>
                    <td><?php echo $checked = JHTML::_('grid.id', $key, $action->id); ?></td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=opengraph&task=actionedit&id=' . $action->id); ?>"><?php echo $action->display_name; ?></a>
                    <td class="center"><?php echo $action->plugin == "" ? "Custom" : (ucwords($action->plugin).$pluginState); ?></td>
                    <td class="center"><?php echo $action->action ?></td>
                    <td class="center"><?php echo JHTML::_('grid.published', $action, $key) ?></td>
                    <td class="center"><?php echo $action->modified ?></td>
                    <td class="center"><?php echo $action->id ?></td>
                </tr>
                    <?php endforeach;
            }
            ?>
            </tbody>
            <!-- <tfoot>
            <tr>
                <td colspan="8"><?php  #echo $this->page->getListFooter(); ?></td>
            </tr>
            </tfoot>-->
        </table>

        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>"/>
        <input type="hidden" name="formtype" value="action"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
