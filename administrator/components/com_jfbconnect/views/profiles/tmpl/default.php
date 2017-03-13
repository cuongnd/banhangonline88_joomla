<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>
<div class="sourcecoast">

    <form method="post" id="adminForm" name="adminForm" class="form-horizontal">

        <?php echo JText::_('COM_JFBCONNECT_PROFILES_DESC'); ?>
        <?php
        if (count($this->profilePlugins) > 0)
        {
            $this->tabsStart('profileTabs', $this->profilePlugins[0]->getName());
            foreach ($this->profilePlugins as $p)
            {
                $this->tabStart('profileTabs', $p->getName(), JText::_($p->getName()));
                $this->formDisplay($p->getName());

                echo $p->getFieldMappingHtml();

                $this->tabEnd();
            }
            $this->tabsEnd();
        }
        else
        {
            echo JText::_('COM_JFBCONNECT_PROFILES_NO_PROFILE_PLUGINS');
        }
        ?>

        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="controller" value="profiles" />
        <input type="hidden" name="task" value="" />
        <?php echo JHTML::_('form.token'); ?>

    </form>
</div>