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

class EasyBlogViewDashboard extends EasyBlogView
{
	/**
	 * Responds to the getcategory ajax call by return a list of category items.
	 *
	 * @access	public
	 * @param	null
	 */
	public function getCategory()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );
		$id 	= JRequest::getInt( 'id' );

		$model	= $this->getModel( 'categories' );
		$items	= $model->getChildCategories( $id , true, true );

		if( !$items )
		{
			return $ajax->success( array() );
		}

		$categories 	= array();

		for($i = 0; $i < count($items); $i++)
		{
			$item           = $items[$i];

			$category 		= EasyBlogHelper::getTable( 'Category' );
			$category->load( $item->id );

			$item->hasChild = $category->getChildCount();
		}

 		$ajax->success( $items );
	}

	public function buildcategorytier()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );
		$id 	= JRequest::getInt( 'id' );

		if( empty($id) )
		{
			return $ajax->fail();
		}

		$loop           = true;
		$scategory 		= EasyBlogHelper::getTable( 'Category' );
		$scategory->load( $id );

		$model			= $this->getModel( 'categories' );
		$tier 			= array();

		$searchId       = $scategory->parent_id;
		while( $loop )
		{
			if( empty( $searchId ) )
			{
				$loop   = false;
			}
			else
			{
				$category 		= EasyBlogHelper::getTable( 'Category' );
				$category->load( $searchId );
				$tier[]   		= $category;

				$searchId = $category->parent_id;
			}
		}

		// get the root tier
		$root   = array_pop( $tier );

		//reverse the array order
		$tier	= array_reverse($tier);

		array_push($tier, $scategory);
// 		echo '<pre>';
// 		print_r($tier);
// 		echo '</pre>';
// 		exit;


		$categories = array();

		foreach( $tier as $cat )
		{

			$pItem  			= new stdClass();

			$pItem->id  		= $cat->id;
			$pItem->parent_id  	= $cat->parent_id;
			$pItem->hasChild    = 1;


			$model	= $this->getModel( 'categories' );
			$items	= $model->getChildCategories( $cat->parent_id, true, true );

			if( !$items )
			{
				$pItem->hasChild = 0;
				$categories[]   = $pItem;
				continue;
			}

			for($i = 0; $i < count($items); $i++)
			{
				$item           = $items[$i];

				$category 		= EasyBlogHelper::getTable( 'Category' );
				$category->load( $item->id );

				$item->hasChild = $category->getChildCount();
			}

			$pItem->childs  = $items;
			$categories[]   = $pItem;
		}


// 		echo '<pre>';
// 		print_r($categories);
// 		echo '</pre>';
// 		exit;

		$ajax->success( $categories );
	}
}
