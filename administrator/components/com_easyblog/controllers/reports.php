<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class EasyBlogControllerReports extends EasyBlogController
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function discard()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'report' );

		$ids 	= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$id 	= (int) $id;

			$report = EasyBlogHelper::getTable( 'Report' );
			$report->load($id);

			$report->delete();
		}
		
		$message 	= JText::_( 'COM_EASYBLOG_REPORTS_DISCARDED_SUCCESSFULLY' );		
		$this->setRedirect( 'index.php?option=com_easyblog&view=reports' , $message , 'info' );
	}
}