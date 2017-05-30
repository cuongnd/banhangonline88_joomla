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

class SocialSharing
{
	static $availableVendors = array(
		'email',
		'facebook',
		'twitter',
		'google',
		'live',
		'linkedin',
		'myspace',
		'vk',
		'stumbleupon',
		'digg',
		'tumblr',
		'evernote',
		'reddit',
		'delicious'
	);

	public $vendors		= array();

	private $options	= array();

	/**
	 * Determines how the share button should behave. (dialog,popover)
	 * @var string
	 */
	public $display 	= 'dialog';

	/**
	 * Determines the title to show in the dialog or popover
	 * @var string
	 */
	public $displayTitle = '';

	/**
	 * Determines the text to be displayed
	 * @var string
	 */
	public $text 		= '';

	/**
	 * Extended css class
	 * @var string
	 */
	public $css 		= '';


	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	An array of options.
	 */
	public function __construct( $options = array() )
	{
		$this->load( $options );
	}

	public static function factory( $options = array() )
	{
		return new self( $options );
	}

	public function load( $options = array() )
	{
		if( !isset( $options['url'] ) )
		{
			$options['url'] = FRoute::_( JRequest::getURI() );
		}

		$this->url		= $options[ 'url' ];

		// If display mode is specified, set it accordingly.
		if( isset( $options[ 'display' ] ) )
		{
			$this->display 	= $options[ 'display' ];
		}

		// Set the default text to our own text.
		$this->text 	= JText::_( 'COM_EASYSOCIAL_SHARING_SHARE_THIS' );

		// If text is provided, allow user to override the default text.
		if( isset( $options[ 'text' ] ) )
		{
			$this->text 	= $options[ 'text' ];
		}

		// Obey settings
		$config = FD::config();
		foreach( self::$availableVendors as $vendor )
		{
			if( $config->get( 'sharing.vendors.' . $vendor ) )
			{
				$this->vendors[] = $vendor;
			}
		}

		// Force exclude
		if( isset( $options['exclude'] ) )
		{
			$this->vendors = array_diff( $this->vendors, self::$availableVendors );

			unset( $options['exclude'] );
		}

		// Force include
		if( isset( $options['include'] ) )
		{
			$notInList = array_diff( $options['include'], $this->vendors );

			$this->vendors = array_merge( $this->vendors, $options['include'] );

			unset( $options['include'] );
		}

		if( isset( $options[ 'css' ] ) )
		{
			$this->css 	= $options[ 'css' ];
		}

		$this->options = $options;
	}

	public function getContents()
	{
		$theme = FD::themes();

		// Extract email out
		if( in_array( 'email', $this->vendors ) )
		{
			$this->vendors = array_diff( $this->vendors, array( 'email' ) );

			$theme->set( 'email' , $this->getVendor( 'email' ) );
		}

		// Get list of vendors
		$vendors	= $this->getVendors();

		$theme->set( 'vendors'	, $vendors );

		$contents 	= $theme->output( 'admin/sharing/base' );

		return $contents;
	}

	/**
	 * Displays the sharing code on the page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHTML($icon=false)
	{
		// Check for global settings
		if (!FD::config()->get('sharing.enabled')) {
			return;
		}

		$theme 		= FD::themes();

		// Generate a unique id for this element
		$uniqueid = uniqid();

		// Set the text
		$theme->set( 'text'			, $this->text );
		$theme->set( 'css' 			, $this->css );
		$theme->set( 'uniqueid'		, $uniqueid );
		$theme->set( 'icon'			, $icon );

		if ($this->display!=="dialog") {
			$contents 	= $this->getContents();
			$theme->set( 'contents'		, $contents );
		}


		// Set the url to share.
		$theme->set( 'url'			, $this->url );

		// Set the title to share.
		$theme->set( 'title'		, !empty( $this->options['title'] ) ? $this->options['title'] : '' );

		// Set the summary to share.
		$theme->set( 'summary'		, !empty( $this->options['summary'] ) ? $this->options['summary'] : '' );

		return $theme->output( 'admin/sharing/base.' . $this->display );
	}

	public function getVendors()
	{
		$vendors = array();

		foreach( $this->vendors as $name )
		{
			$v = $this->getVendor( $name );

			if( $v !== false )
			{
				$vendors[$name] = $v;
			}
		}

		return $vendors;
	}

	public function getVendor( $name )
	{
		static $vendorClasses = array();

		if( empty( $vendorClasses[$name] ) )
		{
			$vendorFile = dirname( __FILE__ ) . '/vendors/' . $name . '.php';

			if( JFile::exists( $vendorFile ) )
			{
				require_once( $vendorFile );

				$vendorClass = 'SocialSharing' . ucfirst( $name );

				if( class_exists( $vendorClass ) )
				{
					$vendor = new $vendorClass( $name, $this->options );
				}
				else
				{
					FD::logError( __FILE__, __LINE__, 'Vendor class not found: ' . $vendorClass );
					return false;
				}
			}
			else
			{
				FD::logError( __FILE__, __LINE__, 'Vendor file not found: ' . $vendorFile );
				return false;
			}

			$vendorClasses[$name] = $vendor;
		}

		return $vendorClasses[$name];
	}

	public function sendLink( $recipients, $token, $content = '' )
	{
		$mailer	= FD::mailer();

		$mail	= $mailer->getTemplate();

		$title	= JText::sprintf( 'COM_EASYSOCIAL_SHARING_EMAIL_TITLE', FD::user()->getName() );

		$url	= base64_decode( $token );

		$mail->setTitle( $title );

		$mail->setTemplate( 'site/sharing/link' , array( 'url' => $url, 'content' => $content, 'senderName' => FD::user()->getName() , 'sender' => $mail->sender_email ) );

		foreach( $recipients as $recipient )
		{
			$mail->setRecipient( '', $recipient );

			$mailer->create( $mail );
		}

		return true;
	}
}
