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
<h1><?php echo JText::_('COM_JFBCONNECT_TIMELINE_ACTIVITY_TITLE'); ?></h1>
<p><?php echo JText::_('COM_JFBCONNECT_TIMELINE_ACTIVITY_DESC'); ?></p>
<p><?php echo JText::_('COM_JFBCONNECT_TIMELINE_DELETE_DESC'); ?></p>
<form action="<?php echo JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=activity'); ?>" method="post" name="adminForm">
    <div class="og_activity">
        <?php
        if (count($this->rows) == 0)
            echo '<div class="row"><strong>' . JText::_('COM_JFBCONNECT_TIMELINE_NOACTIVITY') . '</strong></div>';

        else
        {
            foreach ($this->rows as $row) :
                $user = JFactory::getUser($row->user_id);
                $object = $this->objectModel->getObject($row->object_id);
                $action = $this->actionModel->getAction($row->action_id);
                ?>

                <div class="row">
                    <?php echo $action->display_name ?> : <a
                            href="<?php echo $row->url; ?>"><?php echo $object->display_name ?></a>
                    <?php echo JText::_('COM_JFBCONNECT_TIMELINE_POSTEDON'); ?><?php echo strftime("%Y-%m-%d", strtotime($row->created)) ?>
                    <a href="<?php echo JURI::base(true); ?>/index.php?option=com_jfbconnect&task=opengraph.userdelete&actionid=<?php echo $row->id; ?>&<?php echo JSession::getFormToken() ?>=1">
                        <img src="<?php echo JURI::base(true); ?>/media/sourcecoast/images/icon-16-deny.png" />
                    </a>
                </div>
            <?php endforeach;
        }
        ?>
    </div>
    <div class="pagination">
        <?php echo $this->pagination->getListFooter(); ?>
    </div>
</form>
<p>
    <a href="<?php echo JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=settings'); ?>"><?php echo JText::_('COM_JFBCONNECT_TIMELINE_CHANGESETTINGS'); ?></a>
</p>