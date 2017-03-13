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
        <table width="100%">
            <tbody>
            <tr>
                <td>
                    <table class="adminform table table-striped">
                        <tbody>
                        <tr>
                            <td><label><?php echo JText::_("COM_JFBCONNECT_REQUEST_RECEIVING_USERS");?>:</label></td>
                            <td><strong><?php echo JText::sprintf("COM_JFBCONNECT_REQUEST_USERS", $this->totalUsers);?></strong></td>
                        </tr>
                        <tr>
                            <td width="150"><label><?php echo JText::_("COM_JFBCONNECT_REQUEST_MESSAGE_LABEL");?>:</label></td>
                            <td><?php echo $this->request->message; ?></td>
                        </tr>
                        <tr>
                            <td><label><?php echo JText::_("COM_JFBCONNECT_REQUEST_DESTINATION_URL_LABEL");?>:</label></td>
                            <td><?php echo $this->request->destination_url; ?></td>
                        </tr>
                        <tr>
                            <td><label><?php echo JText::_("COM_JFBCONNECT_REQUEST_REDIRECT_LABEL");?></label></td>
                            <td><?php echo $this->request->breakout_canvas ? 'Yes' : "No"; ?><br/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div id="canvasNotice"
                                     style="display:<?php echo $this->request->breakout_canvas ? "visible" : "none" ?>">
                                    <?php echo JText::_("COM_JFBCONNECT_REQUEST_CANVAS_NOTICE");?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="width:100%" id="sendStatus">
            <?php if ($this->sendToAll) : ?>
            <?php echo JText::_("COM_JFBCONNECT_REQUEST_SEND_STATUS");?>
            <?php endif; ?>
            <div style="text-align:center"><input type="button" class="btn btn-primary" onclick='if (confirm("<?php echo JText::sprintf('COM_JFBCONNECT_REQUEST_SEND_STATUS_DESC', $this->totalUsers);?>")) jfbcAdmin.request.send(true);' value="<?php echo JText::sprintf('COM_JFBCONNECT_REQUEST_SEND_NOTIFICATIONS', $this->totalUsers);?>" /></div>
        </div>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="id" value="<?php echo $this->request->id; ?>"/>
        <?php if (!$this->sendToAll){
                foreach ($this->fbIds as $fbId)
                    echo '<input type="hidden" name="fbIds[]" value="'.$fbId.'"/>';
            }
            echo JHTML::_('form.token'); ?>
        <br/><br/>
    </form>
</div>
