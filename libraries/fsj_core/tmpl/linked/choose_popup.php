<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="fsj fsj_linked fsj_pick">

<?php //echo FSJ_Helper::PageStylePopup(); ?>

<?php //echo FSJ_Helper::PageTitlePopup($this->addtext); ?>
<h1><?php echo JText::_($this->addtext); ?></h1>
<?php $uri = JURI::getInstance();
$uri->delVar('pluginid'); ?>
<form action="<?php echo JRoute::_( "index.php". $uri->toString(array("query")) );?>" method="post" name="pick<?php echo $this->id; ?>Form" id="pick<?php echo $this->id; ?>Form">

<?php if ($this->pluginselect_tabs): ?>

	<dl class="tabs" id="com_fsj_main_overview_tabs">
		<dt style="display:none;"></dt>
		<dd style="display:none;"></dd>
		<?php foreach ($this->plugins as &$plugin) : ?>
			<?php if (isset($plugin->params->pick->type) && $plugin->params->pick->type == "none") continue; ?>
			<?php if (array_key_exists('not_in_popup',$plugin->params)) continue; ?>
		<dt class="tabs specific <?php echo ($plugin->name == $this->pluginid) ? 'open' : 'closed'; ?>" style="cursor: pointer;" id="specific">
			<span>
				<h3>
					<a href="<?php echo JRoute::_("index.php". $uri->toString(array("query")) . '&pluginid=' . $plugin->name); ?>"><?php echo JText::_($plugin->title); ?></a>
				</h3>
			</span>
		</dt>
		<?php endforeach; ?>
	</dl>

	<div class="current"><dd class="tabs" style="display: block;">

<?php else: ?>
	
	<table width="100%">
		<tr>
			<td width="100%">
				<?php echo JText::_('FSJ_PICK_SELECT_TYPE'); ?>
				<?php echo $this->pluginselect; ?>				
			</td>
			<td nowrap="nowrap">
			</td>
		</tr>
	</table>
	
<?php endif; ?>

<?php $this->pick->Display(); ?>

<?php if ($this->pluginselect_tabs): ?>
			<div style="clear:both;"></div>
		</dd>
	</div>
<?php endif; ?>

</form>

<?php //echo FSJ_Helper::PageStylePopupEnd(); ?>

<script>
jQuery(document).ready(function () {
	jQuery('#pluginid').change(function () {
		jQuery('#pick<?php echo $this->id; ?>Form').submit();
	});
	jQuery('#form_reset').click(function (ev) {
		ev.preventDefault();
		alert("Reset");
	});
	
	if (window.parent && window.parent.TINY) {
        var height = jQuery(document.body).height() + 10;
		if (height < 400)
			height = 400;
        if (window.console) console.log('[popup] resize popup to ' + height);
        window.parent.TINY.box.size(800, height, true);
    }
});
</script>

</div>