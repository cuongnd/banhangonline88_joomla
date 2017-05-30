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
 * Displays the group photos in a widget
 *
 * @since	1.2
 * @access	public
 */
class PhotosWidgetsGroups extends SocialAppsWidgets
{
	public function groupAdminStart($group)
	{
		$category = $group->getCategory();

        if (!$category->getAcl()->get('photos.enabled', true) || !$group->getParams()->get('photo.albums', true)) {
            return;
        }

		$this->set( 'group' , $group );
		$this->set( 'app' , $this->app );

		echo parent::display( 'widgets/widget.menu' );
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($groupId, $group)
	{
		// Get recent albums
		$output = $this->getAlbums($group);

		echo $output;
	}


	/**
	 * Display the list of photo albums
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlbums(&$group)
	{
		$params = $this->getParams();

		if (!$params->get('widgets_album', true)) {
			return;
		}

		$model = FD::model('Albums');

		// Get the list of albums from this group
		$albums = $model->getAlbums($group->id, SOCIAL_TYPE_GROUP);
		$options = array('uid' => $group->id, 'type' => SOCIAL_TYPE_GROUP);

		// Get the total number of albums
		$total = $model->getTotalAlbums($options);

		$this->set('total', $total);
		$this->set('albums', $albums);
		$this->set('group', $group);

		return parent::display('widgets/widget.albums');
	}
}
