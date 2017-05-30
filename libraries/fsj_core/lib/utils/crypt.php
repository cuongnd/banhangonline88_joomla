<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

/**
 * Basic cryptograph functions
 */

defined('_JEXEC') or die;

class FSJ_Crypt
{
	static $my_key;
	
	static function xor_this($string) 
	{
		// Let's define our key here
		$key = FSJ_Crypt::get_my_key();
		$fullkey = str_pad('', strlen($string), $key);
		return $string ^ $fullkey;
	}
	
	/**
	 * Creates a session key that can be used to verify a page is from the same session
	 */	
	static function get_my_key()
	{
		if (!FSJ_Crypt::$my_key)
		{
			if (isset($_SESSION['fsj_crypt_key']))
				FSJ_Crypt::$my_key = $_SESSION['fsj_crypt_key'];
			
			if (!FSJ_Crypt::$my_key)
			{
				FSJ_Crypt::$my_key = md5(time() . mt_rand(0,10000));
				
				$_SESSION['fsj_crypt_key'] = FSJ_Crypt::$my_key;
			}
		}
		
		return FSJ_Crypt::$my_key;
	}	

	/**
	 * Returns an encrypted & utf8-encoded
	 */
	static function encrypt($pure_string, $encryption_key) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, $pure_string, MCRYPT_MODE_ECB, $iv);
		return $encrypted_string;
	}

	/**
	 * Returns decrypted original string
	 */
	static function decrypt($encrypted_string, $encryption_key) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		return $decrypted_string;
	}

	static function getEncKey($salt = "")
	{
		$config = new JConfig();

		if ($salt == "") $salt = "fsj_enc_salt";

		return $config->secret . $salt;
	}

}