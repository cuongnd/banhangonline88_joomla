<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>500</width>
	<height>250</height>
	<selectors type="json">
	{
		"{saveButton}": "[data-save-button]",
		"{cancelButton}": "[data-cancel-button]",

		"{notice}": "[data-feeds-form-notice]",
		"{title}": "[data-feeds-form-title]",
		"{url}": "[data-feeds-form-url]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{saveButton} click": function() {
			
			var title = this.title().val();
			var url = this.url().val();
			var notice = this.notice();

			notice.removeClass('alert alert-error').addClass('t-hidden');

			if (title.trim().length == 0) {
				notice.text('<?php echo JText::_('APP_FEEDS_TITLE_EMPTY', true);?>');
				notice.addClass('alert alert-error');
				notice.removeClass('hide');
				return;
			}

			if (url.trim().length == 0) {
				notice.text('<?php echo JText::_('APP_FEEDS_URL_EMPTY', true);?>');
				notice.addClass('alert alert-error');
				notice.removeClass('hide');
				return;
			}

			EasySocial.ajax('apps/user/feeds/controllers/feeds/save', {
				"title": title,
				"url": url,
				"id": "<?php echo $id;?>"
			}).done(function(contents) {
				
				// Close dialog
				EasySocial.dialog().close();

				$('[data-app-contents]').removeClass('is-empty');

				$('[data-feeds-lists]').append(contents);
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('APP_FEEDS_DIALOG_CREATE_TITLE'); ?></title>
	<content>
		<p><?php echo JText::_('APP_FEEDS_DIALOG_CREATE_DESC'); ?></p>

		<div data-feeds-form-notice class="hide"></div>

		<div class="o-form-horizontal">
			<div class="o-form-group">
				<?php echo $this->html('form.label', 'APP_FEEDS_DIALOG_FORM_CREATE_TITLE'); ?>

				<?php echo $this->html('grid.inputbox', 'title', '', 'feed-title', array('data-feeds-form-title', 'placeholder="' . JText::_('APP_FEEDS_DIALOG_FORM_CREATE_TITLE') . '"')); ?>
			</div>

			<div class="o-form-group">
				<?php echo $this->html('form.label', 'APP_FEEDS_DIALOG_FORM_CREATE_URL'); ?>

				<?php echo $this->html('grid.inputbox', 'url', '', 'feed-url', array('data-feeds-form-url', 'placeholder="' . JText::_('APP_FEEDS_DIALOG_FORM_CREATE_URL_PLACEHOLDER') . '"')); ?>
			</div>
		</div>

	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es-default btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('APP_FEEDS_CREATE_BUTTON'); ?></button>
	</buttons>
</dialog>
