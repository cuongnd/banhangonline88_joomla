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
        <fieldset id="filter-bar">
            <div class="filter-search pull-left">
                    <input type="text" name="search" id="filter_search" value="<?php echo $this->lists['search'] ?>"
                           class="filter-search btn-group pull-left" title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_FILTER');?>" placeholder="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_FILTER'); ?>"/>
                    <?php
                    $resetJavascript = "document.getElementById('filter_search').value='';";
                    $resetJavascript .= "this.form.submit();";
                    ?>
                <div class="btn-group pull-left">
                    <button class="btn tip" id="jfbcSubmitButton" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_GO');?>">
                        <?php
                        if(defined('SC30')):
                        echo '<i class="icon-search"></i>';
                        endif; //SC30
                        if(defined('SC16')):
                        echo JText::_('COM_JFBCONNECT_BUTTON_GO');
                        endif; //SC16
                        ?>
                    </button>
                    <button class="btn tip" id="jfbcResetButton" onclick="<?php echo $resetJavascript; ?>" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_RESET');?>">
                        <?php
                        if(defined('SC30')):
                        echo '<i class="icon-remove"></i>';
                        endif; //SC30
                        if(defined('SC16')):
                        echo JText::_('COM_JFBCONNECT_BUTTON_RESET');
                        endif; //SC16
                        ?>
                    </button>
                </div>
            </div>
            <div class="filter-select pull-right">
                    <?php echo $this->lists['state']; ?>
                    <?php echo $this->lists['object']; ?>
                    <?php echo $this->lists['action']; ?>
            </div>
        </fieldset>
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_USER_LABEL'), 'user_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_ACTION_LABEL'), 'action_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_OBJECT_LABEL'), 'object_id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_STATUS_LABEL'), 'status', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_URL_LABEL'), 'url', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_OPENGRAPH_ACTIVITYLIST_FB_ERROR_MSG_LABEL'), 'response', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_CREATED'), 'created', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $key = 0;
            foreach ($this->rows as $row) :
                $key++;
                ?>
            <tr class="row<?php echo ($key % 2); ?>">
                <td><?php echo $key; ?></td>
                <?php
                $user = JFactory::getUser($row->user_id);
                $object = $this->objectModel->getObject($row->object_id);
                $action = $this->actionModel->getAction($row->action_id);
                if ($row->status == OG_ACTIVITY_DELETED)
                    $status = JText::_('COM_JFBCONNECT_OPENGRAPH_DELETED');
                else if ($row->status == OG_ACTIVITY_PUBLISHED)
                    $status = JText::_('JPUBLISHED');
                else if ($row->status == OG_ACTIVITY_ERROR)
                    $status = JText::_('COM_JFBCONNECT_OPENGRAPH_ERROR');

                $uri = new JURI($row->url);
                ?>
                <td><?php if($status != JText::_('JPUBLISHED')) echo $checked = JHTML::_('grid.id', $key, $row->id); ?></td>
                <td><?php echo $user->get('username'); ?></td>
                <td class="center"><?php echo $action->display_name ?></td>
                <td class="center"><?php echo $object->display_name ?></td>
                <td class="center"><?php echo $status ?></td>
                <td class="center"><a href="<?php echo $row->url ?>" target="_BLANK"><?php echo $uri->toString(array('path', 'query', 'fragment')); ?></a></td>
                <td class="center"><?php if ($row->status == OG_ACTIVITY_ERROR) echo $row->response ?></td>
                <td class="center"><?php echo $row->created ?></td>
            </tr>
                <?php endforeach;

            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="9"><?php echo $this->page->getListFooter(); ?></td>
            </tr>
            </tfoot>
        </table>

        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>"/>
        <input type="hidden" name="formtype" value="activity"/>
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
