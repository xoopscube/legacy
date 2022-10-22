<?php
// GIJOE's Ticket Class (based on Marijuana's Oreteki XOOPS)
// nobunobu's suggestions are applied

if( ! class_exists( 'XoopsGTicket' ) ) {

    class XoopsGTicket
    {

        public $_errors = [];
        public $_latest_token = '';

        // render form as plain html
        public function getTicketHtml($salt = '', $timeout = 1800, $area = '')
        {
            return '<input type="hidden" name="XOOPS_G_TICKET" value="' . $this->issue($salt, $timeout, $area) . '" />';
        }

        // returns an object of XoopsFormHidden including theh ticket
    public function getTicketXoopsForm($salt = '', $timeout = 1800, $area = '')
        {
            return new XoopsFormHidden('XOOPS_G_TICKET', $this->issue($salt, $timeout, $area));
        }

        // add a ticket as Hidden Element into XoopsForm
    public function addTicketXoopsFormElement(&$form, $salt = '', $timeout = 1800, $area = '')
        {
            $form->addElement(new XoopsFormHidden('XOOPS_G_TICKET', $this->issue($salt, $timeout, $area)));
        }

        // returns an array for xoops_confirm() ;
    public function getTicketArray($salt = '', $timeout = 1800, $area = '')
        {
            return ['XOOPS_G_TICKET' => $this->issue($salt, $timeout, $area)];
        }

        // return GET parameter string.
    public function getTicketParamString($salt = '', $noamp = false, $timeout = 1800, $area = '')
        {
            return ($noamp ? '' : '&amp;') . 'XOOPS_G_TICKET=' . $this->issue($salt, $timeout, $area);
        }

        // issue a ticket
    public function issue($salt = '', $timeout = 1800, $area = '')
        {
            global $xoopsModule;

            // create a token
            [$usec, $sec] = explode(" ", microtime());
            $appendix_salt = empty($_SERVER['PATH']) ? XOOPS_DB_NAME : $_SERVER['PATH'];
            $token = crypt($salt . $usec . $appendix_salt . $sec);
            $this->_latest_token = $token;

            if (empty($_SESSION['XOOPS_G_STUBS'])) {
                $_SESSION['XOOPS_G_STUBS'] = [];
            }

            // limit max stubs 10
            if (count($_SESSION['XOOPS_G_STUBS']) > 10) {
                $_SESSION['XOOPS_G_STUBS'] = array_slice($_SESSION['XOOPS_G_STUBS'], -10);
            }

            // record referer if browser send it
            $referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['REQUEST_URI'];

            // area as module's dirname
            if (!$area && is_object(@$xoopsModule)) {
                $area = $xoopsModule->getVar('dirname');
            }

            // store stub
            $_SESSION['XOOPS_G_STUBS'][] = [
                'expire' => time() + $timeout,
                'referer' => $referer,
                'area' => $area,
                'token' => $token
            ];

            // paid md5ed token as a ticket
            return md5($token . XOOPS_DB_PREFIX);
        }

        // check a ticket
    public function check($post = true, $area = '')
        {
            global $xoopsModule;

            $this->_errors = [];

            // CHECK: stubs are not stored in session
            if (empty($_SESSION['XOOPS_G_STUBS']) || !is_array($_SESSION['XOOPS_G_STUBS'])) {
                $this->clear();
                $this->_errors[] = 'Invalid Session';
                return false;
            }

            // get key&val of the ticket from a user's query
            if ($post) {
                $ticket = empty($_POST['XOOPS_G_TICKET']) ? '' : $_POST['XOOPS_G_TICKET'];
            } else {
                $ticket = empty($_GET['XOOPS_G_TICKET']) ? '' : $_GET['XOOPS_G_TICKET'];
            }

            // CHECK: no tickets found
            if (empty($ticket)) {
                $this->clear();
                $this->_errors[] = 'Irregular post found';
                return false;
            }

            // gargage collection & find a right stub
            $stubs_tmp = $_SESSION['XOOPS_G_STUBS'];
            $_SESSION['XOOPS_G_STUBS'] = [];
            foreach ($stubs_tmp as $stub) {
                // default lifetime 30min
                if ($stub['expire'] >= time()) {
                    if (md5($stub['token'] . XOOPS_DB_PREFIX) === $ticket) {
                        $found_stub = $stub;
                    } else {
                        // store the other valid stubs into session
                        $_SESSION['XOOPS_G_STUBS'][] = $stub;
                    }
                } else {
                    if (md5($stub['token'] . XOOPS_DB_PREFIX) === $ticket) {
                        // not CSRF but Time-Out
                        $timeout_flag = true;
                    }
                }
            }

            // CHECK: the right stub found or not
            if (empty($found_stub)) {
                $this->clear();
                if (empty($timeout_flag)) {
                    $this->_errors[] = 'Invalid Session';
                }
                else {
                    $this->_errors[] = 'Time out';
                }
                return false;
            }

            // set area if necessary
            // area as module's dirname
            if (!$area && is_object(@$xoopsModule)) {
                $area = $xoopsModule->getVar('dirname');
            }

            // check area or referer
            if (@$found_stub['area'] == $area) {
                $area_check = true;
            }
            if (!empty($found_stub['referer']) && false !== strpos(@$_SERVER['HTTP_REFERER'], $found_stub['referer'])) {
                $referer_check = true;
            }

            // if( empty( $area_check ) || empty( $referer_check ) ) { // restrict
            if (empty($area_check) && empty($referer_check)) { // loose
                $this->clear();
                $this->_errors[] = 'Invalid area or referer';
                return false;
            }

            // all green
            return true;
        }


        // clear all stubs
    public function clear()
        {
            $_SESSION['XOOPS_G_STUBS'] = [];
        }


        // Ticket Using
    public function using()
        {
            if (!empty($_SESSION['XOOPS_G_STUBS'])) {
                return true;
            }

            return false;
        }


        // return errors
    public function getErrors($ashtml = true)
        {
            if ($ashtml) {
                $ret = '';
                foreach ($this->_errors as $msg) {
                    $ret .= "$msg<br>\n";
                }
            } else {
                $ret = $this->_errors;
            }
            return $ret;
        }

// end of class
    }

// create a instance in global scope
$GLOBALS['xoopsGTicket'] = new XoopsGTicket() ;

}

if( ! function_exists( 'admin_refcheck' ) ) {

//Admin Referer Check By Marijuana(Rev.011)
function admin_refcheck($chkref = "") {
	if( empty( $_SERVER['HTTP_REFERER'] ) ) {
		return true ;
	} else {
		$ref = $_SERVER['HTTP_REFERER'];
	}
	$cr = XOOPS_URL;
	if ( $chkref !== "" ) { $cr .= $chkref; }
    return !(strpos($ref, $cr) !== 0);
}

}
