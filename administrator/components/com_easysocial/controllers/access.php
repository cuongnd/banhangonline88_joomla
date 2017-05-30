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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/controllers/controller');

class EasySocialControllerAccess extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('unpublish', 'publish');
	}

	public function remove()
	{
		FD::checkToken();

		$ids = FD::makeArray(JRequest::getVar('cid'));

		$view = $this->getCurrentView();

		if (empty($ids))
		{
			$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_INVALID_ID_PROVIDED') , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		foreach ($ids as $id)
		{
			$acc = FD::table('accessrules');
			$acc->load($id);

			$acc->delete();
		}

		$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_DELETED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);
		return $view->call(__FUNCTION__);
	}

	public function publish()
	{
		FD::checkToken();

		$ids = FD::makeArray(JRequest::getVar('cid'));

		$view = $this->getCurrentView();

		$task 	= $this->getTask();

		if (empty($ids))
		{
			$view->setMessage(JText::_('COM_EASYSOCIAL_ACCESS_INVALID_ID_PROVIDED') , SOCIAL_MSG_ERROR);
			return $view->call(__FUNCTION__);
		}

		foreach ($ids as $id)
		{
			$acc = FD::table('accessrules');
			$acc->load($id);

			$acc->$task();
		}

		$message = $task === 'publish' ? 'COM_EASYSOCIAL_ACCESS_PUBLISHED_SUCCESSFULLY' : 'COM_EASYSOCIAL_ACCESS_UNPUBLISHED_SUCCESSFULLY';

		$view->setMessage(JText::_($message), SOCIAL_MSG_SUCCESS);
		return $view->call(__FUNCTION__);
	}

	public function scanFiles()
	{
		FD::checkToken();

		$view = $this->getCurrentView();

		$config = FD::config();
		$paths 	= $config->get('access.paths');

		$model = FD::model('accessrules');

		$files = array();

		foreach ($paths as $path)
		{
			$data = $model->scan($path);

			$files = array_merge($files, $data);
		}

		return $view->call(__FUNCTION__, $files);
	}

	public function installFile()
	{
		FD::checkToken();

		$view = $this->getCurrentView();

		$file = JRequest::getVar('file', '');

		if (empty($file))
		{
			$view->setError('Invalid file path given to scan.');
			return $view->call(__FUNCTION__);
		}

		$model = FD::model('accessrules');

		$obj = (object) array(
			'file' => str_ireplace(JPATH_ROOT, '', $file),
			'rules' => $model->install($file)
		);

		return $view->call(__FUNCTION__, $obj);
	}

	public function upload()
	{
		$file = JRequest::getVar('package', '', 'FILES');

		$state = $this->installPackage( $file, 'accessrules', array('zip', 'access' ), true );

		$view = $this->getCurrentView();

		return $view->call(__FUNCTION__);
	}
}
