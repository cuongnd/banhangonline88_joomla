<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

$aggregationSite = JFactory::getApplication()->input->get('aggSite', 'common', 'word');

$categories			= $this->categories;
$locations			= $this->locations;
$partners			= $this->partners;
$aggregatorSites	= $this->aggregatorSites;

$aggregatorSiteOption = array();
$option = JHTML::_('select.option', 'common', JText::_('COM_CMGROUPBUYING_SELECT_AGG_SITE'));
array_push($aggregatorSiteOption, $option);

foreach($aggregatorSites as $aggregatorSite)
{
	$option = JHTML::_('select.option', $aggregatorSite['ref'], $aggregatorSite['name']);
	array_push($aggregatorSiteOption, $option);
}
?>
<div class="cmgroupbuying">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
	<?php
	echo '<form action="index.php?option=com_cmgroupbuying&view=aggregationlinks" method="post" name="adminForm" id="adminForm" class="form-horizontal">';
	echo JText::_('COM_CMGROUPBUYING_AGG_SITE_SELECT_SITE') . " " . JHTML::_('select.genericList', $aggregatorSiteOption, 'aggSite', null , 'value', 'text', $aggregationSite);
	echo '<input class="btn" type="submit" name="submit" id="submit" value="' . JText::_('COM_CMGROUPBUYING_FILTER') . '" />';
	echo '</form>';
	?>
	<?php
	echo '<h4>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_ALL_HEADER') .'</h4>';
	echo '<p><strong>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_STRUCTURE') . '</strong> ' . JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=all' . '&ref=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_SITE_ID') . ']</p>';
	$link = JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=all';

	if($aggregationSite != "common")
	{
		$link .= "&ref=" . $aggregationSite;
	}

	echo '<ul><li><a href="' . $link . '" target="_blank">' . $link . '</a>' . '</li></ul>';

	echo '<h4>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_CATEGORY_HEADER') .'</h4>';
	echo '<p><strong>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_STRUCTURE') . '</strong> ' . JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=category&id=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_CATEGORY_ID') . ']&ref=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_SITE_ID') . ']</p>';

	if(empty($categories))
	{
		echo '<p>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_NO_CATEGORY') . '</p>';
	}
	else
	{
		echo '<ul>';

		foreach($categories as $category)
		{
			$link = JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=category&id=' . $category['id'];

			if($aggregationSite != "common")
			{
				$link .= "&ref=" . $aggregationSite;
			}

			echo '<li>' . $category['name'] . ': <a href="' . $link . '" target="_blank">' . $link . '</a>' . '</li>';
		}

		echo '</ul>';
	}

	echo '<h4>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_LOCATION_HEADER') .'</h4>';
	echo '<p><strong>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_STRUCTURE') . '</strong> ' . JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=location&id=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_LOCATION_ID') . ']&ref=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_SITE_ID') . ']</p>';

	if(empty($locations))
	{
		echo '<p>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_NO_LOCATION') . '</p>';
	}
	else
	{
		echo '<ul>';

		foreach($locations as $location)
		{
			$link = JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=location&id=' . $location['id'];

			if($aggregationSite != "common")
			{
				$link .= "&ref=" . $aggregationSite;
			}

			echo '<li>' . $location['name'] . ': <a href="' . $link . '" target="_blank">' . $link . '</a>' . '</li>';
		}

		echo '</ul>';
	}

	echo '<h4>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_PARTNER_HEADER') .'</h4>';
	echo '<p><strong>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_STRUCTURE') . '</strong> ' . JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=partner&id=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_PARTNER_ID') . ']&ref=[' .  JText::_('COM_CMGROUPBUYING_AGG_SITE_SITE_ID') . ']</p>';

	if(empty($partners))
	{
		echo '<p>' . JText::_('COM_CMGROUPBUYING_AGG_LINKS_NO_LOCATION') . '</p>';
	}
	else
	{
		echo '<ul>';

		foreach($partners as $partner)
		{
			$link = JURI::root() . 'index.php?option=com_cmgroupbuying&view=aggregator&type=partner&id=' . $partner['id'];

			if($aggregationSite != "common")
			{
				$link .= "&ref=" . $aggregationSite;
			}

			echo '<li>' . $partner['name'] . ': <a href="' . $link . '" target="_blank">' . $link . '</a>' . '</li>';
		}

		echo '</ul>';
	}
	?>
		</div>
	</div>
</div>