<?php
/**
 * This file is taken from com_media
 * There are some changes to let partners in CMGroupBuying only have access to their own folders.
 */

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
/**
 * Media Component Manager Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.5
 */
class CMGroupBuyingModelImages extends JModelLegacy
{

	function getState($property = null, $default = null)
	{
		$jinput = JFactory::getApplication()->input;
		static $set;

		if (!$set) {
			$folder = $jinput->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$fieldid = $jinput->get('fieldid', '');
			$this->setState('field.id', $fieldid);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * Image Manager Popup
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.5
	 */
	function getFolderList($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = COM_CMGROUPBUYING_PARTNER_BASE;
		}
		//corrections for windows paths
		$base = str_replace(DS, '/', $base);
		$com_media_base_uni = str_replace(DS, '/', COM_CMGROUPBUYING_PARTNER_BASE);

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_MEDIA_INSERT_IMAGE'));

		// Build the array of select options for the folder list
		$options[] = JHtml::_('select.option', "", "/");

		foreach ($folders as $folder)
		{
			$folder		= str_replace($com_media_base_uni, "", str_replace(DS, '/', $folder));
			$value		= substr($folder, 1);
			$text		= str_replace(DS, "/", $folder);
			$options[]	= JHtml::_('select.option', $value, $text);
		}

		// Sort the folder list array
		if (is_array($options)) {
			sort($options);
		}

		// Get asset and author id (use integer filter)
		$jinput = JFactory::getApplication()->input;
		$asset = $jinput->get('asset', 0, 'int');
		$author = $jinput->get('author', 0, 'int');

		// Create the drop-down folder select list
		$list = JHtml::_('select.genericlist',  $options, 'folderlist', 'class="inputbox" size="1" onchange="ImageManager.setFolder(this.options[this.selectedIndex].value, '.$asset.', '.$author.')" ', 'value', 'text', $base);

		return $list;
	}

	function getFolderTree($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = COM_CMGROUPBUYING_PARTNER_BASE;
		}
		//corrections for windows paths
		$base = str_replace(DS, '/', $base);
		$com_media_base_uni = str_replace(DS, '/', COM_CMGROUPBUYING_PARTNER_BASE);

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$tree = array();

		foreach ($folders as $folder)
		{
			$folder		= str_replace(DS, '/', $folder);
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace(COM_CMGROUPBUYING_PARTNER_BASE, '', $folder);
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;
			for ($i=0, $n=count($path); $i<$n; $i++)
			{
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}

				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}

				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('COM_MEDIA_MEDIA'), 'relative' => '', 'absolute' => $base);

		return $tree;
	}
}
