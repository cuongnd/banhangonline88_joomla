<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Response
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JSON Response class.
 *
 * This class serves to provide the Joomla Platform with a common interface to access
 * response variables for e.g. Ajax requests.
 *
 * @package     Joomla.Libraries
 * @subpackage  Response
 * @since       K4.0
 */
class KunenaCompatResponseJson
{
	/**
	 * Determines whether the request was successful
	 *
	 * @var    boolean
	 * @since  K4.0
	 */
	public $success = true;

	/**
	 * The main response message
	 *
	 * @var    string
	 * @since  K4.0
	 */
	public $message = null;

	/**
	 * Array of messages gathered in the JApplication object
	 *
	 * @var    array
	 * @since  K4.0
	 */
	public $messages = null;

	/**
	 * The response data
	 *
	 * @var    mixed
	 * @since  K4.0
	 */
	public $data = null;

	/**
	 * Constructor
	 *
	 * @param   mixed    $response        The Response data
	 * @param   string   $message         The main response message
	 * @param   boolean  $error           True, if the success flag shall be set to false, defaults to false
	 * @param   boolean  $ignoreMessages  True, if the message queue shouldn't be included, defaults to false
	 *
	 * @since   K4.0
	 */
	public function __construct($response = null, $message = null, $error = false, $ignoreMessages = false)
	{
		$this->message = $message;

		// Get the message queue if requested and available
		$app = JFactory::$application;

		if (!$ignoreMessages && !is_null($app) && is_callable(array($app, 'getMessageQueue')))
		{
			$messages = $app->getMessageQueue();

			// Build the sorted messages list
			if (is_array($messages) && count($messages))
			{
				foreach ($messages as $message)
				{
					if (isset($message['type']) && isset($message['message']))
					{
						$lists[$message['type']][] = $message['message'];
					}
				}
			}

			// If messages exist add them to the output
			if (isset($lists) && is_array($lists))
			{
				$this->messages = $lists;
			}
		}

		// Check if we are dealing with an error
		if ($response instanceof Exception)
		{
			// Prepare the error response
			$this->success	= false;
			$this->message	= $response->getMessage();
		}
		else
		{
			// Prepare the response data
			$this->success	= !$error;
			$this->data			= $response;
		}
	}

	/**
	 * Magic toString method for sending the response in JSON format
	 *
	 * @return  string  The response in JSON format
	 *
	 * @since   K4.0
	 */
	public function __toString()
	{
		return json_encode($this);
	}
}
