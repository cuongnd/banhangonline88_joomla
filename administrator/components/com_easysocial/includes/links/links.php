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

// Import the required file and folder classes.
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Links library
 *
 * @since	1.2.11
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialLinks
{
	public function __construct()
	{
		$this->config = FD::config();
	}

	public static function factory()
	{
		return new self();
	}

	/**
	 * Stores a given image link into the local cache
	 *
	 * @since	1.2.11
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cache($imageLink)
	{
		// Check if settings is enabled
		if (!$this->config->get('links.cache.images')) {
			return false;
		}

		// Generate a unique name for this file
		$name = md5($imageLink) . '.png';

		// Get the storage path
		$storageFolder = FD::cleanPath($this->config->get('links.cache.location'));
		$storage = JPATH_ROOT . '/' . $storageFolder . '/' . $name;
		$storageURI = rtrim(JURI::root(), '/') . '/' . $storageFolder . '/' . $name;
		$exists = JFile::exists($storage);

		// If the file is already cached, skip this
		if ($exists) {
			return $storageURI;
		}

		// Crawl the image now.
		$connector = FD::get('Connector');
		$connector->addUrl($imageLink);
		$connector->connect();

		// Get the result and parse them.
		$contents = $connector->getResult($imageLink);

		// Store the file to a temporary directory first
		$tmpFile = SOCIAL_TMP . '/' . $name;
		JFile::write($tmpFile, $contents);

		// Load the image now
		$image = FD::image();
		$image->load($tmpFile);

		// Ensure that image is valid
		if (!$image->isValid()) {
			JFile::delete($tmpFile);
			return false;
		}

		// Delete the temporary file.
		JFile::delete($tmpFile);

		// Unset the image now since we don't want to use asido to resize
		unset($image);

		// Store the file now into our cache storage.
		JFile::write($storage, $contents);

		return $storageURI;
	}
}
