<?php
// namespace administrator\components\com_jchat\framework\google;
/**
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Google login user object mapping responsibilities
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @since 2.0
 */
interface JChatGoogleUserInterface {
	/**
	 * Get the provider name
	 *
	 * @return string
	 */
	public function getProvider();
	
	/**
	 * Get the UID of the user
	 *
	 * @return string
	 */
	public function getUid();
	
	/**
	 * Get the first name of the user
	 *
	 * @return string
	 */
	public function getFirstname();
	
	/**
	 * Get the last name of the user
	 *
	 * @return string
	 */
	public function getLastname();
	
	/**
	 * Get the username
	 *
	 * @return string
	 */
	public function getUsername();
	
	/**
	 * Get the emailaddress
	 *
	 * @return string
	 */
	public function getEmailAddress();
	
	/**
	 * Get the city
	 *
	 * @return string
	 */
	public function getCity();
	
	/**
	 * Get the birthdate
	 *
	 * @return string
	 */
	public function getBirthDate();
	
	/**
	 * Get the gender
	 *
	 * @return string
	 */
	public function getGender();
	
	/**
	 * Get the geolocale
	 *
	 * @return string
	 */
	public function getLocale();
	
	/**
	 * Get the Google picture avatar
	 *
	 * @return string
	 */
	public function getPicture();
}

/**
 * Google login user object mapping concrete implementation
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @since 2.0
 */
class JChatGoogleUser implements JChatGoogleUserInterface {
	
	/**
	 *
	 * @var mixed user data
	 */
	private $userData;
	
	/**
	 * Constructor.
	 *
	 * @param $userData mixed
	 *        	Raw social network data for this particular user
	 */
	public function __construct($userData) {
		$this->userData = $userData;
	}
	
	/**
	 * Get the provider name
	 *
	 * @return string
	 */
	public function getProvider() {
		return "google";
	}
	
	/**
	 * Get the UID of the user
	 *
	 * @return string
	 */
	public function getUid() {
		if (array_key_exists ( 'id', $this->userData )) {
			return $this->userData ['id'];
		}
		return null;
	}
	
	/**
	 * Get the first name of the user
	 *
	 * @return string
	 */
	public function getFirstname() {
		if (array_key_exists ( 'given_name', $this->userData )) {
			return $this->userData ['given_name'];
		}
		return null;
	}
	
	/**
	 * Get the last name of the user
	 *
	 * @return string
	 */
	public function getLastname() {
		if (array_key_exists ( 'family_name', $this->userData )) {
			return $this->userData ['family_name'];
		}
		return null;
	}
	
	/**
	 * Get the username
	 *
	 * @return string
	 */
	public function getUsername() {
		return strtolower($this->getFirstname() . '_' . $this->getLastname());
	}
	
	/**
	 * Get the emailaddress
	 *
	 * @return string
	 */
	public function getEmailAddress() {
		if (array_key_exists ( 'email', $this->userData )) {
			return $this->userData ['email'];
		}
		return null;
	}
	
	/**
	 * Get the city
	 *
	 * @return string
	 */
	public function getCity() {
		return null;
	}
	
	/**
	 * Get the birthdate
	 *
	 * @return string
	 */
	public function getBirthDate() {
		return null;
	}
	
	/**
	 * Get the gender
	 *
	 * @return string
	 */
	public function getGender() {
		if (array_key_exists ( 'gender', $this->userData )) {
			return $this->userData ['gender'];
		}
		return null;
	}
	
	/**
	 * Get the geolocale
	 *
	 * @return string
	 */
	public function getLocale() {
		if (array_key_exists ( 'locale', $this->userData )) {
			return $this->userData ['locale'];
		}
		return null;
	}
	
	/**
	 * Get the Google picture avatar
	 *
	 * @return string
	 */
	public function getPicture() {
		if (array_key_exists ( 'picture', $this->userData )) {
			return $this->userData ['picture'];
		}
		return null;
	}
}
