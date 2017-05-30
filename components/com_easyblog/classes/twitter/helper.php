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
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'twitter' . DIRECTORY_SEPARATOR . 'consumer.php' );

class EasyBlogTwitter extends EasyBlogTwitterOAuth
{
	var $callback = '';

	public function __construct( $key , $secret , $callback )
	{
		parent::__construct($key, $secret);
		$this->callback = $callback;
	}

	public function getRequestToken($oauth_callback = NULL)
	{
		$request		= parent::getRequestToken($this->callback);

		$obj			= new stdClass();
		$obj->token		= $request['oauth_token'];
		$obj->secret	= $request['oauth_token_secret'];

		return $obj;
	}

	public function getAuthorizationURL( $token, $auto_sign_in=false )
	{
		return parent::getAuthorizeURL( $token, $auto_sign_in );
	}

	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}

	public function getAccess( $token , $secret , $verifier )
	{
		$this->token = new OAuthConsumer($token, $secret);

		$access = $this->getAccessToken($verifier);

		if(empty($access['oauth_token']) && empty($access['oauth_token_secret']))
		{
			return false;
		}

		$obj		= new stdClass();

		$obj->token	= $access['oauth_token'];
		$obj->secret= $access['oauth_token_secret'];

		$param			= EasyBlogHelper::getRegistry('');
		$param->set( 'user_id' 	, $access['user_id'] );
		$param->set( 'screen_name' 	, $access['screen_name'] );

		$obj->params	= $param->toString();
		//@todo: expiry

		return $obj;
	}

	/**
	 * Shares a new content on Twitter
	 **/
	public function share( $blog , $message = '' , $oauth , $useSystem = false )
	{
		$content =  $this->processMessage($message, $blog);

		$parameters 	= array('status' => $content);
		$result 		= $this->post('statuses/update', $parameters);
		$status			= array('success'=>true, 'error'=>false);

 		//for issues with unable to authenticate error, somehow they return errors instead of error.
		if( isset( $result->errors[0]->message ) )
		{
			$status['success'] = false;
			$status['error'] = $result->errors[0]->message;
		}

		//for others error that is not authentication issue.
		if( isset( $result->error ) )
		{
			$status['success'] = false;
			$status['error'] = $result->error;
		}

		return $status['success'];
	}

	public function setAccess( $access )
	{
		$access	= EasyBlogHelper::getRegistry( $access );
		$this->token = new OAuthConsumer($access->get('token'), $access->get( 'secret'));
		return $this->token;
	}

	public function revokeApp()
	{
		return true;
	}

	/**
	 * Process message
	 **/
	function processMessage($MsgTemplate, $blog)
	{
		$config		= EasyBlogHelper::getConfig();
		$message	= empty($MsgTemplate)? $config->get('main_twitter_message') : $MsgTemplate;
		$search		= array();
		$replace	= array();

		//replace title
		if (preg_match_all("/.*?(\\{title\\})/is", $message, $matches))
		{
			$search[] = '{title}';
		    $replace[] = $blog->title;
		}

		//replace title
		if (preg_match_all("/.*?(\\{introtext\\})/is", $message, $matches))
		{
			$introtext = empty($blog->intro)? '' : strip_tags( $blog->intro );

			$search[] = '{introtext}';
		    $replace[] = $introtext;
		}

		//replace category
		if (preg_match_all("/.*?(\\{category\\})/is", $message, $matches))
		{
			$category 	= EasyBlogHelper::getTable( 'Category', 'Table' );
			$category->load($blog->category_id);

			$search[]	= '{category}';
		    $replace[]	= $category->title;
		}

		$message = JString::str_ireplace($search, $replace, $message);

		//replace link
		if (preg_match_all("/.*?(\\{link\\})/is", $message, $matches))
		{
			// @task: Twitter now has auto shorten URL so the link will have a max of 20 chars which leaves us to a balance of 120 chars.
			$linkLength	= 20;
			$link		= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blog->id, false, true);

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$mainframe 		= JFactory::getApplication();
			$sh404exists	= EasyBlogRouter::isSh404Enabled();

			if( $mainframe->isAdmin() && $sh404exists )
			{
				$link		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=entry&id='. $blog->id;
			}

			if($config->get('main_twitter_shorten_url'))
			{
				$shortenerLogin		= $config->get('main_twitter_urlshortener_login');
				$shortenerApiKey	= $config->get('main_twitter_urlshortener_apikey');

				if(!empty($shortenerLogin) && !empty($shortenerApiKey))
				{
					require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'urlshortener.php' );

					$urlshortener	= new EasyBlogURLShortenerHelper();
					$result			= $urlshortener->get_short_url($shortenerLogin, $shortenerApiKey, $link, 'bitly');

					if(!empty($result))
					{
						$link = $result;

						$linkLength	= strlen( $link );
					}
				}
			}

			// @task: Get the remaining length that we can use.
			$remainingLength	= 140 - 3 - $linkLength;

			//split the message
			$tempMsg = explode('{link}', $message);

			for($i = 0; $i < count($tempMsg); $i++)
			{
			    $temp   =& $tempMsg[$i];

				$tempLength = strlen($temp);
				if(($tempLength > $remainingLength))
				{
					if($remainingLength<=0)
					{
						$temp = JString::substr($temp, 0, 0);
					}
					else
					{
						if($remainingLength < 6)
						{
							$temp = JString::substr($temp, 0, $remainingLength);
						}
						else
						{
							$temp = JString::substr($temp, 0, $remainingLength - 3);
							$temp .= '.. ';
						}

						$remainingLength = 0;
					}
				}
				else
				{
					$remainingLength -= $tempLength;
				}
			}

			$message = implode($link, $tempMsg);
		}
		else
		{
			$message 	= JString::substr( $message , 0 , 136 ) . '...';
		}
		
		// since we know now twitter has it own shortener, we no longer
		// need to substr as the above algorithm already taken care the
		// lenght of the message ( excluded link length )
		// so we just need to pass the processed message together with the full url link.
		return $message;

		// return JString::substr($message, 0, 140);
	}
}
