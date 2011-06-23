<?php
// $Id: errorhandler.php,v 1.3 2007/06/24 07:54:19 nobunobu Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

/**
 * Error handler class
 *
 * @author Michael van Dam
 */
class XoopsErrorHandler
{
    /**
     * List of errors
     *
     * @var array
     * @access private
     */
    var $_errors = array();

    /**
     * Show error messages?
     *
     * @var boolean
     * @access private
     */
    var $_showErrors = false;

    /**
     * Was there a fatal error (E_USER_ERROR)
     *
     * @var boolean
     * @access private
     */
    var $_isFatal = false;

    /**
     * Constructor
     *
     * Registers the error handler and shutdown functions.  NOTE: when
     * registering an error handler, the setting or 'error_reporting' is
     * ignored and *everything* is trapped.
     */
    function XoopsErrorHandler()
    {
        set_error_handler('XoopsErrorHandler_HandleError');
        register_shutdown_function('XoopsErrorHandler_Shutdown');
    }

    /**
     * Get the (singleton) instance of the error handler
     *
     * @access public
     */
    function &getInstance()
    {
        static $instance = null;
        if (empty($instance)) {
            $instance = new XoopsErrorHandler;
        }
        return $instance;
    }

    /**
     * Activate the error handler
     *
     * @access public
     * @param boolean $showErrors True if debug mode is on
     * @return void
     */
    function activate($showErrors=false)
    {
        $this->_showErrors = $showErrors;
    }

    /**
     * Handle an error
     *
     * @param array $error Associative array containing error info
     * @access public
     * @return void
     */
    function handleError($error)
    {
        if ($error['errno'] == E_USER_ERROR) {
            $this->_isFatal = true;
            exit($error['errstr']);
        }
        if (($error['errno'] & error_reporting()) != $error['errno']) {
            return;
        }
        $this->_errors[] = $error;
    }

    /**
     * Render the list of errors
     *
     * NOTE: Unfortunately PHP 'fatal' and 'parse' errors are not trappable.
     * If the server has 'display_errors Off', then the result will be a
     * blank page.  It would be nice to print a message 'This page cannot
     * be displayed', but there seems to be no way to print this only when
     * exiting due to a fatal error rather than normal end of page.
     *
     * Thus, 'trigger_error' should be used to trap problems early and
     * display a meaningful message before a PHP fatal or parse error can
     * occur.
     *
     * @TODO Use CSS
     * @TODO Use language? or allow customized message?
     *
     * @access public
     * @return void
     */
    function renderErrors()
    {
		//
		// TODO We should plan new style about the following lines.
		//
        $output = '';
        if ($this->_isFatal) {
            $output .= 'This page cannot be displayed due to an internal error.<br/><br/>';
            $output .= 'If you are the administrator of this site, please visit the <a href="http://xoopscube.sourceforge.net/">XOOPS Cube Project Site</a> for assistance.<br/><br/>';
        }
        if (!$this->_showErrors || empty($this->_errors)) {
            return $output;
        }

        foreach( $this->_errors as $error )
        {
            switch ( $error['errno'] )
            {
                case E_USER_NOTICE:
                    $output .= "Notice [Xoops]: ";
                    break;
                case E_USER_WARNING:
                    $output .= "Warning [Xoops]: ";
                    break;
                case E_USER_ERROR:
                    $output .= "Error [Xoops]: ";
                    break;
                case E_NOTICE:
                    $output .= "Notice [PHP]: ";
                    break;
                case E_WARNING:
                    $output .= "Warning [PHP]: ";
                    break;
                default:
                    $output .= "Unknown Condition [" . $error['errno'] . "]: ";
            }
            $output .= sprintf( "%s in file %s line %s<br />\n", $error['errstr'], $error['errfile'], $error['errline'] );
        }
        return $output;
    }

}

/**
 * User-defined error handler (called from 'trigger_error')
 *
 * NOTE: Some recent versions of PHP have a 5th parameter, &$p_ErrContext
 * which is an associative array of all variables defined in scope in which
 * error occurred.  We cannot support this, for compatibility with older PHP.
 *
 * @access public
 * @param int $errNo Type of error
 * @param string $errStr Error message
 * @param string $errFile File in which error occurred
 * @param int $errLine Line number on which error occurred
 * @return void
 */
function XoopsErrorHandler_HandleError($errNo, $errStr, $errFile, $errLine)
{
    // NOTE: we only store relative pathnames
    $new_error = array(
        'errno' => $errNo,
        'errstr' => $errStr,
        'errfile' => preg_replace("|^" . XOOPS_ROOT_PATH . "/|", '', $errFile),
        'errline' => $errLine
        );
    $error_handler =& XoopsErrorHandler::getInstance();
    $error_handler->handleError($new_error);
}

/**
 * User-defined shutdown function (called from 'exit')
 *
 * @access public
 * @return void
 */
function XoopsErrorHandler_Shutdown()
{
    $error_handler =& XoopsErrorHandler::getInstance();
    echo $error_handler->renderErrors();
}

?>
