<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="clearfix">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
	<h3><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_ALL_LOCATIONS'); ?></h3>
	<ul>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=today', false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_TODAY_DEAL'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=all', false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_ALL_DEALS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=active', false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_ACTIVE_DEALS'); ?></a></li>
	</ul>
<?php
foreach($this->locations as $location):
?>   
	<h3><?php echo $location['name']; ?></h3>
	<ul>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=today&location=' . $location['id'], false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_TODAY_DEAL'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=all&location=' . $location['id'], false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_ALL_DEALS'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_cmgroupbuying&view=rssfeeds&type=active&location=' . $location['id'], false); ?>"><?php echo JText::_('COM_CMGROUPBUYING_RSS_FEEDS_ACTIVE_DEALS'); ?></a></li>
   </ul>
<?php endforeach; ?>
</div>