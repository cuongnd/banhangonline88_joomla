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
 *
 * Photo albums library
 *
 * @since	1.0
 * @access	public
 *
 */
class SocialAlbums extends JObject
{
	/**
	 * Static variable for caching.
	 * @var	SocialAlbums
	 */
	private static $instance = null;

	/**
	 * The current unique owner of the item
	 * @var	int
	 */
	public $uid 	= null;

	/**
	 * The current unique string of the item
	 * @var	string
	 */
	public $type 	= null;

	/**
	 * The adapter for albums
	 * @var	string
	 */
	public $adapter 	= null;

	/**
	 * The table mapping for the album.
	 * @var	SocialTableAlbum
	 */
	public $data		= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 */
	public function __construct( $uid , $type , $key = null )
	{
		if ($key instanceof SocialTableAlbum) {
			$this->data 	= $key;
		} else {
			// Load the table mapping
			$table = FD::table( 'Album' );
			$table->load($key);

			$this->data 	= $table;
		}

		$this->uid = $uid;
		$this->type = $type;

		// Get the adapter
		$this->adapter 	= $this->getAdapter($type);

		// Get the Joomla document
		$this->doc = JFactory::getDocument();
	}

	/**
	 * Method to instantiate a new instance of this library.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The unique item id.
	 * @param	string	The unique item identifier. E.g: ( group, user )
	 * @param	mixed	The album's id or the object (Optional)
	 * @return
	 */
	public static function factory( $uid , $type , $key = null )
	{
		return new self( $uid , $type , $key );
	}

	/**
	 * Maps back the call method functions to the adapter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string	Method's name.
	 * @param	mixed	Arguments
	 * @return
	 */
	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->adapter , $method ) , $refArray );
	}

	/**
	 * This will group up albums by date.
	 *
	 * @since	1.0
	 * @deprecated 1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupAlbums( $rows )
	{
		$this->groupAlbumsByDate( $result );
	}

	/**
	 * This will group up albums by date.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupAlbumsByDate( $rows )
	{
		if( !$rows )
		{
			return $rows;
		}

		$albums 	= array();

		foreach( $rows as $row )
		{
			$date 	= FD::date( $row->created );
			$format	= JText::_( 'COM_EASYSOCIAL_ALBUMS_GROUP_DATE_FORMAT' );
			$index 	= $date->format( $format );

			if( !isset( $albums[ $index ] ) )
			{
				$albums[ $index ]	= array();
			}

			$albums[ $index ][]	= $row;
		}

		return $albums;
	}

	public function getPhotos( $albumId, $options = array() )
	{
		$config	= FD::config();

		$start = isset( $options['start'] ) ? $options['start'] : 0;

		$limit = isset( $options['limit'] ) ? $options['limit'] : $config->get( 'photos.pagination.photo' );

		$model = FD::model( 'photos' );

		$counter = 0;

		$nextStart = $start;

		$photos = array();

		$privacy 	= FD::privacy( FD::user()->id );

		while( $counter < $limit )
		{
			$newPhotos = $model->getPhotos( array( 'album_id' => $albumId, 'start' => $nextStart, 'limit' => $limit , 'state' => SOCIAL_STATE_PUBLISHED ) );

			$photosCount = count( $newPhotos );

			if( $photosCount === 0 )
			{
				// If photosCount is 0, means there are no more photos left to load
				$nextStart = -1;
				break;
			}

			foreach( $newPhotos as $photo )
			{
				if ($photo->viewable()) {
					// Add this photo into the photos list if privacy is true
					$photos[] = $photo;

					// Add the counter if privacy is true
					$counter++;
				}

				// Add the nextStart count regardless of the privacy
				$nextStart++;

				// If before the loop ends but we already reach the limit that we need, then break here and we will have the correct nextStart value
				if( $counter >= $limit )
				{
					break;
				}
			}
		}

		return array( 'photos' => $photos, 'nextStart' => $nextStart );
	}

	private $renderItemOptions = array(
		'viewer'       => null,
		'layout'       => 'item',
		'view'		   => 'mine',
		'limit'        => 'auto',
		'canReorder'   => false,
		'canUpload'    => false,
		'showToolbar'  => true,
		'showInfo'     => true,
		'showStats'    => false,
		'showPhotos'   => true,
		'showResponse' => true,
		'showTags'     => true,
		'showForm'     => true,
		'showLoadMore' => true,
		'showViewButton' => false,
		'photoItem'    => array(
			'viewer'       => null,
			'layout'       => 'item',
			'showToolbar'  => true,
			'showInfo'     => true,
			'showStats'    => true,
			'showResponse' => false,
			'showTags'     => false,
			'showForm'     => true,
			'openInPopup'  => true
		)
	);

	/**
	 * Wraps the provided album
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function renderItem($options = array())
	{
		$album	= $this->data;

		// Determine if album is passed in options
		if (isset($options['album'])) {
			$album = $options['album'];
		}

		// Set the default settings for opening photos in a popup
		$config = FD::config();
		$this->renderItemOptions['photoItem']['openInPopup'] = $config->get('photos.popup.default');

		// Built preset options
		$presetOptions	= array('canUpload' => $this->canUpload());

		// Normalize render options
		$options = array_merge($this->renderItemOptions, $presetOptions, $options);

		if (!empty($options['photoItem'])) {
			$options['photoItem'] = array_merge($this->renderItemOptions['photoItem'], $options['photoItem']);
		}

		// Inherit photo item's viewer from album if it is not given
		if (empty($options['photoItem']['viewer'])) {
			$options['photoItem']['viewer'] = $options['viewer'];
		}

		// Set the layout for the photo
		$options['photoItem']['layout']	= $options['layout'];

		// Photos cannot be uploaded to core albums
		if ($album->core) {
			$options['canUpload'] = false;
		}

		// Get album privacy
		// @TODO: Get proper album privacy
		$privacy = FD::privacy();

		// Get album creator
		$creator = FD::user( $album->user_id );

		// Get album viewer
		$viewer = FD::user( $options['viewer'] );

		// Get the photo options
		$photoOptions = array();

		if ($options['limit'] !== 'auto') {
			$photoOptions['limit'] = $options['limit'];
		}

		// Get album phtoos
		$photos = $album->getPhotos($photoOptions);

		// Get album likes
		$likes = FD::likes($album->id, SOCIAL_TYPE_ALBUM , 'create', SOCIAL_APPS_GROUP_USER );

		// Get album shares
		$shares = FD::get('Repost', $album->id, SOCIAL_TYPE_ALBUM , SOCIAL_APPS_GROUP_USER);

		// Get album comments
		$comments = FD::comments($album->id, SOCIAL_TYPE_ALBUM , 'create', SOCIAL_APPS_GROUP_USER , array('url' => $album->getPermalink()));

		// Get a list of tags from this album
		$tags = $album->getTags(true);

		// Build the user alias
		$userAlias = $creator->getAlias();

		// Generate item layout
		$theme = FD::themes();

		// Determines if the current document is RTL
		$rtl = $this->doc->getDirection() == 'rtl' ? true : false;

		$theme->set('rtl', $rtl);
		$theme->set('lib', $this);
		$theme->set('options', $options );
		$theme->set('userAlias', $userAlias );
		$theme->set('album', $album );
		$theme->set('tags', $tags );
		$theme->set('creator', $creator );
		$theme->set('privacy', $privacy );
		$theme->set('likes', $likes );
		$theme->set('shares', $shares );
		$theme->set('comments', $comments );
		$theme->set('photos', $photos['photos'] );
		$theme->set('nextStart', $photos['nextStart']);

		return $theme->output( 'site/albums/item' );
	}

	/**
	 * Retrieves the album's adapter
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdapter( $type )
	{
		$file 	= dirname( __FILE__ ) . '/adapters/' . strtolower( $type ) . '.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		$className 	= 'SocialAlbumsAdapter' . ucfirst( $type );
		$adapter 	= new $className( $this );

		return $adapter;
	}
}
