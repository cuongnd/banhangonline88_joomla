<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * Component's router for apps view.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialRouterApps extends SocialRouterAdapter
{
	/**
	 * Construct's the app's url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function build( &$menu , &$query )
	{
		$segments 	= array();

		// If there is no active menu for friends, we need to add the view.
		if( $menu && $menu->query[ 'view' ] != 'apps' )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}

		if( !$menu )
		{
			$segments[]	= $this->translate( $query[ 'view' ] );
		}
		unset( $query[ 'view' ] );

		// From here if element is set, then we point it to child app router and build the segments from the child app router
		// If not then we proceed with the usual
		// We default group to SOCIAL_APPS_GROUP_USER
		if( !empty( $query['element'] ) )
		{
			$group = empty( $query['group'] ) ? SOCIAL_APPS_GROUP_USER : $query['group'];
			$element = $query['element'];

			unset( $query['group'] );
			unset( $query['element'] );

			$router = $this->getAppRouter( $group, $element );

			// If unable to get the app router, then skip this and continue the normal build
			if( $router !== false && is_callable( array( $router, 'build' ) ) )
			{
				// If router is available, then we append group and element into the segments
				$segments += array( $this->translate( $this->name . '_' . $group ), $this->translate( $this->name . '_' . $group . '_' . $element ) );

				// Append the returned array into the original segment
				$segments += $router->build( $menu, $query );

				return $segments;
			}
		}

		$layout 	= isset( $query[ 'layout' ] ) ? $query[ 'layout' ] : null;

		if( !is_null( $layout ) )
		{
			$segments[]	= $this->translate( 'apps_layout_' . $layout );
			unset( $query[ 'layout' ] );
		}

		$id 		= isset( $query[ 'id' ] ) ? $query[ 'id' ] : null;

		if( !is_null( $id ) )
		{
			$segments[]	= $id;
			unset( $query[ 'id' ] );
		}

		// Determines if filter is set
		$filter 		= isset( $query[ 'filter' ] ) ? $query[ 'filter' ] : null;

		if( !is_null( $filter ) )
		{
			$segments[]	= $this->translate( 'apps_filter_' . $filter );
			unset( $query[ 'filter' ] );
		}

		// Translate uid and type for apps view
		$uid 	= isset( $query[ 'uid' ] ) ? $query[ 'uid' ] : null;
		$type 	= isset( $query[ 'type' ] ) ? $query[ 'type' ] : null;

		if( !is_null( $uid ) && !is_null( $type ) )
		{
			$segments[]	= $type;
			$segments[]	= $uid;

			unset( $query[ 'uid' ] );
			unset( $query[ 'type' ] );
		}

		// Determines if filter is set
		$sort 		= isset( $query[ 'sort' ] ) ? $query[ 'sort' ] : null;

		if( !is_null( $sort ) )
		{
			$segments[]	= $this->translate( 'apps_sort_' . $sort );
			unset( $query[ 'sort' ] );
		}

		// Determines if userid is set
		$userId 		= isset( $query[ 'userid' ] ) ? $query[ 'userid' ] : null;

		if( !is_null( $userId ) )
		{
			$segments[]	= $query[ 'userid' ];
			unset( $query[ 'userid' ] );
		}

		return $segments;
	}


	/**
	 * Translates the SEF url to the appropriate url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	An array of url segments
	 * @return	array 	The query string data
	 */
	public function parse( &$segments )
	{
		$vars 		= array();
		$total 		= count( $segments );

		// URL: http://site.com/menu/apps
		if( $total == 1 && $segments[ 0 ] == $this->translate( 'apps' ) )
		{
			$vars[ 'view' ]	= 'apps';

			return $vars;
		}

		// Check if should go to child router or not
		// URL: http://site.com/menu/apps/group/element
		if( $total >= 3 )
		{
			// Check for possible group here
			if( $segments[1] === $this->translate( $this->name . '_' . SOCIAL_APPS_GROUP_USER ) )
			{
				$group = $segments[1];
				$element = $segments[2];

				$router = $this->getAppRouter( $group, $element );

				if( $router !== false && is_callable( array( $router, 'parse' ) ) )
				{
					// Only need to pass the remaining segments
					$rebuild = array();

					if( $total > 3 )
					{
						$rebuild = array_slice( $segments, 3 );
					}

					$childVars = $router->parse( $rebuild );

					// It is possible that childVars return false because child router can verify if the url is valid or not
					if( $childVars === false )
					{
						return array();
					}

					$vars = array_merge( $vars, $childVars );

					return $vars;
				}
			}

			// If no group matched, then we proceed to normal parsing below
		}

		// URL: http://site.com/menu/apps/mine
		if( $total == 2 && $segments[ 0 ] == $this->translate( 'apps' ) && $segments[ 1 ] == $this->translate( 'apps_filter_mine' ) )
		{
			$vars[ 'view' ]		= 'apps';
			$vars[ 'filter' ]	= 'mine';

			return $vars;
		}

		// URL: http://site.com/menu/apps/trending
		$sortItems	= array( $this->translate('apps_sort_alphabetical') , $this->translate( 'apps_sort_trending' ) , $this->translate( 'apps_sort_recent' ) );

		if( $total == 2 && in_array( $segments[ 1 ] , $sortItems ) )
		{
			$vars[ 'view' ]	= 'apps';

			if( $segments[ 1 ] == $this->translate( 'apps_sort_alphabetical' ) )
			{
				$sort 	= 'alphabetical';
			}

			if( $segments[ 1 ] == $this->translate( 'apps_sort_trending' ) )
			{
				$sort 	= 'trending';
			}

			if( $segments[ 1 ] == $this->translate( 'apps_sort_recent' ) )
			{
				$sort 	= 'recent';
			}

			$vars[ 'sort' ]	= $sort;

			return $vars;
		}

		// URL: http://site.com/menu/apps/canvas/ID-app-alias
		if( $total == 3 && $segments[ 1 ] == $this->translate( 'apps_layout_canvas' ) )
		{
			$vars[ 'view' ]		= 'apps';
			$vars[ 'layout' ]	= 'canvas';
			$vars[ 'id' ]		= $segments[ 2 ];

			return $vars;
		}

		// URL: http://site.com/menu/apps/canvas/ID-app-alias/ID-user-alias
		if( $total == 4 && $segments[ 1 ] == $this->translate( 'apps_layout_canvas' ) )
		{
			$vars[ 'view' ]		= 'apps';
			$vars[ 'layout' ]	= 'canvas';
			$vars[ 'id' ]		= $segments[ 2 ];
			$vars[ 'userid' ]	= $this->getUserId( $segments[ 3 ] );

			return $vars;
		}

		// URL: http://site.com/menu/apps/canvas/ID-app-alias/GROUP/ID-user-alias
		if( $total == 5 && $segments[ 1 ] == $this->translate( 'apps_layout_canvas' ) )
		{
			$vars[ 'view' ]		= 'apps';
			$vars[ 'layout' ]	= 'canvas';
			$vars[ 'id' ]		= $this->getIdFromPermalink( $segments[ 2 ] , SOCIAL_TYPE_APPS );
			$vars[ 'type' ]		= $segments[ 3 ];
			$vars[ 'uid' ]		= $this->getUserId( $segments[ 4 ] );

			return $vars;
		}

		return $vars;
	}

	private function getAppRouter( $group, $element )
	{
		static $adapters = array();

		if( empty( $adapters[$group][$element] ) )
		{
			$file = SOCIAL_APPS . '/' . $group . '/' . $element . '/router.php';

			if( !JFile::exists( $file ) )
			{
				return false;
			}

			$classname = 'SocialRouterApps' . ucfirst( $group ) . ucfirst( $element );

			if( !class_exists( $classname ) )
			{
				require_once( $file );
			}

			require_once( $file );

			if( !class_exists( $classname ) )
			{
				return false;
			}

			$class = new $classname($this->name);

			// Init a few properties
			$class->group = $group;
			$class->element = $element;

			$adapters[$group][$element] = $class;
		}

		return $adapters[$group][$element];
	}
}

abstract class SOcialRouterAppsAdapter extends SocialRouterApps
{
	// This is a function for child router to use
	// Rather than constructing a the translation string from APPS_GROUP_ELEMENT_TASK, child router only need to pass in TASK
	public function subtranslate( $task )
	{
		$prefix = $this->name . '_' . $this->group . '_' . $this->element;

		$string = $prefix . '_' . $task;

		return $this->translate( $string );
	}
}
