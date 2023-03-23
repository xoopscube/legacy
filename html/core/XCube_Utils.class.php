<?php
/**
 * /core/XCube_Utils.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
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

		$variables = [];
		if ( is_array( $arr[1] ) ) {
			$variables = $arr[1];
		} else {
			$variables = $arr;
			array_shift( $variables );
		}
		foreach ( $variables as $i => $iValue ) {
			// Temporary....
			// @gigamaster merged calls
			$message = str_replace(
				[
					'{' . ( $i ) . '}',
					'{' . ( $i ) . ':ucFirst}',
					'{' . ( $i ) . ':toLower}',
					'{' . ( $i ) . ':toUpper}'
				],
				[
					$variables[ $i ],
					ucfirst( $iValue ),
					strtolower( $iValue ),
					strtoupper( $iValue )
				],
				$message );
		}

		return $message;
	}

	/**
	 * @public
	 * @brief [Static] To encrypt strings by "DES-ECB".
	 *
	 * @param string $plain_text
	 * @param string|null $key
	 *
	 * @return string - Encrypted string.
	 */
	public static function encrypt(string $plain_text, string $key = null ) {
		if ($plain_text === '') {
			return $plain_text;
		}

        // @todo @gigamaster
        // TODO check if ondition is unnecessary because it is checked by '! is_string( $key )'
        // if (! is_string( $key )) {
         if ( null === $key || ! is_string( $key ) ) {
			if ( ! defined( 'XOOPS_SALT' ) ) {
				return $plain_text;
			}
			$key = XOOPS_SALT;
		}
		$key = substr( md5( $key ), 0, 8 );

        // Legacy backwards compatibility
/*		if ( ! function_exists( 'openssl_encrypt' ) ) {
			if ( ! extension_loaded( 'mcrypt' ) ) {
				return $plain_text;
			}
			$td = mcrypt_module_open( 'des', '', 'ecb', '' );

			if ( mcrypt_generic_init( $td, $key, 'iv_dummy' ) < 0 ) {
				return $plain_text;
			}

			$crypt_text = base64_encode( mcrypt_generic( $td, $plain_text ) );

			mcrypt_generic_deinit( $td );
			mcrypt_module_close( $td );
		} else {*/
			$crypt_text = openssl_encrypt( $plain_text, 'DES-ECB', $key );
//		}

		return false === $crypt_text ? $plain_text : $crypt_text;
	}

	/**
	 * @public
	 * @brief [Static] To decrypt strings by "DES-ECB".
	 *
	 * @param string $crypt_text
	 * @param string|null $key
	 *
	 * @return string - Decrypted string.
	 */
	public static function decrypt(string $crypt_text, string $key = null ) {
		if ( '' === $crypt_text ) {
			return $crypt_text;
		}

        // @todo @gigamaster
        // Condition is unnecessary because it is checked by '! is_string( $key )'
        // if (! is_string( $key )) {
        if ( null === $key || ! is_string( $key ) ) {
			if ( ! defined( 'XOOPS_SALT' ) ) {
				return $crypt_text;
			}
			$key = XOOPS_SALT;
		}
		$key = substr( md5( $key ), 0, 8 );

        // Legacy backwards compatibility
		// PHP < 5.4.0 can not use `OPENSSL_ZERO_PADDING`
//		if ( ! function_exists( 'openssl_decrypt' ) || PHP_VERSION_ID < 50400 ) {
//			if ( ! extension_loaded( 'mcrypt' ) ) {
//				return $crypt_text;
//			}
//			$td = mcrypt_module_open( 'des', '', 'ecb', '' );
//
//			if ( mcrypt_generic_init( $td, $key, 'iv_dummy' ) < 0 ) {
//				return $crypt_text;
//			}
//
//			$plain_text = mdecrypt_generic( $td, base64_decode( $crypt_text ) );
//
//			mcrypt_generic_deinit( $td );
//			mcrypt_module_close( $td );
//		} else {
			$plain_text = openssl_decrypt( $crypt_text, 'DES-ECB', $key, OPENSSL_ZERO_PADDING );
//		}
		// remove \0 padding for mcrypt encrypted text
		$plain_text = rtrim( $plain_text, "\0" );
		// remove pkcs#7 padding for openssl encrypted text if padding string found
		$pad_ch  = substr( $plain_text, - 1 );
		$pad_len = ord( $pad_ch );
		if ( 0 == substr_compare( $plain_text, str_repeat( $pad_ch, $pad_len ), - $pad_len ) ) {
			$plain_text = substr( $plain_text, 0, strlen( $plain_text ) - $pad_len );
		}

		return false === $plain_text ? $crypt_text : $plain_text;
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
