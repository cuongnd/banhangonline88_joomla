<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php 
$anyallowed = false;
foreach ($this->components as $component)
{
	if (JFactory::getUser()->authorise('core.manage', $component->element))
	{
		$anyallowed = true;
		break;	
	}
}
?>

<?php if ($anyallowed): ?>
<div class="well well-small pull-left margin-small">
	<h4><?php echo JText::_('FSJ_M_COMPONENTS'); ?></h4>
	
<ul class="nav nav-list">

	<?php foreach ($this->components as $component): ?>
		<?php $xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$component->element.DS.str_replace("com_fsj_", "", $component->element) . ".xml";
			if (file_exists($xmlfile))
			{
				$xmlobj = simplexml_load_file($xmlfile);
				if (isset($xmlobj->overview))
				{
					if ($xmlobj->overview->attributes()->not_in_main) continue;	
				}
			}
		?>
		<?php
			// check permissions for component
			if (!JFactory::getUser()->authorise('core.manage', $component->element)) continue;
		?>	
		<li>		
			<a href="<?php echo JRoute::_('index.php?option=' . $component->element); ?>">
				<?php 
					$icon_elem = $component->element;
					if (in_array(strtolower($component->element), array("com_fss", "com_fst", "com_fsf")))
						$icon_elem = "com_fsj_main";
				?>
				<img width="24" height="24" src="<?php echo JURI::root( true ); ?>/administrator/components/<?php echo $icon_elem; ?>/assets/images/<?php echo $component->element;?>-48.png">
				<?php echo JText::_($component->displayname); ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<?php endif; ?>
	
<?php
// display includes if we have them installed
$file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_includes'.DS.'helpers'.DS.'overview.php';
if (file_exists($file))
{
	require_once($file);
	$ov = new fsj_includes_overview();
	$ov->position_1();	
}
?>
