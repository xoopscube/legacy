<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

class FormProcessByHtml {
	public $fields = [];
	public $form_html = '';
	public $column_separator = ',';
	public $types = [ 'int', 'double', 'singlebytes', 'email', 'url', 'telephone' ];
	public $validator_dir;

//	public function FormProcessByHtml(): FormProcessByHtml
//    {
//		return $this->__construct();
//	}

	public function __construct() {
		// register validators
		$this->validator_dir = __DIR__ . '/validators';

		if ( $handler = @opendir( $this->validator_dir ) ) {
			while ( ( $file = readdir( $handler ) ) !== false) {
				if ( strpos( $file, '.' ) === 0 ) {
					continue;
				}
				$this->types[] = substr( $file, 0, - 4 );
			}
		}
	}

	public function setFieldsByForm( $form_html, $ignore_names = [] ): void {
		// initialize
		$this->fields    = [];
		$this->form_html = $form_html;

		// get name="..." from the form
		preg_match_all( '#<[^>]+name=([\'"]?)([^\'" ]+)\\1[^>]*>#iU', $this->form_html, $matches, PREG_SET_ORDER );

		$tags = [];
		foreach ( $matches as $match ) {
			$tags[] = [ $match[0], $match[2] ];
		}

		// parse HTML and file label
		foreach ( $tags as $tag_and_name ) {
			[ $tag, $field_name_raw ] = $tag_and_name;

			// check whether the field is array or not (TODO)
			$count = 1; // number of controllers with the "name"
			if ( ( $pos = strpos( $field_name_raw, '[' ) ) > 0 ) {
				$field_name = substr( $field_name_raw, 0, $pos );
				$array_type = 'linear';
			} else {
				$field_name = $field_name_raw;
				$array_type = '';
			}

			// ignore the form with specified name like cancel button
			if ( in_array( $field_name, $ignore_names, true ) ) {
				continue;
			}

			// options for radio/checkbox or multiple text with the same "name"
			$options = [];
			if ( isset( $this->fields[ $field_name ] ) ) {
				$this->fields[ $field_name ]['count'] ++;
				$this->fields[ $field_name ]['tags'][]    = $tag;
				$this->fields[ $field_name ]['options'][] = $this->fieldValueFromTag( $tag );
				continue;
			}

			$options[] = $this->fieldValueFromTag( $tag );

			// tag kind - Form fields type
			if ( strncasecmp( $tag, '<textarea', 9 ) === 0) {
				$tag_kind = 'textarea';
			} elseif ( strncasecmp( $tag, '<select', 7 ) === 0) {
				$tag_kind = 'select';
				if ( stripos( $tag, 'multiple' ) !== false ) {
					$count = 0x10000; // large enough
				}
			} elseif ( stripos( $tag, 'type="checkbox"' ) !== false ) {
				$tag_kind = 'checkbox';
			} elseif ( stripos( $tag, 'type="radio"' ) !== false ) {
				$tag_kind = 'radio';
			} elseif ( stripos( $tag, 'type="hidden"' ) !== false ) {
				$tag_kind = 'hidden';
			} elseif ( stripos( $tag, 'type="text"' ) !== false ) {
				$tag_kind = 'text';
            } elseif ( stripos( $tag, 'type="url"' ) !== false ) {
                $tag_kind = 'url';
			} elseif ( stripos( $tag, 'type="submit"' ) !== false ) {
				$tag_kind = 'submit';
			} else {
				continue;
			}

			// get id of the tag
			$id = '';
			if ( preg_match( '/id\s*=\s*"([^"]+)"/', $tag, $regs ) ) {
				$id = trim( $regs[1] );
			}

			// get title of the tag
			$title = '';
			if ( preg_match( '/title\s*=\s*"([^"]+)"/', $tag, $regs ) ) {
				$title = trim( $regs[1] );
			}

			// get classes of the tag
			$classes = [];
			if ( preg_match( '/class\s*=\s*"([^"]+)"/', $tag, $regs ) ) {
				$classes = array_map( 'trim', explode( ' ', trim( $regs[1] ) ) );
			}

			// required
			$required = in_array( 'required', $classes, true );

			// type
			$type = 'string';
			foreach ( $this->types as $eachtype ) {
				if ( in_array( $eachtype, $classes, true ) ) {
					$type = $eachtype;
					break;
				}
			}
//            $type = 'url';
//            if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $tag, $regs ) ) {
//
//            }

                // get label as title of the field
			$label = empty( $title ) ? $field_name : $title;
			if ( ! in_array( $tag_kind, [ 'radio', 'checkbox' ] ) ) {
				// search <label> for other than radio/checkbox
				if ( preg_match( '/#for\s*=\s*([\'"]?)' . preg_quote( $id ) . '\\1>(.*)<\/label>#iU/', $this->form_html, $regs ) ) {
					$label = strip_tags( @$regs[2] );
				}
			} else {
				// search the nearest <legend> for radio/checkbox
				foreach ( preg_split( '#</fieldset>#i', $form_html ) as $fieldsetblock ) {
					if ( strpos( $fieldsetblock, $tag ) !== false && preg_match( '#<legend[^>]*>([^<]+)</legend>#', $fieldsetblock, $sub_regs ) ) {
						$label = strip_tags( @$sub_regs[1] );
						break;
					}
				}
			}

			$this->fields[ $field_name ] = [
				'field_name_raw' => $field_name_raw,
				'tags'           => [ $tag ],
				'tag_kind'       => $tag_kind,
				'id'             => $id,
				'classes'        => $classes,
				'label'          => $label,
				'required'       => $required,
				'array_type'     => $array_type,
				'type'           => $type,
				'options'        => $options,
				'count'          => $count,
				'errors'         => [],
			];
		}
	}

	public function fieldValueFromTag( $tag ): string {
		if ( preg_match( '/value\s*=\s*"([^"]+)"/', $tag, $regs ) ) {
			return trim( $regs[1] );
		}

		return 'on';
	}

	public function importSession( $session_data, $check_fields = true ): void {
		if ( $check_fields ) {
			foreach ( array_keys( $this->fields ) as $field_name ) {
				if ( isset( $session_data[ $field_name ] ) ) {
					$this->fields[ $field_name ] = $session_data[ $field_name ];
				}
			}
		} else {
			$this->fields = $session_data;
		}
	}

	public function fetchPost( $input_encoding = null ): array {
		( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &( new MyTextSanitizer )->getInstance();

		$_post = $this->_getPostAsArray( $input_encoding );

		foreach ( $this->fields as $field_name => $attribs ) {
			$value = @$_post[ $field_name ];

			// array checks (TODO)
			$value4reqcheck = $value;
			if ( is_array( $value ) ) {
				if ( $attribs['count'] <= 1 ) {
					$value          = (string) array_pop( $value );
					$value4reqcheck = $value;
				} else {
					$value          = array_slice( $value, 0, $attribs['count'] );
					$value4reqcheck = implode( '', $value );
				}
			}

			// missing required
			if ( true === $attribs['required'] && ( '' === $value4reqcheck || null === $value4reqcheck ) ) {
				$this->fields[ $field_name ]['errors'][] = in_array( $attribs['tag_kind'], [
					'text',
					'textarea'
				] ) ? 'missing required' : 'missing selected';
			}

			$value                                = $this->_validateValueRecursive( $value, $field_name, $attribs );
			$this->fields[ $field_name ]['value'] = $value;
		}

		return $this->fields;
	}

	public function _getPostAsArray( $input_encoding = null ): array {
		$ret = [];

		$query    = file_get_contents( 'php://input' );
		$key_vals = explode( '&', $query );
		foreach ( $key_vals as $key_val ) {
			@[$key, $val] = array_map( 'urldecode', explode( '=', $key_val ) );
			if ( $input_encoding ) {
				$key = $this->convertEncodingToIE( $key, $input_encoding );
				$val = $this->convertEncodingToIE( $val, $input_encoding );
			}
			@[$key_pref, ] = explode( '[', $key );
			if ( $key_pref !== $key ) {
				// don't parse explicit array with []
				$ret[ $key_pref ] = $this->stripMQGPC( @$_POST[ $key_pref ] );
			} elseif ( isset( $ret[ $key ] ) ) {
				// implicit array without []
				if ( is_array( $ret[ $key ] ) ) {
					// add a member of the array
					$ret[ $key ][] = $val;
				} else {
					// convert string into array
					$ret[ $key ] = [ $ret[ $key ], $val ];
				}
			} else {
				// string
				$ret[ $key ] = $val;
			}
		}

		return $ret;
	}

	public function convertEncodingToIE( $string, $input_encoding ) {
		if ( function_exists( 'mb_convert_encoding' ) ) {
			return mb_convert_encoding( $string, _CHARSET, $input_encoding );
		}

		if ( function_exists( 'iconv' ) ) {
			return iconv( $string, $input_encoding, _CHARSET );
		}

		return $string;
	}

	// fetch post data from RAW DATA

	public function stripMQGPC( $data ) {
		//trigger_error("assume magic_quotes_gpc is off", E_USER_NOTICE);
		return $data;
	}

	public function _validateValueRecursive( $value, $fn = null, $at = null ) {
		static $field_name, $attribs;

		if ( ! empty( $fn ) ) {
			$field_name = $fn;
		}
		if ( ! empty( $at ) ) {
			$attribs = $at;
		}

		if ( is_array( $value ) ) {
			return array_map( [ $this, '_validateValueRecursive' ], $value );
		}

		// tag_kind validation (range check)
		// select
		if ( 'select' === $attribs['tag_kind'] && ! $this->validateSelectOption( $attribs['tags'][0], $value ) ) {
			$this->fields[ $field_name ]['errors'][] = 'invalid option';
		}
		// radio/checkbox
		if ( in_array( $attribs['tag_kind'], [
				'radio',
				'checkbox'
			] ) && ! empty( $value ) && ! in_array( $value, $attribs['options'], true ) ) {
			$this->fields[ $field_name ]['errors'][] = 'invalid option';
		}
		// hidden
		if ( 'hidden' === $attribs['tag_kind'] ) {
			$value = @$attribs['options'][0];
		}

		// type checks & conversions
		switch ( $attribs['type'] ) {
			case 'int':
				$value = $this->convertZenToHan( trim( $value ) );
				if ( ! empty( $value ) ) {
					if ( is_numeric( $value ) ) {
						$value = (int) $value;
					} else {
						$this->fields[ $field_name ]['errors'][] = 'invalid number';
					}
				}
				break;

			case 'double':
				$value = $this->convertZenToHan( trim( $value ) );
				if ( ! empty( $value ) ) {
					if ( is_numeric( $value ) ) {
						$value = (float) $value;
					} else {
						$this->fields[ $field_name ]['errors'][] = 'invalid number';
					}
				}
				break;

			case 'telephone':
				$value = $this->convertZenToHan( trim( $value ) );
				if ( ! empty( $value ) && preg_match( '/[^()0-9+.-]/', $value ) ) {
					$this->fields[ $field_name ]['errors'][] = 'invalid general';
				}
				break;

			case 'email':
				$value = $this->convertZenToHan( $value );
				if ( ! empty( $value ) && ! $this->checkEmailAddress( $value ) ) {
					$this->fields[ $field_name ]['errors'][] = 'invalid email';
				}
				break;

            case 'url':
                $value = $this->convertZenToHan( $value );
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->fields[ $field_name ]['errors'][] = 'Invalid URL format';
                }
                break;

			case 'singlebytes':
				$value = $this->convertZenToHan( $value );
				break;

			default:
				if ( in_array( $attribs['type'], $this->types, true ) ) {
					// custom validator
					require_once $this->validator_dir . '/' . $attribs['type'] . '.php';
					$func_name = 'formprocess_validator_' . $attribs['type'];
					$value     = $func_name( $value, $field_name, $this );
				}
				break;
		}

		return $value;
	}

	public function validateSelectOption( $tag, $value ): ?bool {
		$value4html = htmlspecialchars( $value, ENT_QUOTES );

		[ $before, $options_html_tmp ] = explode( $tag, $this->form_html, 2 );
		[ $options_html, $after ] = explode( '</select>', $options_html_tmp, 2 );

		return strpos( $options_html, 'value="' . $value4html . '"' ) !== false;
	}

	public function convertZenToHan( $text ): string {
		if ( function_exists( 'mb_convert_kana' ) ) {
			return mb_convert_kana( $text, 'as' );
		}

		return $text;
	}

    function checkEmailAddress($email=null)
    {
        /*  email request variable */
        if (isset($_REQUEST['email'])) {
            $email = $_REQUEST['email'];
        }
        /* email address validation - return false */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            echo $email . ' is NOT a valid email address.';
            die();
        }
        /* email domain check return false
         * uncomment to validate domain
         * in production
        */
        /*
        $atPos = mb_strpos($email, '@');
        $domain = mb_substr($email, $atPos + 1);
        if (!checkdnsrr($domain . '.', 'MX')) {

            echo 'Domain "' . $domain . '" is not valid';
            die();
        }
        */
        /* email is valid return true */
        return true;
    }

	public function getErrors(): array {
		$ret = [];
		foreach ( $this->fields as $field_name => $attribs ) {
			if ( ! empty( $attribs['errors'] ) && is_array( $attribs['errors'] ) ) {
				foreach ( $attribs['errors'] as $error_msg ) {
					$ret[] = [
						'name'       => $field_name,
						'label4disp' => htmlspecialchars( $attribs['label'], ENT_QUOTES ),
						'message'    => $error_msg,
					];
				}
			}
		}

		return $ret;
	}

	public function replaceValues( $form_html = null ) {
		if ( empty( $form_html ) ) {
			$form_html = $this->form_html;
		}

		foreach ( $this->fields as $field_name => $attribs ) {
			switch ( $attribs['tag_kind'] ) {
				case 'textarea':
					$form_html = $this->replaceContentTextarea( $form_html, $attribs );
					break;
				case 'text':
					$form_html = $this->replaceValueTextbox( $form_html, $attribs );
					break;
				case 'select':
					$form_html = $this->replaceSelectedOptions( $form_html, $attribs );
					break;
				case 'radio':
					$form_html = $this->replaceCheckedRadios( $form_html, $attribs, $field_name );
					break;
				case 'checkbox':
					$form_html = $this->replaceCheckedCheckboxes( $form_html, $attribs, $field_name );
					break;
				default:
					break;
			}
		}

		return $form_html;
	}

	public function replaceContentTextarea( $form_html, $attribs ) {
		$value4html = htmlspecialchars( $attribs['value'], ENT_QUOTES );

		[ $before, $content_html_tmp ] = explode( $attribs['tags'][0], $form_html, 2 );
		[ $content_html, $after ] = explode( '</textarea>', $content_html_tmp, 2 );

		return $before . $attribs['tags'][0] . $value4html . '</textarea>' . $after;
	}

	public function replaceValueTextbox( $form_html, $attribs ) {
		$values = $attribs['value'];
		if ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		foreach ( array_keys( $values ) as $i ) {
			$value      = $values[ $i ];
			$tag        = @$attribs['tags'][ $i ];
			$old_tag    = $tag;
			$value4html = htmlspecialchars( $value, ENT_QUOTES );

			if ( stripos( $tag, 'value=' ) !== false ) {
				$new_tag = preg_replace( '/value=\"(.*)\"/', 'value="' . $value4html . '"', $old_tag );
			} else {
				//$new_tag = str_replace( '/>', 'value="' . $value4html . '" />', $old_tag ); TODO remove  trailing slash “/”
                $new_tag = str_replace( '>', 'value="' . $value4html . '">', $old_tag );
			}
			$form_html = str_replace( $old_tag, $new_tag, $form_html );
		}

		return $form_html;
	}

	public function replaceSelectedOptions( $form_html, $attribs ): string
    {
		$values = $attribs['value'];
		if ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		[ $before, $options_html_tmp ] = explode( $attribs['tags'][0], $form_html, 2 );

		[ $options_html, $after ] = explode( '</select>', $options_html_tmp, 2 );

		$new_options_html = str_replace( 'selected="selected"', '', $options_html );
		foreach ( $values as $value ) {
			$value4html = htmlspecialchars( $value, ENT_QUOTES );

			$new_options_html = str_replace( 'value="' . $value4html . '"', 'value="' . $value4html . '" selected="selected"', $new_options_html );
		}

		return $before . $attribs['tags'][0] . $new_options_html . '</select>' . $after;
	}

	public function replaceCheckedRadios( $form_html, $attribs, $field_name )
    {
		$value4html = htmlspecialchars( $attribs['value'], ENT_QUOTES );

		preg_match_all( '/<input\s+type="radio"[^>]*name="' . preg_quote( $field_name ) . '"[^>]*>/', $form_html, $matches, PREG_PATTERN_ORDER );

		$ret = $form_html;
		foreach ( $matches[0] as $match_from ) {
			$match_to = str_replace( 'checked="checked"', '', $match_from );
			if ( strpos( $match_from, 'value="' . $value4html . '"' ) !== false ) {
				$match_to = str_replace( 'value="' . $value4html . '"', 'value="' . $value4html . '" checked="checked"', $match_to );
			}
			$ret = str_replace( $match_from, $match_to, $ret );
		}

		return $ret;
	}

	public function replaceCheckedCheckboxes( $form_html, $attribs, $field_name )
    {
		$values = $attribs['value'];
		if ( ! is_array( $values ) ) {
			$values = [ $values ];
		}

		preg_match_all( '/<input\s+type\="checkbox"[^>]*name\="' . preg_quote( $attribs['field_name_raw'] ) . '"[^>]*>/', $form_html, $matches, PREG_PATTERN_ORDER );

		$ret = $form_html;
		foreach ( $matches[0] as $match_from ) {
			$match_to = str_replace( 'checked="checked"', '', $match_from );
			foreach ( $values as $value ) {
				$value4html = htmlspecialchars( $value, ENT_QUOTES );
				if ( strpos( $match_from, 'value="' . $value4html . '"' ) !== false ) {
					$match_to = str_replace( 'value="' . $value4html . '"', 'value="' . $value4html . '" checked="checked"', $match_to );
					break;
				}
			}
			$ret = str_replace( $match_from, $match_to, $ret );
		}

		return $ret;
	}

	public function renderForMail( $field_separator = "\n", $mid_separator = "\n" )
    {
		$ret = '';
		foreach ( $this->fields as $field_name => $attribs ) {
			$ret .= $field_separator . $attribs['label'] . $mid_separator;
			if ( 'linear' === $attribs['array_type'] ) {
				$ret .= implode( $this->column_separator, $attribs['value'] );
			} elseif ( $attribs['count'] > 1 && is_array( $attribs['value'] ) ) {
				$ret .= implode( $this->column_separator, $attribs['value'] );
			} else {
				$ret .= $attribs['value'];
			}
		}

		return $ret;
	}

	public function renderForDB(): array {
		$ret = [];
		foreach ( $this->fields as $field_name => $attribs ) {
			$ret[ $attribs['label'] ] = $attribs['value'];
		}

		return $ret;
	}
}
