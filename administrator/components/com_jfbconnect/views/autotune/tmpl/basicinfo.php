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
<style>
    .sourcecoast .autotune input[type='text'] {
        width: 290px;
    }
</style>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <form method="post" id="adminForm" name="adminForm">
                <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_BASICINFO_LABEL'); ?></h3>
                <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_BASICINFO_DESC'); ?></p>

                <?php
                $providers = JFBCFactory::getAllProviders();
                for ($i=0; $i < count($providers); $i++) :
                    $provider = $providers[$i];
                    if ($i % 2 == 0)
                    {
                        $divOpen = true;
                        echo '<div class="span12">';
                    }
                $pkey = strtoupper($provider->systemName);
                ?>
                    <div class="span6 well" style="font-size:16px">
                        <div class="span12">
                            <img src="<?php echo JURI::root() . '/media/sourcecoast/images/provider/' . $provider->systemName . '/icon.png'; ?>" />
                            <?php echo JText::_('COM_JFBCONNECT_CONFIG_'.$pkey.'_API_APP_ID_LABEL'); ?>
                            <span style="font-size:12px">(<?php echo JText::_('COM_JFBCONNECT_CONFIG_'.$pkey.'_APP_SETUP_LINK'); ?>)</span>
                        </div>
                        <div class="span12">
                            <input type="text" name="<?php echo $provider->systemName;?>_app_id" size="75" style="font-weight:bold" value="<?php echo $this->config->getSetting($provider->systemName.'_app_id'); ?>" />
                        </div>
                        <div class="span12"><?php echo JText::_('COM_JFBCONNECT_CONFIG_'.$pkey.'_API_SECRET_KEY_LABEL'); ?></div>
                        <div class="span12">
                            <input type="text" name="<?php echo $provider->systemName;?>_secret_key" size="75" style="font-weight:bold" value="<?php echo $this->config->getSetting($provider->systemName.'_secret_key'); ?>" />
                        </div>
                    </div>
                <?php if ($i % 2 == 1)
                {
                    $divOpen = false;
                    echo '</div>';
                }
                endfor;
                if ($divOpen)
                    echo '</div>';
                ?>
                <div class="span12">
                    <div class="span6 well" style="font-size:16px">
                        <div class="span12">
                            <?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_BASICINFO_SC_SUBSCRIBER_ID_LABEL'); ?>
                        </div>
                        <div class="span12">
                            <input type="text" name="subscriberId" size="75" style="font-weight:bold" value="<?php echo $this->subscriberId; ?>" />
                        </div>
                    </div>
                </div>
                <div class="span12" style="text-align:center; font-size:16px">
                    <input type="submit" value="Save" class="btn btn-primary" />
                </div>
                <div class="span12" style="margin-top:10px; text-align:left; font-size:16px">
                    <ul>
                        <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_BASICINFO_SC_ID_DESC'); ?>
                            <ul>
                                <li><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_BASICINFO_SC_ID_DESC2'); ?>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="task" value="saveBasicInfo" />
            </form>
        </div>
    </div>
</div>
