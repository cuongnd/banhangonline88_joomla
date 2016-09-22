<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="well well-small pull-left margin-small">
	<h4><?php echo JText::_('FSJ_ADMIN_SETTINGS'); ?></h4>

	<ul class="nav nav-list">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_fsj_main&view=settings&admin_com=main&settings=global'); ?>">
				<img width="24" height="24" src="<?php echo JURI::root( true ); ?>/administrator/components/com_fsj_main/assets/images/globalsettings-48.png">
				<?php echo JText::_("FSJ_ADMIN_GLOBAL_SETTINGS"); ?>
			</a>
		</li>
	</ul>
</div>

<?php foreach ($this->global_xml->admin->section as $section): ?>
	<?php
		if (isset($section->auth))
		{
			$com = $section->auth->attributes()->com;
			$perm = $section->auth->attributes()->perm;
			if (!JFactory::getUser()->authorise($perm, $com)) {
				continue;
			}		
		}
	?>
	<div class="well well-small pull-left margin-small">

		<h4><?php echo JText::_($section->attributes()->name); ?> </h4>
		<ul class="nav nav-list">
			<?php foreach ($section->item as $item): ?>
					<?php
						if (isset($item->auth))
						{
							$com = (string)$item->auth->attributes()->com;
							$perm = (string)$item->auth->attributes()->perm;
							if (!JFactory::getUser()->authorise($perm, $com)) {
								continue;
							}		
						}
					?>
				<li>
					<?php if ($item->attributes()->link): ?>
						<a href="<?php echo JRoute::_((string)$item->attributes()->link); ?>">
					<?php else : ?>
						<a href="<?php echo JRoute::_("index.php?option=com_fsj_main&view=" . (string)$item->attributes()->id."s"); ?>">
					<?php endif; ?>
						<img src="<?php echo JURI::root( true ); ?>/administrator/components/com_fsj_main/assets/images/<?php echo $item->attributes()->icon; ?>-48.png" width="24" height="24">
						<?php echo JText::_($item->title); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endforeach; ?>
