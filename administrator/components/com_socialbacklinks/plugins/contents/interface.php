<?php
/**
 * SocialBacklinks Interface for content plugins
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
 * Interface for Content plugins
 */
interface SBPluginsContentsInterface
{
	/**
	 * Returns the list of Where clauses for query
	 * @param array The settigns for query
	 * @return array
	 */
	public function getNewItemsConditions( $settings );

	/**
	 * Returns the SQL-query to get the list of articles of specific category
	 * @param integer $category_id 	identifier of specific category
	 * @param integer $level 		the level of category
	 * @return string
	 */
	public function getCategoryItems( $category_id, $level );
	
	/**
	 * Returns parts of SQL-query to get the list of detailed information about specific articles
	 * @return object
	 */
	public function getItemsDetailed( );

	/**
	 * Returns the link to item
	 * @param  JObject $item The item object
	 * @return string
	 */
	public function getItemRoute( $item );

	/**
	 * Returns the hierarchical tree of categories
	 * @return array
	 */
	public function getTreeOfCategories( );
}
