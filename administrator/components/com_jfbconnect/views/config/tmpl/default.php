<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

$providers = JFBCFactory::getAllProviders();

?>
<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm" class="form-horizontal">
        <?php
        $this->tabsStart('myTab', 'config_general');

        $this->tabStart('myTab', 'config_general', JText::_('COM_JFBCONNECT_CONFIG_MENU_GENERAL'));
        $this->formDisplay('config');
        $this->tabEnd();

        foreach ($providers as $p)
        {
            $this->tabStart('myTab', 'config_' . $p->systemName, JText::_('COM_JFBCONNECT_CONFIG_MENU_' . strtoupper($p->name) . '_API'));
            $this->formDisplay($p->systemName);
            $this->tabEnd();
        }

        $this->tabsEnd();
        ?>

        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="controller" value="config" />
        <input type="hidden" name="cid[]" value="0" />
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </form>
</div>