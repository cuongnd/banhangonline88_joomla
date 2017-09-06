<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include the fields library
ES::import('admin:/includes/fields/dependencies');

// Include helper file.
ES::import('fields:/page/permalink/helper');

/**
 * Processes ajax calls for the permalink field.
 *
 * @since	2.0
 */
class SocialFieldsPagePermalink extends SocialFieldItem
{
	/**
	 * Validates the username.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	JSON	A jsong encoded string.
	 *
	 */
	public function isValid()
	{
		// Render the ajax lib.
		$ajax = ES::ajax();

		// Get the page id
		$pageId = JRequest::getInt('pageid', 0);

		// Set the current username
		$current = '';

		if (!empty($pageId)) {
			$page = ES::page($pageId);
			$current = $page->alias;
		}

		// Get the provided permalink
		$permalink = JRequest::getVar('permalink', '');

		// Check if the field is required
		if (!$this->field->isRequired() && empty($permalink)) {
			return true;
		}

		// Check if the permalink provided is allowed to be used.
		$allowed = SocialFieldsPagePermalinkHelper::allowed($permalink);
		if (!$allowed) {
			return $this->ajax->reject(JText::_('PLG_FIELDS_PERMALINK_NOT_ALLOWED'));
		}

		// Check if the permalink provided is valid
		if (!SocialFieldsPagePermalinkHelper::valid($permalink, $this->params)) {
			return $ajax->reject(JText::_('PLG_FIELDS_PAGE_PERMALINK_INVALID_PERMALINK'));
		}

		// Test if permalink exists
		if (SocialFieldsPagePermalinkHelper::exists($permalink) && $permalink != $current) {
			return $ajax->reject(JText::_('PLG_FIELDS_PAGE_PERMALINK_NOT_AVAILABLE'));
		}

		$text = JText::_('PLG_FIELDS_PAGE_PERMALINK_AVAILABLE');

		return $ajax->resolve($text);
	}
}
