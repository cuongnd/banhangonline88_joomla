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

class EasyBlogSubscriptionHelper
{


	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function addMailQueue( EasyBlogSubscriptionItem $item )
	{
		$config		= EasyBlogHelper::getConfig();	

		// building unsubcribe linke object
		$obj = new stdClass();
		$obj->type 		= $item->utype;
		$obj->id 		= $item->uid;
		$obj->user_id 	= $item->user_id;
		$obj->created 	= $item->ucreated;	


		// building email data
		$maildata 					= array();
		$maildata['fullname'] 		= $item->ufullname;
		$maildata['target']       	= $item->targetname;
		$maildata['targetlink']   	= $item->targetlink;
		$maildata['type']   		= $item->utype;


		$recipient 				= new stdClass();
		$recipient->email 		= $item->uemail;
		$recipient->unsubscribe = EasyBlogHelper::getUnsubscribeLink( $obj , true );

		$emailTitle = JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_CONFIRMATION');
		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$notification->send( array($recipient) , $emailTitle , 'email.subscriptions.confirmation' , $maildata );
	}

	public function getTemplate()
	{
		$template = new EasyBlogSubscriptionItem();
		return $template;
	}
}

class EasyBlogSubscriptionItem
{
	var $uid			= null;
	var $utype			= null;
	var $user_id	    = null;
	var $uemail 		= null;
	var $ufullname		= null;
	var $ucreated       = null;

	// eg. blog post title and link
	// eg. blogger name and link
	// eg. category name and link
	// and etc
	var $targetname 	= null;
	var $targetlink 	= null;

	public function __construct(){}

}
