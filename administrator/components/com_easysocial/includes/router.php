<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/includes/router/router' );

/**
 * Routing library for EasySocial
 *
 * @since   3.0
 * @author  Mark Lee <mark@stackideas.com>
 */
class FRoute
{
    static $base    = 'index.php?option=com_easysocial';
    static $views   = array(
                                'account',
                                'activities',
                                'albums',
                                'apps',
                                'badges',
                                'conversations',
                                'events',
                                'groups',
                                'dashboard',
                                'fields',
                                'friends',
                                'followers',
                                'profile',
                                'profiles',
                                'unity',
                                'users',
                                'stream',
                                'notifications',
                                'leaderboard',
                                'points',
                                'photos',
                                'registration',
                                'search',
                                'login',
                                'unity'
                            );

    /**
     * Translates URL to SEF friendly
     *
     * External true SEF true
     * http://solo.dev/joomla321/dashboard/registration/oauthDialog/facebook
     * External true SEF false
     * http://solo.dev/joomla321/index.php?option=com_easysocial&view=registration&layout=oauthDialog&client=facebook&Itemid=135
     * External false SEF true
     * /joomla321/dashboard/registration/oauthDialog/facebook
     * External false SEF false
     * index.php?option=com_easysocial&view=registration&layout=oauthDialog&client=facebook&Itemid=135
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function _( $url , $xhtml = false , $view = array() , $ssl = null , $tokenize = false , $external = false , $tmpl = '' , $controller = '', $sef = true )
    {
        if( $tokenize )
        {
            $url    .= '&' . FD::token() . '=1';
        }

        if( !empty( $controller ) )
        {
            $url    = $url . '&controller=' . $controller;
        }

        // If this is an external URL, we want to fetch the full URL.
        if( $external )
        {
            return FRoute::external( $url , $xhtml , $ssl , $tmpl, $sef );
        }

        if( !empty( $controller ) && $sef )
        {
            $url    = JRoute::_( $url , $xhtml );
            return $url;
        }

        // We don't want to do any sef routing here.
        // Only external = false and sef = false will come here
        // IMPORTANT: handler needs to FRoute::_() the link
        if( $tmpl == 'component' || $sef === false )
        {
            return $url;
        }

        return JRoute::_( $url , $xhtml , $ssl );
    }

    /**
     * Returns the raw url without going through any sef urls.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function raw( $url )
    {
        $uri    = rtrim( JURI::root() , '/' ) . '/' . $url;

        return $uri;
    }

    /**
     * Builds an external URL that may be used in an email or other external apps
     *
     * @since   1.0
     * @access  public
     * @return
     */
    public static function external( $url , $xhtml = false , $ssl = null , $tmpl = false, $sef = true )
    {
        $uri    = JURI::getInstance();

        // If this is an external URL, we will not want to xhtml it.
        $xhtml  = false;

        // Determine if the current browser is from the back end.
        $app    = JFactory::getApplication();

        // if( $app->isAdmin() )
        // {
        //  jimport( 'joomla.libraries.cms.router' );

        //  // Reset the application
        //  JFactory::$application = JApplication::getInstance('site');
        // }

        // Send the URL for processing only if tmpl != component
        if( $tmpl !== 'component' && $sef !== false )
        {
            $url    = FRoute::_( $url , $xhtml , array(), $ssl, false, false, '', '', $sef );
        }

        // Remove the /administrator/ part from the URL.
        $url    = str_ireplace( '/administrator/' , '/' , $url );
        $url    = ltrim( $url , '/' );

        if ($sef === false || $tmpl === 'component') {
            // If we do not want sef, then we need to manually append the front part taking into account that this is not JRouted, hence we use JURI::root() to ensure subfolders
            $url = rtrim(JURI::root(), '/') . '/' .  $url;
        } else {
            // We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
            // since $url already has the subfolder.
            $url    = $uri->toString( array( 'scheme' , 'host' , 'port' ) ) . '/' . $url;
        }

        return $url;
    }

    public static function tokenize( $url , $xhtml = false , $ssl = null )
    {
        $url    .= '&' . FD::token() . '=1';

        return FRoute::_( $url , $xhtml , $ssl );
    }

    /**
     * Retrieves the current url that is being accessed.
     *
     * @since   1.0
     * @access  public
     * @param   bool    Determines if we should append this as a callback url.
     * @return  string  The current url.
     */
    public static function current( $isCallback = false )
    {
        $uri    = JRequest::getURI();

        if( $isCallback )
        {
            return '&callback=' . base64_encode( $uri );
        }

        return $uri;
    }

    /**
     * Retrieves the default menu id
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public static function getDefaultItemId( $view )
    {
        $db     = FD::db();
        $sql    = $db->sql();

        // Get the first public page available if there's any
        $sql->select( '#__menu' );
        $sql->where( 'link' , 'index.php?option=com_easysocial&view=dashboard%' , 'LIKE' );
        $sql->where( 'published' , SOCIAL_STATE_PUBLISHED );

        $db->setQuery( $sql );
        $id     = $db->loadResult();

        // Check for more specificity
        if( !$id )
        {
            $sql->clear();
            $sql->select( '#__menu' );
            $sql->where( 'link' , 'index.php?option=com_easysocial&view=' . $view , '=' );
            $sql->where( 'published' , SOCIAL_STATE_PUBLISHED );

            $db->setQuery( $sql );
            $id     = $db->loadResult();
        }

        // If the url doesn't exist, we use "LIKE" to search instead.
        if( !$id )
        {
            $sql->clear();
            $sql->select( '#__menu' );
            $sql->where( 'link' , 'index.php?option=com_easysocial%' , 'LIKE' );
            $sql->where( 'published' , SOCIAL_STATE_PUBLISHED );

            $db->setQuery( $sql );
            $id     = $db->loadResult();
        }

        if( !$id )
        {
            // Try to get from the current Itemid in query string
            $id     = JRequest::getInt( 'Itemid' , 0 );
        }

        if( !$id )
        {
            // Try to get
            $id     = false;
        }

        return $id;
    }

    /**
     * Retrieves the item id based on the view and the layout.
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return  Array   An array of menu items
     */
    public static function getMenus( $view , $layout = null, $id = null )
    {
        static $menus       = null;
        static $selection   = array();

        // Always ensure that layout is lowercased
        $layout     = strtolower($layout);

        // We want to cache the selection user made.
        $key        = $view . $layout . $id;
        $language   = false;

        // If language filter is enabled, we need to get the language tag
        if (!JFactory::getApplication()->isAdmin()) {
            $language       = JFactory::getApplication()->getLanguageFilter();
            $languageTag    = JFactory::getLanguage()->getTag();
        }


        // Preload the list of menus first.
        if (is_null($menus)) {

            $db     = FD::db();
            $sql    = $db->sql();

            $sql->select( '#__menu' );
            $sql->where( 'published' , SOCIAL_STATE_PUBLISHED );
            $sql->where( 'link' , 'index.php?option=com_easysocial%' , 'LIKE' );

            if ($language) {
                $sql->where('(', '', '', 'AND');
                $sql->where('language', $languageTag, '=', 'OR');
                $sql->where('language', '*', '=', 'OR');
                $sql->where(')');
            }

            $db->setQuery($sql);

            $result  = $db->loadObjectList();
            $menus   = array();

            // We need to format them accordingly.
            if (!$result) {
                return array();
            }

            foreach ($result as $row) {

                // Remove the index.php?option=com_easysocial from the link
                $tmp    = str_ireplace('index.php?option=com_easysocial', '', $row->link);

                // Parse the URL
                parse_str($tmp, $segments);

                // Convert the segments to std class
                $segments       = (object) $segments;

                // if there is no view, most likely this menu item is a external link type. lets skip this item.
                if(!isset($segments->view)) {
                    continue;
                }

                $obj            = new stdClass();
                $obj->segments  = $segments;
                $obj->link      = $row->link;
                $obj->view      = $segments->view;
                $obj->layout    = isset($segments->layout) ? $segments->layout : 0;
                $obj->id        = $row->id;
                $menus[$obj->view][$obj->layout][]        = $obj;
            }
        }

        // Get the current selection of menus from the cache
        if (!isset($selection[ $key ])) {

            if (isset($menus[$view]) && isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && !is_null($id) && !empty($id)) {
                $tmpMenus = $menus[ $view ][ $layout ];
                foreach ($tmpMenus as $tMenus) {
                    if (isset($tMenus->segments->id) && (int)$tMenus->segments->id == (int)$id) {
                        $selection[ $key ]    = array($tMenus);
                        break;
                    }
                }
                // there is no menu item created for this view/item/id
                // let just use the view.
                if (!isset($selection[ $key ]) && isset($menus[$view]) && isset($menus[$view][0])) {
                    // var_dump($key, $menus[$view][0]);exit;
                    $selection[$key] = $menus[$view][0];
                }
            }

            if (isset($menus[$view]) && isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && (is_null($id) || empty($id)) ) {
                $selection[ $key ]    = $menus[ $view ][ $layout ];
            }

            // If the user is searching for $views only.
            if (isset($menus[ $view ]) && isset($menus[ $view ]) && (is_null($layout) || empty($layout))) {
                // $selection[ $key ]    = $menus[$view][0];
                $selection[ $key ]    = isset( $menus[$view][0] ) ? $menus[$view][0] : false;
            }

            // If we still can't find any menu, lets check if the view exits or not. if yes, used it.
            if (!isset($selection[ $key ]) && isset($menus[$view]) && isset($menus[$view][0])) {
                $selection[$key] = $menus[$view][0];
            }


            // var_dump($key);

            // if we are trying to get the dashboard view menu item and the result was not found,
            // this mean the site do not have any menu item created for dashbaord. If that is the case,
            // we need to take whatever menu item created for EasySocial or else, the sef link will become
            // site.com/component/easysocial/?Itemid=
            if (!isset($selection[ $key ]) && $key == 'dashboard' && $menus ) {

                // get menu keys
                $menuKey = array_keys($menus);
                $tmpMenu = $menus[$menuKey[0]];

                //get layout keys
                $layoutKeys = array_keys($tmpMenu);
                $theOneMenu = false;
                if ($layoutKeys && isset($tmpMenu[$layoutKeys[0]])) {
                    $theOneMenu = $tmpMenu[$layoutKeys[0]];
                }
                else if( isset($menus[$menuKey[0]][0]) )
                {
                    $theOneMenu = $menus[$menuKey[0]][0];
                }

                if($theOneMenu) {
                    $selection[ $key ] = $theOneMenu;
                }
            }

            // If we still can't find any menu, skip this altogether.
            if (!isset($selection[ $key ])) {
                $selection[$key]  = false;
            }

        }

        return $selection[$key];
    }

    /**
     * Retrieves the item id of the current view.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function getItemId( $view , $layout = '', $id = '')
    {
        static $views   = array();

        // Cache the result
        $key        = $view . $layout . $id;

        if (!isset($views[$key])) {

            // Retrieve the list of default menu
            $defaultMenu    = FRoute::getMenus('dashboard','');

            // Initial menu should be false
            $menuId     = false;

            if (!empty($layout)) {

                // Try to locate menu with just the view if we still can't find a menu id.
                $menus      = FRoute::getMenus($view, $layout, $id);

                if (!$menuId && $menus) {
                    // If this menu contains data about "id", we shouldn't simply use it.
                    // if (!isset($menus[0]->segments->id)) {
                    //     $menuId     = $menus[0]->id;
                    // }
                    // var_dump($menus[0]);exit;
                    $menuId     = $menus[0]->id;
                }
            }

            // Try to locate menu with just the view if we still can't find a menu id.
            $menus      = FRoute::getMenus($view, '', '');

            // If menu id for view + layout doesn't exists, use the one from the view
            if (!$menuId && $menus) {
                $menuId     = $menus[0]->id;
            }

            // If we still don't have a menu id, we use the default dashboard view.
            if (!$menuId && $defaultMenu) {
                $menuId     = $defaultMenu[0]->id;
            }

            $views[ $key ]  = $menuId;
        }

        return $views[ $key ];
    }

    /**
     * Builds the controller url
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function controller( $name , $params = array() , $xhtml = true , $ssl = null )
    {
        // For controller urls, we shouldn't pass it to the router.
        $url    = 'index.php?option=com_easysocial&controller=' . $name;

        // Determines if this url is an external url.
        $external   = isset( $params[ 'external' ] ) ? $params[ 'external' ] : false;
        $tokenize   = isset( $params[ 'tokenize' ] ) ? $params[ 'tokenize' ] : false;

        unset( $params[ 'external' ] );
        unset( $params[ 'tokenize' ] );

        if( $params )
        {
            foreach( $params as $key => $value )
            {
                $url    .= '&' . $key . '=' . $value;
            }
        }

        $url    = FRoute::_( $url , $xhtml , '' , $ssl , $tokenize , $external );

        return $url;
    }

    /**
     * Returns the sef url for registration links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::registration();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     */
    public static function account()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for registration links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::registration();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     */
    public static function registration()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for apps
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function apps()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for following links
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function points()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for following links
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function followers()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for profile links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function profile()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for profile links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function groups()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function profiles()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function login()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function friends()
    {
        $args   = func_get_args();
        return self::callStatic(__FUNCTION__, $args);
    }

    /**
     * Calls the adapter file
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function callStatic($view, $args)
    {
        $router     = FD::router($view);

        return call_user_func_array(array($router, 'route'), $args);
    }

    /**
     * Returns the sef url for activity logs
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function activities()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for activity logs
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function leaderboard()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }


    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function conversations()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function stream()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for badge links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to badges page
     * echo FRoute::badges();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function badges()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to users
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function users()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to albums
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function albums()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to albums
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function photos()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to search
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function search()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to unity
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function unity()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }


    /**
     * Returns the URL to dashboard
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function dashboard()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for login links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to registration page.
     * echo FRoute::profile();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   int     (Optional) User's id.
     */
    public static function notifications()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the URL to fields
     *
     * @since   1.0
     * @access  public
     * @param   array
     * @return
     */
    public static function fields()
    {
        $args   = func_get_args();
        return self::callStatic( __FUNCTION__ , $args );
    }

    /**
     * Returns the sef url for events links.
     *
     * Example:
     * <code>
     * <?php
     * // Returns routed url to events page.
     * echo FRoute::events();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     */
    public static function events()
    {
        $args = func_get_args();
        return self::callStatic(__FUNCTION__, $args);
    }

    /**
     * Parses url
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function parse( &$segments )
    {
        $vars   = array();

        $app    = JFactory::getApplication();

        // If there is only 1 segment and the segment is index.php, it's just submitting
        if (count($segments) == 1 && $segments[0] == 'index.php') {
            return array();
        }

        // Get the menu object.
        $menu   = $app->getMenu();

        // Get the active menu object.
        $active = $menu->getActive();

        // Check if the view exists in the segments
        $view           = '';
        $viewExists     = false;

        // Replace all ':' with '-'
        self::encode($segments);

        foreach (self::$views as $systemView) {
            if (SocialRouterAdapter::translate($systemView) == $segments[ 0 ] || $systemView == $segments[0]) {
                $view           = $systemView;
                $viewExists     = true;
                break;
            }
        }


        if (!$viewExists && $active) {

            // If there is no view in the segments, we treat it that the user
            // has created a menu item on the site.
            $view   = $active->query['view'];

            // Add the view to the top of the element
            array_unshift($segments, $view);
        }

        // Load up the router object so that we can translate the view
        $router = FD::router($view);

        // Parse the segments
        $vars   = $router->parse($segments);

        return $vars;
    }

    /**
     * Replaces all ':' with '-'
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public static function encode( &$segments )
    {
        foreach( $segments as &$segment )
        {
            $segment        = str_ireplace( ':' , '-' , $segment );
        }
    }

    /**
     * Build urls
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public static function build(&$query)
    {
        $app        = JFactory::getApplication();
        $segments   = array();

        $menu       = $app->getMenu();

        //remove ts from the query
        if (isset($query['_ts'])) {
            unset($query['_ts']);
        }

        // If there is item id already assigned to the query, we need to query for the active menu
        if (isset($query['Itemid'])) {

            // Get the menu item based on the item id.
            $active     = $menu->getItem($query['Itemid']);

        } else {

            // If we don't have the item id, use the default one.
            $active     = $menu->getActive();

        }

        // If there's no view, we wouldn't want to set anything
        if( !isset( $query[ 'view' ] ) )
        {
            return $segments;
        }

        // Get the view.
        $view       = isset( $query[ 'view' ] ) ? $query[ 'view' ] : $active->query[ 'view' ];

        // Initialize router object
        $router     = FD::router( $view );

        $segments   = $router->build( $active , $query );

        return $segments;
    }

    public static function url($options = array())
    {
        // Set option as com_easysocial by default
        if (!isset($options['option'])) {
            $options['option'] = SOCIAL_COMPONENT_NAME;
        }

        // Remove external
        $external = false;
        if (isset($options['external'])) {
            $external = $options['external'];
            unset($options['external']);
        }

        // Remove sef
        $sef = false;
        if (isset($options['sef'])) {
            $sef = $options['sef'];
            unset($options['sef']);
        }

        // Remove tokenize
        $tokenize = false;
        if (isset($options['tokenize'])) {
            $tokenize = $options['tokenize'];
            unset($options['tokenize']);
        }

        // Remove ssl
        $ssl = false;
        if (isset($options['ssl'])) {
            $ssl = $options['ssl'];
            unset($options['ssl']);
        }

        // Remove xhtml
        $xhtml = false;
        if (isset($options['xhtml'])) {
            $xhtml = $options['xhtml'];
            unset($options['xhtml']);
        }

        $base = 'index.php?' . JURI::buildQuery($options);

        return FRoute::_($base, $xhtml, array(), $ssl, $tokenize, $external, '', '', $sef);
    }
}
