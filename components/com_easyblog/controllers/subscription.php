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

require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );

class EasyBlogControllerSubscription extends EasyBlogParentController
{
	var $err	= null;

	function display()
	{
        parent::display();
	}
	
	function unsubscribe()
	{
		$my = JFactory::getUser();
		
		$redirectLInk = 'index.php?option=com_easyblog&view=subscription';
		if( $my->id == 0)
		    $redirectLInk = 'index.php?option=com_easyblog&view=latest';
		
		//type=site - subscription type
		//sid=1 - subscription id
		//uid=42 - user id
		//token=0fd690b25dd9e4d2dc47a252d025dff4 - md5 subid.subdate
		$data = base64_decode(JRequest::getVar('data', ''));
		
		$param = EasyBlogHelper::getRegistry($data);
		$param->type	= $param->get('type', '');
		$param->sid 	= $param->get('sid', '');
		$param->uid 	= $param->get('uid', '');
		$param->token 	= $param->get('token', '');
		
		$subtable = EasyBlogHelper::getTable($param->type, 'Table');
		$subtable->load($param->sid);
		
		$token 		= md5($subtable->id.$subtable->created);
		$paramToken = md5($param->sid.$subtable->created);
		
		if( $subtable->id != 0 )
		{
			if($token != $paramToken)
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_FAILED') , 'error');
				$this->setRedirect(EasyBlogRouter::_($redirectLInk, false));
				return false;
			}

			if(!$subtable->delete($param->sid))
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS') , 'error');
				$this->setRedirect(EasyBlogRouter::_($redirectLInk, false));
				return false;
			}
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_SUCCESS') );
		$this->setRedirect(EasyBlogRouter::_($redirectLInk, false));
		return true;
	}
}