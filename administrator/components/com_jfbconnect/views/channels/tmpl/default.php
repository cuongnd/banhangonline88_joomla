<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
?>

<div class="sourcecoast">
    <form action="index.php" method="post" name="adminForm" id="adminForm">
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th></th>
                <th class="title"><?php echo JText::_('COM_JFBCONNECT_CHANNEL_TITLE_LABEL') ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_CHANNEL_PROVIDER_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_CHANNEL_TYPE_LABEL'); ?></th>
                <th><?php echo JText::_('JPUBLISHED'); ?></th>
                <th><?php echo JText::_('JGLOBAL_MODIFIED'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_CHANNEL_ID_LABEL'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $key = 0;
            if ($this->items && count($this->items) > 0)
            {

                foreach ($this->items as $channel) :
                    $key++;
                    ?>
                    <tr class="row<?php echo($key % 2); ?>">
                        <td><?php echo $key; ?></td>
                        <td><?php echo $checked = JHTML::_('grid.id', $key, $channel->id); ?></td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&task=channel.edit&id=' . $channel->id); ?>"><?php echo $channel->title; ?></a>
                        </td>
                        <td class="center"><img src="<?php echo JURI::root() . '/media/sourcecoast/images/provider/' . $channel->provider; ?>/icon.png" /></td>
                        <td class="center"><?php echo ucwords($channel->type) ?></td>
                        <td class="center"><?php echo JHTML::_('jgrid.published', $channel->published, $key, 'channels.') ?></td>
                        <td class="center"><?php echo $channel->modified ?></td>
                        <td class="center"><?php echo $channel->id ?></td>
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

        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
        <input type="hidden" name="formtype" value="channel" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
