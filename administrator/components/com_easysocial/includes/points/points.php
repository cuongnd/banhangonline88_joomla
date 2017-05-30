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
 * Class for points manipulation.
 *
 * @since 	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialPoints
{
	/**
	 * Points factory.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	SocialPoints
	 */
	public static function getInstance()
	{
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Allows 3rd party to discover rule files with the given path
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The path to the stored .points file
	 * @return	bool
	 */
	public function discover($path)
	{
		if (!$path) {
			return false;
		}

		$model = FD::model('Points');
		$state = $model->install($path);

		return $state;
	}

	/**
	 * Retrieve the params of a specific points
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getParams($command, $extension)
	{
		$table = FD::table('Points');
		$table->load(array('command' => $command, 'extension' => $extension));

		$params = $table->getParams();

		return $params;
	}

	/**
	 * Updates the cache copy of the user's points.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @param	int		The total number of points
	 * @return	bool	True if success false otherwise.
	 */
	public function updateUserPoints($userId , $points)
	{
		// Load user's app
		FD::apps()->load(SOCIAL_TYPE_USER);

		// Load the user
		$user = FD::user($userId);

		// Get the dispatcher
		$dispatcher = FD::dispatcher();

		// Construct the arguments to pass to the apps
		$args = array(&$user, &$points);

		// @trigger onBeforeAssignPoints
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onBeforeAssignPoints', $args);

		$user->addPoints( $points );

		// @trigger onAfterAssignPoints
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onAfterAssignPoints', $args);

		return true;
	}

	/**
	 * Allows caller to assign a custom point
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @param	int		The number of points to insert.
	 * @param	string	Any custom message for this point assignment.
	 * @return	bool	True if success, false otherwise.
	 */
	public function assignCustom( $userId , $points , $message = '' )
	{
		// Add history.
		$history 				= FD::table( 'PointsHistory' );
		$history->user_id 		= $userId;
		$history->points 		= $points;
		$history->state 		= SOCIAL_STATE_PUBLISHED;
		$history->message 		= $message;
		$state 	= $history->store();

		if( $state )
		{
			$this->updateUserPoints( $userId , $points );
		}
		return $state;
	}

	/**
	 * Assign points to a specific user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The command to be executed. Refer to `#__social_points_commands`.`command`
	 * @param	string	The extension or app name. Refer to `#__social_points_commands`.`extension`
	 * @param	int 	The target user's id.
	 * @return	bool	True if point is given. False otherwise.
	 */
	public function assign($command, $extension , $userId)
	{
		$config = FD::config();

		// Check if points system is enabled.
		if (!$config->get('points.enabled')) {
			return false;
		}

		// If user id is empty or 0, we shouldn't assign anything
		if (!$userId) {
			return false;
		}

		// Retrieve the points table.
		$points = FD::table('Points');
		$state  = $points->load(array('command' => $command, 'extension' => $extension));

		// Check the command and extension and see if it is valid.
		if (!$state) {
			return false;
		}

		// Check the rule and see if it is published.
		if ($points->state != SOCIAL_STATE_PUBLISHED) {
			return false;
		}

		// @TODO: Check points threshold.
		if ($points->threshold) {
		}

		// @TODO: Check the interval to see if the user has achieved this for how many times.
		if ($points->interval != SOCIAL_POINTS_EVERY_TIME) {
		}

		// @TODO: Customizable point system where only users from specific profile type may achieve this point.

		// Add history.
		$history = FD::table('PointsHistory');
		$history->points_id = $points->id;
		$history->user_id = $userId;
		$history->points = $points->points;
		$history->state = SOCIAL_STATE_PUBLISHED;
		$history->store();

		$this->updateUserPoints($userId , $points->points);

		// Assign a badge to the user for earning points.
		$badge 	= FD::badges();
		$badge->log('com_easysocial', 'points.achieve', $userId, JText::_('COM_EASYSOCIAL_POINTS_BADGE_EARNED_POINT'));

		return true;
	}

}
