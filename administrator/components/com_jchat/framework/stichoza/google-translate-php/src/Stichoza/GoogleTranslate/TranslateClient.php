<?php
namespace Stichoza\GoogleTranslate;
/**
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage stichoza
 * @subpackage google-translate-php
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 *         
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Exception;
use ErrorException;
use BadMethodCallException;
use InvalidArgumentException;
use UnexpectedValueException;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Stichoza\GoogleTranslate\Tokens\GoogleTokenGenerator;

/**
 * Free Google Translate API PHP Package
 *
 * @author Levan Velijanashvili <me@stichoza.com>
 * @link http://stichoza.com/
 * @license MIT
 */
class TranslateClient {
	/**
	 *
	 * @var TranslateClient Because nobody cares about singletons
	 */
	private static $staticInstance;
	
	/**
	 *
	 * @var \GuzzleHttp\Client HTTP Client
	 */
	private $httpClient;
	
	/**
	 *
	 * @var string Source language - from where the string should be translated
	 */
	private $sourceLanguage;
	
	/**
	 *
	 * @var string Target language - to which language string should be translated
	 */
	private $targetLanguage;
	
	/**
	 *
	 * @var string boolean detected source language
	 */
	private static $lastDetectedSource;
	
	/**
	 *
	 * @var string Google Translate URL base
	 */
	private $urlBase = 'http://translate.google.com/translate_a/single';
	
	/**
	 *
	 * @var array URL Parameters
	 */
	private $urlParams = [ 
			'client' => 't',
			'hl' => 'en',
			'dt' => 't',
			'sl' => null, // Source language
			'tl' => null, // Target language
			'text' => null, // String to translate
			'ie' => 'UTF-8', // Input encoding
			'oe' => 'UTF-8', // Output encoding
			'tk' => null, // Token
			'multires' => 1,
			'otf' => 0,
			'pc' => 1,
			'trs' => 1,
			'ssel' => 0,
			'tsel' => 0,
			'sc' => 1 
	];
	
	/**
	 *
	 * @var array Regex key-value patterns to replace on response data
	 */
	private $resultRegexes = [ 
			'/,+/' => ',',
			'/\[,/' => '[' 
	];
	
	/**
	 * Class constructor
	 *
	 * For more information about HTTP client configuration options, visit
	 * "Creating a client" section of GuzzleHttp docs.
	 * 5.x - http://guzzle.readthedocs.org/en/5.3/clients.html#creating-a-client
	 *
	 * @param string $source
	 *        	Source language (Optional)
	 * @param string $target
	 *        	Target language (Optional)
	 * @param array $options
	 *        	Associative array of http client configuration options (Optional)
	 */
	public function __construct($source = null, $target = 'en', $options = []) {
		$this->httpClient = new GuzzleHttpClient ( $options ); // Create HTTP client
		$this->setSource ( $source )->setTarget ( $target ); // Set languages
		$this::$lastDetectedSource = false;
		$this->tokenProvider = new GoogleTokenGenerator();
	}
	
	/**
	 * Override translate method for static call
	 *
	 * @throws BadMethodCallException If calling nonexistent method
	 * @throws InvalidArgumentException If parameters are passed incorrectly
	 * @throws InvalidArgumentException If the provided argument is not of type 'string'
	 * @throws ErrorException If the HTTP request fails
	 * @throws UnexpectedValueException If received data cannot be decoded
	 */
	public static function __callStatic($name, $args) {
		switch ($name) {
			case 'translate' :
				if (count ( $args ) < 3) {
					throw new InvalidArgumentException ( "Expecting 3 parameters" );
				}
				try {
					$result = self::staticTranslate ( $args [0], $args [1], $args [2] );
				} catch ( Exception $e ) {
					throw $e;
				}
				return $result;
			case 'getLastDetectedSource' :
				return self::staticGetLastDetectedSource ();
			default :
				throw new BadMethodCallException ( "Method [{$name}] does not exist" );
		}
	}
	
	/**
	 * Override translate method for instance call
	 *
	 * @throws BadMethodCallException If calling nonexistent method
	 * @throws InvalidArgumentException If parameters are passed incorrectly
	 * @throws InvalidArgumentException If the provided argument is not of type 'string'
	 * @throws ErrorException If the HTTP request fails
	 * @throws UnexpectedValueException If received data cannot be decoded
	 */
	public function __call($name, $args) {
		switch ($name) {
			case 'translate' :
				if (count ( $args ) < 1) {
					throw new InvalidArgumentException ( "Expecting 1 parameter" );
				}
				try {
					$result = $this->instanceTranslate ( $args [0] );
					
					// Rebuild correct HTML removing wrong translation spaces if any
					$result = \JString::str_ireplace ( "< ", "<", $result );
					$result = \JString::str_ireplace ( " >", ">", $result );
					$result = \JString::str_ireplace ( "= ", "=", $result );
					$result = \JString::str_ireplace ( " =", "=", $result );
					$result = \JString::str_ireplace ( "\/ ", "\/", $result );
					$result = \JString::str_ireplace ( "\" ", "\"", $result );
					$result = \JString::str_ireplace ( " \"", "\" ", $result );
					$result = \JString::str_ireplace ( "http ", "http", $result );
					$result = \JString::str_ireplace ( ": //", "://", $result );
					$result = \JString::str_ireplace ( ":// ", "://", $result );
					$result = \JString::str_ireplace ( " //", "//", $result );
					$result = \JString::str_ireplace ( "// ", "//", $result );
					$result = \JString::str_ireplace ( " /", "/", $result );
					$result = \JString::str_ireplace ( "/ ", "/", $result );
					$result = \JString::str_ireplace ( " .", ".", $result );
					$result = \JString::str_ireplace ( "</ ", "</", $result );
				} catch ( Exception $e ) {
					throw $e;
				}
				return $result;
			case 'getLastDetectedSource' :
				return $this::staticGetLastDetectedSource ();
			default :
				throw new BadMethodCallException ( "Method [{$name}] does not exist" );
		}
	}
	
	/**
	 * Check if static instance exists and instantiate if not
	 *
	 * @return void
	 */
	private static function checkStaticInstance() {
		if (! isset ( self::$staticInstance )) {
			self::$staticInstance = new self ();
		}
	}
	
	/**
	 * Set source language we are transleting from
	 *
	 * @param string $source
	 *        	Language code
	 * @return TranslateClient
	 */
	public function setSource($source = null) {
		$this->sourceLanguage = is_null ( $source ) ? 'auto' : $source;
		return $this;
	}
	
	/**
	 * Set translation language we are transleting to
	 *
	 * @param string $target
	 *        	Language code
	 * @return TranslateClient
	 */
	public function setTarget($target) {
		$this->targetLanguage = $target;
		return $this;
	}
	
	/**
	 * Get response array
	 *
	 * @param string $string
	 *        	Text to translate
	 * @throws InvalidArgumentException If the provided argument is not of type 'string'
	 * @throws ErrorException If the HTTP request fails
	 * @throws UnexpectedValueException If received data cannot be decoded
	 * @return array Response
	 */
	public function getResponse($string) {
		if (! is_string ( $string )) {
			throw new InvalidArgumentException ( "Invalid string provided" );
		}
		
		// Strip tags if the target language affects the HTML attributes
		if (in_array ( $this->targetLanguage, array (
				'bn',
				'fa',
				'ja',
				'ko',
				'sr',
				'ru',
				'sw',
				'ta',
				'th',
				'uk',
				'zh_tw',
				'zh_cn' 
		) )) {
			$string = strip_tags ( $string );
		}
		if (in_array ( $this->targetLanguage, array (
				'ca',
				'fr',
				'es' 
		) )) {
			$string = preg_replace ( '/title=".*"/iU', '', $string );
		}
		
		$queryArray = array_merge ( $this->urlParams, array (
				'text' => $string,
				'sl' => $this->sourceLanguage,
				'tl' => $this->targetLanguage,
				'tk' => $this->tokenProvider->generateToken($string)
		) );
		
		// Randomize the user agent to avoid Google ban
		$userAgents = array (
				"Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0",
				"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0",
				"Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0",
				"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0",
				"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36",
				"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36",
				"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36",
				"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36",
				"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
				"Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
				"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko",
				"Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko",
				"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
				"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
				"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
				"Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)" 
		);
		$ua = $userAgents [rand ( 0, count ( $userAgents ) - 1 )];
		
		try {
			$response = $this->httpClient->post ( $this->urlBase, array (
					'body' => $queryArray,
					'headers' => array (
							'User-Agent' => $ua,
							'Referer' => 'https://translate.google.com' 
					) 
			) );
		} catch ( GuzzleRequestException $e ) {
			throw new ErrorException ( $e->getMessage () );
		}
		
		$body = $response->getBody (); // Get response body
		                               
		// Modify body to avoid json errors
		$bodyJson = preg_replace ( array_keys ( $this->resultRegexes ), array_values ( $this->resultRegexes ), $body );
		
		// Decode JSON data
		if (($bodyArray = json_decode ( $bodyJson, true )) === null) {
			throw new UnexpectedValueException ( 'Data cannot be decoded or it\'s deeper than the recursion limit' );
		}
		
		return $bodyArray;
	}
	
	/**
	 * Translate text
	 *
	 * This can be called from instance method translate() using __call() magic method.
	 * Use $instance->translate($string) instead.
	 *
	 * @param string $string
	 *        	Text to translate
	 * @throws InvalidArgumentException If the provided argument is not of type 'string'
	 * @throws ErrorException If the HTTP request fails
	 * @throws UnexpectedValueException If received data cannot be decoded
	 * @return string boolean text
	 */
	private function instanceTranslate($string) {
		// Patch for Google Translate tk token issue
		$string = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $string);
		$string = \JChatHelpersLanguage::utf8_latin_to_ascii ( $string  );
		
		// Rethrow exceptions
		try {
			$responseArray = $this->getResponse ( $string );
		} catch ( Exception $e ) {
			throw $e;
		}
		
		// Check if translation exists
		if (! isset ( $responseArray [0] ) || empty ( $responseArray [0] )) {
			return false;
		}
		
		// Detect languages
		$detectedLanguages = [ ];
		
		// Add detected languages
		foreach ( $responseArray as $item ) {
			if (is_string ( $item )) {
				$detectedLanguages [] = $item;
			}
		}
		
		// Another case of detected language
		if (isset ( $responseArray [count ( $responseArray ) - 2] [0] [0] )) {
			$detectedLanguages [] = $responseArray [count ( $responseArray ) - 2] [0] [0];
		}
		
		// Set initial detected language to null
		$this::$lastDetectedSource = false;
		
		// Iterate and set last detected language
		foreach ( $detectedLanguages as $lang ) {
			if ($this->isValidLocale ( $lang )) {
				$this::$lastDetectedSource = $lang;
				break;
			}
		}
		
		// Reduce array to generate translated sentenece
		if(is_array($responseArray)) {
			return array_reduce ( $responseArray [0], function ($carry, $item) {
				$carry .= $item [0];
				return $carry;
			} );
		} else {
			return $responseArray;
		}
	}
	
	/**
	 * Translate text statically
	 *
	 * This can be called from static method translate() using __callStatic() magic method.
	 * Use TranslateClient::translate($source, $target, $string) instead.
	 *
	 * @param string $source
	 *        	Source language
	 * @param string $target
	 *        	Target language
	 * @param string $string
	 *        	Text to translate
	 * @throws InvalidArgumentException If the provided argument is not of type 'string'
	 * @throws ErrorException If the HTTP request fails
	 * @throws UnexpectedValueException If received data cannot be decoded
	 * @return string boolean text
	 */
	private static function staticTranslate($source, $target, $string) {
		self::checkStaticInstance ();
		try {
			$result = self::$staticInstance->setSource ( $source )->setTarget ( $target )->translate ( $string );
		} catch ( Exception $e ) {
			throw $e;
		}
		return $result;
	}
	
	/**
	 * Get last detected language
	 * 
	 * @return string boolean detected language or boolean FALSE
	 */
	private static function staticGetLastDetectedSource() {
		return self::$lastDetectedSource;
	}
	
	/**
	 * Check if given locale is valid
	 * 
	 * @param string $lang
	 *        	Langauge code to verify
	 * @return boolean
	 */
	private function isValidLocale($lang) {
		return ! ! preg_match ( '/^([a-z]{2})(-[A-Z]{2})?$/', $lang );
	}
}