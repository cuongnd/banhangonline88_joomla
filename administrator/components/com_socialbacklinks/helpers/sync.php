<?php
/**
 * SocialBacklinks Requirements helper
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

jimport('joomla.factory');
JLoader::import('joomla.application.component.model'); 


/**
 * SocialBacklinks Back-End Controller's helper for syncronizing
 * @static
 */
class SBHelpersSync extends JObject
{
	/**
	 * Stores last sync date object
	 * @var JDate
	 */
	private static $_last_sync = null;
	
	/**
	 * Stores identifiers of last records
	 * @var array
	 */
	private static $_last_ids = array( 'history' => -1, 'error' => -1 );
	
	/**
	 * Stores a cache for the routes of the articles
	 * @var array
	 */
	private static $_route_cache = array();
	
	/**
	 * Calls the front-end asynchronously to trigger a sync
	 */
	public static function asynchronousCall() {
		$url = JURI::root();

		// see http://petewarden.typepad.com/searchbrowser/2008/06/how-to-post-an.html
	    $parts = parse_url($url);
		$host = $parts['host'];
		if (isset($parts['scheme']) && $parts['scheme'] == 'https') {
			$host = 'ssl://'.$host;
		}
		
		$port = isset($parts['port']) ? $parts['port'] : ((isset($parts['scheme']) && $parts['scheme'] == 'https')  ? 443 : 80);

	    $fp = fsockopen($host, $port, $errno, $errstr, 10);
		
		if ($fp) {
			$path = JRoute::_('index.php?option=com_socialbacklinks&task=sync');
			$path = str_replace('/administrator/', '/', $path);
			$path = str_replace('&amp;', '&', $path);
			$path = str_replace(' ', '%20', $path);
			
			$out = "GET ".$path." HTTP/1.1\r\n";
		    $out.= "Host: ".$parts['host']."\r\n";
		    $out.= "Connection: Close\r\n\r\n";

		    fwrite($fp, $out);
		    fclose($fp);
		}
	}
	
	/**
	 * Checks periodicity of the synchronization articles with social networks
	 * @return boolean
	 */
	public static function isNeedSync( )
	{
		if ( !$last_sync = self::getLastSyncDate( ) ) {
			return true;
		}

		if ( !$item = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( 'sync_periodicity' )->getItem( ) ) {
			$period = 5;
		}
		else {
			$period = (int)$item->value;
		}

		$date = self::convertDate( );

		$date_diff = ($date->toUnix( ) - $last_sync->toUnix( )) / 60;
		return ($date_diff >= $period);
	}

	/**
	 * Converts date to date according current timezone
	 * @param  string. If null, the function will return current date
	 * @return JDate
	 */
	public static function convertDate( $date = 'now' )
	{
		// if ($date == 'now')
		// {
			// $db = &JFactory::getDbo( );
			// $db->setQuery('SELECT now() as cur_date;');
			// $date = $db->loadResult();
		// }
		
		$config = JFactory::getConfig( );
		$result = JFactory::getDate( $date );
		$tz = $config->get( 'offset' );

		$tz_object = new DateTimeZone( $tz);
		$result->setTimezone( $tz_object );
		
		return $result;
	}
	
	/**
	 * Returns the last sync date
	 * @return date|null
	 */
	public static function getLastSyncDate( )
	{
		if ( !self::$_last_sync && ($config = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( 'last_sync' )->getItem( )) ) {
			self::$_last_sync = self::convertDate( $config->value );
		}
		return self::$_last_sync;
	}

	/**
	 * Updates the date of last synchronizing
	 * @return void
	 */
	public static function updateLastSyncDate( )
	{
		$db = &JFactory::getDbo( );
		// $db->setQuery('SELECT now() as cur_date;');
		// $now = $db->loadResult();
		$params = array(
			'section' => 'basic',
			'name' => 'last_sync',
			// 'value' => $db->getEscaped($now)
			'value' => $db->escape( self::convertDate( )->toSql() )
		);
		JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->setData( $params )->update( );
	}

	/**
	 * Returns the last identifier of history or errors
	 * @param 	string 	The type of id
	 * @param 	boolean Shows must we use db connection or not
	 * @return 	integer
	 */
	public static function getLastId( $type = 'history', $use_db = true )
	{
		if ( !key_exists($type, self::$_last_ids) ) {
			throw new SBException( JText::sprintf( 'SB_NO_VALID_PARAM', $type, __FUNCTION__ ) );
		}
		if ( $use_db ) {
			$result = 0;
			if ( $type == 'error' ) {
				$model = JModelLegacy::getInstance( 'SBModelsErrors' );
				//TODO It doesn't work for errors. Should it?
				$select = 'MAX(socialbacklinks_error_id)';
			}
			else {
				$model = JModelLegacy::getInstance( 'SBModelsHistories' );
				$select = 'MAX(socialbacklinks_history_id)';
			}
			$model->reset( )->select( $select . ' FROM ' . $model->getTable( )->getTableName( ) );
	
			if ( $list = $model->getList( ) ) {
				$res = (array) current( $list );
				if ( $res = current( $res ) ) {
					$result = (int) $res;
				}
			}
		}
		else {
			$result = self::$_last_ids[$type];
		}
		return $result;
	}
	
	/**
	 * Saves identifier of last record before add new record
	 * @param string $type Type of the identifier
	 * @return void
	 */
	public static function setLastId( $type = 'history' )
	{
		if ( !key_exists($type, self::$_last_ids) ) {
			throw new SBException( JText::sprintf( 'SB_NO_VALID_PARAM', $type, __FUNCTION__ ) );
		}
		
		if ( self::$_last_ids[$type] < 0 ) {
			self::$_last_ids[$type] = self::getLastId( $type );
		}
	}

	/**
	 * Checks if there were any error
	 * @return boolean
	 */
	public static function hasError()
	{
		return ( self::$_last_ids['error'] >= 0 );
	}
	
	/**
	 * Calls the Content plugins
	 * @param String The string to transform
	 * @return The transformed string
	 */
	public static function contentPrepare($html) 
	{	
		$prx = new stdClass();
		$prx->text = $html;
		
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();
		$params = array();
		$dispatcher->trigger('onContentPrepare', array(null, &$prx, &$params, 0));
		
		return $prx->text;
	}
	
	/**
	 * Formats data of the item
	 * @param  SBPluginsContentsInterface The content plugin object
	 * @param  JObject Item data
	 * @return JObject
	 */
	public static function formatData( SBPluginsContentsInterface $plugin, $row )
	{
		$item = new JObject( );
		$sync_updated = (bool)$plugin->sync_updated;
		$is_new = true;
		$db = JFactory::getDBO( );
		
		// Checks whether item is new record
		$nulldate = $db->Quote( $db->getNullDate( ) );
		if ( $sync_updated && ($row->modified != $nulldate) ) {
			$created = self::convertDate( $row->created );
			$modified = self::convertDate( $row->modified );
			$sec_in_day = 3600 * 24;

			$date_diff = ($modified->toUnix( ) - $created->toUnix( )) / $sec_in_day;
			$is_new = false;
			if ( !self::_getBasicParam( 'clean_history' ) || ($date_diff < (int)self::_getBasicParam( 'clean_history_periodicity', 30 )) ) {
				if ( !JModelLegacy::getInstance( 'SBModelsHistories' )->reset( )->extension( $plugin->getAlias( ) )->item_id( $row->id )->result( 1 )->getItem( ) ) {
					$is_new = true;
				}
			}
		}

		// Creates title of the item
		$title_suffix = '';
		if ( !$is_new ) {
			$date = SBHelpersSync::convertDate( $row->modified )->format( 'M d, Y H:i', true );
			$title_suffix = ' (' . JText::sprintf( 'SB_CONTENT_UPDATED', $date ) . ')';
		}
		
		$max_title_length = 115;
		$title = $row->title;
		if ( strlen( $title ) + strlen( $title_suffix ) > $max_title_length )
		{
			$title_length = $max_title_length - strlen( $title_suffix ) - 3;
			$buff = strpos( wordwrap( $title, $title_length, '^~^', true ), '^~^' );
			$title = substr( $title, 0, $buff ) . '...';
		}
		$item->title = $title . $title_suffix;		
		
		$item->link = self::_getLink( $plugin, $row );

		// Creates description of the element
		if ( $plugin->sync_desc ) {
			$max_length = 80;
			$desc = strip_tags( $row->introtext );
			if ( mb_strlen($desc) >= $max_length ) {
				$desc = mb_substr( $desc, 0, $max_length ) . '...';
			}
			$item->desc = $desc;
 		}
		else {
			$item->desc = '';
		}
		
		// Get the first image
		$base = JURI::root();
		$image = null;
		
		if (isset($row->introtext)) {
			$dom = new DOMDocument();
			$dom->loadHtml(self::contentPrepare($row->introtext));
			$imgs = $dom->getElementsByTagName('img');
			foreach($imgs as $img)
			{
				if ($img->hasAttributes() && $img->attributes->getNamedItem('src') !== NULL)
				{
					$image = $img->attributes->getNamedItem('src')->value;
					if (stripos($image, 'http') === FALSE) {
						$image = $base . $image;
						$image = str_replace(' ', '%20', $image);
					}
				
					// Is this a valid url ? Not a 100% fiable but it's something
					if (!filter_var($image, FILTER_VALIDATE_URL)) {
							$image = null;
					}
				}
			}
		}
		
		if ($image === null &&  isset($row->images) && !empty($row->images)) {
			$images = json_decode($row->images);
			if (isset($images->image_intro) && !empty($images->image_intro)) {
				if (stripos($images->image_intro, 'http') === FALSE) {
					$image = $base . $images->image_intro;
				} else {
					$image = $images->image_intro;
				}
			}
			
			if ($image === null && isset($images->image_fulltext) && !empty($images->image_fulltext)) {
				if (stripos($images->image_fulltext, 'http') === FALSE) {
					$image = $base . $images->image_fulltext;
				} else {
					$image = $images->image_fulltext;
				}
			}
		}
		
		if ($image !== null) {
			$image = str_replace(' ', '%20', $image);
		}
		
		$item->image = $image;

		return $item;
	}

	/**
	 * Returns basic config parameter
	 * @param  string Parameter to be returned
	 * @param  mixed  Default value
	 * @return mixed
	 */
	protected static function _getBasicParam( $param, $default = null )
	{
		if ( $config = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( $param )->getItem( ) ) {
			return $config->value;
		}
		return $default;
	}

	/**
	 * Returns the link to the article
	 * @param  SBPluginsContentsInterface $plugin
	 * @param  JObject $article
	 * @return string
	 */
	protected static function _getLink( SBPluginsContentsInterface $plugin, $item )
	{
		if (JRequest::getInt('diagnose',0) == 1) {
			error_reporting(E_ALL ^ E_NOTICE);
		}
		// In the back end we need to set the application to the site app instead

		$itemRoute = $plugin->getItemRoute( $item );
		$key = base64_encode($itemRoute);
		if (isset(self::$_route_cache[$key]) && !empty(self::$_route_cache[$key]))
		{
			return self::$_route_cache[$key];
		}

		$link = null;
		$curl_info = null;

		try {
			$request = JRoute::_(trim(JURI::root(),'/').'/index.php?option=com_socialbacklinks&encode='.urlencode($key));
			
			require_once(JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/helpers/WebClient.php');
			$wc = new WebClient();
			$sefUrl = $wc->Navigate($request);
			
			$obj = new stdClass();
			if ($sefUrl !== FALSE)
				$obj = json_decode($sefUrl);
			
			if ($sefUrl !== FALSE && !empty($obj)) {
				$link = $obj->SEF;
			} else {
				throw new Exception("Getting URL from Front-end failed");
			}
		} catch(Exception $e) {
			if (JRequest::getInt('diagnose',0) == 1) {
				echo $e->getMessage().'<br />';
				var_dump($curl_info);
			}

			try {
				// Old fashioned way
				if ( JPATH_BASE != JPATH_SITE ) {
					$app = &JFactory::getApplication();
					$site_app = &JApplication::getInstance( 'site' );
					$admin_app = clone $app;
					$app = $site_app;
				}

				$itemRoute = $plugin->getItemRoute( $item );
				$link = JRoute::_( $itemRoute, false, 2 );

				// Set the appilcation back to the administartor app
				if ( JPATH_BASE != JPATH_SITE ) {
					$link = str_replace( '/administrator/', '/', $link );
					$app = $admin_app;
				}
			} catch( Exception $e2 ) {
				if (JRequest::getInt('diagnose',0) == 1) {
					echo $e2->getMessage().'<br />';
				}
			}

		} 
		
		$root = SBHelpersConfig::getProperty('sync_domain', 'basic', JURI::root());
		if (!empty($root) && $root != JURI::root()) {
			if (substr($root, -1) != '/') {
				$root .= '/';
			}
			
			$link = str_replace(JURI::root(), $root, $link);
		}

		if (JRequest::getInt('diagnose',0) == 1) {
			echo "Link: <a href=\"{$link}\">{$link}</a><br />";
		}

		self::$_route_cache[$key] = $link;

		return $link;
	}
	
	/**
	 * Deletes old history records
	 * @return void
	 */
	public static function cleanHistory()
	{
		if ( !($item = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( 'clean_history' )->getItem( )) || !($item->value) ) {
			return true;
		}

		if ( !$item = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( 'clean_history_periodicity' )->getItem( ) ) {
			$period = 30;
		}
		else {
			$period = (int)$item->value;
		}
		JModelLegacy::getInstance( 'SBModelsHistories' )->setData( 'periodicity', $period )->delete( );
	}
	
	/**
	 * Sends email message about errors
	 * @return boolean
	 */
	public static function sendErrorEmail()
	{
		// Get statistics information after last record
		$rows = array();
		if ( self::getLastId( 'error', false ) >= 0 ) {
			$model = JModelLegacy::getInstance( 'SBModelsErrors' );
			foreach (SBPlugin::get('content.') as $content) {
				if ( $new_rows = $model->reset()->last_id( self::getLastId( 'error', false ) )->plugin( $content )->getList() ) {
					$rows = array_merge( $rows, $new_rows );
				}
			}
		}
		
		$success = true;
		if ( !empty($rows) ) {
			$config = JFactory::getConfig( );
			// send mail to users
			$type = SBHelpersConfig::getProperty( 'errors_recipient_type', 'basic', 0 );
			
			if ( $type == 1 ) {
				$username = JText::_( 'SB_ADMINISTRATOR' );
	
				$sitename = $config->get( 'sitename' );
				$siteurl = JURI::root( );
				$siteadmin_link_start = '<a href="' . $siteurl . 'administrator/" target="_blank">';
				$siteadmin_link_end = '</a>';
	
				$html = '';
				ob_start( );
				// need such values: $username, $rows, $sitename, $siteurl, $siteadmin_link_start, $siteadmin_link_end
				require_once JPATH_ROOT . '/administrator/components/com_socialbacklinks/views/errors/tmpl/mail.php';
				$html = ob_get_contents( );
				ob_clean( );
	
				$subject = JText::sprintf( 'SB_ERRORS_EMAIL_SUBJECT', $sitename );
	
				$send_errors_email = SBHelpersConfig::getProperty( 'send_errors_email' );
				if ( is_null( $send_errors_email ) ) {
					return true;
				}

				$success = JMail::sendMail( $config->get( 'mailfrom' ), $config->get( 'fromname' ), $send_errors_email, $subject, $html, true );
			}
			elseif ( $type == 2 ) {
				$sitename = $config->get( 'sitename' );
				$siteurl = JURI::root( );
				$siteadmin_link_start = '<a href="' . $siteurl . 'administrator/" target="_blank">';
				$siteadmin_link_end = '</a>';
	
				$subject = JText::sprintf( 'SB_ERRORS_EMAIL_SUBJECT', $sitename );
	
				$users = SBHelpersUser::getSuperAdministrators( );
	
				foreach ($users as $user) {
					if ( !$user->sendEmail ) {
						continue;
					}
					$username = $user->name;
	
					$html = '';
					ob_start( );
					// need such values: $username, $rows, $sitename, $siteurl, $siteadmin_link_start, $siteadmin_link_end
					require_once JPATH_ROOT . '/administrator/components/com_socialbacklinks/views/errors/tmpl/mail.php';
					$html = ob_get_contents( );
					ob_clean( );
	
					if ( !JMail::sendMail( $config->get( 'mailfrom' ), $config->get( 'fromname' ), $user->email, $subject, $html, true ) ) {
						$success = false;
					}
				}
			}
		}
		return $success;
	}

}
