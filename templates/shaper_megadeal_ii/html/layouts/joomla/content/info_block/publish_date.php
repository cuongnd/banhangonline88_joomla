<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
<dd class="published">
	<time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished" data-toggle="tooltip" title="<?php echo JText::_('COM_CONTENT_PUBLISHED_DATE'); ?>">
		<?php echo JHtml::_('date', $displayData['item']->publish_up, 'd F, Y'); ?>
		<span class="dt-separator"><?php echo JText::_('HELIX_AT_TEXT'); ?></span>
		<?php echo JHtml::_('date', $displayData['item']->publish_up, 'H:i'); ?>
	</time>
</dd>