<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.require()
	.script('tag')
	.done(function($){
		$("<?php echo $microblogType ?> .tag-form")
			.implement(
				"EasyBlog.Controller.Tag.Form",
				{
					tagLimit: <?php echo $system->config->get( 'max_tags_allowed' ); ?>,
					tagSelections: <?php echo $this->json_encode($tags); ?>
				}
			);
	});
</script>

<div class="tag-form">
	<div class="tag-creation">
		<ul class="tag-list creation reset-ul float-li clearfix">
			<?php if ($this->acl->rules->create_tag): ?>
			<li class="new-tag-item">
				<input type="text" name="tag-input" class="tag-input tagInput canCreate" autocomplete="off"/>
				<button type="button" class="tag-create"><?php echo JText::_('COM_EASYBLOG_ADD_TAG'); ?></button>
			</li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="tag-selection no-selection">
		<?php if (!$this->acl->rules->create_tag): ?>
		<div class="tag-selection-filter-container"><input type="text" class="tag-selection-filter tagInput" autocomplete="off"/></div>
		<?php endif; ?>

		<div class="tag-no-selection-hint"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE'); ?></div>

		<ul class="tag-list selection reset-ul float-li clearfix"></ul>

		<div class="tag-selection-actions clearfix">
			<a class="ui-button show-all-tags"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_SHOW_ALL_TAGS' ); ?></a>

			<?php if( $system->config->get( 'max_tags_allowed' ) > 0 ) { ?>
			<span class="tag-limit"><span class="total-tags">0</span>/<span class="max-tags"><?php echo $system->config->get( 'max_tags_allowed' ); ?> <?php echo JText::_( 'COM_EASYBLOG_NUMBER_TAGS_ALLOWED' ); ?></span></span>
			<?php } ?>
		</div>
	</div>
</div>
