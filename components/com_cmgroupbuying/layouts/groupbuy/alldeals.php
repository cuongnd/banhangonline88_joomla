<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;
$view = $this->view;
$numOfColumns = (int)$this->numOfColumns;
$deals = $this->deals;

if($numOfColumns == 0) $numOfColumns = 2;

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="all_deal_section">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
<?php
if(empty($deals))
{
	echo '<p class="cmgroupbuying_error">' . $this->noDeal . '</p>';
}
else
{
?>
	<ul class="thumbnails">
	<?php
		$i = 1;

		foreach($deals as $deal)
		{
			$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
			$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);
			$dealName = $deal['name'];
			$dealPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price'], true, $configuration);
			$dealOriginalPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price'], true, $configuration);
			$dealImages = array();

			for($j = 1; $j <= 5; $j++)
			{
				$columnName = 'image_path_' . $j;

				if($deal[$columnName] != '')
				{
					$dealImages[] = JURI::root() . $deal[$columnName];
				}
			}

			if(!empty($dealImages))
			{
				$image = '<img src="' . $dealImages[0] . '" />';
			}
			else
			{
				$image = '';
			}

			$span = 12 / $numOfColumns;
			$span = 'span' . $span;

			if($i % $numOfColumns == 1) echo '<div class="row-fluid">';

			echo '<li class="' . $span . ' deal" id ="deal_' . $deal['id'] . '">';
			echo '<div class="thumbnail">';
			echo '<div class="deal_list">';
			echo '<a href="' . $link . '"><div class="deal_photo">' . $image . '</div></a>';
			echo '<div class="deal_info">';
			echo '<div class="deal_name">' . $dealName . '</div>';
			echo '<div class="deal_price">' . $dealPrice . '</div>';
			echo '<div class="deal_original_price">' . $dealOriginalPrice . '</div>';
			echo '<div class="deal_view_button"><a href="' . $link . '"><div class="btn btn-success">' . JText::_('COM_CMGROUPBUYING_DEAL_VIEW_BUTTON') . '</div></a></div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';

			if($i % $numOfColumns == 0) echo '</div>';

			$i++;
		}
	?>
	</ul>
<?php
}
?>
</div>
<form action="<?php echo JRoute::_('index.php', false); ?>" method="post">
	<input type="hidden" name="option" id="option" value="com_cmgroupbuying"/>
	<input type="hidden" name="view" value="<?php echo $view; ?>" />
	<?php if($view != 'search'): ?>
	<div class="cmgroupbuying_pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
	<?php endif; ?>
</form>