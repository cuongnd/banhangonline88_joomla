<?php
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * The first PHP Library to support OAuth for Twitter's REST API.
 */

if ( !class_exists('OAuthServer') )
{
	/* Load OAuth lib. You can find it at http://oauth.net */
	require_once('OAuth.php');
}

/**
 * Twitter OAuth class
 */
class LinkedInOAuth {
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
  /* Set up the API root URL. */
  public $host = "https://api.linkedin.com/v1/";
  /* Set timeout default. */
  public $timeout = 30;
  /* Set connect timeout. */
  public $connecttimeout = 30; 
  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  /* Respons format. */
  public $format = 'xml';
  /* Decode returned json data. */
  public $decode_xml = TRUE;
  /* Contains the last HTTP headers returned. */
  public $http_info;
  /* Set the useragnet. */
  public $useragent = 'LinkedInOAuth v0.1.0';
  /* Immediately retry the API call if the response was not successful. */
  //public $retry = TRUE;




  /**
   * Set API URLS   --- the linkedin root is https://api.linkedin.com/
   */
  function accessTokenURL()  { return 'https://api.linkedin.com/uas/oauth/accessToken'; }
  //function authenticateURL() { return 'https://api.linkedin.com/uas/oauth/authenticate'; }
  function authorizeURL()    { return 'https://api.linkedin.com/uas/oauth/authenticate'; }
  function requestTokenURL() { return 'https://api.linkedin.com/uas/oauth/requestToken?scope=r_network+w_messages+rw_nus+rw_groups+r_contactinfo+r_emailaddress+r_fullprofile+r_basicprofile'; }

  /**
   * Debug helpers
   */
  function lastStatusCode() { return $this->http_status; }
  function lastAPICall() { return $this->last_api_call; }

  /**
   * construct TwitterOAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }


  /**
   * Get a request_token from Twitter
   *
   * @returns a key/value array containing oauth_token and oauth_token_secret
   */
  function getRequestToken($oauth_callback = NULL) {
    $parameters = array();
    if (!empty($oauth_callback)) {
      $parameters['oauth_callback'] = $oauth_callback;
    } 
    $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * Get the authorize URL
   *
   * @returns a string
   */
  function getAuthorizeURL($token, $sign_in_with_twitter = TRUE) {
    if (is_array($token)) {
      $token = $token['oauth_token'];
    }
    if (empty($sign_in_with_twitter)) {
      return $this->authorizeURL() . "?oauth_token={$token}";
    } else {
       return $this->authorizeURL() . "?oauth_token={$token}";
    }
  }

  /**
   * Exchange request token and secret for an access token and
   * secret, to sign API calls.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham")
   */
  function getAccessToken($oauth_verifier = FALSE) {
    $parameters = array();
    if (!empty($oauth_verifier)) {
      $parameters['oauth_verifier'] = $oauth_verifier;
    }
    $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * One time exchange of username and password for access token and secret.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham",
   *                "x_auth_expires" => "0")
   */  
  function getXAuthToken($username, $password) {
    $parameters = array();
    $parameters['x_auth_username'] = $username;
    $parameters['x_auth_password'] = $password;
    $parameters['x_auth_mode'] = 'client_auth';
    $request = $this->oAuthRequest($this->accessTokenURL(), 'POST', $parameters);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * GET wrapper for oAuthRequest.
   */
  function get($url, $selectors = array(), $parameters = array()) { // added $selectors to handle linkedin's... strange field selectors
  	$url .= ':(';
  	foreach( $selectors as $selector ) {
  		$url .= $selector . ',';
  	}
  	$url .= ')';
  	//echo $url;
    $response = $this->oAuthRequest($url, 'GET', $parameters);
    if ($this->format === 'xml' && $this->decode_xml) {
      return new SimpleXMLElement($response);
    }
    return $response;
  }
  
  /**
   * POST wrapper for oAuthRequest.
   */
  function post($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'POST', $parameters);
    if ($this->format === 'xml' && $this->decode_xml) {
      return new SimpleXMLElement($response);
    }
    return $response;
  }
  
  /**
   * DELETE wrapper for oAuthReqeust.
   */
  function delete($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'DELETE', $parameters);
    if ($this->format === 'xml' && $this->decode_xml) {
      return json_decode($response);
    }
    return $response;
  }
  
  /**
   * POST wrapper for httpRequest.
   */
  function post2($url, $body = null) {
  	if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}";
    }
    
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, "POST", $url);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);
    $auth_header = $request->to_header("https://api.linkedin.com");
    
    $response = $this->httpRequest($url, $auth_header, "POST", $body);
	
	if ( $this->http_code == 201 )
	{
		return true;
	}
	else {
		if ( ( $this->format === 'xml' ) && $this->decode_xml ) 
		{
			return new SimpleXMLElement( $response );
		}
		else {
			return false;
		}
	}
  }
  
  /**
   * PUT wrapper for httpRequest.
   */
  function put($url, $body = null) {
  	if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}";
    }
    
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, "PUT", $url);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);
    $auth_header = $request->to_header("https://api.linkedin.com");
    
    $response = $this->httpRequest($url, $auth_header, "PUT", $body);
	
	if (empty($response))
	{
		return true;
	}
	
    if ($this->format === 'xml' && $this->decode_xml) {
      return new SimpleXMLElement($response);
    }
    return $response;
  }

  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $method, $parameters) {
    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}";
    }
       
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);
        
    switch ($method) {
    case 'GET':
      return $this->http($request->to_url(), 'GET');
    default:
      return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
  }

  /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $method, $postfields = NULL) {
    $this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    $this->url = $url;
    curl_close ($ci);
    return $response;
  }

  /**
   * Get the header info to store.
   */
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value;
    }
    return strlen($header);
  }
  
  /**
   * Make an HTTP request, another variant 
   *
   * @return API results
   */
  function httpRequest($url, $auth_header, $method, $body = NULL) {
    if (!$method) {
      $method = "GET";
    };

	$this->http_info = array();
    $curl = curl_init();
	
	/* Curl settings */
    curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
	
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header)); // Set the headers.

    if ($body) {
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header, "Content-Type: text/xml;charset=utf-8"));   
    }
	
	$response = curl_exec($curl);
	$this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $this->http_info = array_merge( $this->http_info, curl_getinfo($curl) );
    $this->url = $url;
    
    curl_close($curl);
    return $response; 
  }
}
