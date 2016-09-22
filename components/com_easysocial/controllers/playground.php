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

// Import main controller
FD::import( 'site:/controllers/controller' );

jimport( 'joomla.filesystem.file' );

class EasySocialControllerPlayground extends EasySocialController
{
	public function explorer() {

		$ajax = FD::ajax();

		// Note: This is a just a sample code to mock requests.
		$hook = JRequest::getCmd('hook');

		switch ($hook) {

			case 'getFolders':
				$start = JRequest::getInt('start');
				$limit = JRequest::getInt('limit');
				$folders = $this->folders($start, $limit);

				// // Sample code to use
				// $explorer 	= FD::explorer( 1 , SOCIAL_TYPE_GROUP );
				// $result 	= $explorer->hook( $hook );

				// Take all files and build file map
				$files = $this->files(0, 1500);
				$map = array();

				foreach($files as $file) {
					$map[] = $file->id;
				}

				$folders[0]->map = $map;

				$ajax->resolve($folders);
				break;

			case 'addFolder':

				$name = JRequest::getCmd('name');

				if (empty($name)) {
					$exception = FD::exception('Invalid name provided');
					$ajax->reject($exception->toArray());
				} else {
					$data = array(
						'id' => mt_rand(10, 999),
						'name' => $name,
						'count' => 0,
						'data' => (object) array(),
						'settings' => (object) array()
					);
					$ajax->resolve($data);
				}
				break;

			case 'removeFolder':

				// Mock error removal by adding error=1
				$error = JRequest::getBool('error', false);

				if ($error) {
					$exception = FD::exception('Unable to remove folder.');
					$ajax->reject($exception);
				} else {
					$exception = FD::exception('Remove successful!', SOCIAL_MSG_SUCCESS);
					$ajax->resolve($exception);
				}
				break;

			case 'getFiles':

				$id = JRequest::getInt('id');
				$start = JRequest::getInt('start');
				$limit = JRequest::getInt('limit');

				switch ($id) {

					// 1500 files
					case 0:
						$files = $this->files($start, $limit);
						$ajax->resolve($files);
						break;

					// 0 files
					case 1:
						$ajax->resolve(array());
						break;

					// 1 files
					case 2:
						$files = $this->smallfiles($start, $limit);
						$ajax->resolve($files);
						break;
				}
				break;

			case 'addFile':

				// Define uploader options
				$options = array('name' => 'file', 'maxsize' => '32M');

				// Get uploaded file
				$file = FD::uploader($options)->getFile();

				// If there was an error getting uploaded file, stop.
				if ($file instanceof SocialException)
				{
					$ajax->reject($file->toArray());
				}

				// Get filename
				$name = $file['name'];

				$data = array(
					'id' => mt_rand(1501, 2000),
					'name' => $name,
					'folder' => 0,
					'count' => 0,
					'data' => (object) array(),
					'settings' => (object) array()
				);

				$ajax->resolve($data);
				break;

			case 'removeFile':
				// Mock error removal by adding error=1
				$error = JRequest::getBool('error', false);

				if ($error) {
					$exception = FD::exception('Unable to remove file.');
					$ajax->reject($exception);
				} else {
					$exception = FD::exception('Remove successful!', SOCIAL_MSG_SUCCESS);
					$ajax->resolve($exception);
				}
				break;

			default:
				$exception = FD::exception('Invalid hook provided.');
				$ajax->reject($exception->toArray());
				break;
		}

		$ajax->send();
	}

	public function folders($start, $limit) {
		$raw_folders = JFile::read(SOCIAL_MEDIA . '/samples/folder.json');
		$all_folders = json_decode($raw_folders);
		$folder = array_slice($all_folders, $start, $limit);
		return $folder;
	}

	public function files($start, $limit) {
		$raw_files = JFile::read(SOCIAL_MEDIA . '/samples/files.json');
		$all_files = json_decode($raw_files);
		$files = array_slice($all_files, $start, $limit);
		return $files;
	}

	public function smallfiles($start, $limit) {
		$raw_files = JFile::read(SOCIAL_MEDIA . '/samples/smallfiles.json');
		$all_files = json_decode($raw_files);
		$files = array_slice($all_files, $start, $limit);
		return $files;
	}
}
