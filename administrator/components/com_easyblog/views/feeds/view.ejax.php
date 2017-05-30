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
require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );

class EasyBlogViewFeeds extends EasyBlogAdminView
{
	function download($params)
	{
	    $post	= EasyBlogStringHelper::ejaxPostToArray($params);

	    $cids   = $post['cid'];
		$ids    = implode('|', $cids);

	    $this->_process($ids);
	}

	function _process( $ids, $itemMigrated = 0 )
	{
	    $ejax				= new EJax();
	    $post       	 	= explode( '|', $ids);
	    $startPercentage 	= 1;

	    if( count($post) > 0)
	    {
	        //calculate the percentage.
	        $percentage = floor( 100 / count($post) );

		    $cid    = array_shift( $post );

		    if( !empty( $cid ) )
		    {
		        //calling helper to process the feed retrieval.
		        $tbl    = EasyBlogHelper::getTable( 'Feed' );
		        $tbl->load( $cid );
		        $tbl->flag  = '1';
		        $tbl->store();

				$itemCnt	= EasyBlogHelper::getHelper('Feeds')->import( $tbl, 0);

				$date           	= EasyBlogHelper::getDate();
				$tbl->last_import 	= $date->toMySQL();
				$tbl->flag  = '0';
				$tbl->store();

				$itemMigrated   += $itemCnt;
		    }

		    if( count($post) > 0)
		    {
			    $ids    = implode('|', $post);

			    $ejax->script( '$("#bar-progress").css("width" , "'. $percentage .'%");' );
			    $ejax->script("ejax.load('feeds','_process', '$ids', '$itemMigrated');");
		    }
		    else
		    {
		        $ejax->script( '$("#bar-progress").css("width" , "100%");' );
		        $ejax->script( '$("#feeds-msg").html("'. JText::_('COM_EASYBLOG_FEEDS_MIGRATE_COMPLETED') .'");' );
		    }
		}
		else
		{
		    $ejax->script( '$("#bar-progress").css("width" , "100%");' );
		    $ejax->script( '$("#feeds-msg").html("'. JText::_('COM_EASYBLOG_FEEDS_MIGRATE_COMPLETED') .'");' );
		}

	    $ejax->send();
	}
}
