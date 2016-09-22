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

jimport('joomla.html.pagination');

class EasyBlogPagination extends JPagination
{
	public function __construct($total, $limitstart, $limit, $prefix = '')
	{
		parent::__construct($total, $limitstart, $limit, $prefix);
	}

	// alias method
	public function getPagesLinks( $viewpage = 'latest', $filtering = array(), $doReplace = false )
	{
		return $this->toHTML( $viewpage, $filtering, $doReplace );
	}

	/**
	 *
	 * $filtering
	 *      if index page:
	 *      category_id
	 *      filter
	 *      sort
	 *      query
	 */
	public function toHTML( $viewpage = 'index', $doReplace = false )
	{
		$data	= $this->getData();

		if( count( $data->pages ) == $this->get('pages.total') && $this->get('pages.total') == '1' || $this->get( 'pages.total' ) == 0 )
		{
			return false;
		}

		$queries	= '';

		if( !empty( $data ) && $doReplace)
		{
			$curPageLink	= 'index.php?option=com_easyblog&view=' . $viewpage . $queries;

			foreach( $data->pages as $page )
			{
				if( !empty( $page->link ) )
				{
					$limitstart	= ( !empty($page->base) ) ? '&limitstart=' . $page->base : '';
					$page->link	= EasyBlogRouter::_( $curPageLink . $limitstart);
				}
			}

			// newer link
			if( !empty( $data->next->link ) )
			{
				$limitstart = ( !empty($data->next->base) ) ? '&limitstart=' . $data->next->base : '';
				$data->next->link = EasyBlogRouter::_( $curPageLink . $limitstart);
			}

			// older link
			if( !empty( $data->previous->link ) )
			{
				$limitstart = ( !empty($data->previous->base) ) ? '&limitstart=' . $data->previous->base : '';
				$data->previous->link = EasyBlogRouter::_( $curPageLink . $limitstart);
			}
		}

		$theme		= new CodeThemes();
		$theme->set( 'data' , $data );
		return $theme->fetch( 'blog.pagination.php' );
	}
}
