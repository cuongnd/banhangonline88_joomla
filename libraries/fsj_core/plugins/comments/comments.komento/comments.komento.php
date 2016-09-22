<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * This needs a komento_plugin.php file creating for each target that we are adding comments to. See the faqs one for an example 
 **/
class FSJ_Plugin_Comments_Komento
{
	function getCommentCounts($item_set, $ids)
	{
		return array();
	}
	
	function displayComments($id, $set, $title)
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );
		
		$article = new stdClass();
		$article->id = $id;
		$article->title = $title;
		
		return Komento::commentify( $set, $article, array('enabled' => 1) );
	}
}