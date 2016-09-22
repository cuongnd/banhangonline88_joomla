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

abstract class SocialSharingVendor
{
	public $name		= '';

	public $base		= '';

	public $params		= array();

	public $map			= array(
		'url'		=> 'url',
		'title'		=> 'title',
		'summary'	=> 'summary'
	);

	public $popup		= array(
		'menubar'		=> 0,
		'resizble'		=> 0,
		'scrollbars'	=> 0,
		'width'			=> 660,
		'height'		=> 320
	);

	public $link		= '';

	public $isFirst		= null;

	public $token		= '&';

	public function __construct( $name, $options = array() )
	{
		$this->name = $name;

		$this->setParams( $options );
	}

	public function getHTML()
	{
		$theme = FD::themes();

		$theme->set( 'name'		, $this->name );
		$theme->set( 'link'		, $this->getLink() );
		$theme->set( 'icon'		, $this->getIcon() );
		$theme->set( 'title'	, $this->getTitle() );
		$theme->set( 'popup'	, $this->getPopup() );

		return $theme->output( $this->getThemeFile() );
	}

	public function getThemeFile()
	{
		return 'admin/sharing/vendor';
	}

	public function getIcon()
	{
		return '<i class="icon-es-24 icon-es-' . $this->name . '"></i>';
	}

	public function getTitle()
	{
		return JText::_( 'COM_EASYSOCIAL_SHARING_' . JString::strtoupper( $this->name ) );
	}

	public function getLink()
	{
		if( empty( $this->link ) )
		{
			$this->link = $this->base;

			foreach( $this->map as $key => $paramkey )
			{
				$value = $this->getParam( $key );

				if( $value !== false )
				{
					$this->addParam( $paramkey, $value );
				}
			}
		}

		return $this->link;
	}

	public function getPopup()
	{
		$optionString = array();

		foreach( $this->popup as $key => $value )
		{
			$optionString[] = $key . '=' . $value;
		}

		return implode( ',', $optionString );
	}

	public function setParams( $params = array(), $force = false )
	{
		foreach( $params as $key => $value )
		{
			if( array_key_exists( $key, $this->map ) || $force )
			{
				$this->params[$this->map[$key]] = $value;
			}
		}
	}

	public function addParam( $key, $value )
	{
		$token = $this->token;

		if( $this->isFirst === null )
		{
			$this->checkFirst();
		}

		if( $this->isFirst === true )
		{
			$token = '?';
			$this->isFirst = false;
		}

		$this->link .= $token . $key . '=' . $value;
	}

	public function getParam( $key )
	{
		$method = 'getParam' . ucfirst( $key );

		if( method_exists( $this, $method ) )
		{
			return $this->$method();
		}

		if( empty( $this->map[$key] ) || empty( $this->params[$this->map[$key]] ) )
		{
			return false;
		}

		return $this->params[$this->map[$key]];
	}

	public function getParamUrl()
	{
		if( empty( $this->map['url'] ) || empty( $this->params[$this->map['url']] ) )
		{
			return false;
		}

		return $this->params[$this->map['url']];
	}

	public function getParamTitle()
	{
		if( empty( $this->map['title'] ) || empty( $this->params[$this->map['title']] ) )
		{
			return false;
		}

		return $this->params[$this->map['title']];
	}

	public function getParamSummary()
	{
		if( empty( $this->map['summary'] ) || empty( $this->params[$this->map['summary']] ) )
		{
			return false;
		}

		return $this->params[$this->map['summary']];
	}

	public function checkFirst()
	{
		$this->isFirst = ( JString::strpos( $this->link, '?' ) ) === false ? true : false;
	}
}
