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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewSearch extends EasyBlogView
{
	public function search( $query = '' )
	{
		$ajax	= new Ejax();

		$model	= $this->getModel( 'Search' );
		$result	= $model->searchText( $query );

		if( empty($result) )
		{
			$ajax->script( '$("#editor-content .search-results-content").height(24);' );
			$ajax->assign( 'editor-content .search-results-content', JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_SEARCH_NO_RESULT' ) );
			return $ajax->send();
		}

		$count = count($result);

		if($count > 10)
		{
			$height = "240";
		}
		else
		{
			$height = "24" * $count;
		}

		$theme	= new CodeThemes('dashboard');
		$theme->set( 'result' 	, $result );
		$ajax->assign( 'editor-content .search-results-content' , $theme->fetch( 'dashboard.write.search.result.php' ) );
		$ajax->script( '$("#editor-content .search-results-content").height('.$height.');' );
		$ajax->script( '$("#editor-content .search-results-content").show();' );
		// $ajax->script( 'eblog.fileManager.setDockLayout();' );

		return $ajax->send();
	}
}
