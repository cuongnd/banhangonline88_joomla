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

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.toolbar' );

class EasyBlogViewSubscription extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$mainframe		= JFactory::getApplication();
		$document		= JFactory::getDocument();
		$config			= EasyBlogHelper::getConfig();
		$my				= JFactory::getUser();
		$postTable		= EasyBlogHelper::getTable('Blog'		, 'Table');
		$bloggerTable	= EasyBlogHelper::getTable('Profile'		, 'Table');
		$catTable		= EasyBlogHelper::getTable('Category'	, 'Table');
		$teamTable		= EasyBlogHelper::getTable('TeamBlog'	, 'Table');

		$document->setTitle( JText::_( 'COM_EASYBLOG_SUBSCRIPTIONS_PAGE_TITLE' ) );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'subscription' ) )
	    	$this->setPathway( JText::_( 'COM_EASYBLOG_SUBSCRIPTIONS_BREADCRUMB' ) );

		$subscription = array();
		if(!empty($my->id))
		{
			$subs = EasyBlogHelper::getSubscriptionbyUser($my->id);

			if(!empty($subs))
			{
				foreach($subs as $sub)
				{
					$temp = new stdClass();
					$temp->id			= $sub->id;
					$temp->type			= $sub->type;
					$temp->unsublink	= EasyBlogHelper::getUnsubscribeLink($sub, false);

					switch($sub->type)
					{
						case 'sitesubscription':
							$temp->name = '';
							$temp->link = '';
							break;
						case 'subscription':
							$postTable 	= EasyBlogHelper::getTable( 'Blog' );
							$postTable->load($sub->cid);
							$temp->name = $postTable->title;
							$temp->link = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$postTable->id, false);
							break;
						case 'bloggersubscription':
							$bloggerTable 	= EasyBlogHelper::getTable( 'Profile' );
							$bloggerTable->load($sub->cid);
							$temp->name = $bloggerTable->getName();
							$temp->link = $bloggerTable->getProfileLink();
							break;
						case 'categorysubscription':
							$catTable 	= EasyBlogHelper::getTable( 'Category' );
							$catTable->load($sub->cid);
							$temp->name = $catTable->title;
							$temp->link = EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$catTable->id, false);
							break;
						case 'teamsubscription':
							$teamTable 	= EasyBlogHelper::getTable( 'TeamBlog' );
							$teamTable->load($sub->cid);
							$temp->name = $teamTable->title;
							$temp->link = EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$teamTable->id, false);
							break;
						default:
							//dont do anything if it is an unrecognize type.
					}

					$subscription[$sub->type][] = $temp;
				}
			}
		}

		$theme		= new CodeThemes();
		$theme->set('my'			, $my );
		$theme->set('subscription'	, $subscription );
		$html		= $theme->fetch( 'subscription.php' );

		echo $html;
	}
}
