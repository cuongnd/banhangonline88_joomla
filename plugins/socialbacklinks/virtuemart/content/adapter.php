<?php
/**
 * SocialBacklinks Virtuemart plugin
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
 * Plugin for default Virtuemart content
 */
class PlgSBVirtuemartAdapter extends SBPluginsContent
{
	/**
	 * The lowercase lang tag
	 * ex fr_fr
	 * @var string
	 */
	protected $_lang_tag;

	/**
	 * The fields map
	 * @var array
	 */
	protected $_map;

	/**
	 * Constructor
	 * @param  Jplugin Object that has registered current plugin
	 * @param  array  The list of plugin options
	 * @return void
	 */
	public function __construct( $caller, $options = array() )
	{
		// We - unfortunately - need a dedicated MySQL connection because
		// we are in the constructor and the constructor is called
		// by the synchronizer (which is a system plugin).
		
		$conf = JFactory::getConfig();
		$host = $conf->get('host');
		$user = $conf->get('user');
		$password = $conf->get('password');
		$database = $conf->get('db');
		$prefix = $conf->get('dbprefix');
		$driver = $conf->get('dbtype');
		
		$query = "SELECT `config` FROM #__virtuemart_configs ORDER BY `virtuemart_config_id` DESC LIMIT 1;";
		$query = str_replace('#__', $prefix, $query);
		
		$result = null;
		try {
			if ($driver == 'mysql') {
				$c = @mysql_connect($host, $user, $password, TRUE);
				@mysql_select_db($database, $c);
				$r = @mysql_query($query, $c);
				$r1 = @mysql_fetch_array($r);
				$result = $r1['config'];
				@mysql_close($c);
			} else { // mysqli
				$c = @mysqli_connect($host, $user, $password, $database);
				$r = @mysqli_query($c, $query);
				$r1 = @mysqli_fetch_array($r);
				$result = $r1['config'];
				@mysqli_close($c);
			}
		} catch(Exception $e) { }
		
		if ($result == NULL)
			return;

		
		$entries = explode('|', $result);
		$config = array();
		foreach($entries as $entry)
		{
			$contents = explode('=', $entry);
			$name = array_shift($contents);
			$value = unserialize(implode('=', $contents));
			$config[$name] = $value;
		}
		$lang_tag = @$config['vmlang'];
		$this->_lang_tag = $lang_tag;
		
		$this->_map = array(
						'items_table' => array( 
							'__table' => "(SELECT * FROM #__virtuemart_products p LEFT JOIN #__virtuemart_products_{$lang_tag} pl USING(`virtuemart_product_id`))",
							'id' => 'virtuemart_product_id',
							'title' => 'product_name', 
							'content' => 'product_desc',
							'created' => 'created_on',
							'created_by' => 'created_by',
							'modified' => 'modified_on',
							'modified_by' => 'modified_by',
							'publish_up' => null,
							'publish_down' => null,
							),
						'categories_table' => array( 
							'__table' => "#__virtuemart_categories_{$lang_tag}" ,
							'id' => 'virtuemart_category_id',
							'title' => 'category_name'
							)
						);
		
		/*$default = array(
			'add_plugin_content_items_js_file' => true,
			'use_plugin_category_row_tmpl' => true, 
			'sync_desc' => false
		);*/
		
		$default = array(
			'sync_desc' => true,
			'fields_to_show_in_query_list' => array(
					array(
						'title' => 'Title',
						'field' => 'title',
						'class' => 'first'
					),
					array(
						'title' => 'Categories',
						'field' => 'cctitle',
						'width' => '140'
					),
					array(
						'title' => 'Modification',
						'field' => 'author',
						'width' => '40'
					),
					array(
						'title' => 'Creation',
						'field' => 'product_created',
						'width' => '40'
					),
					array(
						'title' => 'ID',
						'field' => 'product_id',
						'width' => '10'
					)
				)
			);

		$options = array_merge( $default, $options );
		parent::__construct( $caller, $options );
	}

	/**
	 * @see SBAdaptersPlugin::getAlias()
	 */
	public function getAlias( )
	{
		return 'virtuemart';
	}

	/**
	 * @see SBPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where = array();
		$nowdate = $settings['nowdate'];
		$last_sync = $settings['last_sync'];
		$nulldate = $settings['nulldate'];
		
		// Not available in virtuemart
		//$where['publish_up'] = '(tbl.`' . $this->get( 'items_table.publish_up' ) . '` = 0 OR tbl.`' . $this->get( 'items_table.publish_up' ) . '` <= now())';
		//$where['publish_down'] = '(tbl.`' . $this->get( 'items_table.publish_down' ) . "` = 0 OR tbl.`" . $this->get( 'items_table.publish_down' ) . "` > now())";

		if ( $this->sync_updated ) {
			$where[] = '(TIMEDIFF(p.`' . $this->get( 'items_table.modified' ) . "`,$last_sync) >= 0 OR TIMEDIFF(p.`" . $this->get( 'items_table.created' ) . "`,$last_sync) >= 0)";
		}
		else {
			$where[] = 'TIMEDIFF(p.`' . $this->get( 'items_table.created' ) . "`,$last_sync) >= 0";
		}

		if ( $this->selected_content ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' p.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' p.`' . $this->get( 'items_table.catid' ) . '` IN (' . implode( ', ', $this->categories ) . ') ';
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}
		
		$where[] = 'p.`published` = 1';
		
		if ( isset( $where['selected_content'] ) ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' p.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' EXISTS( SELECT 1 FROM `#__virtuemart_product_categories` cati'
					. ' WHERE cati.`virtuemart_product_id` = p.`' . $this->get( 'items_table.id' ) . '`'
					. ' AND cati.`virtuemart_category_id` IN (' . implode( ', ', $this->categories ) . ') ) ';
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}
		
		return $where;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemRoute()
	 */
	public function getItemRoute( $item )
	{
		return 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->id;
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO ();
		$query = 'SELECT c.`virtuemart_category_id`, cl.`category_name`, cxref.`category_parent_id`'
				. ' FROM #__virtuemart_categories c'
				. " INNER JOIN #__virtuemart_categories_{$this->_lang_tag} cl ON c.`virtuemart_category_id` = cl.`virtuemart_category_id`"
				. ' LEFT JOIN #__virtuemart_category_categories cxref ON c.`virtuemart_category_id` = cxref.`category_child_id`'
				. ' WHERE c.`published` = 1'
				. ' ORDER BY c.`ordering`';
		$db->setQuery ($query);
		$cats = $db->loadObjectList ();
		
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[] = array(
				'_type' => 'category',
				'title' => $cat->category_name,
				'id' => $cat->virtuemart_category_id,
				'parent_id' => $cat->category_parent_id,
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
	
	/**
	 * @see SBPluginsContentsInterface::getCategoryItems()
	 */
	public function getCategoryItems( $category_id, $level )
	{
		$query = 'SELECT p.`' . $this->get( 'items_table.title' ) . '` AS title, p.`' . $this->get( 'items_table.id' ) . '` AS id'
				. ' FROM ' . $this->get( 'items_table.__table' ) . ' AS p'
				. ' INNER JOIN `#__virtuemart_product_categories` cati ON cati.`virtuemart_product_id` = p.`' . $this->get( 'items_table.id' ) . '`'
				. " WHERE cati.`virtuemart_category_id` = '$category_id'";

		return $query;
	}
	
	/**
	 * @see SBPluginsContentsInterface::getItemsDetailed()
	 */
	public function getItemsDetailed()
	{
		$query = new stdClass();
		
		$subquery = "(SELECT GROUP_CONCAT(DISTINCT cat.`" . $this->get( 'categories_table.title' ) . "` ORDER BY cat.`" . $this->get( 'categories_table.title' ) . "` DESC SEPARATOR ', ')"
			. ' FROM `' . $this->get( 'categories_table.__table' ) . '` AS cat'
			. ' INNER JOIN `#__virtuemart_product_categories` AS cati ON cati.`virtuemart_category_id` = cat.`' . $this->get( 'categories_table.id' ) . '`'
			. ' WHERE cati.`virtuemart_product_id` = p.`' . $this->get( 'items_table.id' ) . '`'
			. ' GROUP BY cati.`virtuemart_product_id`)';
		
		$query->select = 'SELECT p.`'. $this->get('items_table.id') .'` AS id, p.`' . $this->get('items_table.created') . '` AS created, p.`' . $this->get('items_table.modified') . '` AS author, p.`' . $this->get( 'items_table.title' ) . '` AS title, p.`' . $this->get( 'items_table.content' ) . '` AS introtext, ' . $subquery . ' AS cctitle, \'\' AS rien'
			. ' FROM ' . $this->get( 'items_table.__table' ) . ' AS p';

		return $query;
	}
	
}
