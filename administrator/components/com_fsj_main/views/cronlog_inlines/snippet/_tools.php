<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
		
			<label for="filter_search" class="element-invisible">
				<?php echo JText::_('FSJ_SEARCH_FILTER'); ?>
			</label>
			<div class="btn-wrapper input-append">
				<input type="text" name="filter_search" class="js-stools-field-search" placeholder="<?php echo JText::_('FSJ_SEARCH_FILTER'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
				<button type="submit" class="btn hasTooltip js-stools-btn-search" title="<?php echo JText::_('FSJ_SEARCH_FILTER_SUBMIT'); ?>">
					<i class="icon-search"></i>
				</button>
			</div>
							<div class="btn-wrapper hidden-phone">
					<button type="button" class="btn hasTooltip js-stools-btn-filter" title="<?php echo JText::_('FSJ_SEARCH_TOOLS_DESC'); ?>">
						<?php echo JText::_('FSJ_SEARCH_TOOLS');?> <i class="caret"></i>
					</button>
				</div>
						<div class="btn-wrapper">
				<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo JText::_('FSJ_SEARCH_FILTER_CLEAR'); ?>">
					<?php echo JText::_('FSJ_SEARCH_FILTER_CLEAR');?>
				</button>
			</div>
	
																	
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php //echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
				<?php $current = $this->state->get('list.fullordering'); ?>
				<?php if ($current == "") $current = $this->state->get('list.ordering') . " " . strtoupper($this->state->get('list.direction')); ?>
			<select id="js-stools-field-order" class="js-stools-field-order" name="list[fullordering]" onchange="this.form.submit();">
				<?php foreach ($this->orderings as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php if ($key == $current) echo "selected='selected' "?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>	
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container-filters hidden-phone clearfix">

								<div class="js-stools-field-filter">
				<?php echo $this->filters['success']; ?>
			</div>
								<div class="js-stools-field-filter">
				<?php echo $this->filters['source']; ?>
			</div>
								<div class="js-stools-field-filter">
				<?php echo $this->filters['whendate']; ?>
			</div>
		
		
		

		
		
			</div>
</div>
