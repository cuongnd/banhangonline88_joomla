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
JHTML::_('behavior.tooltip');
?>

<style type="text/css">
    div.config_setting {
        width: 150px;
    }

    div.config_option {
        width: 450px;
    }
    .sourcecoast li {
        list-style-type:none;
    }
</style>

<div class="sourcecoast">
    <form action="index.php" method="post" name="adminForm" id="adminForm">
        <div class="row-fluid">
            <div class="pull-left span7">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_JFBCONNECT_REQUEST_REQUEST_SETTING'); ?></legend>
                    <ul class="adminformlist">
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_REQUEST_TITLE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_REQUEST_TITLE_LABEL'); ?>
                            </label>
                            <input id="title" type="text" size="60" name="title" maxlength="50"
                                   value="<?php echo $this->request->title; ?>">
                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_REQUEST_MESSAGE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_REQUEST_MESSAGE_LABEL'); ?>
                            </label>
                            <textarea id="message" name="message" rows="3" cols="55"
                                      maxlength="250"><?php echo $this->request->message; ?></textarea>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_REQUEST_DESTINATION_URL_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_REQUEST_DESTINATION_URL_LABEL"); ?>
                            </label>
                            <input id="destination_url" type="text" size="60" maxlength="250" name="destination_url"
                                   value="<?php echo $this->request->destination_url; ?>">

                        </li>
                        <li><label class="hasTip"
                                   title="<?php echo JText::_('COM_JFBCONNECT_REQUEST_THANKYOU_URL_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_REQUEST_THANKYOU_URL_LABEL"); ?>
                            </label>
                            <input id="thanks_url" type="text" size="60" maxlength="250" name="thanks_url"
                                   value="<?php echo $this->request->thanks_url; ?>">
                        </li>
                        <?php if ($this->canvasEnabled) : ?>
                            <li><label class="hasTip"
                                       title='<?php echo JText::_("COM_JFBCONNECT_REQUEST_REDIRECT_DESC"); ?>'><?php echo JText::_("COM_JFBCONNECT_REQUEST_REDIRECT_LABEL"); ?>
                                </label>
                                <input id="breakout_canvas" type="checkbox" name="breakout_canvas"
                                <?php echo $this->request->breakout_canvas ? 'checked="checked"' : ""; ?>">
                            </li>
                        <?php endif; ?>
                        <li><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_OPENGRAPH_ACTION_PUBLISHED_LABEL"); ?></label>
                            <select name="published">
                                <option value="1" <?php echo $this->request->published ? 'selected' : ""; ?> ><?php echo JText::_('JPUBLISHED'); ?></option>
                                <option value="0" <?php echo !$this->request->published ? 'selected' : ''; ?> ><?php echo JText::_('JUNPUBLISHED'); ?></option>
                            </select>
                        </li>
                    </ul>
                </fieldset>

            </div>
            <div style="span4">
                <fieldset class="adminform">
                    <dl>
                        <dt><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_REQUEST_REQUEST_ID"); ?></label></dt>
                        <dd><p><?php echo $this->request->id ?></p></dd>
                        <dt><label class="hasTip"><?php echo JText::_("JGLOBAL_CREATED"); ?></dt>
                        <dd><p><?php echo $this->request->created; ?></p></dd>
                        <dt><label class="hasTip"><?php echo JText::_("JGLOBAL_MODIFIED"); ?>
                                <dd><p><?php echo $this->request->modified; ?></p></dd>
                    </dl>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo JText::_("COM_JFBCONNECT_REQUEST_NOTIFICATIONS"); ?></legend>
                    <dl>
                        <dt><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_REQUEST_TOTAL"); ?></label></dt>
                        <dd><p><?php echo $this->totalNotifications; ?></p></dd>
                        <dt><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_REQUEST_PENDING"); ?></label></dt>
                        <dd><p><?php echo $this->pendingNotifications; ?></p></dd>
                        <dt><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_REQUEST_READ"); ?></label></dt>
                        <dd><p><?php echo $this->readNotifications; ?></p></dd>
                        <dt><label class="hasTip"><?php echo JText::_("COM_JFBCONNECT_REQUEST_EXPIRED"); ?></label></dt>
                        <dd><p><?php echo $this->expiredNotifications; ?></p></dd>
                        <dt>
                            <a
                                    href="<?php echo JRoute::_('index.php?option=com_jfbconnect&controller=notification&task=display&requestid=' . $this->request->id); ?>"><?php echo JText::_("COM_JFBCONNECT_REQUEST_SEE_ALL"); ?></a>
                        </dt>
                    </dl>
                </fieldset>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <p><?php echo JText::_("COM_JFBCONNECT_REQUEST_DESC"); ?></p>

                <p><strong><?php echo JText::_("COM_JFBCONNECT_REQUEST_REQUEST_TAG_EXAMPLES"); ?></strong>
                <ul class="adminformlist">
                    <?php $tagId = empty($this->request->id) ? "XX" : $this->request->id; ?>
                    <li><p>{JFBCRequest request_id=<?php echo $tagId; ?> link_text=Invite Friends}</p></li>
                    <li>{JFBCRequest request_id=<?php echo $tagId; ?> link_image=/your-invitation-image.png}</li>
                </ul>
                </p>
            </div>
        </div>
        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="view" value="request" />
        <input type="hidden" name="task" value="apply" />
        <input type="hidden" name="id" value="<?php echo $this->request->id; ?>" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>