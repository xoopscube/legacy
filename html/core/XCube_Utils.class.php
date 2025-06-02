<?php
/**
 * /core/XCube_Utils.class.php
 * @package    XCube
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      The utility class collecting static helper functions.
 */

class XCube_Utils {
	/**
	 * @private
	 * @brief Private Constructor. In other words, it's impossible to generate an instance of this class.
	 */
	public function __construct() {
	}

	/**
	 * @public
	 * @brief [Static] The alias for the current controller::redirectHeader(). This function will be deprecated.
	 *
	 * @param string $url
	 * @param int $time
	 * @param null $messages
	 *
	 * @return void
	 *
	 * @deprecated XCube 1.0 will remove this method. Don't use static function of XCube
	 *             layer for redirect.
	 */
	public static function redirectHeader( $url, $time, $messages = null ) {
		$root =& XCube_Root::getSingleton();
		$root->mController->executeRedirect( $url, $time, $messages );
	}

	/**
	 * @public
	 * @brief [Static] Formats string with special care for international.
	 * @return string - Rendered string.
	 *
	 * This method renders a String with modifiers and parameters.
	 * Parameters are .NET style, not printf style. Because .NET style is clear.
	 *
	 * \code
	 *   XCube_Utils::formatString("{0} is {1}", "Time", "Money");
	 *   // Result "Time is Money"
	 * \endcode
	 *
	 * It's possible to add a modifier to the parameter placeholders with ':' mark.
	 *
	 * \code
	 *   XCube_Utils::formatString("{0:ucFirst} is {1:toLower}", "Time", "Money");
	 *   // Result "Time is Money"
	 * \endcode
	 *
	 * This function is useful for messages automatically combined by message catalogues.
	 *
	 * \par Modifiers
	 *   -li ufFirst - Upper the first character of the parameter's value.
	 *   -li toUpper - Upper the parameter's value.
	 *   -li toLower - Lower the parameter's value.
	 *
	 * @remarks
	 *     This method does not implement or provide a formatted string for a given locale.
	 *     This method is therefore provisionally implemented.
	 */
    public static function formatString() {
        $arr = func_get_args();

        if (count($arr) === 0) {
            return null;
        }

        $message = $arr[0];
        
        // Initialize variables array
        $variables = [];
        
        // Only process variables if we have more than one argument
        if (count($arr) > 1) {
            if (is_array($arr[1])) {
                $variables = $arr[1];
            } else {
                $variables = $arr;
                array_shift($variables);
            }
        }

        // Only process replacements if we have variables
        if (!empty($variables)) {
            foreach ($variables as $i => $iValue) {
                if (!is_scalar($iValue)) {
                    continue; // Skip non-scalar values
                }
                
                $search = [
                    '{' . $i . '}',
                    '{' . $i . ':ucFirst}',
                    '{' . $i . ':toLower}',
                    '{' . $i . ':toUpper}'
                ];
                
                $replace = [
                    (string)$variables[$i],
                    ucfirst((string)$iValue),
                    strtolower((string)$iValue),
                    strtoupper((string)$iValue)
                ];
                
                $message = str_replace($search, $replace, $message);
            }
        }

        return $message;
    }

    /**
     * Encrypts strings using OpenSSL DES-ECB encryption
     *
     * @param string $plain_text The text to encrypt
     * @param ?string $key Optional encryption key (uses XOOPS_SALT if not provided)
     * @return string The encrypted string or original text if encryption fails
     */
    public static function encrypt(string $plain_text, ?string $key = null): string {
        // Return early if nothing to encrypt
        if ($plain_text === '') {
            return $plain_text;
        }

        // Use XOOPS_SALT as fallback key
        if ($key === null) {
            if (!defined('XOOPS_SALT')) {
                return $plain_text;
            }
            $key = XOOPS_SALT;
        }

        // Generate 8-byte key from input
        $key = substr(md5($key), 0, 8);

        // Add PKCS#7 padding
        $block_size = 8; // DES block size is 8 bytes
        $pad_length = $block_size - (strlen($plain_text) % $block_size);
        $plain_text .= str_repeat(chr($pad_length), $pad_length);

        // Perform encryption
        $crypt_text = openssl_encrypt($plain_text, 'DES-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

        return $crypt_text !== false ? base64_encode($crypt_text) : $plain_text;
    }

    /**
     * Decrypts strings using OpenSSL DES-ECB encryption
     *
     * @param string $crypt_text The text to decrypt
     * @param ?string $key Optional decryption key (uses XOOPS_SALT if not provided)
     * @return string The decrypted string or original text if decryption fails
     */
    public static function decrypt(string $crypt_text, ?string $key = null): string {
        // Return early if nothing to decrypt
        if ($crypt_text === '') {
            return $crypt_text;
        }

        // Use XOOPS_SALT as fallback key
        if ($key === null) {
            if (!defined('XOOPS_SALT')) {
                return $crypt_text;
            }
            $key = XOOPS_SALT;
        }

        // Generate 8-byte key from input
        $key = substr(md5($key), 0, 8);

        // Decode base64
        $decoded = base64_decode($crypt_text, true);
        if ($decoded === false) {
            return $crypt_text;
        }

        // Perform decryption
        $plain_text = openssl_decrypt($decoded, 'DES-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
        
        if ($plain_text === false) {
            return $crypt_text;
        }

        // Remove PKCS#7 padding
        $pad_length = ord($plain_text[strlen($plain_text) - 1]);
        if ($pad_length > 0 && $pad_length <= 8) {
            $plain_text = substr($plain_text, 0, -$pad_length);
        }

        return $plain_text;
    }

	/**
	 * @deprecated XCube 1.0 removes this method.
	 * @see XCube_Utils::formatString()
	 */
	public function formatMessage() {
		$arr = func_get_args();

		if ( 0 == count( $arr ) ) {
			return null;
		}

		if ( 1 == count( $arr ) ) {
			//@gigamaster replaced by self - this return XCube_Utils::formatString($arr[0]);
			return self::formatString( $arr[0] );
		}

		if ( count( $arr ) > 1 ) {
			$vals = $arr;
			array_shift( $vals );

			//@gigamaster replaced by self - return XCube_Utils::formatString($arr[0], $vals);
			return self::formatString( $arr[0], $vals );
		}
	}

	/**
	 * @param $subject
	 * @param $arr
	 *
	 * @return string|string[]
	 * @deprecated XCube 1.0 will remove this method.
	 */
	public function formatMessageByMap( $subject, $arr ) {
		$searches = [];
		$replaces = [];
		foreach ( $arr as $key => $value ) {
			$searches[] = '{' . $key . '}';
			$replaces[] = $value;
		}

		return str_replace( $searches, $replaces, $subject );
	}

    /**
     * Recursively delete directory /install
     *
     * @param string $delete_path
     */
    public function recursiveRemove( string $delete_path ) {
        if (is_dir( $delete_path )) {
            foreach (scandir( $delete_path ) as $entry ) {
                if (!in_array( $entry, ['.', '..'], true )) {
                    $this->recursiveRemove($delete_path . DIRECTORY_SEPARATOR . $entry);
                }
            }
            rmdir( $delete_path );
        } else {
            unlink( $delete_path );
        }
    }

    /**
     * Preload Active
     * Copy preload from directory /preload/disabled
     *
     * @param string $pre_disable
     * @param string $pre_active
     */
    public function preloadActive(string $pre_disable, string $pre_active ) {

        if( !copy( $pre_disable, $pre_active ) ) {
            echo "File can't be copied! \n";
        } else {
            echo "File has been copied! \n";
        }

    }
}
