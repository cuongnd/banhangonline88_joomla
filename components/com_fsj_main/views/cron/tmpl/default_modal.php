<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

?>
<div class='fsj'>

<?php echo FSJ_Page::Popup_Begin($this->cron->event . " - " . date("Y-m-d H:i:s") . " - " . ($this->success ? "Success" : "Fail")); ?>

<h4><?php echo $this->result; ?></h4>
<?php echo $this->log; ?>

<?php 
echo FSJ_Page::Popup_End();
?>

</div>