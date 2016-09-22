<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<table class="table table-narrow table-bordered pull-left" style="margin-right: 16px;">
	<tr><th colspan="2">Components</th></tr>
	<?php 
		jimport( 'fsj_core.lib.utils.admin');
		
		$lang = JFactory::getLanguage();
		$has_includes = false;
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE element LIKE 'com_fsj_%' ORDER BY element";
		$db->setQuery($qry);

		$exts = $db->loadObjectList();

		foreach ($exts as $ext)
		{
			if ($ext->element == "com_fsj_includes")
			{ 
				$has_includes = true;
				continue;
			}
			if ($ext->element == "com_fsj_main") continue;
			$ext->manifest_cache = json_decode($ext->manifest_cache);
			?>
			<tr>
				<td>
					<?php echo JText::_($ext->element . "_menu"); ?>
				</td>
				<td>
					v<b><?php echo FSJ_Admin_Helper::getVersion($ext->element); ?></b>
				</td>
			</tr>
		<?php
		}
	?>
</table>


<table class="table table-narrow table-bordered pull-left" style="margin-right: 16px;">
	<tr><th colspan="2">Core Components</th></tr>
	<tr>
		<td>
			<?php echo JText::_("com_fsj_main_menu"); ?>
		</td>
		<td>
			v<b><?php echo FSJ_Admin_Helper::getVersion("com_fsj_main"); ?></b>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo JText::_("FSJ Core Libraries"); ?>
		</td>
		<td>
			v<b><?php echo FSJ_Admin_Helper::getVersion("fsj_core", "library"); ?></b>
		</td>
	</tr>
	<?php if ($has_includes): ?>
	<tr>
		<td>
			<?php echo JText::_("com_fsj_includes_menu"); ?>
		</td>
		<td>
			v<b><?php echo FSJ_Admin_Helper::getVersion("com_fsj_includes"); ?></b>
		</td>
	</tr>
	<?php endif; ?>
</table>
<table class="table table-narrow table-bordered pull-left" style="margin-right: 16px;">
	<tr><th colspan="2">Plugins</th></tr>
	<?php 
		$qry = "SELECT * FROM #__extensions WHERE element LIKE 'fsj_%' AND type = 'plugin' ORDER BY element";
		$db->setQuery($qry);

		$exts = $db->loadObjectList();

		foreach ($exts as $ext)
		{
			$ext->manifest_cache = json_decode($ext->manifest_cache);
		$lang->Load($ext->name.".sys");
	?>
			<tr>
				<td>
					<?php echo JText::_($ext->name); ?>
				</td>
				<td>
					v<b><?php echo $ext->manifest_cache->version; ?></b>
				</td>
			</tr>
		<?php
		}
	?>
</table>

<table class="table table-narrow table-bordered pull-left" style="margin-right: 16px;">
	<tr><th colspan="2">Modules</th></tr>
	<?php 
		$qry = "SELECT * FROM #__extensions WHERE element LIKE 'mod_fsj_%' AND type = 'module' ORDER BY element";
		$db->setQuery($qry);

		$exts = $db->loadObjectList();

		foreach ($exts as $ext)
		{
			$ext->manifest_cache = json_decode($ext->manifest_cache);			
		$lang->Load($ext->element.".sys", JPATH_SITE);
	?>
			<tr>
				<td>
					<?php echo JText::_($ext->name); ?>
				</td>
				<td>
					v<b><?php echo $ext->manifest_cache->version; ?></b>
				</td>
			</tr>
		<?php
		}
	?>
</table>

