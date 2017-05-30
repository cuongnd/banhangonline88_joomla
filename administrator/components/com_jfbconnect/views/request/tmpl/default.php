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
        <table class="jfbcAdminTableFilters">
            <tr>
                <td class="jfbcAdminTableFiltersSearch">
                    <input type="text" name="search" id="search" value="<?php echo $this->lists['search'] ?>" class="filter-search btn-group pull-left"
                           title="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>" placeholder="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>" />

                    <div class="btn-group pull-left">
                        <button class="btn tip" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_GO'); ?>" id="jfbcSubmitButton">
                            <?php
                            if (defined('SC30')):
                                echo '<i class="icon-search"></i>';
                            endif; //SC30
                            if (defined('SC16')):
                                echo JText::_('COM_JFBCONNECT_BUTTON_GO');
                            endif; //SC16
                            ?>
                        </button>
                        <?php
                        $resetJavascript = "document.getElementById('search').value='';";
                        $resetJavascript .= "document.getElementById('filter_published').value='-1';";
                        $resetJavascript .= "this.form.submit();";
                        ?>
                        <button class="btn tip" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_RESET'); ?>" id="jfbcResetButton"
                                onclick="<?php echo $resetJavascript; ?>">
                            <?php
                            if (defined('SC30')):
                                echo '<i class="icon-remove"></i>';
                            endif; //SC30
                            if (defined('SC16')):
                                echo JText::_('COM_JFBCONNECT_BUTTON_RESET');
                            endif; //SC16
                            ?>
                        </button>
                    </div>
                </td>
                <td class="jfbcAdminTableFiltersSelects">
                    <?php echo $this->lists['published']; ?>
                </td>
            </tr>
        </table>
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_REQUESTS_TITLE_TITLE'), 'title', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('JPUBLISHED'), 'published', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_REQUESTS_TITLE_SENDCOUNT'), 'send_count', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_REQUESTS_TITLE_DESTINATION_URL'), 'destination_url', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <?php if ($this->canvasEnabled) : ?>
                    <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_REQUESTS_TITLE_BREAKOUT_CANVAS'), 'breakout_canvas', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
                <?php endif; ?>
                <th><?php echo JHTML::_('grid.sort', JText::_('COM_JFBCONNECT_REQUESTS_TITLE_ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($this->rows)
                foreach ($this->rows as $key => $row): ?>
                    <tr class="row<?php echo($key % 2); ?>">
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $checked = JHTML::_('grid.id', $key, $row->id); ?></td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=request&task=edit&cid=' . $row->id); ?>"><?php echo $row->title; ?></a>
                        </td>
                        <td class="center"><?php echo JHTML::_('grid.published', $row, $key) ?></td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&requestid=' . $row->id); ?>"><?php echo $row->send_count ?></a>
                        </td>
                        <td><?php echo $row->destination_url; ?></td>
                        <?php if ($this->canvasEnabled) : ?>
                            <td>
                                <?php if ($row->breakout_canvas)
                                {
                                    ?>
                                    <a title=<?php echo JText::_("COM_JFBCONNECT_REQUEST_DISABLE_BREAKOUT_CANVAS"); ?> onclick="return listItemTask('cb<?php echo $key;?>','disable_breakout_canvas')" href="javascript:void(0);">
                                <img border="0" alt="<?php echo JText::_('COM_JFBCONNECT_REQUESTS_ALT_BREAKOUT_ENABLED'); ?>"
                                     src="components/com_jfbconnect/assets/images/icon-16-allow.png">
                            </a>
                        <?php } else
                                {
                                    ?>
                                    <a title=<?php echo JText::_("COM_JFBCONNECT_REQUEST_ENABLE_BREAKOUT_CANVAS"); ?> onclick="return listItemTask('cb<?php echo $key;?>','enable_breakout_canvas')" href="javascript:void(0);">
                                <img border="0" alt="<?php echo JText::_('COM_JFBCONNECT_REQUESTS_ALT_BREAKOUT_DISABLED'); ?>"
                                     src="components/com_jfbconnect/assets/images/icon-16-deny.png">
                            </a>
                        <?php } ?>
                            </td>
                        <?php endif; ?>
                        <td><?php echo $row->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="8"><?php echo $this->page->getListFooter(); ?></td>
            </tr>
            </tfoot>
        </table>

        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="view" value="request" />
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
        <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
