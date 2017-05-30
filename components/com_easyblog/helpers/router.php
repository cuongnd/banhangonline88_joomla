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

jimport( 'joomla.filter.filteroutput');

$jVerArr	= explode('.', JVERSION);
$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

if( $jVersion <= '3.1' )
{
	jimport( 'joomla.application.router' );
}
else
{
	jimport( 'joomla.libraries.cms.router' );
}


require_once( JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogJoomlaRouter extends JRouter
{
	public function encode( $segments )
	{
		return parent::_encodeSegments( $segments );
	}
}

class EasyBlogRouter
{
	public static function generatePermalink( $string )
	{
		$config 	= EasyBlogHelper::getConfig();
		$permalink  = '';

		if( $config->get( 'main_sef_unicode' ) )
		{
			//unicode support.
			$permalink  = EasyBlogRouter::permalinkUnicodeSlug($string);
		}
		else
		{
			// Replace accents to get accurate string
			$string     = EasyBlogRouter::replaceAccents( $string );

			// no unicode supported.
			$permalink	= JFilterOutput::stringURLSafe( $string );

			// check if anything return or not. If not, then we give a date as the alias.
			if(trim(str_replace('-','',$permalink)) == '')
			{
				$datenow	= EasyBlogHelper::getDate();
				$permalink 	= $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
			}
		}

		return $permalink;
	}

	public static function getLanguageQuery()
	{
		if( EasyBlogHelper::getJoomlaVersion() < '1.6' )
		{
			return '';
		}

		$lang		= JFactory::getLanguage()->getTag();

		$langQuery	= '';

		if( !empty( $lang ) && $lang != '*' )
		{
			$db			= EasyBlogHelper::db();
			$langQuery	= ' AND (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'language' ) . '=' . $db->Quote( $lang ) . ' OR `language` = '.$db->Quote('*').' )';
		}

		return $langQuery;
	}

	public static function permalinkUnicodeSlug( $string )
	{
		$str    = '';
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$str	= JFilterOutput::stringURLUnicodeSlug($string);
		}
		else
		{
			//replace double byte whitespaces by single byte (Far-East languages)
			$str = preg_replace('/\xE3\x80\x80/', ' ', $string);


			// remove any '-' from the string as they will be used as concatenator.
			// Would be great to let the spaces in but only Firefox is friendly with this

			$str = str_replace('-', ' ', $str);

			// replace forbidden characters by whitespaces
			$str = preg_replace( '#[:\#\*"@+=;!&\.%()\]\/\'\\\\|\[]#',"\x20", $str );

			//delete all '?'
			$str = str_replace('?', '', $str);

			//trim white spaces at beginning and end of alias, make lowercase
			$str = trim(JString::strtolower($str));

			// remove any duplicate whitespace and replace whitespaces by hyphens
			$str =preg_replace('#\x20+#','-', $str);
		}
		return $str;
	}

	public static function getDefaultRoutingOrder()
	{
		$config = EasyBlogHelper::getConfig();

		$routingType  		= array('bloggerstandalone','entry','category','blogger','teamblog');
		$routingTypeOrder 	= array();

		foreach($routingType as $key)
		{
			$config_key 	= 'main_routing_order_' . $key;
			$config_ignore 	= 'main_routing_order_' . $key . '_ignore';

			if( ! $config->get($config_ignore) )
			{
				$routingTypeOrder[$key]   = $config->get( $config_key , '0');
			}
		}

		// real sorting perform here.
		asort($routingTypeOrder);

		return $routingTypeOrder;
	}

	public static function _($url, $xhtml = true, $ssl = null , $search = false, $isCanonical = false )
	{
		$mainframe 	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();

		static $loaded 	= array();
		static $eUri 	= array();

		$useXHTML		= (int) $xhtml . (int) $isCanonical;

		if( isset( $loaded[$url . $useXHTML ] ) )
		{
			return $loaded[$url . $useXHTML ];
		}

		$rawURL			= $url;
		$blogger		= JRequest::getVar( 'blogger' , '' );

		if( !empty($blogger) )
		{
			$url	.= '&blogger=' . $blogger;
		}

		//$jURL	= JRoute::_($url, false);
		$jURL   = $url;

		// convert the string to variable so that we can access it.
		parse_str($jURL, $post);

		$view   = isset( $post['view'] ) ? $post['view'] : 'latest';
		$Itemid = isset( $post['Itemid'] ) ? $post['Itemid'] : '';

		$routingBehavior    = $config->get( 'main_routing', 'currentactive');
		$exItemid   		= '';
		$findItemId 		= false;
		$dropSegment       = false;

		if( ($routingBehavior == 'currentactive' || $routingBehavior == 'menuitemid') && !$isCanonical )
		{
			$routingMenuItem    = $config->get('main_routing_itemid','');

			if( ($routingBehavior == 'menuitemid') && ($routingMenuItem != '') )
			{
				$exItemid = $routingMenuItem;
			}

			// @rule: If there is already an item id, try to use the explicitly set one.
			if( empty( $exItemid ) )
			{
				if( $view == 'entry' )
				{
					$blogId = $post['id'];
					$blog	= EasyBlogHelper::getTable( 'Blog', 'Table' );
					$blog->load( $blogId );

					$author 	= $blog->created_by;
					if( !empty( $author ) )
					{
						$tmpItemid 	= EasyBlogRouter::getItemIdByBlogger( $author );
						if( !empty( $tmpItemid ) )
						{
							$isBloggerMode  = EasyBlogRouter::isMenuABloggerMode( $tmpItemid );
							if( $isBloggerMode )
							{
								$exItemid = $tmpItemid;
							}
						}
					}
				}

				// @rule: If it is not a blogger mode, we just use the current one.
				if ( !$mainframe->isAdmin() )
				{
					// Retrieve the active menu item.
					$menu = JFactory::getApplication()->getMenu();
					$item = $menu->getActive();

					if( isset( $item->id ) )
					{
						$isBloggerMode  = EasyBlogRouter::isMenuABloggerMode( $item->id );

						if( !$isBloggerMode )
						{
							$exItemid = $item->id;
						}
					}
				}
			}
		}
		else
		{
			switch( $view )
			{
				case 'entry':

					$routingOrder	= EasyBlogRouter::getDefaultRoutingOrder();
					$exItemid       = '';

					if( !empty($routingOrder) )
					{
						$blogId = $post['id'];

						$blog	= EasyBlogHelper::getTable( 'Blog', 'Table' );
						$blog->load( $blogId );

						$authorId   = $blog->created_by;
						$categoryId = $blog->category_id;

						foreach( $routingOrder as $key => $val )
						{
							switch( $key )
							{
								case 'bloggerstandalone':
									$bloggerId	= EasyBlogRouter::isBloggerMode();
									if( $bloggerId !== false )
									{
										$exItemid = EasyBlogRouter::getItemIdByBlogger( $bloggerId );
									}
									break;
								case 'entry':
									$exItemid = EasyBlogRouter::getItemIdByEntry( $blogId );
									break;
								case 'category':
									$exItemid = EasyBlogRouter::getItemIdByCategories( $categoryId );
									break;
								case 'blogger':
									$exItemid = EasyBlogRouter::getItemIdByBlogger( $authorId );
									break;
								case 'teamblog':
									$teamId     = $blog->getTeamContributed();
									if( ! empty( $teamId ) )
										$exItemid 	= EasyBlogRouter::getItemIdByTeamBlog( $teamId );
									break;
							}

							if( !empty( $exItemid ) )
								break;
						}

					}

					if( empty( $exItemid ) )
					{
						$view       = 'latest';
						$findItemId = true;
					}
					break;
				case 'blogger':
					//if( isset( $post['layout'] ) && isset( $post['id'] ) )
					if( isset( $post['id'] ) )
					{
						$exItemid = EasyBlogRouter::getItemIdByBlogger( $post['id'] );
						if( !empty( $exItemid ) )
						{
							$dropSegment    = true;
						}
					}

					if( empty( $exItemid ) )
					{
						$exItemid = EasyBlogRouter::getItemId( 'blogger', true );
					}

					if( empty( $exItemid ) )
					{
						$view       = 'latest';
						$findItemId = true;
					}
					break;
				case 'categories':
					//if( isset( $post['layout'] ) && isset( $post['id'] ) )
					if( isset( $post['id'] ) )
					{
						$exItemid = EasyBlogRouter::getItemIdByCategories( $post['id'] );
						if( !empty( $exItemid ) )
						{
							$dropSegment    = true;
						}
					}

					if( empty( $exItemid ) )
					{
						$bloggerId	= EasyBlogRouter::isBloggerMode();
						if( $bloggerId !== false )
						{
							$exItemid = EasyBlogRouter::getItemIdByBlogger( $bloggerId );
						}

						if( empty( $exItemid ) )
						{
							$exItemid = EasyBlogRouter::getItemId( 'categories', true );
						}

						if( empty( $exItemid ) )
						{
							$view       = 'latest';
							$findItemId = true;
						}
					}

					break;
				case 'teamblog':
					//if( isset( $post['layout'] ) && isset( $post['id'] ) )
					if( isset( $post['id'] ) )
					{
						$exItemid = EasyBlogRouter::getItemIdByTeamBlog( $post['id'] );
						if( !empty( $exItemid ) )
						{
							$dropSegment    = true;
						}
					}

					if( empty( $exItemid ) )
					{
						$exItemid = EasyBlogRouter::getItemId( 'teamblog', true );
					}

					if( empty( $exItemid ) )
					{
						$view       = 'latest';
						$findItemId = true;
					}
					break;
				case 'tags':
					if( isset( $post['layout'] ) &&  isset( $post['id'] )  )
					{
						//now check the active menu whether a blogger isolation mode or not.
						$bloggerId	= EasyBlogRouter::isBloggerMode();

						if( $bloggerId !== false )
						{
							$exItemid = EasyBlogRouter::getItemIdByBlogger( $bloggerId );
						}

						if( empty( $exItemid ) )
						{
							$exItemid = EasyBlogRouter::getItemIdByTag( $post['id'] );
							if( !empty( $exItemid ) )
							{
								$dropSegment    = true;
							}
						}
					}
					else
					{
						$exItemid = EasyBlogRouter::getItemId( 'tags' );
					}

					if( empty( $exItemid ) )
					{
						$view       = 'latest';
						$findItemId = true;
					}


					break;
				case 'dashboard':
					if( isset( $post['layout'] ) )
					{
						$exItemid = EasyBlogRouter::getItemIdByDashboardLayout( $post['layout'] );
					}

					if( empty( $exItemid ) )
					{
						$exItemid = EasyBlogRouter::getItemId( 'dashboard', true );
					}

					if( empty( $exItemid ) )
					{
						$findItemId = true;
					}
					break;
				case 'latest':
					$bloggerId	= EasyBlogRouter::isBloggerMode();

					if( $bloggerId !== false )
					{
						$exItemid = EasyBlogRouter::getItemIdByBlogger( $bloggerId );
					}

					if( empty( $exItemid ) )
					{
						$exItemid = EasyBlogRouter::getItemId( 'latest' );
					}

					if( empty( $exItemid ) )
					{
						$findItemId = true;
					}
					break;
				default:
					break;

			}

		}


		if( !empty($exItemid) )
		{
			if( self::isSefEnabled() && $dropSegment )
			{
				$url    = 'index.php?Itemid=' . $exItemid;

				$loaded[ $rawURL . $useXHTML ]	= JRoute::_($url, $xhtml, $ssl);
				return $loaded[$rawURL . $useXHTML];
			}

			//check if there is any anchor in the link or not.
			$pos = JString::strpos($url, '#');

			// If item id is provided in the query itself, we do not need to append any item id.
			// otherwise the result would be &Itemid=1&Itemid=2
			if( !$Itemid )
			{
				if ($pos === false)
				{
						$url .= '&Itemid='.$exItemid;
				}
				else
				{
					$url = JString::str_ireplace('#', '&Itemid='.$exItemid.'#', $url);
				}
			}

			$loaded[ $rawURL . $useXHTML ]	= JRoute::_($url, $xhtml, $ssl);
			return $loaded[$rawURL . $useXHTML];
		}

		//fall back to previous style if getting the Itemid
		if( empty( $Itemid ) && $findItemId )
		{
			$tmpId  = '';
			$useDefaultWay  = false;

			if ( $mainframe->isAdmin())
			{
				//from backend.
				$useDefaultWay  = true;
			}
			else
			{
				//from frontend.
				//lets try to get from the default itemId.
				$menu = JFactory::getApplication()->getMenu();
				$item = $menu->getActive();

				if( !$findItemId && isset($item->id) && ($item->component == 'com_easyblog' ) && !$search )
				{
					$tmpId  = $item->id;
				}
				else
				{
					$useDefaultWay  = true;
				}
			}

			if($useDefaultWay)
			{
				if(empty($eUri[$view]))
				{
					$tmpId			= EasyBlogRouter::getItemId($view);
					$eUri[$view]	= $tmpId;
				}
				else
				{
					$tmpId = $eUri[$view];
				}
			}

			//check if there is any anchor in the link or not.
			$pos = JString::strpos($url, '#');
			if ($pos === false)
			{
				$url .= '&Itemid='.$tmpId;
			}
			else
			{
				$url = JString::str_ireplace('#', '&Itemid='.$tmpId.'#', $url);
			}
		}
		$loaded[ $rawURL . $useXHTML ]	= JRoute::_($url, $xhtml, $ssl);

		return $loaded[$rawURL . $useXHTML];

	}

	public static function isSefEnabled()
	{
		$jConfig	= EasyBlogHelper::getJConfig();
		$isSef		= false;

		$isSef		= self::isSh404Enabled();

		// if sh404sef not enabled, we check on joomla
		if(! $isSef)
		{
			$isSef = $jConfig->get( 'sef' );
		}

		return $isSef;
	}

	public static function isSh404Enabled()
	{
		$isEnabled = false;

		//check if sh404sef enabled or not.
		if( defined('sh404SEF_AUTOLOADER_LOADED') && JFile::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_sh404sef' . DIRECTORY_SEPARATOR . 'sh404sef.class.php'))
		{
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_sh404sef'.DIRECTORY_SEPARATOR.'sh404sef.class.php');
			if( class_exists( 'shRouter' ) )
			{
				$sefConfig = shRouter::shGetConfig();

				if ($sefConfig->Enabled)
					$isEnabled  = true;
			}
		}

		return $isEnabled;
	}

	public static function getTagPermalink( $id )
	{
		$config 	= EasyBlogHelper::getConfig();

		JTable::addIncludePath( EBLOG_TABLES );
		$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
		$table->load( $id );

		if( $config->get( 'main_sef_unicode' ) )
		{
			return $table->id . '-' . $table->alias;
		}
		else
		{
			return $table->alias;
		}

	}

	public static function getTeamBlogPermalink( $id )
	{
		$config 	= EasyBlogHelper::getConfig();

		JTable::addIncludePath( EBLOG_TABLES );
		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load( $id );

		if( $config->get( 'main_sef_unicode' ) )
		{
			return $team->id . '-' . $team->alias;
		}
		else
		{
			return $team->alias;
		}
	}

	public static function getBloggerPermalink( $id )
	{
		$config = EasyBlogHelper::getConfig();

		JTable::addIncludePath( EBLOG_TABLES );

		if( $id == 0 )
		{
			$id 	= null;
		}

		$user		= JFactory::getUser($id);
		$profile	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$profile->load($id);
		$profile->setUser($user);

		if( empty($user->username) )
		{
			// blogger not exists
			return JText::_('COM_EASYBLOG_INVALID_PERMALINK_BLOGGER');
		}

		$bloggerPermalink        = $profile->permalink;
		if( empty( $bloggerPermalink ) )
		{

			$bloggerPermalink    = EasyBlogRouter::generatePermalink( $user->username );

			$db     = EasyBlogHelper::db();
			$query	= 'UPDATE `#__easyblog_users` SET `permalink` =' . $db->Quote( $bloggerPermalink ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '=' . $db->Quote( $profile->id );
			$db->setQuery( $query );
			$db->query();
		}

		if( $config->get( 'main_sef_unicode' ) )
		{
			return $profile->id . '-' . $bloggerPermalink;
		}
		else
		{
			return $bloggerPermalink;
		}

	}

	public static function getCategoryPermalink( $id )
	{
		$config = EasyBlogHelper::getConfig();

		JTable::addIncludePath( EBLOG_TABLES );
		$table	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$table->load( $id );

		if( $config->get( 'main_sef_unicode' ) )
		{
			return $table->id . '-' . $table->alias;
		}
		else
		{
			return $table->alias;
		}

	}

	public static function getBlogSefPermalink( $id , $external = false )
	{
		$config = EasyBlogHelper::getConfig();

		static $permalinks	= null;

		if( !isset( $permalinks[ $id ] ) )
		{
			JTable::addIncludePath( EBLOG_TABLES );
			$db		= EasyBlogHelper::db();
			$query 	= 'SELECT a.* FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' as a '
					. 'WHERE a.`id` ' . '=' . $db->Quote( $id );
			$db->setQuery( $query );
			$data	= $db->loadObject();
			$config = EasyBlogHelper::getConfig();

			if( empty($data) )
			{
				// blog post not exists
				$permalinks[ $id ] = JText::_('COM_EASYBLOG_INVALID_PERMALINK_POST');
				return $permalinks[ $id ];
			}

			// Empty permalinks needs to be regenerated.
			if( empty($data->permalink) )
			{
				$data->permalink	= EasyBlogHelper::getPermalink( $data->title );

				$query	= 'UPDATE #__easyblog_post SET permalink=' . $db->Quote( $data->permalink ) . ' '
						. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '=' . $db->Quote( $id );
				$db->setQuery( $query );
				$db->Query();
			}

			if( $config->get( 'main_sef_unicode' ) )
			{
				$data->permalink    = $data->id . '-' . urlencode($data->permalink);
			}

			switch( $config->get( 'main_sef' ) )
			{
				// Date based SEF mode
				case 'date':
					$date				= EasyBlogHelper::getDate( $data->created );
					$data->permalink	= $date->toFormat('%Y') . '/' . $date->toFormat( '%m' ) . '/' . $date->toFormat('%d') . '/' . $data->permalink;
				break;

				case 'datecategory':
					$date				= EasyBlogHelper::getDate( $data->created );
					$catPermalink   	= EasyBlogRouter::getCategoryPermalink( $data->category_id );

					$data->permalink	= $catPermalink . '/' . $date->toFormat('%Y') . '/' . $date->toFormat( '%m' ) . '/' . $date->toFormat('%d') . '/' . $data->permalink;
				break;

				case 'category':
					$catPermalink   	= EasyBlogRouter::getCategoryPermalink( $data->category_id );
					$data->permalink	= $catPermalink . '/' . $data->permalink;
				break;

				case 'custom':
					$data->permalink	= self::getCustomPermalink( $data );
				break;
				// Default SEF mode leave it unchanged
				default:
				break;
			}

			if( $external )
			{
				$uri		= JURI::getInstance();
				return $uri->toString( array('scheme', 'host', 'port')) . '/' . $data->permalink;
			}

			$permalinks[ $id ] = $data->permalink;
		}
		return $permalinks[ $id ];
	}

	public static function getCustomPermalink( &$data )
	{
		$cfg 		= EasyBlogHelper::getConfig();
		$custom		= $cfg->get( 'main_sef_custom' );
		$date		= EasyBlogHelper::getDate( $data->created );

		// @task: If the user didn't enter any values for the custom sef, we'll just load the default one which is the 'date' based
		if( empty( $custom ) )
		{

			return $date->toFormat('%Y') . '/' . $date->toFormat( '%m' ) . '/' . $date->toFormat('%d') . '/' . $data->permalink;
		}

		// @task: Break all parts separated by /
		$pieces		= explode( '/' , $custom );

		if( !$pieces )
		{
			$date				= EasyBlogHelper::getDate( $data->created );
			return $date->toFormat('%Y') . '/' . $date->toFormat( '%m' ) . '/' . $date->toFormat('%d') . '/' . $data->permalink;
		}

		$result	= array();

		foreach( $pieces as $piece )
		{
			// @task: Replace %year_num%
			$piece	= str_ireplace( '%year_num%' , $date->toFormat( '%Y' ) , $piece );

			// @task: Replace %month_num%
			$piece	= str_ireplace( '%month_num%' , $date->toFormat( '%m' ) , $piece );

			// @task: Replace %day_num%
			$piece 	= str_ireplace( '%day_num%' , $date->toFormat( '%d' ) , $piece );

			// @task: Replace %day%
			$piece	= str_ireplace( '%day%' , $date->toFormat( '%A' ) , $piece );

			// @task: Replace %month%
			$piece	= str_ireplace( '%month%' , $date->toFormat( '%b' ) , $piece );

			// @task: Replace %blog_id%
			$piece 	= str_ireplace( '%blog_id%' , $data->id , $piece );

			// @task: Replace %category%
			$piece	= str_ireplace( '%category%' , EasyBlogRouter::getCategoryPermalink( $data->category_id ) , $piece );

			// @task: Replace %category_id%
			$piece	= str_ireplace( '%category_id%' , $data->category_id , $piece );

			$result[]	= $piece;
		}

		$url	= implode( '/' , $result );
		$url	.= '/' . $data->permalink;

		return $url;
	}

	public static function getRoutedURL( $url , $xhtml = false , $external = false, $isCanonical = false )
	{
		if( !$external )
		{
			return EasyBlogRouter::_( $url , $xhtml, null, false, $isCanonical );
		}

		$mainframe 	= JFactory::getApplication();
		$uri		= JURI::getInstance();

		$isDashboard    = false;
		if ( !$mainframe->isAdmin() )
		{
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getActive();

			if( isset($item->link) )
			{
				$pos = strpos($item->link, 'view=dashboard');
				if( $pos !== false)
				{
					$isDashboard    = true;
				}
			}
		}

		//To fix 1.6 Jroute issue as it will include the administrator into the url path.
		$oriURL = $url;
		
		if ($mainframe->isAdmin() && EasyBlogRouter::isSefEnabled()) {

		//add for which publish the blog post from backend the autopost will be generate to SEF
		require_once(JPATH_ROOT . '/components/com_easyblog/router.php');

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('site');
			}

			if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
			{
				jimport( 'joomla.libraries.cms.router' );
			}
			else
			{
				require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'router.php' );
				require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'application.php' );
			}

			$router = new JRouterSite(array('mode'=>JROUTER_MODE_SEF));
			$urls 	= str_replace('/administrator/', '/', EasyBlogRouter::_( $oriURL  , $xhtml, null, $isDashboard, $isCanonical ));
			$urls	= rtrim( JURI::root(), '/') . '/' . ltrim( str_replace('/administrator/', '/', $urls) , '/' );

			$container  = explode('/', $urls);
			$container	= array_unique($container);
			$urls = implode('/', $container);

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('administrator');
			}

			return $urls;
		}
		else
		{
			$url 	= str_replace('/administrator/', '/', EasyBlogRouter::_( $url  , $xhtml, null, $isDashboard, $isCanonical ));
		}

		// We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
		// since $url already has the subfolder.
		if( $mainframe->isAdmin() )
		{
			return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );
		}

		return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );
	}

	public static function _isBlogPermalinkExists( $permalink )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'permalink' ) . '=' . $db->Quote( $permalink );

		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	public static function replaceAccents( $string )
	{
		$a = array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß' , 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('AE', 'ae', 'OE', 'oe', 'UE', 'ue', 'ss', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		return str_replace($a, $b, $string);
	}

	public static function getEntryRoute( $id )
	{
		$url	= 'index.php?option=com_easyblog&view=entry&id=' . $id;
		$url	.= '&Itemid=' . EasyBlogRouter::getItemId('entry');

		return $url;
	}

	public static function getItemIdByEntry( $blogId )
	{
		static $entriesItems	= null;

		if( !isset( $entriesItems[ $blogId ] ) )
		{
			$db		= EasyBlogHelper::db();

			// We need to check against the correct latest entry to be used based on the category this article is in
			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ',' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'params') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' )
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=latest' )
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery();

			$db->setQuery( $query );
			$menus	= $db->loadObjectList();

			$blog	= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $blogId );

			if( $menus )
			{
				foreach( $menus as $menu )
				{
					$params		= EasyBlogHelper::getRegistry( $menu->params );
					$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );

					if( empty( $inclusion ) )
					{
						continue;
					}

					if( !is_array( $inclusion ) )
					{
						$inclusion	= array( $inclusion );
					}

					if( in_array( $blog->category_id , $inclusion ) )
					{
						$entriesItems[ $blogId ]	= $menu->id;
					}
				}
			}

			// Test if there is any entry specific view as this will always override the latest above.
			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=entry&id='.$blogId ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			if( $itemid )
			{
				$entriesItems[ $blogId ]    = $itemid;
			}
			else
			{
				return '';
			}

		}

		return $entriesItems[ $blogId ];
	}


	public static function getItemIdByDashboardLayout( $layout )
	{
		static $dashboardLayout	= null;

		if( !isset( $dashboardLayout[ $layout ] ) )
		{
			$db	= EasyBlogHelper::db();

			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=dashboard&layout=' .$layout) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$dashboardLayout[ $layout ]    = $itemid;

			return $itemid;
		}
		else
		{
			return $dashboardLayout[ $layout ];
		}
	}

	public static function getItemIdByTeamBlog( $teamId )
	{
		static $teamblogItems	= null;

		if( !isset( $teamblogItems[ $teamId ] ) )
		{
			$db	= EasyBlogHelper::db();

			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . 'LIKE' . $db->Quote( 'index.php?option=com_easyblog&view=teamblog&layout=listings&id='.$teamId.'%' ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$teamblogItems[ $teamId ]    = $itemid;

			return $itemid;
		}
		else
		{
			return $teamblogItems[ $teamId ];
		}
	}

	public static function getCategoryParentId( $categoryId , &$parents )
	{
		$category	= EasyBlogHelper::getTable( 'Category' );
		$category->load( $categoryId );

		if( !empty( $category->parent_id) )
		{
			$parents[]	= $category->parent_id;

			self::getCategoryParentId( $category->parent_id , $parents );
		}
	}

	public static function getItemIdByCategories( $categoryId )
	{
		static $categoryItems	= null;

		if( !isset( $categoryItems[ $categoryId ] ) )
		{
			$categories		= array( $categoryId );
			// self::getCategoryParentId( $categoryId , $categories );

			$itemid 		= false;

			$db	= EasyBlogHelper::db();
			foreach( $categories as $categoryId )
			{
				$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
						. 'WHERE (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=categories&layout=listings&id='.$categoryId) . ' '
						. 'OR ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=categories&layout=listings&id='.$categoryId . '&limit%') . ') '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
						. self::getLanguageQuery()
						. ' LIMIT 1';

				$db->setQuery( $query );

				$result = $db->loadResult();

				if( $result )
				{
					$itemid = $result;
					break;
				}

			}

			$categoryItems[ $categoryId ]    = $itemid;
			return $itemid;

		}
		else
		{
			return $categoryItems[ $categoryId ];
		}
	}

	public static function getItemIdByBlogger( $bloggerId )
	{
		static $bloggerItems	= null;

		if( !isset( $bloggerItems[ $bloggerId ] ) )
		{
			$db	= EasyBlogHelper::db();

			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=blogger&layout=listings&id='.$bloggerId ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$bloggerItems[ $bloggerId ]    = $itemid;

			return $itemid;
		}
		else
		{
			return $bloggerItems[ $bloggerId ];
		}
	}

	public static function getItemIdByTag( $tagId )
	{
		static $tagItems	= null;

		if( !isset( $tagItems[ $tagId ] ) )
		{
			$db	= EasyBlogHelper::db();

			$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
					. 'WHERE (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=tags&layout=tag&id='.$tagId) . ' '
					. 'OR ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=tags&layout=tag&id='.$tagId . '&limit%') . ') '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$tagItems[ $tagId ]    = $itemid;

			return $itemid;
		}
		else
		{
			return $tagItems[ $tagId ];
		}
	}

	public static function getItemId( $view='' ,$exactMatch = false )
	{
		static $items	= null;

		if( !isset( $items[ $view ] ) )
		{
			$db	= EasyBlogHelper::db();

			switch($view)
			{
				case 'archive':
					$view='archive';
					break;
				case 'blogger':
					$view='blogger';
					break;
				case 'calendar':
					$view='calendar';
					break;
				case 'categories':
					$view='categories';
					break;
				case 'dashboard':
					$view='dashboard';
					break;
				case 'myblog':
					$view='myblog';
					break;
				case 'profile';
					$view='dashboard&layout=profile';
					break;
				case 'subscription':
					$view='subscription';
					break;
				case 'tags':
					$view='tags';
					break;
				case 'teamblog':
					$view='teamblog';
					break;
				case 'search':
					$view='search';
					break;
				case 'latest':
				default:
					$view='latest';
					break;
			}

			$config 	= EasyBlogHelper::getConfig();
			$routingBehavior    = $config->get( 'main_routing', 'currentactive');

			if( $routingBehavior == 'menuitemid' )
			{
				$routingMenuItem    = $config->get('main_routing_itemid','');

				$items[ $view ]	= $routingMenuItem;
			}
			else
			{
				$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
						. 'WHERE (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view='.$view ) . ' '
						. 'OR ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view='.$view.'&limit=%' ) . ') '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery( $query );
				$itemid = $db->loadResult();


				if( ! $exactMatch )
				{

					// @rule: Try to fetch based on the current view.
					if( empty( $itemid ) )
					{
						$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
								. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=' . $view . '%' ) . ' '
								. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
								. self::getLanguageQuery()
								. ' LIMIT 1';
						$db->setQuery( $query );
						$itemid = $db->loadResult();
					}

				}

				if(empty($itemid))
				{
					$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
							. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=latest' ) . ' '
							. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				//last try. get anything view that from easyblog.
				if(empty($itemid))
				{
					$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
							. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=%' ) . ' '
							. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				// if still failed the get any item id, then get the joomla default menu item id.
				if( empty($itemid) )
				{
					$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__menu' ) . ' '
							. 'WHERE `home` = ' . $db->Quote( '1' ) . ' '
							. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				$items[ $view ]	= !empty($itemid)? $itemid : 1;
			}
		}
		return $items[ $view ];
	}

	public static function _encodeSegments($segments)
	{
		$router 	= new EasyBlogJoomlaRouter();

		$segments 	= $router->encode( $segments );

		return $segments;
	}

	public static function _getBloggerId($itemId)
	{
		$db     = EasyBlogHelper::db();

		$query  = 'select `link` from `#__menu` where `id` = ' . $db->Quote($itemId);
		$db->setQuery($query);

		$link   = $db->loadResult();
		parse_str($link);

		return $id;
	}

	public static function isBloggerMode()
	{
		static $itemIds	= null;

		$itemId = JRequest::getInt('Itemid', 0);

		$blogger	= JRequest::getVar( 'blogger' , '' );

		if( !empty( $blogger ) )
		{
			return $blogger;
		}

		if(empty($itemId))
		{
			return false;
		}

		if( !isset( $itemIds[ $itemId ] ) )
		{
			$isBloggerMode  = false;
			$menu 			= JFactory::getApplication()->getMenu();
			$menuparams 	= $menu->getParams( $itemId );

			if($menuparams->get('standalone_blog', false))
			{
				$isBloggerMode  = EasyBlogRouter::_getBloggerId($itemId);
			}

			$itemIds[$itemId]   = $isBloggerMode;
		}

		return $itemIds[$itemId];
	}

	public static function isMenuABloggerMode( $itemId )
	{
		$mainframe  = JFactory::getApplication();

		if ( !$mainframe->isAdmin() )
		{
			$menu 			= JFactory::getApplication()->getMenu();
			$menuparams 	= $menu->getParams( $itemId );
			$isBloggerMode  = $menuparams->get('standalone_blog', false);
			return $isBloggerMode;
		}
		return false;
	}

	// return true or false
	public static function isCurrentActiveMenu( $view, $id = 0 )
	{
		$activemenu = JFactory::getApplication()->getMenu();
		$activeitem = $activemenu->getActive();

		if( ! empty($activeitem) )
		{

			if( !empty( $id ) )
			{
				if( (strpos( $activeitem->link, 'view=' . $view ) !== false) && (strpos( $activeitem->link, 'id=' . $id ) !== false) )
					return true;
			}
			else if( strpos( $activeitem->link, 'view=' . $view  ) !== false)
			{
				return true;
			}
		}

		return false;
	}
}
