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
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <form method="post" id="adminForm" name="adminForm">

                <iframe src="<?php echo $this->iframeUrl ?>" width="99%" height="500px"></iframe>
                <input type="hidden" name="option" value="com_jfbconnect" />
                <input type="hidden" name="view" value="autotune" />
                <input type="hidden" name="task" value="startAutoTune" />
            </form>
        </div>
    </div>
</div>
<div class="clear:both"></div>