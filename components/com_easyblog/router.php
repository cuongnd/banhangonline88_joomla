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

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
JTable::addIncludePath( EBLOG_TABLES );

function EasyBlogBuildRoute(&$query)
{
	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

	$segments	= array();
	$config		= EasyBlogHelper::getConfig();

	if(isset($query['view']) && $query['view'] == 'entry' && isset($query['id']) )
	{
		if( $config->get( 'main_sef' ) != 'simple' )
		{
			$segments[] = EasyBlogSEFTranslation::_( $query['view'] );
		}

		$segments[]	= EasyBlogRouter::getBlogSefPermalink( $query['id'] );

		unset($query['id']);
		unset($query['view']);
	}

	if(isset($query['view']) && $query['view'] == 'categories' && isset($query['id']))
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );

		if( $config->get( 'main_sef' ) != 'simple' )
		{
			if(isset($query['layout']))
			{
				$segments[] = EasyBlogSEFTranslation::_( $query['layout'] );
			}
		}

		$segments[]	= EasyBlogRouter::getCategoryPermalink( $query['id'] );

		unset($query['id']);
		unset($query['view']);
		unset($query['layout']);
	}

	if(isset($query['view']) && $query['view'] == 'tags' && isset($query['id']) && isset($query['layout']) )
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );
		$segments[]	= EasyBlogSEFTranslation::_( $query['layout'] );
		$segments[]	= EasyBlogRouter::getTagPermalink( $query['id'] );

		unset($query['id']);
		unset($query['view']);
		unset($query['layout']);
	}

	if(isset($query['view']) && $query['view'] == 'teamblog' && isset($query['id'])  )
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );

		if( isset( $query['layout'] ) )
		{
			if($query['layout'] == "statistic")
			{
				$segments[]	= EasyBlogRouter::getTeamBlogPermalink( $query['id'] );
				$segments[] = EasyBlogSEFTranslation::_( $query['layout'] );
				$segments[]	= EasyBlogSEFTranslation::_( $query['stat'] );

				if($query['stat'] == 'category')
				{
					$segments[]	= EasyBlogRouter::getCategoryPermalink( $query['catid'] );
					unset($query['catid']);
				}

				if($query['stat'] == 'tag')
				{
					$segments[]	= EasyBlogRouter::getTagPermalink( $query['tagid'] );
					unset($query['tagid']);
				}
			}
			else
			{
				$segments[]	= EasyBlogSEFTranslation::_( $query['layout'] );
				$segments[]	= EasyBlogRouter::getTeamBlogPermalink( $query['id'] );
			}
		}
		else
		{
			$segments[]	= EasyBlogRouter::getTeamBlogPermalink( $query['id'] );
		}

		unset($query['id']);
		unset($query['stat']);
		unset($query['layout']);
		unset($query['view']);
	}

	if(isset($query['view']) && $query['view'] == 'blogger' && isset($query['id']))
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );

		if(isset($query['layout']))
		{
			if($query['layout'] == "statistic")
			{
				$segments[]	= EasyBlogRouter::getBloggerPermalink( $query['id'] );
				$segments[]	= EasyBlogSEFTranslation::_( $query['layout'] );
				$segments[]	= $query['stat'];

				if($query['stat'] == 'category')
				{
					$segments[]	= EasyBlogRouter::getCategoryPermalink( $query['catid'] );
					unset($query['catid']);
				}

				if($query['stat'] == 'tag')
				{
					$segments[]	= EasyBlogRouter::getTagPermalink( $query['tagid'] );
					unset($query['tagid']);
				}
			}
			else
			{
				$segments[]	= EasyBlogSEFTranslation::_( $query['layout'] );
				$segments[]	= EasyBlogRouter::getBloggerPermalink( $query['id'] );
			}
		}
		else
		{
			$segments[]	= EasyBlogRouter::getBloggerPermalink( $query['id'] );
		}

		unset($query['id']);
		unset($query['stat']);
		unset($query['view']);
		unset($query['layout']);
	}

	if(isset($query['view']) && $query['view'] == 'dashboard' && isset($query['layout']) )
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );
		$segments[] = EasyBlogSEFTranslation::_( $query['layout'] );

		if( isset($query['filter']) )
		{
			$segments[]	= $query['filter'];
			unset( $query['filter'] );
		}


		if( isset( $query[ 'postType' ] ) )
		{
			$segments[]	= $query['postType'];
			unset( $query['postType'] );
		}

		unset($query['view']);
		unset($query['layout']);
	}

	/**
	 * Route for archive links
	 **/
	if(isset($query['view']) && $query['view'] == 'archive' )
	{
		$segments[] = EasyBlogSEFTranslation::_( $query['view'] );
		unset( $query[ 'view' ] );

		if( isset( $query[ 'layout'] ) )
		{
			$segments[]	= $query[ 'layout' ];
			unset( $query[ 'layout' ] );
		}

		if( isset( $query['archiveyear'] ) )
		{
			$segments[]	= $query['archiveyear'];
			unset( $query[ 'archiveyear' ] );
		}

		if( isset( $query['archivemonth'] ) )
		{
			$segments[]	= $query[ 'archivemonth' ];
			unset( $query[ 'archivemonth' ] );
		}

		if( isset( $query['archiveday'] ) )
		{
			$segments[]	= $query[ 'archiveday' ];
			unset( $query[ 'archiveday' ] );
		}
	}

	if(isset($query['view']) && $query['view'] == 'search' )
	{
		$segments[] 	= EasyBlogSEFTranslation::_( $query['view'] );

		unset( $query[ 'view' ] );

		if( isset( $query['layout'] ) )
		{
			$segments[]	= $query[ 'layout' ];
			unset( $query[ 'layout' ] );
		}

		if( isset( $query['query'] ) )
		{
			$segments[]	= $query[ 'query' ];
			unset( $query[ 'query' ] );
		}
	}

	if( isset( $query['view'] ) && $query['view'] != 'images' )
	{
		$segments[] 	= EasyBlogSEFTranslation::_( $query['view'] );
		unset( $query['view'] );
	}

	if( isset($query['type'] ) )
	{
		if(!isset($query['format']) && !isset($query['controller']))
		{
			$segments[]	= $query['type'];
			unset( $query['type'] );
		}
	}

	if( !isset($query['Itemid'] ) )
	{
		$query['Itemid']	= EasyBlogRouter::getItemId();
	}

	return $segments;
}

function EasyBlogParseRoute( &$segments )
{
	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

	$vars	= array();
	$menu	= JFactory::getApplication()->getMenu();
	$item	= $menu->getActive();
	$config	= EasyBlogHelper::getConfig();

	//feed view
	if(isset($segments[1]))
	{
		if( $segments[1] == 'rss' || $segments[1] == 'atom' )
		{
			$vars['view']	= $segments[0];
			unset( $segments );
			return $vars;
		}
	}

	// If user chooses to use the simple sef setup, we need to add the proper view
	if( $config->get( 'main_sef' ) == 'simple' && count( $segments ) == 1 )
	{
		$files = JFolder::folders( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'views' );
		$views = array();

		foreach( $files as $file )
		{
			$views[] = EasyBlogSEFTranslation::_( $file );
		}

		if( !in_array( $segments[0] , $views ) )
		{
			array_unshift( $segments , EasyBlogSEFTranslation::_( 'entry' ) );
		}
	}

	if( $config->get( 'main_sef' ) == 'simple' && count( $segments ) == 2 && $segments[ 0 ] == 'categories' )
	{
		array_push( $segments , EasyBlogSEFTranslation::_( 'listings' ) );
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'entry' ) )
	{
		$count	= count($segments);

		$entryId    = '';
		if( $config->get( 'main_sef_unicode' ) )
		{
			// perform manual split on the string.
			$permalinkSegment   = $segments[ ( $count - 1 ) ];
			$permalinkArr    	= explode( ':', $permalinkSegment);
			$entryId            = $permalinkArr[0];
		}
		else
		{
			$table			= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$table->load( $segments[ ( $count - 1 ) ] , true );
			$entryId		= $table->id;
		}

		if( $entryId )
		{
			$vars[ 'id' ]	= $entryId;
		}
		$vars[ 'view' ]	= 'entry';
	}

	if( isset( $segments[ 0 ] ) && $segments[ 0 ] == EasyBlogSEFTranslation::_('archive' ) )
	{
		$vars[ 'view' ]	= 'archive';

		$count			= count($segments);
		$totalSegments	= $count - 1;

		if( $totalSegments >= 1 )
		{
			$indexSegment	= 1;

			if( $segments[ 1 ] == 'calendar' )
			{
				$vars[ 'layout' ]	= 'calendar';
				$indexSegment		= 2;
			}

			// First segment is always the year
			if( isset( $segments[ $indexSegment ] ) )
			{
				$vars[ 'archiveyear' ]	= $segments[ $indexSegment ];
			}

			// Second segment is always the month
			if( isset( $segments[ $indexSegment + 1 ] ) )
			{
				$vars[ 'archivemonth' ]	= $segments[ $indexSegment + 1 ];
			}

			// Third segment is always the day
			if( isset( $segments[ $indexSegment + 2 ] ) )
			{
				$vars[ 'archiveday' ]	= $segments[ $indexSegment + 2 ];
			}
		}

	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'categories' ) )
	{
		$count	= count($segments);
		if( $count > 1 )
		{
			$categoryId = '';
			if( $config->get( 'main_sef_unicode' ) )
			{
				$segmentIndex 		= $count - 1;

				if( $config->get( 'main_sef' ) == 'simple' )
				{
					$segmentIndex	= 1;
				}

				// perform manual split on the string.
				$permalinkSegment   = $segments[ $segmentIndex ];
				$permalinkArr    	= explode( ':', $permalinkSegment);
				$categoryId         = $permalinkArr[0];
			}

			$segments   	= EasyBlogRouter::_encodeSegments($segments);

			if( empty( $categoryId ) )
			{
				$table			= EasyBlogHelper::getTable( 'Category' , 'Table' );
				$permalink 		= $segments[ 1 ];

				$table->load( $permalink , true );

				if( !$table->id )
				{
					$table->load( $segments[ ( $count - 1 ) ] , true );
				}

				$categoryId = $table->id;
			}

			$vars[ 'id' ]	= $categoryId;

			$vars['layout']	= 'listings';
		}
		$vars[ 'view' ]	= 'categories';
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'tags' ) )
	{
		$count	= count($segments);
		if( $count > 1 )
		{
			$tagId = '';
			if( $config->get( 'main_sef_unicode' ) )
			{
				// perform manual split on the string.
				$permalinkSegment   = $segments[ ( $count - 1 ) ];
				$permalinkArr    	= explode( ':', $permalinkSegment);
				$tagId         = $permalinkArr[0];
			}

			$segments   	= EasyBlogRouter::_encodeSegments($segments);
			if( empty( $tagId ) )
			{
				$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
				$table->load( $segments[ ( $count - 1 ) ] , true);
				$tagId  = $table->id;
			}

			$vars[ 'id' ]	= $tagId;
			$vars['layout']	= 'tag';
		}
		$vars[ 'view' ]	= 'tags';
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'blogger' ) )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$user			= 0;
			$rawSegments	= $segments;
			$segments		= EasyBlogRouter::_encodeSegments($segments);

			if( $config->get( 'main_sef_unicode' ) )
			{
				// perform manual split on the string.
				if( isset($segments[2]) && $segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
				{
					$permalinkSegment   = $rawSegments[1];
				}
				else
				{
					$permalinkSegment   = $rawSegments[ ( $count - 1 ) ];
				}

				$permalinkArr    	= explode( ':', $permalinkSegment);
				$bloggerId         	= $permalinkArr[0];
			}
			else
			{
				if( isset($segments[2]) && $segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
				{
					$permalink   = $segments[1];
				}
				else
				{
					$permalink = $segments[$count - 1];
				}

				if( $id = EasyBlogHelper::getUserId( $permalink ) )
				{
					$user      = JFactory::getUser( $id );
				}

				if( !$user )
				{
					// For usernames with spaces, we might need to replace with dashes since SEF will rewrite it.
					$id			= EasyBlogHelper::getUserId( JString::str_ireplace( '-' , ' ' , $permalink ) );
					$user		= JFactory::getUser( $id );
				}

				if( !$id )
				{
					// For usernames with spaces, we might need to replace with dashes since SEF will rewrite it.
					$id			= EasyBlogHelper::getUserId( JString::str_ireplace( '-' , '_' , $permalink ) );
					$user		= JFactory::getUser( $id );
				}

				$bloggerId  = $user->id;
			}

			$vars['id']		= $bloggerId;

			if($count > 2)
			{
				if($segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
				{
					$vars['layout']	= 'statistic';

					if($count == 5)
					{
						if(isset($segments[3]))
						{
							$vars['stat'] = '';

							switch( EasyBlogSEFTranslation::_( $segments[3] ) )
							{
								case EasyBlogSEFTranslation::_( 'category' ):
									if( $config->get( 'main_sef_unicode' ) )
									{
										// perform manual split on the string.
										$permalinkSegment   = $rawSegments[4];
										$permalinkArr    	= explode( ':', $permalinkSegment);
										$categoryId         = $permalinkArr[0];
									}
									else
									{
										$table = EasyBlogHelper::getTable( 'Category' , 'Table' );
										$table->load( $segments[4] , true );
										$categoryId = $table->id;
									}
									$vars['stat'] = 'category';
									$vars['catid'] = $categoryId;
									break;
								case EasyBlogSEFTranslation::_( 'tag' ):
									if( $config->get( 'main_sef_unicode' ) )
									{
										// perform manual split on the string.
										$permalinkSegment   = $segments[4];
										$permalinkArr    	= explode( ':', $permalinkSegment);
										$tagId         		= $permalinkArr[0];
									}
									else
									{
										$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
										$table->load( $segments[4] , true);
										$tagId  = $table->id;
									}
									$vars['stat'] = 'tag';
									$vars['tagid'] = $tagId;
									break;
								default:
									// do nothing.
							}
						}
					}
				}
				else
				{
					$vars['layout']	= 'listings';
				}
			}
		}
		$vars[ 'view' ]	= 'blogger';
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'dashboard' ) )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			switch( EasyBlogSEFTranslation::_( $segments[1] ) )
			{
				case EasyBlogSEFTranslation::_( 'write' ):
					$vars['layout']	= 'write';
				break;
				case EasyBlogSEFTranslation::_( 'profile' ):
					$vars['layout']	= 'profile';
				break;
				case EasyBlogSEFTranslation::_( 'drafts' ):
					$vars['layout']	= 'drafts';
				break;
				case EasyBlogSEFTranslation::_( 'entries' ):
					$vars['layout']	= 'entries';
				break;
				case EasyBlogSEFTranslation::_( 'comments' ):
					$vars['layout']	= 'comments';
				break;
				case EasyBlogSEFTranslation::_( 'categories' ):
					$vars['layout']	= 'categories';
				break;
				case EasyBlogSEFTranslation::_( 'listCategories' ):
					$vars['layout']	= 'listCategories';
				break;
				case EasyBlogSEFTranslation::_( 'category' ):
					$vars['layout']	= 'category';
				break;
				case EasyBlogSEFTranslation::_( 'tags' ):
					$vars['layout']	= 'tags';
				break;
				case EasyBlogSEFTranslation::_( 'review' ):
					$vars['layout']	= 'review';
				break;
				case EasyBlogSEFTranslation::_( 'pending' ):
					$vars['layout']	= 'pending';
				break;
				case EasyBlogSEFTranslation::_( 'teamblogs' ):
					$vars['layout']	= 'teamblogs';
				break;
				case EasyBlogSEFTranslation::_( 'microblog' ):
					$vars['layout']	= 'microblog';
				break;
			}

			if( isset( $vars['layout'] ) && $vars['layout'] == 'entries' )
			{
				if( count( $segments ) == 3 )
				{
					if( isset($segments[2]) )
					{
						$vars['postType']	= $segments[2];
					}
				}

				if( count( $segments ) == 4 )
				{
					if( isset($segments[2]) )
					{
						$vars['filter']	= $segments[2];
					}

					if( isset($segments[3]) )
					{
						$vars['postType'] = $segments[3];
					}
				}
			}
			else
			{
				if( isset($segments[2]) )
				{
					$vars['filter']	= $segments[2];
				}
			}
		}
		$vars[ 'view' ]	= 'dashboard';
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'teamblog' ) )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$rawSegments	= $segments;
			$segments   	= EasyBlogRouter::_encodeSegments($segments);

			if( $config->get( 'main_sef_unicode' ) )
			{
				// perform manual split on the string.

				if( isset($segments[2]) && $segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
				{
					$permalinkSegment   = $rawSegments[1];
				}
				else
				{
					$permalinkSegment   = $rawSegments[ ( $count - 1 ) ];
				}

				$permalinkArr    	= explode( ':', $permalinkSegment);
				$teamId         	= $permalinkArr[0];
			}
			else
			{
				if( isset($segments[2]) && $segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
				{
					$permalink = $segments[1];
				}
				else
				{
					$permalink = $segments[ ( $count - 1 ) ];
				}

				$table	= EasyBlogHelper::getTable( 'TeamBlog'  , 'Table' );
				$loaded = $table->load( $permalink , true);

				if( !$loaded )
				{
					$name = $segments[ ($count - 1 ) ];
					$name = JString::str_ireplace( ':' , ' ' , $name );
					$name = JString::str_ireplace( '-', ' ' , $name );
					$table->load( $name , true );
				}

				$teamId = $table->id;
			}
			$vars['id']		= $teamId;

			if(isset($segments[2]) && $segments[2] == EasyBlogSEFTranslation::_( 'statistic' ) )
			{
				$vars['layout']	= EasyBlogSEFTranslation::_( $segments[2] );

				if($count == 5)
				{
					if(isset($segments[3]))
					{
						$vars['stat'] = EasyBlogSEFTranslation::_( $segments[3] );

						switch( EasyBlogSEFTranslation::_( $segments[3] ) )
						{
							case EasyBlogSEFTranslation::_( 'category' ):
								if( $config->get( 'main_sef_unicode' ) )
								{
									// perform manual split on the string.
									$permalinkSegment   = $rawSegments[4];
									$permalinkArr    	= explode( ':', $permalinkSegment);
									$categoryId         = $permalinkArr[0];
								}
								else
								{
									$table = EasyBlogHelper::getTable( 'Category' , 'Table' );
									$table->load( $segments[4] , true );
									$categoryId = $table->id;
								}
								$vars['catid'] = $categoryId;
								break;
							case EasyBlogSEFTranslation::_( 'tag' ):
								if( $config->get( 'main_sef_unicode' ) )
								{
									// perform manual split on the string.
									$permalinkSegment   = $segments[4];
									$permalinkArr    	= explode( ':', $permalinkSegment);
									$tagId         		= $permalinkArr[0];
								}
								else
								{
									$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
									$table->load( $segments[4] , true);
									$tagId  = $table->id;
								}
								$vars['tagid'] = $tagId;
								break;
							default:
								// do nothing.
						}
					}
				}
			}
			else
			{
				$vars['layout']	= 'listings';
			}

		}

		$vars[ 'view' ]	= 'teamblog';
	}

	if( isset($segments[0]) && $segments[0] == EasyBlogSEFTranslation::_( 'search' ) )
	{
		$count	= count($segments);
		if( $count == 2 )
		{
			if($segments[1] == "parsequery")
			{
				$vars[ 'layout' ] = EasyBlogSEFTranslation::_( $segments[1] );
			}
			else
			{
				$vars[ 'query' ] = $segments[1];
			}

		}
		$vars['view']	= 'search';
	}

	$count	= count($segments);
	if( $count == 1 )
	{
		switch( EasyBlogSEFTranslation::_( $segments[0] ) )
		{
			case EasyBlogSEFTranslation::_( 'latest' ):
				$vars['view']	= 'latest';
				break;
			case EasyBlogSEFTranslation::_( 'featured' ):
				$vars['view']	= 'featured';
				break;
			case EasyBlogSEFTranslation::_( 'images' ):
				$vars['view']	= 'images';
				break;
			case EasyBlogSEFTranslation::_( 'login' ):
				$vars['view']	= 'login';
				break;
			case EasyBlogSEFTranslation::_( 'myblog' ):
				$vars['view']	= 'myblog';
				break;
			case EasyBlogSEFTranslation::_( 'ratings' ):
				$vars['view']	= 'ratings';
				break;
			case EasyBlogSEFTranslation::_( 'subscription' ):
				$vars['view']	= 'subscription';
				break;
			case EasyBlogSEFTranslation::_( 'trackback' ):
				$vars['view']	= 'trackback';
				break;
		}
	}

	if (!isset($vars['view'])) {
		return JError::raiseError(404, 'View Not Found');
	}

	return $vars;
}

class EasyBlogSEFTranslation
{
	public static function _( $val )
	{
		$config = EasyBlogHelper::getConfig();

		if( !$config->get( 'main_url_translation', 0) )
			return $val;

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
		$new	= JText::_( 'COM_EASYBLOG_SEF_' . strtoupper( $val ) );

		// If translation fails, we try to use the original value instead.
		if( stristr( $new , 'COM_EASYBLOG_SEF_' ) === false )
		{
			return $new;
		}


		return $val;
	}
}
