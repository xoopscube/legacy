<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_TRUST_PATH . '/modules/pico/class/FormProcessByHtml.class.php';

class PicoFormProcessBySmartyBase {
	public $mypluginname = 'base';
	public $mydirname;
	public $mod_url;
	public $content4disp;
	public $content_uri;
	public $session_index;
	public $form_processor;
	public $form_body;
	public $form_body4disp;
	public $extra_form = '';
	public $error_html = '';
	public $mail_body_pre = ''; // public
	public $mail_body_post = ''; // public
	public $mail_subject; // public
	public $toEmails = []; // public
	public $fromEmail; // public
	public $fromName; // public
	public $canPostAgain = true; // public
	public $finished_message; // public
	public $confirm_message; // public
	public $from_field_name = ''; // public
	public $fromname_field_name = ''; // public
	public $replyto_field_name = ''; // public
	public $cc_field_name = ''; // public
	public $cc_mail_body_pre = ''; // public
	public $cc_mail_body_post = ''; // public
	public $cc_mail_subject; // public

	public $ignore_field_names = [ 'cancel' ]; // public
	public $cancel_field_name = 'cancel'; // public

//	public function PicoFormProcessBySmartyBase(): PicoFormProcessBySmartyBase {
//		return $this->__construct();
//	}

	public function __construct()
 {
 }

	public function init( $params, $smarty ): void {
		$this->mydirname     = $smarty->_tpl_vars['mydirname'];
		$this->mod_url       = $smarty->_tpl_vars['mod_url'];
		$this->content4disp  = $smarty->_tpl_vars['content'];
		$this->content_uri   = pico_common_unhtmlspecialchars( XOOPS_URL . '/modules/' . $this->mydirname . '/' . $this->content4disp['link'] );
		$this->session_index = $this->mydirname . '_' . $this->content4disp['id'] . '_' . $this->mypluginname;
	}

	public function parseParameters( $params ): void {
		// mail_body_pre
		if ( ! empty( $params['mail_body_pre'] ) ) {
			$this->mail_body_pre = $params['mail_body_pre'];
		}

		// mail_body_post
		if ( ! empty( $params['mail_body_post'] ) ) {
			$this->mail_body_post = $params['mail_body_post'];
		}

		// mail_body_subject
		if ( isset( $params['mail_subject'] ) ) {
			$this->mail_subject = $params['mail_subject'];
		}

		// toEmails
		if ( ! empty( $params['to'] ) ) {
			$this->toEmails = explode( ',', $params['to'] );
		}

		// fromEmail
		if ( ! empty( $params['from'] ) ) {
			$this->fromEmail = $params['from'];
		}

		// fromName
		if ( ! empty( $params['from_name'] ) ) {
			$this->fromName = $params['from_name'];
		}

		// canPostAgain
		if ( isset( $params['can_post_again'] ) ) {
			$this->canPostAgain = (bool) $params['can_post_again'];
		}

		// finished_message
		if ( isset( $params['finished_message'] ) ) {
			$this->finished_message = $params['finished_message'];
		}

		// confirm_message
		if ( isset( $params['confirm_message'] ) ) {
			$this->confirm_message = $params['confirm_message'];
		}

		// field name for "from"
		if ( isset( $params['from_field_name'] ) ) {
			$this->from_field_name = $params['from_field_name'];
		}

		// field name for "fromname"
		if ( isset( $params['fromname_field_name'] ) ) {
			$this->fromname_field_name = $params['fromname_field_name'];
		}

		// field name for "reply-to"
		if ( isset( $params['replyto_field_name'] ) ) {
			$this->replyto_field_name = $params['replyto_field_name'];
		}

		// field name for sending "confirm mail"
		if ( isset( $params['cc_field_name'] ) ) {
			$this->cc_field_name = $params['cc_field_name'];
		}

		// cc_mail_body_pre
		if ( ! empty( $params['cc_mail_body_pre'] ) ) {
			$this->cc_mail_body_pre = $params['cc_mail_body_pre'];
		}

		// cc_mail_body_post
		if ( ! empty( $params['cc_mail_body_post'] ) ) {
			$this->cc_mail_body_post = $params['cc_mail_body_post'];
		}

		// cc_mail_subject
		if ( ! empty( $params['cc_mail_subject'] ) ) {
			$this->cc_mail_subject = $params['cc_mail_subject'];
		}
	}

	public function checkCurrentPage(): bool {
		global $xoopsModule;

        $page = $_GET['page'] ?? 'index';
		// session clear in contentmanager or makecontent
		if ( in_array( $page, [ 'contentmanager', 'makecontent' ] ) ) {
			unset( $_SESSION[ $this->session_index ] );

			return false;
		}

		// check this contents is in main area of pico
		if ( ! is_object( @$xoopsModule ) ) {
			return false;
		}
		if ( $xoopsModule->getVar( 'dirname' ) !== $this->mydirname ) {
			return false;
		}

		// if( intval( @$GLOBALS['content_id'] ) != $this->content4disp['id'] ) return false ;

		return true;
	}

	public function readLanguage( $filename = null ): void {
		if ( empty( $filename ) ) {
			$filename = $this->mypluginname;
		}

		// read language files for this plugin
		$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';
		require_once( $langmanpath );
		$langman = D3LanguageManager::getInstance();
		$langman->read( $filename . '.php', $this->mydirname, 'pico' );
	}

	public function fetchFormBody( $params, $smarty ): void {
		// get captured form
		if ( ! empty( $params['name'] ) && ! empty( $smarty->_smarty_vars['capture'][ $params['name'] ] ) ) {
			$this->form_body = $smarty->_smarty_vars['capture'][ $params['name'] ];
		} elseif ( (is_countable($smarty->_smarty_vars['capture']) ? count( $smarty->_smarty_vars['capture'] ) : 0) > 0 ) {
			$this->form_body = $smarty->_smarty_vars['capture']['default'];
		} else {
			echo '<em>confirm <{capture}><{/capture}> block exists before this tag</em>';
			$this->form_body = '';
		}
		$this->form_body4disp = $this->form_body;
	}

	public function isMobile(): ?bool {
		if ( class_exists( 'Wizin_User', false ) ) {
			// WizMobile (gusagi)
			$user = &Wizin_User::getSingleton();

			return $user->bIsMobile;
		}

		return defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER;
	}

	public function getInputEncoding() {
		if ( class_exists( 'Wizin_User' ) ) {
			// WizMobile (gusagi)
			$user = &Wizin_User::getSingleton();

			return $user->bIsMobile ? $user->sEncoding : null;
		}

		if ( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER ) {
			// hyp_common ktai-renderer (nao-pon)
			return HYP_POST_ENCODING;
			// judging by input (old code)
			// return mb_detect_encoding( urldecode( file_get_contents( 'php://input' ) ) , array( 'SJIS-Win' , 'SJIS' , 'EUCJP-Win' , 'EUC-JP' , 'UTF-8' ) ) ;
		}

		return null;
	}

	public function reload(): void {
		if ( ! headers_sent() && ! $this->isMobile() ) {
			header( 'Location: ' . $this->content_uri );
		} else {
			redirect_header( htmlspecialchars( $this->content_uri, ENT_QUOTES ), 1, '&nbsp;' );
		}
		exit;
	}

	public function replaceFormTag(): void {
		$this->form_body4disp = str_replace( '<form>', '<form action="' . htmlspecialchars( $this->content_uri, ENT_QUOTES ) . '" method="post">', $this->form_body4disp );
	}

	public function getTokenName(): string {
		return $this->mypluginname . '_confirm';
	}

	public function getTokenValue( $time = null ): string {
		if ( empty( $time ) ) {
			$time = time();
		}

		return md5( gmdate( 'YmdH', $time ) . XOOPS_DB_PREFIX . XOOPS_DB_NAME . XOOPS_ROOT_PATH );
	}

	public function validateToken(): bool {
		$value = @$_POST[ $this->getTokenName() ];
		if ( $value === $this->getTokenValue() ) {
			return true;
		}
		if ( $value === $this->getTokenValue( time() - 3600 ) ) {
			return true;
		}
		if ( $value === $this->getTokenValue( time() - 7200 ) ) {
			return true;
		}

		return false;
	}

	public function processConfirm(): void {
		// display confirm
		$this->extra_form = $this->confirm_message ?? '<form action="'
		                                              . htmlspecialchars( $this->content_uri, ENT_QUOTES )
		                                              . '" method="post">'
		                                              . _MD_PICO_FORMMAIL_BLOCK_POSTCONFIRM
		                                              . '<input type="hidden" name="'
		                                              . $this->getTokenName()
		                                              . '" value="'
		                                              . $this->getTokenValue()
		                                              . '"></form>';
	}

	public function displayFinished(): void {
		if ( isset( $this->finished_message ) ) {
			echo $this->finished_message;
		} else {
			echo '<div class="resultMsg form_finished">' . _MD_PICO_FORMMAIL_MSG_SENTSUCCESSFULLY . '</div>';
		}

		if ( $this->canPostAgain ) {
			// clear the session
			unset( $_SESSION[ $this->session_index ]['step'] );
		}
	}

	public function displayConfirm(): void {
		echo @$this->extra_form;
		echo @$this->error_html;
		echo @$this->form_body4disp;
	}

	public function displayDefault(): void {
		echo @$this->form_body4disp;
	}

	public function processError( $errors ): void {
		$this->error_html = _MD_PICO_FORMMAIL_BLOCK_ERROR_BEGIN;
		foreach ( $errors as $error ) {
			$constname = strtoupper( '_MD_PICO_FORMMAIL_ERRFMT_' . str_replace( ' ', '_', $error['message'] ) );
			if ( defined( $constname ) ) {
				$this->error_html .= sprintf( constant( $constname ), $error['label4disp'] );
			} else {
				$this->error_html .= '<li>' . $error['message'] . ':' . $error['label4disp'] . '</li>';
			}
		}
		$this->error_html .= _MD_PICO_FORMMAIL_BLOCK_ERROR_END;
	}

	public function execute( $params, $smarty ): void {
		// initials
		$this->init( $params, $smarty );
		if ( ! $this->checkCurrentPage() ) {
			return;
		}
		$this->readLanguage( 'formmail' );
		$this->fetchFormBody( $params, $smarty );

		// Form Processor
		$this->form_processor = new FormProcessByHtml();
		$this->form_processor->setFieldsByForm( $this->form_body, $this->ignore_field_names );

		// process post (then redirect)
		if ( ! empty( $_POST ) ) {
			if ( ! empty( $_POST[ $this->cancel_field_name ] ) ) {
				unset( $_SESSION[ $this->session_index ] );
			} elseif ( isset( $_POST[ $this->getTokenName() ] ) ) {
				if ( $this->validateToken() && isset( $_SESSION[ $this->session_index ]['fields'] ) ) {
					$this->form_processor->importSession( $_SESSION[ $this->session_index ]['fields'] );
					$errors = $this->form_processor->getErrors();
					if ( empty( $errors ) ) {
						$this->executeLast();
						// clear data part of session
						unset( $_SESSION[ $this->session_index ]['fields'] );
						$_SESSION[ $this->session_index ]['step'] = 'finished';
					}
				}
			} else {
				$_SESSION[ $this->session_index ]['fields'] = $this->form_processor->fetchPost( $this->getInputEncoding() );
				$_SESSION[ $this->session_index ]['step']   = 'confirm';
				//error_log( print_r( $_SESSION , 1 ) , 3 , '/tmp/error_log' ) ;
			}
			$this->reload();
		}
		//error_log( print_r( $_SESSION , 1 ) , 3 , '/tmp/error_log' ) ;

		// process get
		if ( isset( $_SESSION[ $this->session_index ]['fields'] ) ) {
			$this->form_processor->importSession( $_SESSION[ $this->session_index ]['fields'] );
			$errors = $this->form_processor->getErrors();
			if ( empty( $errors ) ) {
				$this->processConfirm(); // confirm
			} else {
				$this->processError( $errors ); // errors
			}

			// replace value="" / selected="selected"
			$this->form_body4disp = $this->form_processor->replaceValues( $this->form_body4disp );
		}
		$this->replaceFormTag();

		// display
		switch ( @$_SESSION[ $this->session_index ]['step'] ) {
			case 'finished':
				$this->displayFinished();
				break;
			case 'confirm':
				$this->displayConfirm();
				break;
			default:
				$this->displayDefault();
				break;
		}
		// @Test
		//var_dump( $_SESSION[ $this->session_index ]['fields']['favorite_fruits'] ) ;
		//var_dump( $_SESSION[ $this->session_index ]['fields']['selbox'] ) ;
		//var_dump( $_SESSION[ $this->session_index ]['fields'] ) ;

	}

	// abstract
	public function executeLast() {
		// send a mail
		// store into db
		// etc.
	}

	// methods for inside executeLast()
	public function sendMail(): void {
		$mail_body    = $this->makeMailBody();
		$cc_mail_body = $this->makeCCMailBody();
		$subject      = $this->makeMailSubject();
		$cc_subject   = $this->makeCCMailSubject();

		// easiestml
		if ( function_exists( 'easiestml' ) ) {
			$mail_body    = easiestml( $mail_body );
			$cc_mail_body = easiestml( $cc_mail_body );
			$subject      = easiestml( $subject );
			$cc_subject   = easiestml( $cc_subject );
		}

		// send main mail (server to admin/poster)
		if ( ! empty( $this->toEmails ) ) {
			// initialize
			$toMailer = &getMailer();
			$toMailer->useMail();
			$toMailer->setFromEmail( $this->fromEmail );
			$toMailer->setFromName( $this->fromName );

			// "from" overridden by form data
			if ( ! empty( $this->from_field_name ) && $this->isValidEmail( $this->form_processor->fields[ $this->from_field_name ]['value'] ) ) {
				$toMailer->setFromEmail( $this->form_processor->fields[ $this->from_field_name ]['value'] );
				if ( ! empty( $this->fromname_field_name ) && ! empty( $this->form_processor->fields[ $this->fromname_field_name ]['value'] ) ) {
					// remove cr, lf, null
					$toMailer->setFromName( str_replace( [
						"\n",
						"\r",
						"\0"
					], '', $this->form_processor->fields[ $this->fromname_field_name ]['value'] ) );
				}
			}

			// "Reply-To" header
			if ( ! empty( $this->replyto_field_name ) && $this->isValidEmail( $this->form_processor->fields[ $this->replyto_field_name ]['value'] ) ) {
				$toMailer->addHeaders( 'Reply-To: ' . $this->form_processor->fields[ $this->replyto_field_name ]['value'] );
			}

			$toMailer->setToEmails( array_unique( $this->toEmails ) );
			$toMailer->setSubject( $subject );
			$toMailer->setBody( $mail_body );
			$toMailer->send();
		}

		// send confirming mail (server to visitor)
		if ( ! empty( $this->cc_field_name ) && ! empty( $this->form_processor->fields[ $this->cc_field_name ]['value'] ) ) {
			// initialize
			$ccMailer = &getMailer();
			$ccMailer->useMail();
			$ccMailer->setFromEmail( $this->fromEmail );
			$ccMailer->setFromName( $this->fromName );

			$ccMailer->setToEmails( $this->form_processor->fields[ $this->cc_field_name ]['value'] );
			$ccMailer->setSubject( $cc_subject );
			$ccMailer->setBody( $cc_mail_body );
			$ccMailer->send();
		}
	}

	// mail utilities
	public function makeMailBody(): string {
		return $this->mail_body_pre . $this->content4disp['subject_raw'] . "\n" . 'URL: ' . $this->content_uri . "\n" . $this->form_processor->renderForMail( _MD_PICO_FORMMAIL_MAILFLDSEP, _MD_PICO_FORMMAIL_MAILMIDSEP ) . $this->mail_body_post;
	}

	public function makeCCMailBody(): string {
		return $this->cc_mail_body_pre . $this->content4disp['subject_raw'] . "\n" . 'URL: ' . $this->content_uri . "\n" . $this->form_processor->renderForMail( _MD_PICO_FORMMAIL_MAILFLDSEP, _MD_PICO_FORMMAIL_MAILMIDSEP ) . $this->cc_mail_body_post;
	}

	public function makeMailSubject(): string {
		return $this->mail_subject ?? sprintf( _MD_PICO_FORMMAIL_MAILSUBJECT, $this->content4disp['subject_raw'] );
	}

	public function makeCCMailSubject(): string {
		return $this->cc_mail_subject ?? sprintf( _MD_PICO_FORMMAIL_CCMAILSUBJECT, $this->content4disp['subject_raw'] );
	}

	public function countValidToEmails(): int {
		// simple check
		$ret = 0;
		foreach ( $this->toEmails as $mail ) {
			if ( $this->isValidEmail( $mail ) ) {
				$ret ++;
			}
		}

		return $ret;
	}

	public function isValidEmail( $email ): bool {
		//return preg_match( '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i', $email ) ? true : false;
        return (bool)preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i', $email);
	}

	public function storeDB(): void {
		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$content_id     = (int) $this->content4disp['id'];
		$extra_type4sql = addslashes( 'smarty_plugin::' . $this->mypluginname );
		$data4sql       = addslashes( pico_common_serialize( $this->form_processor->renderForDB() ) );
		$sql            = 'INSERT INTO ' . $db->prefix( $this->mydirname . '_content_extras' ) . " SET `content_id`=$content_id, `extra_type`='$extra_type4sql', `data`='$data4sql', created_time=UNIX_TIMESTAMP(), modified_time=UNIX_TIMESTAMP()";

		$db->queryF( $sql );
	}
}
