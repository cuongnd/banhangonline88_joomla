<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

$changelogLink = '<a href="https://www.sourcecoast.com/jfbconnect/docs/general/changelog" target="_BLANK" class="btn btn-info">'.JText::_('COM_JFBCONNECT_UPDATES_VIEW_CHANGELOG').'</a>';

?>
<div class="sourcecoast">
    <div class="row-fluid">
        <div class="span12">
            <h2><?php echo JText::_('COM_JFBCONNECT_OVERVIEW_EXTENSION_CHECK'); ?></h2>

            <div class="well" style="background-color: #efe0b5; font-size: 14px">
                <p><strong><?php echo JText::_('COM_JFBCONNECT_UPDATES_JFBCONNECT_VERSION_INSTALLED');?></strong> v<?php echo $this->jfbcVersion; ?></p>

                <?php
                if ($this->jfbcUpdateSiteEnabled)
                {
                    if (is_object($this->jfbcUpdate) && ($this->jfbcUpdate->version != $this->jfbcVersion) &&
                            version_compare($this->jfbcUpdate->version, $this->jfbcVersion, '>')
                    )
                    {
                        ?>
                        <p><strong><?php echo JText::_('COM_JFBCONNECT_UPDATES_JFBCONNECT_VERSION_LATEST');?> v<?php echo $this->jfbcUpdate->version; ?></strong></p>

                        <div class="row-fluid">
                            <div class="span3">
                                <form action="index.php" method="post">
                                    <button type="submit" href="blah.html" class="btn btn-primary">
                                        <?php echo JText::_('COM_JFBCONNECT_UPDATES_JFBCONNECT_VERSION_RECOMMENDED'); echo $this->jfbcUpdate->version; ?>
                                    </button>
                                    <input type="hidden" name="option" value="com_installer" />
                                    <input type="hidden" name="view" value="update" />
                                    <input type="hidden" name="task" value="update.update" />
                                    <input type="hidden" name="cid[]" value="<?php echo $this->jfbcUpdate->update_id; ?>" />
                                    <?php echo JHTML::_('form.token'); ?>
                                </form>
                            </div>
                            <div class="span5">
                                <?php echo $changelogLink; ?>
                            </div>
                        </div>
                        <p><?php echo JText::_('COM_JFBCONNECT_UPDATES_REVIEW_SETTINGS');?></p>
                    <?php }
                    else
                    { ?>
                        <p><?php echo JText::_('COM_JFBCONNECT_UPDATES_JFBCONNECT_LATEST_IS_INSTALLED');?></p>
                        <?php echo $changelogLink; ?>
                    <?php
                    }
                }
                else
                {
                    ?>
                    <p class="warning"><?php echo JText::_('COM_JFBCONNECT_UPDATES_SITE_CONNECTION_ERROR');?><br />
                        <?php echo JText::_('COM_JFBCONNECT_UPDATES_VIEW_CHANGELOG_FOR_UPDATE');?></p>
                    <?php echo $changelogLink; ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div style="clear: both"></div>

<form method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_jfbconnect" />
    <input type="hidden" name="task" value="" />
</form>