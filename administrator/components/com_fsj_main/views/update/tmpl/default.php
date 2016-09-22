<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

	<h1>Your upgrade has been completed.</h1>
	<h4>The log of this process is below.</h4>
<?php $logno = 1; ?>

<?php foreach ($this->log as &$comp) : ?>
	<?php if (!$comp) continue; ?>
	<?php if (count($comp['log']) < 1) continue; ?>
	<?php $logno++; ?>
	<div>
	<div style="margin:4px;font-size:125%;">
			<a href="#" onclick="ToggleLog('log<?php echo $logno; ?>');return false;">+<?php echo $comp['name']; ?></a>
		</div>
		<div id="log<?php echo $logno; ?>" style="display:none;margin-left:16px;">
<?php foreach ($comp['log'] as &$log): ?>
	<?php //if (!is_array($log['log'])) continue; ?>
	<?php //if (count($log['log']) < 1) continue; ?>
	<?php //if ($log['log'] == "") continue; ?>
		<?php $logno++; ?>
			<div style="margin:4px;font-size:115%;">
				<a href="#" onclick="ToggleLog('log<?php echo $logno; ?>');return false;">+<?php echo $log['name']; ?></a>
			</div>
			<div id="log<?php echo $logno; ?>" style="display:none;">
				<pre style="margin-left: 20px;border: 1px solid black;padding: 2px;background-color: ghostWhite;"><?php echo $log['log']; ?><?php if ($log['log'] == "") echo "OK, no changes required"; ?></pre>
			</div>
<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>

<script>
function ToggleLog(log)
{
	if (document.getElementById(log).style.display == "block")
	{
		document.getElementById(log).style.display = 'none';
	} else {
		document.getElementById(log).style.display = 'block';
	}
}

jQuery(document).ready(function () {
	jQuery('#toolbar-cancel button').attr('onclick', '');
	jQuery('#toolbar-cancel button').unbind('click');
	jQuery('#toolbar-cancel button').click(function (ev) {
		ev.preventDefault();
		window.location = '<?php echo JRoute::_('index.php?option=com_fsj_main'); ?>';
	});
});

</script>
