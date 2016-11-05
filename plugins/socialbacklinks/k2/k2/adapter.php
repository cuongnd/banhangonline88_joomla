<?php
/**
 * SocialBacklinks content plugin for K2
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Plugin for K2
 */
class PlgSBK2Adapter extends SBPluginsContent
{
	protected $_map = array(
		'items_table' => array( '__table' => '#__k2_items' ),
		'categories_table' => array( '__table' => '#__k2_categories', 'title'=>'name' )
	);

	/**
	 * @see SBAdaptersPlugin::getAlias()
	 */
	public function getAlias( )
	{
		return 'k2';
	}

	/**
	 * @see SBAdaptersPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where = parent::getNewItemsConditions( $settings );
		$where[] = 'tbl.`published` = 1';
		$where[] = 'tbl.`trash` = 0';
		return $where;
	}

	/**
	 * @see SBAdaptersPluginsContentsInterface::getItemRoute()
	 */
	public function getItemRoute( $item )
	{
		JLoader::register( 'K2HelperRoute', JPATH_ROOT . '/components/com_k2/helpers/route.php' );
		return K2HelperRoute::getItemRoute( $item->id, $item->catid );
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO ();
		$query = 'SELECT cat.`id`, cat.`name`, cat.`parent` FROM `#__k2_categories` AS cat WHERE cat.`published` = 1 AND cat.`trash` = 0 ORDER BY cat.`ordering`';
		$db->setQuery ($query);
		$cats = $db->loadObjectList ();
		
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[] = array(
				'_type' => 'category',
				'title' => $cat->name,
				'id' => $cat->id,
				'parent_id' => $cat->parent,
				'_hasChildren' => false,
				'_children' => array()
			);
		}

		$root = array(
				'_type' => 'category',
				'title' => 'Select a category',
				'id' => 0,
				'parent_id' => null,
				'_hasChildren' => false,
				'_children' => array( )
			);
		$this->assignChildren( $root, $categories );
		
		$result[] = $root;
		
		return $result;
	}

	/**
	 * Recursive function that uses pointers to get the Tree
	 */
	public function assignChildren( &$item, &$categories )
	{
		if ($item['_hasChildren'])
			return;
			
		$item['_hasChildren'] = true;
		foreach( $categories as &$category )
		{
			if ( $category['parent_id'] == $item['id'] )
			{
				$item['_children'][] = &$category;
				$this->assignChildren( $category, $categories );
			}
		}
	}

}
