<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_Utils.class.php,v 1.5 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * @public
 * @brief The utility class collecting static helper functions.
 */
class XCube_Utils
{
	/**
	 * @private
	 * @brief Private Constructor. In other words, it's impossible to generate an instance of this class.
	 */
	function XCube_Utils()
	{
	}
	
	/**
	 * @public
	 * @brief [Static] The alias for the current controller::redirectHeader(). This function will be deprecated.
	 * @param $url string
	 * @param $time int
	 * @param $message mixed - string or string[] - If you want to multiline message, you must set message as array.
	 * @return void
	 * 
	 * @deprecated XCube 1.0 will remove this method. Don't use static function of XCube
	 *             layer for redirect.
	 */
	function redirectHeader($url, $time, $messages = null)
	{
		$root =& XCube_Root::getSingleton();
		$root->mController->executeRedirect($url, $time, $messages);
	}

	/**
	 * @public
	 * @brief [Static] Formats string with special care for international.
	 * @return string - Rendered string.
	 * 
	 * This method renders string with modifiers and parameters. Parameters are .NET
	 * style, not printf style. Because .NET style is clear.
	 * 
	 * \code
	 *   XCube_Utils::formatString("{0} is {1}", "Time", "Money");
	 *   // Result "Time is Money"
	 * \endcode
	 * 
	 * It's possible to add a modifier to parameter place holders with ':' mark. 
	 * 
	 * \code
	 *   XCube_Utils::formatString("{0:ucFirst} is {1:toLower}", "Time", "Money");
	 *   // Result "Time is Money"
	 * \endcode
	 * 
	 * This feature is useful for automatic combined messages by message catalogs.
	 * 
	 * \par Modifiers
	 *   -li ufFirst - Upper the first charactor of the parameter's value. 
	 *   -li toUpper - Upper the parameter's value.
	 *   -li toLower - Lower the parameter's value.
	 * 
	 * @remarks
	 *     This method doesn't implement the provider which knows how to format
	 *     for each locales. So, this method is interim implement.
	 */
	function formatString()
	{
		$arr = func_get_args();
		
		if(count($arr)==0)
			return null;
		
		$message = $arr[0];
		
		$variables = array();
		if (is_array($arr[1])) {
			$variables = $arr[1];
		}
		else {
			$variables = $arr;
			array_shift($variables);
		}
		
		for ($i = 0; $i < count($variables); $i++) {
			$message = str_replace("{" . ($i) . "}", $variables[$i], $message);
			
			// Temporary....
			$message = str_replace("{" . ($i) . ":ucFirst}", ucfirst($variables[$i]), $message);
			$message = str_replace("{" . ($i) . ":toLower}", strtolower($variables[$i]), $message);
			$message = str_replace("{" . ($i) . ":toUpper}", strtoupper($variables[$i]), $message);
		}
		
		return $message;
	}
	
	/**
	 * @public
	 * @brief [Static] To encrypt strings.
	 * @param $plain_text string
	 * @param $key        string
	 * @param $algorithm  string
	 * @param $$mode      string
	 * @return string - Encrypted string.
	 */
	public static function encrypt($plain_text, $key = null, $algorithm = 'des', $mode = 'ecb') {
		if (! extension_loaded('mcrypt')) return $plain_text;
		
		if (is_null($key) || ! is_string($key)) {
			if (! defined('XOOPS_SALT')) return $plain_text;
			$key = XOOPS_SALT;
		}
		$key = md5($key);
		
		$td  = mcrypt_module_open($algorithm, '', $mode, '');
		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
		$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		
		if (mcrypt_generic_init($td, $key, $iv) < 0) {
			return $plain_text;
		}
		
		$crypt_text = base64_encode(mcrypt_generic($td, $plain_text));
		
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return $crypt_text;
	}
	
	/**
	 * @public
	 * @brief [Static] To decrypt strings.
	 * @param $crypt_text string
	 * @param $key        string
	 * @param $algorithm  string
	 * @param $$mode      string
	 * @return string - Decrypted string.
	 */
	public static function decrypt($crypt_text, $key = null, $algorithm = 'des', $mode = 'ecb') {
		if ($crypt_text === '' || ! extension_loaded('mcrypt')) return $crypt_text;
		
		if (is_null($key) || ! is_string($key)) {
			if (! defined('XOOPS_SALT')) return $plain_text;
			$key = XOOPS_SALT;
		}
		$key = md5($key);
		
		$td  = mcrypt_module_open($algorithm, '', $mode, '');
		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
		$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		
		if (mcrypt_generic_init($td, $key, $iv) < 0) {
			return $crypt_text;
		}
		
		$plain_text = mdecrypt_generic($td, base64_decode($crypt_text));
		
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return $plain_text;
	}
	
	/**
	 * @deprecated XCube 1.0 will remove this method.
	 * @see XCube_Utils::formatString()
	 */	
	function formatMessage()
	{
		$arr = func_get_args();
		
		if (count($arr) == 0) {
			return null;
		}
		else if (count($arr) == 1) {
			return XCube_Utils::formatString($arr[0]);
		}
		else if (count($arr) > 1) {
			$vals = $arr;
			array_shift($vals);
			return XCube_Utils::formatString($arr[0], $vals);
		}
	}
	
	/**
	 * @deprecated XCube 1.0 will remove this method.
	 */
	function formatMessageByMap($subject,$arr)
	{
		$searches=array();
		$replaces=array();
		foreach($arr as $key=>$value) {
			$searches[]="{".$key."}";
			$replaces[]=$value;
		}

		return str_replace($searches,$replaces,$subject);
	}
}

?>